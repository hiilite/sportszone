<?php
/**
 * Core Cover Image attachment class.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 2.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * BP Attachment Cover Image class.
 *
 * Extends BP Attachment to manage the cover images uploads.
 *
 * @since 2.4.0
 */
class SZ_Attachment_Cover_Image extends SZ_Attachment {
	/**
	 * The constuctor.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		// Allowed cover image types & upload size.
		$allowed_types        = sz_attachments_get_allowed_types();
		$max_upload_file_size = sz_attachments_get_max_upload_file_size('cover_image');

		parent::__construct( array(
			'action'                => 'sz_cover_image_upload',
			'file_input'            => 'file',
			'original_max_filesize' => sz_core_cover_image_original_max_filesize(),
			//'base_dir'              => sz_attachments_uploads_dir_get( 'dir' ),
			'required_wp_files'     => array( 'file', 'image' ),

			// Specific errors for cover images.
			'upload_error_strings'  => array(
				11  => sprintf( __( 'That image is too big. Please upload one smaller than %s', 'sportszone' ), size_format( $max_upload_file_size ) ),
				12  => sprintf( _n( 'Please upload only this file type: %s.', 'Please upload only these file types: %s.', count( $allowed_types ), 'sportszone' ), self::get_cover_image_types( $allowed_types ) ),
			),
		) );
	}

	/**
	 * Gets the available cover image types.
	 *
	 * @since 2.4.0
	 *
	 * @param array $allowed_types Array of allowed cover image types.
	 * @return string $value Comma-separated list of allowed cover image types.
	 */
	public static function get_cover_image_types( $allowed_types = array() ) {
		$types = array_map( 'strtoupper', $allowed_types );
		$comma = _x( ',', 'cover image types separator', 'sportszone' );
		return join( $comma . ' ', $types );
	}
	
	/**
	 * Set Upload Dir data for cover_images.
	 *
	 * @since 2.3.0
	 */
	public function set_upload_dir() {
		if ( sz_core_cover_image_upload_path() && sz_core_cover_image_url() ) {
			$this->upload_path = sz_core_cover_image_upload_path();
			$this->url         = sz_core_cover_image_url();
			$this->upload_dir  = sz_upload_dir();
		} else {
			parent::set_upload_dir();
		}
	}

	/**
	 * Cover image specific rules.
	 *
	 * Adds an error if the cover image size or type don't match SportsZone needs.
	 * The error code is the index of $upload_error_strings.
	 *
	 * @since 2.4.0
	 *
	 * @param array $file The temporary file attributes (before it has been moved).
	 * @return array $file The file with extra errors if needed.
	 */
	public function validate_upload( $file = array() ) {
		// Bail if already an error.
		if ( ! empty( $file['error'] ) ) {
			return $file;
		}

		// File size is too big.
		if ( ! sz_core_check_cover_image_size( array( 'file' => $file ) ) ) {
			$file['error'] = 9;

		// File is of invalid type.
		} elseif ( ! sz_core_check_cover_image_type( array( 'file' => $file ) ) ) {
			$file['error'] = 10;
		}


		// Return with error code attached.
		return $file;
	}
	
	/**
	 * Maybe shrink the attachment to fit maximum allowed width.
	 *
	 * @since 2.3.0
	 * @since 2.4.0 Add the $ui_available_width parameter, to inform about the Cover Image UI width.
	 *
	 * @param string $file               The absolute path to the file.
	 * @param int    $ui_available_width Available width for the UI.
	 * @return false|string|WP_Image_Editor|WP_Error
	 */
	public static function shrink( $file = '', $ui_available_width = 0 ) {
		// Get image size.
		$cover_image_data = parent::get_image_data( $file );

		// Init the edit args.
		$edit_args = array();

		// Defaults to the Cover Image original max width constant.
		$original_max_width = sz_core_cover_image_original_max_width();

		// The ui_available_width is defined and it's smaller than the Cover Image original max width.
		if ( ! empty( $ui_available_width ) && $ui_available_width < $original_max_width ) {
			/**
			 * In this case, to make sure the content of the image will be fully displayed
			 * during the cropping step, let's use the Cover Image UI Available width.
			 */
			$original_max_width = $ui_available_width;

			// $original_max_width has to be larger than the cover_image's full width
			if ( $original_max_width < sz_core_cover_image_full_width() ) {
				$original_max_width = sz_core_cover_image_full_width();
			}
		}

		// Do we need to resize the image?
		if ( isset( $cover_image_data['width'] ) && $cover_image_data['width'] > $original_max_width ) {
			$edit_args = array(
				'max_w' => $original_max_width,
				'max_h' => $original_max_width,
			);
		}

		// Do we need to rotate the image?
		$angles = array(
			3 => 180,
			6 => -90,
			8 =>  90,
		);

		if ( isset( $cover_image_data['meta']['orientation'] ) && isset( $angles[ $cover_image_data['meta']['orientation'] ] ) ) {
			$edit_args['rotate'] = $angles[ $cover_image_data['meta']['orientation'] ];
		}

		// No need to edit the cover_image, original file will be used.
		if ( empty( $edit_args ) ) {
			return false;

		// Add the file to the edit arguments.
		} else {
			$edit_args['file'] = $file;
		}

		return parent::edit_image( 'cover_image', $edit_args );
	}
	
	/**
	 * Check if the image dimensions are smaller than full cover_image dimensions.
	 *
	 * @since 2.3.0
	 *
	 *
	 * @param string $file the absolute path to the file.
	 * @return bool
	 */
	public static function is_too_small( $file = '' ) {
		$uploaded_image = @getimagesize( $file );
		$full_width     = sz_core_cover_image_full_width();
		$full_height    = sz_core_cover_image_full_height();

		if ( isset( $uploaded_image[0] ) && $uploaded_image[0] < $full_width || $uploaded_image[1] < $full_height ) {
			return true;
		}

		return false;
	}
	
	/**
	 * Crop the cover_image.
	 *
	 * @since 2.3.0
	 *
	 * @see  SZ_Attachment::crop for the list of parameters
	 *
	 * @param array $args Array of arguments for the cropping.
	 * @return array The cropped cover_images (full and thumb).
	 */
	public function crop( $args = array() ) {
		// Bail if the original file is missing.
		
		
		if ( empty( $args['original_file'] ) ) {
			return false;
		}
		
		if ( ! sz_attachments_current_user_can( 'edit_cover_image', $args ) ) {
			return false;
		}
		
		if ( 'user' === $args['object'] ) {
			$cover_image_dir = 'cover-images';
		} else {
			$cover_image_dir = sanitize_key( $args['object'] ) . '-cover-images';
		}
		
		$args['item_id'] = (int) $args['item_id'];
		
		/**
		 * Original file is a relative path to the image
		 * eg: /cover-images/1/cover_image.jpg
		 */
		$relative_path = sprintf( '/%s/%s/%s', $cover_image_dir, $args['item_id'], basename( $args['original_file'] ) );
		$absolute_path = $this->upload_path . $relative_path;
		// Bail if the cover_image is not available.
		if ( ! file_exists( $absolute_path ) )  {
			return false;
		}
		
		if ( empty( $args['item_id'] ) ) {

			/** This filter is documented in sz-core/sz-core-cover-images.php */
			$cover_image_folder_dir = apply_filters( 'sz_core_cover_image_folder_dir', dirname( $absolute_path ), $args['item_id'], $args['object'], $args['cover_image_dir'] );
		} else {

			/** This filter is documented in sz-core/sz-core-cover-images.php */
			$cover_image_folder_dir = apply_filters( 'sz_core_cover_image_folder_dir', $this->upload_path . '/' . $args['cover_image_dir'] . '/' . $args['item_id'], $args['item_id'], $args['object'], $args['cover_image_dir'] );
		}

		// Bail if the cover_image folder is missing for this item_id.
		if ( ! file_exists( $cover_image_folder_dir ) ) {
			return false;
		}
		
		// Delete the existing cover_image files for the object.
		$existing_cover_image = sz_core_fetch_cover_image( array(
			'object'  => $args['object'],
			'item_id' => $args['item_id'],
			'html' => false,
		) );

		/**
		 * Check that the new cover_image doesn't have the same name as the
		 * old one before deleting
		 */
		if ( ! empty( $existing_cover_image ) && $existing_cover_image !== $this->url . $relative_path ) {
			sz_core_delete_existing_cover_image( array( 'object' => $args['object'], 'item_id' => $args['item_id'], 'cover_image_path' => $cover_image_folder_dir ) );
		}

		// Make sure we at least have minimal data for cropping.
		if ( empty( $args['crop_w'] ) ) {
			$args['crop_w'] = sz_core_cover_image_full_width();
		}

		if ( empty( $args['crop_h'] ) ) {
			$args['crop_h'] = sz_core_cover_image_full_height();
		}
	
		// Get the file extension.
		$data = @getimagesize( $absolute_path );
		$ext  = $data['mime'] == 'image/png' ? 'png' : 'jpg';

		$args['original_file'] = $absolute_path;
		$args['src_abs']       = false;
		$cover_image_types = array( 'full' => '', 'thumb' => '' );

		foreach ( $cover_image_types as $key_type => $type ) {
			if ( 'thumb' === $key_type ) {
				$args['dst_w'] = sz_core_cover_image_thumb_width();
				$args['dst_h'] = sz_core_cover_image_thumb_height();
			} else {
				$args['dst_w'] = sz_core_cover_image_full_width();
				$args['dst_h'] = sz_core_cover_image_full_height();
			}

			$filename         = wp_unique_filename( $cover_image_folder_dir, uniqid() . "-bp{$key_type}.{$ext}" );
			$args['dst_file'] = $cover_image_folder_dir . '/' . $filename;

			$cover_image_types[ $key_type ] = parent::crop( $args );
		}

		// Remove the original.
		@unlink( $absolute_path );
		
		// Return the full and thumb cropped cover_images.
		return $cover_image_types;
	}
	
	/**
	 * Set the directory when uploading a file.
	 *
	 * @since 2.4.0
	 *
	 * @param array $upload_dir The original Uploads dir.
	 * @return array $value Upload data (path, url, basedir...).
	 */
	public function upload_dir_filter( $upload_dir = array() ) {
		return sz_attachments_cover_image_upload_dir();
	}

	/**
	 * Adjust the cover image to fit with advised width & height.
	 *
	 * @since 2.4.0
	 *
	 * @param string $file       The absolute path to the file.
	 * @param array  $dimensions Array of dimensions for the cover image.
	 * @return mixed
	 */
	public function fit( $file = '', $dimensions = array() ) {
		if ( empty( $dimensions['width'] ) || empty( $dimensions['height'] ) ) {
			return false;
		}

		// Get image size.
		$cover_data = parent::get_image_data( $file );

		// Init the edit args.
		$edit_args = array();

		// Do we need to resize the image?
		if ( ( isset( $cover_data['width'] ) && $cover_data['width'] > $dimensions['width'] ) ||
		( isset( $cover_data['height'] ) && $cover_data['height'] > $dimensions['height'] ) ) {
			$edit_args = array(
				'max_w' => $dimensions['width'],
				'max_h' => $dimensions['height'],
				'crop'  => true,
			);
		}

		// Do we need to rotate the image?
		$angles = array(
			3 => 180,
			6 => -90,
			8 =>  90,
		);

		if ( isset( $cover_data['meta']['orientation'] ) && isset( $angles[ $cover_data['meta']['orientation'] ] ) ) {
			$edit_args['rotate'] = $angles[ $cover_data['meta']['orientation'] ];
		}

		// No need to edit the cover_image, original file will be used.
		if ( empty( $edit_args ) ) {
			return false;

		// Add the file to the edit arguments.
		} else {
			$edit_args = array_merge( $edit_args, array( 'file' => $file, 'save' => false ) );
		}

		// Get the editor so that we can use a specific save method.
		$editor = parent::edit_image( 'cover_image', $edit_args );

		if ( is_wp_error( $editor ) )  {
			return $editor;
		} elseif ( ! is_a( $editor, 'WP_Image_Editor' ) ) {
			return false;
		}

		// Save the new image file.
		return $editor->save( $this->generate_filename( $file ) );
	}

	/**
	 * Generate a filename for the cover image.
	 *
	 * @since 2.4.0
	 *
	 * @param string $file The absolute path to the file.
	 * @return false|string $value The absolute path to the new file name.
	 */
	public function generate_filename( $file = '' ) {
		if ( empty( $file ) || ! file_exists( $file ) ) {
			return false;
		}

		$info = pathinfo( $file );
		$ext  = strtolower( $info['extension'] );
		$name = wp_unique_filename( $info['dirname'], uniqid() . "-sz-cover-image.$ext" );

		return trailingslashit( $info['dirname'] ) . $name;
	}

	/**
	 * Build script datas for the Uploader UI.
	 *
	 * @since 2.4.0
	 *
	 * @return array The javascript localization data
	 */
	public function script_data() {
		// Get default script data.
		$script_data = parent::script_data();

		if ( sz_is_user() ) {
			$item_id = sz_displayed_user_id();

			$script_data['sz_params'] = array(
				'object'          => 'user',
				'item_id'         => $item_id,
				'has_cover_image' => sz_attachments_get_user_has_cover_image( $item_id ),
				'nonces'  => array(
					'set'    => wp_create_nonce( 'sz_cover_image_cropstore' ),
					'remove' => wp_create_nonce( 'sz_delete_cover_image' ),
				),
			);

			// Set feedback messages.
			$script_data['feedback_messages'] = array(
				1 => __( 'Your new cover image was uploaded successfully.', 'sportszone' ),
				2 => __( 'There was a problem deleting your cover image. Please try again.', 'sportszone' ),
				3 => __( 'Your cover image was deleted successfully!', 'sportszone' ),
			);
		} elseif ( sz_is_group() ) {
			$item_id = sz_get_current_group_id();

			$script_data['sz_params'] = array(
				'object'          => 'group',
				'item_id'         => sz_get_current_group_id(),
				'has_cover_image' => sz_attachments_get_group_has_cover_image( $item_id ),
				'nonces'  => array(
					'set'    => wp_create_nonce( 'sz_cover_image_cropstore' ),
					'remove' => wp_create_nonce( 'sz_delete_cover_image' ),
				),
			);

			// Set feedback messages.
			$script_data['feedback_messages'] = array(
				1 => __( 'There was a problem cropping the group profile photo.', 'sportszone' ),
				2 => __( 'The group profile photo was uploaded successfully.', 'sportszone' ),
				3 => __( 'There was a problem deleting the group profile photo. Please try again.', 'sportszone' ),
				4 => __( 'The group profile photo was deleted successfully!', 'sportszone' ),
			);
		} else {

			/**
			 * Filters the cover image params to include specific SportsZone params for your object.
			 * e.g. Cover image for blogs single item.
			 *
			 * @since 2.4.0
			 *
			 * @param array $value The cover image specific SportsZone parameters.
			 */
			$script_data['sz_params'] = apply_filters( 'sz_attachment_cover_image_params', array() );
		}

		// Include our specific js & css.
		$script_data['extra_js']  = array( 'sz-cover-image' );
		$script_data['extra_css'] = array( 'sz-cover-image' );

		/**
		 * Filters the cover image script data.
		 *
		 * @since 2.4.0
		 *
		 * @param array $script_data Array of data for the cover image.
		 */
		return apply_filters( 'sz_attachments_cover_image_script_data', $script_data );
	}
}
