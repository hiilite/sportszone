<?php
/**
 * SportsZone Attachments functions.
 *
 * @package SportsZone
 * @subpackage Attachments
 * @since 2.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Check if the current WordPress version is using Plupload 2.1.1
 *
 * Plupload 2.1.1 was introduced in WordPress 3.9. Our sz-plupload.js
 * script requires it. So we need to make sure the current WordPress
 * match with our needs.
 *
 * @since 2.3.0
 * @since 3.0.0 We now require WP >= 4.5, so this is always true.
 *
 * @return bool Always true.
 */
function sz_attachments_is_wp_version_supported() {
	return true;
}

/**
 * Get the Attachments Uploads dir data.
 *
 * @since 2.4.0
 *
 * @param string $data The data to get. Possible values are: 'dir', 'basedir' & 'baseurl'.
 *                     Leave empty to get all datas.
 * @return string|array The needed Upload dir data.
 */
function sz_attachments_uploads_dir_get( $data = '' ) {
	$attachments_dir = 'sportszone';
	$retval          = '';

	if ( 'dir' === $data ) {
		$retval = $attachments_dir;
	} else {
		$upload_data = sz_upload_dir();

		// Return empty string, if Uploads data are not available.
		if ( ! $upload_data ) {
			return $retval;
		}

		// Build the Upload data array for SportsZone attachments.
		foreach ( $upload_data as $key => $value ) {
			if ( 'basedir' === $key || 'baseurl' === $key ) {
				$upload_data[ $key ] = trailingslashit( $value ) . $attachments_dir;

				// Fix for HTTPS.
				if ( 'baseurl' === $key && is_ssl() ) {
					$upload_data[ $key ] = str_replace( 'http://', 'https://', $upload_data[ $key ] );
				}
			} else {
				unset( $upload_data[ $key ] );
			}
		}

		// Add the dir to the array.
		$upload_data['dir'] = $attachments_dir;

		if ( empty( $data ) ) {
			$retval = $upload_data;
		} elseif ( isset( $upload_data[ $data ] ) ) {
			$retval = $upload_data[ $data ];
		}
	}

	/**
	 * Filter here to edit the Attachments upload dir data.
	 *
	 * @since 2.4.0
	 *
	 * @param string|array $retval The needed Upload dir data or the full array of data
	 * @param string       $data   The data requested
	 */
	return apply_filters( 'sz_attachments_uploads_dir_get', $retval, $data );
}

/**
 * Gets the upload dir array for cover images.
 *
 * @since 3.0.0
 *
 * @return array See wp_upload_dir().
 */
function sz_attachments_cover_image_upload_dir( $args = array() ) {
	// Default values are for profiles.
	$object_id = sz_displayed_user_id();

	if ( empty( $object_id ) ) {
		$object_id = sz_loggedin_user_id();
	}

	$object_directory = 'members';

	// We're in a group, edit default values.
	if ( sz_is_group() || sz_is_group_create() ) {
		$object_id        = sz_get_current_group_id();
		$object_directory = 'groups';
	}

	$r = sz_parse_args( $args, array(
		'object_id' => $object_id,
		'object_directory' => $object_directory,
	), 'cover_image_upload_dir' );


	// Set the subdir.
	$subdir  = '/' . $r['object_directory'] . '/' . $r['object_id'] . '/cover-image';

	$upload_dir = sz_attachments_uploads_dir_get();

	/**
	 * Filters the cover image upload directory.
	 *
	 * @since 2.4.0
	 *
	 * @param array $value      Array containing the path, URL, and other helpful settings.
	 * @param array $upload_dir The original Uploads dir.
	 */
	return apply_filters( 'sz_attachments_cover_image_upload_dir', array(
		'path'    => $upload_dir['basedir'] . $subdir,
		'url'     => set_url_scheme( $upload_dir['baseurl'] ) . $subdir,
		'subdir'  => $subdir,
		'basedir' => $upload_dir['basedir'],
		'baseurl' => set_url_scheme( $upload_dir['baseurl'] ),
		'error'   => false,
	), $upload_dir );
}

/**
 * Get the max upload file size for any attachment.
 *
 * @since 2.4.0
 *
 * @param string $type A string to inform about the type of attachment
 *                     we wish to get the max upload file size for.
 * @return int Max upload file size for any attachment.
 */
function sz_attachments_get_max_upload_file_size( $type = '' ) {
	$fileupload_maxk = sz_core_get_root_option( 'fileupload_maxk' );

	if ( '' === $fileupload_maxk ) {
		$fileupload_maxk = 5120000; // 5mb;
	} else {
		$fileupload_maxk = $fileupload_maxk * 1024;
	}

	/**
	 * Filter here to edit the max upload file size.
	 *
	 * @since 2.4.0
	 *
	 * @param int    $fileupload_maxk Max upload file size for any attachment.
	 * @param string $type            The attachment type (eg: 'avatar' or 'cover_image').
	 */
	return apply_filters( 'sz_attachments_get_max_upload_file_size', $fileupload_maxk, $type );
}

/**
 * Get allowed types for any attachment.
 *
 * @since 2.4.0
 *
 * @param string $type The extension types to get.
 *                     Default: 'avatar'.
 * @return array The list of allowed extensions for attachments.
 */
function sz_attachments_get_allowed_types( $type = 'avatar' ) {
	// Defaults to SportsZone supported image extensions.
	$exts = array( 'jpeg', 'gif', 'png' );

	/**
	 * It's not a SportsZone feature, get the allowed extensions
	 * matching the $type requested.
	 */
	if ( 'avatar' !== $type && 'cover_image' !== $type ) {
		// Reset the default exts.
		$exts = array();

		switch ( $type ) {
			case 'video' :
				$exts = wp_get_video_extensions();
			break;

			case 'audio' :
				$exts = wp_get_video_extensions();
			break;

			default:
				$allowed_mimes = get_allowed_mime_types();

				/**
				 * Search for allowed mimes matching the type.
				 *
				 * Eg: using 'application/vnd.oasis' as the $type
				 * parameter will get all OpenOffice extensions supported
				 * by WordPress and allowed for the current user.
				 */
				if ( '' !== $type ) {
					$allowed_mimes = preg_grep( '/' . addcslashes( $type, '/.+-' ) . '/', $allowed_mimes );
				}

				$allowed_types = array_keys( $allowed_mimes );

				// Loop to explode keys using '|'.
				foreach ( $allowed_types as $allowed_type ) {
					$t = explode( '|', $allowed_type );
					$exts = array_merge( $exts, (array) $t );
				}
			break;
		}
	}

	/**
	 * Filter here to edit the allowed extensions by attachment type.
	 *
	 * @since 2.4.0
	 *
	 * @param array  $exts List of allowed extensions.
	 * @param string $type The requested file type.
	 */
	return apply_filters( 'sz_attachments_get_allowed_types', $exts, $type );
}

/**
 * Get allowed attachment mime types.
 *
 * @since 2.4.0
 *
 * @param string $type          The extension types to get (Optional).
 * @param array  $allowed_types List of allowed extensions.
 * @return array List of allowed mime types.
 */
function sz_attachments_get_allowed_mimes( $type = '', $allowed_types = array() ) {
	if ( empty( $allowed_types ) ) {
		$allowed_types = sz_attachments_get_allowed_types( $type );
	}

	$validate_mimes = wp_match_mime_types( join( ',', $allowed_types ), wp_get_mime_types() );
	$allowed_mimes  = array_map( 'implode', $validate_mimes );

	/**
	 * Include jpg type if jpeg is set
	 */
	if ( isset( $allowed_mimes['jpeg'] ) && ! isset( $allowed_mimes['jpg'] ) ) {
		$allowed_mimes['jpg'] = $allowed_mimes['jpeg'];
	}

	return $allowed_mimes;
}

/**
 * Check the uploaded attachment type is allowed.
 *
 * @since 2.4.0
 *
 * @param string $file          Full path to the file.
 * @param string $filename      The name of the file (may differ from $file due to $file being
 *                              in a tmp directory).
 * @param array  $allowed_mimes The attachment allowed mimes (Required).
 * @return bool True if the attachment type is allowed. False otherwise
 */
function sz_attachments_check_filetype( $file, $filename, $allowed_mimes ) {
	$filetype = wp_check_filetype_and_ext( $file, $filename, $allowed_mimes );

	if ( ! empty( $filetype['ext'] ) && ! empty( $filetype['type'] ) ) {
		return true;
	}

	return false;
}

/**
 * Use the absolute path to an image to set an attachment type for a given item.
 *
 * @since 2.4.0
 *
 * @param string $type The attachment type to create (avatar or cover_image). Default: avatar.
 * @param array  $args {
 *     @type int    $item_id   The ID of the object (Required). Default: 0.
 *     @type string $object    The object type (eg: group, user, blog) (Required). Default: 'user'.
 *     @type string $component The component for the object (eg: groups, xprofile, blogs). Default: ''.
 *     @type string $image     The absolute path to the image (Required). Default: ''.
 *     @type int    $crop_w    Crop width. Default: 0.
 *     @type int    $crop_h    Crop height. Default: 0.
 *     @type int    $crop_x    The horizontal starting point of the crop. Default: 0.
 *     @type int    $crop_y    The vertical starting point of the crop. Default: 0.
 * }
 * @return bool True on success, false otherwise.
 */
function sz_attachments_create_item_type( $type = 'avatar', $args = array() ) {
	if ( empty( $type ) || ( $type !== 'avatar' && $type !== 'cover_image' ) ) {
		return false;
	}

	$r = sz_parse_args( $args, array(
		'item_id'   => 0,
		'object'    => 'user',
		'component' => '',
		'image'     => '',
		'crop_w'    => 0,
		'crop_h'    => 0,
		'crop_x'    => 0,
		'crop_y'    => 0
	), 'create_item_' . $type );

	if ( empty( $r['item_id'] ) || empty( $r['object'] ) || ! file_exists( $r['image'] ) || ! @getimagesize( $r['image'] ) ) {
		return false;
	}

	// Make sure the file path is safe.
	if ( 1 === validate_file( $r['image'] ) ) {
		return false;
	}

	// Set the component if not already done.
	if ( empty( $r['component'] ) ) {
		if ( 'user' === $r['object'] ) {
			$r['component'] = 'xprofile';
		} else {
			$r['component'] = $r['object'] . 's';
		}
	}

	// Get allowed mimes for the Attachment type and check the image one is.
	$allowed_mimes = sz_attachments_get_allowed_mimes( $type );
	$is_allowed    = wp_check_filetype( $r['image'], $allowed_mimes );

	// It's not an image.
	if ( ! $is_allowed['ext'] ) {
		return false;
	}

	// Init the Attachment data.
	$attachment_data = array();

	if ( 'avatar' === $type ) {
		// Set crop width for the avatar if not given.
		if ( empty( $r['crop_w'] ) ) {
			$r['crop_w'] = sz_core_avatar_full_width();
		}

		// Set crop height for the avatar if not given.
		if ( empty( $r['crop_h'] ) ) {
			$r['crop_h'] = sz_core_avatar_full_height();
		}

		if ( is_callable( $r['component'] . '_avatar_upload_dir' ) ) {
			$dir_args = array( $r['item_id'] );

			// In case  of xprofile, we need an extra argument.
			if ( 'xprofile' === $r['component'] ) {
				$dir_args = array( false, $r['item_id'] );
			}

			$attachment_data = call_user_func_array( $r['component'] . '_avatar_upload_dir', $dir_args );
		}
	} elseif ( 'cover_image' === $type ) {
		/*$attachment_data = sz_attachments_cover_image_upload_dir();

		// The BP Attachments Uploads Dir is not set, stop.
		if ( ! $attachment_data ) {
			return false;
		}

		// Default to members for xProfile.
		$object_subdir = 'members';

		if ( 'xprofile' !== $r['component'] ) {
			$object_subdir = sanitize_key( $r['component'] );
		}

		// Set Subdir.
		$attachment_data['subdir'] = $object_subdir . '/' . $r['item_id'] . '/cover-image';

		// Set Path.
		$attachment_data['path'] = trailingslashit( $attachment_data['basedir'] ) . $attachment_data['subdir'];
		*/
		
		// Set crop width for the avatar if not given.
		if ( empty( $r['crop_w'] ) ) {
			$r['crop_w'] = sz_core_cover_image_full_width();
		}

		// Set crop height for the avatar if not given.
		if ( empty( $r['crop_h'] ) ) {
			$r['crop_h'] = sz_core_cover_image_full_height();
		}

		if ( is_callable( $r['component'] . '_cover_image_upload_dir' ) ) {
			$dir_args = array( $r['item_id'] );

			// In case  of xprofile, we need an extra argument.
			if ( 'xprofile' === $r['component'] ) {
				$dir_args = array( false, $r['item_id'] );
			}

			$attachment_data = call_user_func_array( $r['component'] . '_cover_image_upload_dir', $dir_args );
		}
		
	}

	if ( ! isset( $attachment_data['path'] ) || ! isset( $attachment_data['subdir'] ) ) {
		return false;
	}

	// It's not a regular upload, we may need to create some folders.
	if ( ! is_dir( $attachment_data['path'] ) ) {
		if ( ! wp_mkdir_p( $attachment_data['path'] ) ) {
			return false;
		}
	}

	// Set the image name and path.
	$image_file_name = wp_unique_filename( $attachment_data['path'], basename( $r['image'] ) );
	$image_file_path = $attachment_data['path'] . '/' . $image_file_name;

	// Copy the image file into the avatar dir.
	if ( ! copy( $r['image'], $image_file_path ) ) {
		return false;
	}

	// Init the response.
	$created = false;

	// It's an avatar, we need to crop it.
	if ( 'avatar' === $type ) {
		$created = sz_core_avatar_handle_crop( array(
			'object'        => $r['object'],
			'avatar_dir'    => trim( dirname( $attachment_data['subdir'] ), '/' ),
			'item_id'       => (int) $r['item_id'],
			'original_file' => trailingslashit( $attachment_data['subdir'] ) . $image_file_name,
			'crop_w'        => $r['crop_w'],
			'crop_h'        => $r['crop_h'],
			'crop_x'        => $r['crop_x'],
			'crop_y'        => $r['crop_y']
		) );

	// It's a cover image we need to fit it to feature's dimensions.
	} elseif ( 'cover_image' === $type ) {
		/*$cover_image = sz_attachments_cover_image_generate_file( array(
			'file'            => $image_file_path,
			'component'       => $r['component'],
			'cover_image_dir' => $attachment_data['path']
		) );

		$created = ! empty( $cover_image['cover_file'] );*/
		
		$created = sz_core_avatar_handle_crop( array(
			'object'        => $r['object'],
			'cover_image_dir'    => trim( dirname( $attachment_data['subdir'] ), '/' ),
			'item_id'       => (int) $r['item_id'],
			'original_file' => trailingslashit( $attachment_data['subdir'] ) . $image_file_name,
			'crop_w'        => $r['crop_w'],
			'crop_h'        => $r['crop_h'],
			'crop_x'        => $r['crop_x'],
			'crop_y'        => $r['crop_y']
		) );
	}

	// Remove copied file if it fails.
	if ( ! $created ) {
		@unlink( $image_file_path );
	}

	// Return the response.
	return $created;
}

/**
 * Get the url or the path for a type of attachment.
 *
 * @since 2.4.0
 *
 * @param string $data whether to get the url or the path.
 * @param array  $args {
 *     @type string $object_dir  The object dir (eg: members/groups). Defaults to members.
 *     @type int    $item_id     The object id (eg: a user or a group id). Defaults to current user.
 *     @type string $type        The type of the attachment which is also the subdir where files are saved.
 *                               Defaults to 'cover-image'
 *     @type string $file        The name of the file.
 * }
 * @return string|bool The url or the path to the attachment, false otherwise
 */
function sz_attachments_get_attachment( $data = 'url', $args = array() ) {
	// Default value.
	$attachment_data = false;

	$r = sz_parse_args( $args, array(
		'object_dir' => 'members',
		'item_id'    => sz_loggedin_user_id(),
		'type'       => 'cover-image',
		'file'       => '',
	), 'attachments_get_attachment_src' );

	/**
	 * Filters whether or not to handle fetching a SportsZone image attachment.
	 *
	 * If you want to override this function, make sure you return false.
	 *
	 * @since 2.5.1
	 *
	 * @param null|string $value If null is returned, proceed with default behaviour. Otherwise, value returned verbatim.
	 * @param array $r {
	 *     @type string $object_dir The object dir (eg: members/groups). Defaults to members.
	 *     @type int    $item_id    The object id (eg: a user or a group id). Defaults to current user.
	 *     @type string $type       The type of the attachment which is also the subdir where files are saved.
	 *                              Defaults to 'cover-image'
	 *     @type string $file       The name of the file.
	 * }
	 */
	$pre_filter = apply_filters( 'sz_attachments_pre_get_attachment', null, $r );
	if ( $pre_filter !== null ) {
		return $pre_filter;
	}

	// Get SportsZone Attachments Uploads Dir datas.
	$sz_attachments_uploads_dir = sz_attachments_uploads_dir_get();

	// The BP Attachments Uploads Dir is not set, stop.
	if ( ! $sz_attachments_uploads_dir ) {
		return $attachment_data;
	}

	$type_subdir = $r['object_dir'] . '/' . $r['item_id'] . '/' . $r['type'];
	$type_dir    = trailingslashit( $sz_attachments_uploads_dir['basedir'] ) . $type_subdir;

	if ( 1 === validate_file( $type_dir ) || ! is_dir( $type_dir ) ) {
		return $attachment_data;
	}

	if ( ! empty( $r['file'] ) ) {
		if ( ! file_exists( trailingslashit( $type_dir ) . $r['file'] ) ) {
			return $attachment_data;
		}

		if ( 'url' === $data ) {
			$attachment_data = trailingslashit( $sz_attachments_uploads_dir['baseurl'] ) . $type_subdir . '/' . $r['file'];
		} else {
			$attachment_data = trailingslashit( $type_dir ) . $r['file'];
		}

	} else {
		$file = false;

		// Open the directory and get the first file.
		if ( $att_dir = opendir( $type_dir ) ) {

			while ( false !== ( $attachment_file = readdir( $att_dir ) ) ) {
				// Look for the first file having the type in its name.
				if ( false !== strpos( $attachment_file, $r['type'] ) && empty( $file ) ) {
					$file = $attachment_file;
					break;
				}
			}
		}

		if ( empty( $file ) ) {
			return $attachment_data;
		}

		if ( 'url' === $data ) {
			$attachment_data = trailingslashit( $sz_attachments_uploads_dir['baseurl'] ) . $type_subdir . '/' . $file;
		} else {
			$attachment_data = trailingslashit( $type_dir ) . $file;
		}
	}

	return $attachment_data;
}

/**
 * Delete an attachment for the given arguments
 *
 * @since 2.4.0
 *
 * @see sz_attachments_get_attachment() For more information on accepted arguments.
 *
 * @param array $args Array of arguments for the attachment deletion.
 * @return bool True if the attachment was deleted, false otherwise.
 */
function sz_attachments_delete_file( $args = array() ) {
	$attachment_path = sz_attachments_get_attachment( 'path', $args );

	/**
	 * Filters whether or not to handle deleting an existing SportsZone attachment.
	 *
	 * If you want to override this function, make sure you return false.
	 *
	 * @since 2.5.1
	 *
	 * @param bool $value Whether or not to delete the SportsZone attachment.
`	 * @param array $args Array of arguments for the attachment deletion.
	 */
	if ( ! apply_filters( 'sz_attachments_pre_delete_file', true, $args ) ) {
		return true;
	}

	if ( empty( $attachment_path ) ) {
		return false;
	}

	@unlink( $attachment_path );
	return true;
}

/**
 * Get the SportsZone Plupload settings.
 *
 * @since 2.3.0
 *
 * @return array List of SportsZone Plupload settings.
 */
function sz_attachments_get_plupload_default_settings() {

	$max_upload_size = wp_max_upload_size();

	if ( ! $max_upload_size ) {
		$max_upload_size = 0;
	}

	$defaults = array(
		'runtimes'            => 'html5,flash,silverlight,html4',
		'file_data_name'      => 'file',
		'multipart_params'    => array(
			'action'          => 'sz_upload_attachment',
			'_wpnonce'        => wp_create_nonce( 'sz-uploader' ),
		),
		'url'                 => admin_url( 'admin-ajax.php', 'relative' ),
		'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
		'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
		'filters' => array(
			'max_file_size'   => $max_upload_size . 'b',
		),
		'multipart'           => true,
		'urlstream_upload'    => true,
	);

	// WordPress is not allowing multi selection for iOs 7 device.. See #29602.
	if ( wp_is_mobile() && strpos( $_SERVER['HTTP_USER_AGENT'], 'OS 7_' ) !== false &&
		strpos( $_SERVER['HTTP_USER_AGENT'], 'like Mac OS X' ) !== false ) {

		$defaults['multi_selection'] = false;
	}

	$settings = array(
		'defaults' => $defaults,
		'browser'  => array(
			'mobile'    => wp_is_mobile(),
			'supported' => _device_can_upload(),
		),
		'limitExceeded' => is_multisite() && ! is_upload_space_available(),
	);

	/**
	 * Filter the SportsZone Plupload default settings.
	 *
	 * @since 2.3.0
	 *
	 * @param array $settings Default Plupload parameters array.
	 */
	return apply_filters( 'sz_attachments_get_plupload_default_settings', $settings );
}

/**
 * Builds localization strings for the SportsZone Uploader scripts.
 *
 * @since 2.3.0
 *
 * @return array Plupload default localization strings.
 */
function sz_attachments_get_plupload_l10n() {
	// Localization strings.
	return apply_filters( 'sz_attachments_get_plupload_l10n', array(
			'queue_limit_exceeded'      => __( 'You have attempted to queue too many files.', 'sportszone' ),
			'file_exceeds_size_limit'   => __( '%s exceeds the maximum upload size for this site.', 'sportszone' ),
			'zero_byte_file'            => __( 'This file is empty. Please try another.', 'sportszone' ),
			'invalid_filetype'          => __( 'This file type is not allowed. Please try another.', 'sportszone' ),
			'not_an_image'              => __( 'This file is not an image. Please try another.', 'sportszone' ),
			'image_memory_exceeded'     => __( 'Memory exceeded. Please try another smaller file.', 'sportszone' ),
			'image_dimensions_exceeded' => __( 'This is larger than the maximum size. Please try another.', 'sportszone' ),
			'default_error'             => __( 'An error occurred. Please try again later.', 'sportszone' ),
			'missing_upload_url'        => __( 'There was a configuration error. Please contact the server administrator.', 'sportszone' ),
			'upload_limit_exceeded'     => __( 'You may only upload 1 file.', 'sportszone' ),
			'http_error'                => __( 'HTTP error.', 'sportszone' ),
			'upload_failed'             => __( 'Upload failed.', 'sportszone' ),
			'big_upload_failed'         => __( 'Please try uploading this file with the %1$sbrowser uploader%2$s.', 'sportszone' ),
			'big_upload_queued'         => __( '%s exceeds the maximum upload size for the multi-file uploader when used in your browser.', 'sportszone' ),
			'io_error'                  => __( 'IO error.', 'sportszone' ),
			'security_error'            => __( 'Security error.', 'sportszone' ),
			'file_cancelled'            => __( 'File canceled.', 'sportszone' ),
			'upload_stopped'            => __( 'Upload stopped.', 'sportszone' ),
			'dismiss'                   => __( 'Dismiss', 'sportszone' ),
			'crunching'                 => __( 'Crunching&hellip;', 'sportszone' ),
			'unique_file_warning'       => __( 'Make sure to upload a unique file', 'sportszone' ),
			'error_uploading'           => __( '&#8220;%s&#8221; has failed to upload.', 'sportszone' ),
			'has_avatar_warning'        => __( 'If you&#39;d like to delete the existing profile photo but not upload a new one, please use the delete tab.', 'sportszone' ),
			'has_cover_image_warning'        => __( 'If you&#39;d like to delete the existing profile photo but not upload a new one, please use the delete tab.', 'sportszone' )
	) );
}

/**
 * Enqueues the script needed for the Uploader UI.
 *
 * @since 2.3.0
 *
 * @see SZ_Attachment::script_data() && SZ_Attachment_Avatar::script_data() for examples showing how
 * to set specific script data.
 *
 * @param string $class Name of the class extending SZ_Attachment (eg: SZ_Attachment_Avatar).
 * @return null|WP_Error
 */
function sz_attachments_enqueue_scripts( $class = '' ) {
	// Enqueue me just once per page, please.
	if ( did_action( 'sz_attachments_enqueue_scripts' ) ) {
		return;
	}

	if ( ! $class || ! class_exists( $class ) ) {
		return new WP_Error( 'missing_parameter' );
	}

	// Get an instance of the class and get the script data.
	$attachment = new $class;
	$script_data  = $attachment->script_data();

	$args = sz_parse_args( $script_data, array(
		'action'            => '',
		'file_data_name'    => '',
		'max_file_size'     => 0,
		'browse_button'     => 'sz-browse-button',
		'container'         => 'sz-upload-ui',
		'drop_element'      => 'drag-drop-area',
		'sz_params'         => array(),
		'extra_css'         => array(),
		'extra_js'          => array(),
		'feedback_messages' => array(),
	), 'attachments_enqueue_scripts' );

	if ( empty( $args['action'] ) || empty( $args['file_data_name'] ) ) {
		return new WP_Error( 'missing_parameter' );
	}

	// Get the SportsZone uploader strings.
	$strings = sz_attachments_get_plupload_l10n();

	// Get the SportsZone uploader settings.
	$settings = sz_attachments_get_plupload_default_settings();

	// Set feedback messages.
	if ( ! empty( $args['feedback_messages'] ) ) {
		$strings['feedback_messages'] = $args['feedback_messages'];
	}

	// Use a temporary var to ease manipulation.
	$defaults = $settings['defaults'];

	// Set the upload action.
	$defaults['multipart_params']['action'] = $args['action'];

	// Set SportsZone upload parameters if provided.
	if ( ! empty( $args['sz_params'] ) ) {
		$defaults['multipart_params']['sz_params'] = $args['sz_params'];
	}

	// Merge other arguments.
	$ui_args = array_intersect_key( $args, array(
		'file_data_name' => true,
		'browse_button'  => true,
		'container'      => true,
		'drop_element'   => true,
	) );

	$defaults = array_merge( $defaults, $ui_args );

	if ( ! empty( $args['max_file_size'] ) ) {
		$defaults['filters']['max_file_size'] = $args['max_file_size'] . 'b';
	}

	// Specific to SportsZone Avatars.
	if ( 'sz_avatar_upload' === $defaults['multipart_params']['action'] ) {

		// Include the cropping informations for avatars.
		$settings['crop'] = array(
			'full_h'  => sz_core_avatar_full_height(),
			'full_w'  => sz_core_avatar_full_width(),
		);

		// Avatar only need 1 file and 1 only!
		$defaults['multi_selection'] = false;

		// Does the object already has an avatar set.
		$has_avatar = $defaults['multipart_params']['sz_params']['has_avatar'];

		// What is the object the avatar belongs to.
		$object = $defaults['multipart_params']['sz_params']['object'];

		// Init the Avatar nav.
		$avatar_nav = array(
			'upload' => array( 'id' => 'upload', 'caption' => __( 'Upload', 'sportszone' ), 'order' => 0  ),

			// The delete view will only show if the object has an avatar.
			'delete' => array( 'id' => 'delete', 'caption' => __( 'Delete', 'sportszone' ), 'order' => 100, 'hide' => (int) ! $has_avatar ),
		);

		// Create the Camera Nav if the WebCam capture feature is enabled.
		if ( sz_avatar_use_webcam() && 'user' === $object ) {
			$avatar_nav['camera'] = array( 'id' => 'camera', 'caption' => __( 'Take Photo', 'sportszone' ), 'order' => 10 );

			// Set warning messages.
			$strings['camera_warnings'] = array(
				'requesting'  => __( 'Please allow us to access to your camera.', 'sportszone'),
				'loading'     => __( 'Please wait as we access your camera.', 'sportszone' ),
				'loaded'      => __( 'Camera loaded. Click on the "Capture" button to take your photo.', 'sportszone' ),
				'noaccess'    => __( 'It looks like you do not have a webcam or we were unable to get permission to use your webcam. Please upload a photo instead.', 'sportszone' ),
				'errormsg'    => __( 'Your browser is not supported. Please upload a photo instead.', 'sportszone' ),
				'videoerror'  => __( 'Video error. Please upload a photo instead.', 'sportszone' ),
				'ready'       => __( 'Your profile photo is ready. Click on the "Save" button to use this photo.', 'sportszone' ),
				'nocapture'   => __( 'No photo was captured. Click on the "Capture" button to take your photo.', 'sportszone' ),
			);
		}

		/**
		 * Use this filter to add a navigation to a custom tool to set the object's avatar.
		 *
		 * @since 2.3.0
		 *
		 * @param array  $avatar_nav {
		 *     An associative array of available nav items where each item is an array organized this way:
		 *     $avatar_nav[ $nav_item_id ].
		 *     @type string $nav_item_id The nav item id in lower case without special characters or space.
		 *     @type string $caption     The name of the item nav that will be displayed in the nav.
		 *     @type int    $order       An integer to specify the priority of the item nav, choose one.
		 *                               between 1 and 99 to be after the uploader nav item and before the delete nav item.
		 *     @type int    $hide        If set to 1 the item nav will be hidden
		 *                               (only used for the delete nav item).
		 * }
		 * @param string $object The object the avatar belongs to (eg: user or group).
		 */
		$settings['nav'] = sz_sort_by_key( apply_filters( 'sz_attachments_avatar_nav', $avatar_nav, $object ), 'order', 'num' );

	// Specific to SportsZone cover images.
	} elseif ( 'sz_cover_image_upload' === $defaults['multipart_params']['action'] ) {
		// Include the cropping informations for avatars.
		$settings['crop'] = array(
			'full_h'  => sz_core_cover_image_full_height(),
			'full_w'  => sz_core_cover_image_full_width(),
		);
		// Cover images only need 1 file and 1 only!
		$defaults['multi_selection'] = false;
		
		// Does the object already has an avatar set.
		$has_cover_image = $defaults['multipart_params']['sz_params']['has_cover_image'];

		// Default cover component is xprofile.
		//$cover_component = 'xprofile';

		// Get the object we're editing the cover image of.
		$object = $defaults['multipart_params']['sz_params']['object'];
		
		// Init the Avatar nav.
		$cover_image_nav = array(
			'upload' => array( 'id' => 'upload', 'caption' => __( 'Upload', 'sportszone' ), 'order' => 0  ),

			// The delete view will only show if the object has an avatar.
			'delete' => array( 'id' => 'delete', 'caption' => __( 'Delete', 'sportszone' ), 'order' => 100, 'hide' => (int) ! $has_cover_image ),
		);

		// Set the cover component according to the object.
		/*if ( 'group' === $object ) {
			$cover_component = 'groups';
		} elseif ( 'user' !== $object ) {
			$cover_component = apply_filters( 'sz_attachments_cover_image_ui_component', $cover_component );
		}
		// Get cover image advised dimensions.
		$cover_dimensions = sz_attachments_get_cover_image_dimensions( $cover_component );

		// Set warning messages.
		$strings['cover_image_warnings'] = apply_filters( 'sz_attachments_cover_image_ui_warnings', array(
			'dimensions'  => sprintf(
					__( 'For better results, make sure to upload an image that is larger than %1$spx wide, and %2$spx tall.', 'sportszone' ),
					(int) $cover_dimensions['width'],
					(int) $cover_dimensions['height']
				),
		) );*/
		
		// Create the Camera Nav if the WebCam capture feature is enabled.
		if ( sz_cover_image_use_webcam() && 'user' === $object ) {
			$cover_image_nav['camera'] = array( 'id' => 'camera', 'caption' => __( 'Take Photo', 'sportszone' ), 'order' => 10 );

			// Set warning messages.
			$strings['camera_warnings'] = array(
				'requesting'  => __( 'Please allow us to access to your camera.', 'sportszone'),
				'loading'     => __( 'Please wait as we access your camera.', 'sportszone' ),
				'loaded'      => __( 'Camera loaded. Click on the "Capture" button to take your photo.', 'sportszone' ),
				'noaccess'    => __( 'It looks like you do not have a webcam or we were unable to get permission to use your webcam. Please upload a photo instead.', 'sportszone' ),
				'errormsg'    => __( 'Your browser is not supported. Please upload a photo instead.', 'sportszone' ),
				'videoerror'  => __( 'Video error. Please upload a photo instead.', 'sportszone' ),
				'ready'       => __( 'Your profile photo is ready. Click on the "Save" button to use this photo.', 'sportszone' ),
				'nocapture'   => __( 'No photo was captured. Click on the "Capture" button to take your photo.', 'sportszone' ),
			);
		}
		
		$settings['nav'] = sz_sort_by_key( apply_filters( 'sz_attachments_cover_image_nav', $cover_image_nav, $object ), 'order', 'num' );
	}

	// Set Plupload settings.
	$settings['defaults'] = $defaults;

	/**
	 * Enqueue some extra styles if required
	 *
	 * Extra styles need to be registered.
	 */
	if ( ! empty( $args['extra_css'] ) ) {
		foreach ( (array) $args['extra_css'] as $css ) {
			if ( empty( $css ) ) {
				continue;
			}

			wp_enqueue_style( $css );
		}
	}

	wp_enqueue_script ( 'sz-plupload' );
	wp_localize_script( 'sz-plupload', 'SZ_Uploader', array( 'strings' => $strings, 'settings' => $settings ) );

	/**
	 * Enqueue some extra scripts if required
	 *
	 * Extra scripts need to be registered.
	 */
	if ( ! empty( $args['extra_js'] ) ) {
		foreach ( (array) $args['extra_js'] as $js ) {
			if ( empty( $js ) ) {
				continue;
			}

			wp_enqueue_script( $js );
		}
	}

	/**
	 * Fires at the conclusion of sz_attachments_enqueue_scripts()
	 * to avoid the scripts to be loaded more than once.
	 *
	 * @since 2.3.0
	 */
	do_action( 'sz_attachments_enqueue_scripts' );
}

/**
 * Check the current user's capability to edit an avatar for a given object.
 *
 * @since 2.3.0
 *
 * @param string $capability The capability to check.
 * @param array  $args       An array containing the item_id and the object to check.
 * @return bool
 */
function sz_attachments_current_user_can( $capability, $args = array() ) {
	$can = false;

	if ( 'edit_avatar' === $capability || 'edit_cover_image' === $capability ) {
		/**
		 * Needed avatar arguments are set.
		 */
		if ( isset( $args['item_id'] ) && isset( $args['object'] ) ) {
			// Group profile photo.
			if ( sz_is_active( 'groups' ) && 'group' === $args['object'] ) {
				if ( sz_is_group_create() ) {
					$can = (bool) groups_is_user_creator( sz_loggedin_user_id(), $args['item_id'] ) || sz_current_user_can( 'sz_moderate' );
				} else {
					$can = (bool) groups_is_user_admin( sz_loggedin_user_id(), $args['item_id'] ) || sz_current_user_can( 'sz_moderate' );
				}
			// User profile photo.
			} elseif ( sz_is_active( 'xprofile' ) && 'user' === $args['object'] ) {
				$can = sz_loggedin_user_id() === (int) $args['item_id'] || sz_current_user_can( 'sz_moderate' );
			}
		/**
		 * No avatar arguments, fallback to sz_user_can_create_groups()
		 * or sz_is_item_admin()
		 */
		} else {
			if ( sz_is_group_create() ) {
				$can = sz_user_can_create_groups();
			} else {
				$can = sz_is_item_admin();
			}
		}
	}

	return apply_filters( 'sz_attachments_current_user_can', $can, $capability, $args );
}

/**
 * Send a JSON response back to an Ajax upload request.
 *
 * @since 2.3.0
 *
 * @param bool  $success  True for a success, false otherwise.
 * @param bool  $is_html4 True if the Plupload runtime used is html4, false otherwise.
 * @param mixed $data     Data to encode as JSON, then print and die.
 */
function sz_attachments_json_response( $success, $is_html4 = false, $data = null ) {
	$response = array( 'success' => $success );

	if ( isset( $data ) ) {
		$response['data'] = $data;
	}

	// Send regular json response.
	if ( ! $is_html4 ) {
		wp_send_json( $response );

	/**
	 * Send specific json response
	 * the html4 Plupload handler requires a text/html content-type for older IE.
	 * See https://core.trac.wordpress.org/ticket/31037
	 */
	} else {
		echo wp_json_encode( $response );

		wp_die();
	}
}

/**
 * Get an Attachment template part.
 *
 * @since 2.3.0
 *
 * @param string $slug Template part slug. eg 'uploader' for 'uploader.php'.
 * @return bool
 */
function sz_attachments_get_template_part( $slug ) {
	$attachment_template_part = 'assets/_attachments/' . $slug;

	// Load the attachment template in WP Administration screens.
	if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		$attachment_admin_template_part = sportszone()->themes_dir . '/sz-legacy/sportszone/' . $attachment_template_part . '.php';

		// Check whether the template part exists.
		if ( ! file_exists( $attachment_admin_template_part ) ) {
			return false;
		}

		// Load the template part.
		require( $attachment_admin_template_part );

	// Load the attachment template in WP_USE_THEMES env.
	} else {
		sz_get_template_part( $attachment_template_part );
	}
}

/** Cover Image ***************************************************************/

/**
 * Get the cover image settings
 *
 * @since 2.4.0
 *
 * @param string $component The component to get the settings for ("xprofile" for user or "groups").
 * @return false|array The cover image settings in array, false on failure.
 */
function sz_attachments_get_cover_image_settings( $component = 'xprofile' ) {
	// Default parameters.
	$args = array();

	// First look in BP Theme Compat.
	$cover_image = sz_get_theme_compat_feature( 'cover_image' );

	if ( ! empty( $cover_image ) ) {
		$args = (array) $cover_image;
	}

	/**
	 * Then let people override/set the feature using this dynamic filter
	 *
	 * Eg: for the user's profile cover image use:
	 * add_filter( 'sz_before_xprofile_cover_image_settings_parse_args', 'your_filter', 10, 1 );
	 *
	 * @since 2.4.0
	 *
	 * @param array $settings The cover image settings
	 */
	$settings = sz_parse_args( $args, array(
		'components'    => array(),
		'width'         => 1300,
		'height'        => 315,
		'callback'      => '',
		'theme_handle'  => '',
		'default_cover' => '',
	), $component . '_cover_image_settings' );

	if ( empty( $settings['components'] ) || empty( $settings['callback'] ) || empty( $settings['theme_handle'] ) ) {
		return false;
	}

	// Current component is not supported.
	if ( ! in_array( $component, $settings['components'] ) ) {
		return false;
	}

	// Finally return the settings.
	return $settings;
}

/**
 * Get cover image Width and Height.
 *
 * @since 2.4.0
 *
 * @param string $component The SportsZone component concerned ("xprofile" for user or "groups").
 * @return array|bool An associative array containing the advised width and height for the cover image. False if settings are empty.
 */
function sz_attachments_get_cover_image_dimensions( $component = 'xprofile' ) {
	// Let's prevent notices when setting the warning strings.
	$default = array( 'width' => 0, 'height' => 0 );

	$settings = sz_attachments_get_cover_image_settings( $component );

	if ( empty( $settings ) ) {
		return false;
	}

	// Get width and height.
	$wh = array_intersect_key( $settings, $default );

	/**
	 * Filter here to edit the cover image dimensions if needed.
	 *
	 * @since 2.4.0
	 *
	 * @param array  $wh       An associative array containing the width and height values.
	 * @param array  $settings An associative array containing all the feature settings.
	 * @param string $compnent The requested component.
	 */
	return apply_filters( 'sz_attachments_get_cover_image_dimensions', $wh, $settings, $component );
}

/**
 * Are we on a page to edit a cover image?
 *
 * @since 2.4.0
 *
 * @return bool True if on a page to edit a cover image, false otherwise.
 */
function sz_attachments_cover_image_is_edit() {
	$retval = false;

	$current_component = sz_current_component();
	if ( sz_is_active( 'xprofile' ) && sz_is_current_component( 'xprofile' ) ) {
		$current_component = 'xprofile';
	}

	if ( ! sz_is_active( $current_component, 'cover_image' ) ) {
		return $retval;
	}

	if ( sz_is_user_change_cover_image() ) {
		$retval = ! sz_disable_cover_image_uploads();
	}

	if ( ( sz_is_group_admin_page() && 'group-cover-image' == sz_get_group_current_admin_tab() )
		|| ( sz_is_group_create() && sz_is_group_creation_step( 'group-cover-image' ) ) ) {
		$retval = ! sz_disable_group_cover_image_uploads();
	}

	return apply_filters( 'sz_attachments_cover_image_is_edit', $retval, $current_component );
}

/**
 * Does the user has a cover image?
 *
 * @since 2.4.0
 *
 * @param int $user_id User ID to retrieve cover image for.
 * @return bool True if the user has a cover image, false otherwise.
 */
function sz_attachments_get_user_has_cover_image( $user_id = 0 ) {
	if ( empty( $user_id ) ) {
		$user_id = sz_displayed_user_id();
	}

	$cover_src = sz_attachments_get_attachment( 'url', array(
		'item_id'   => $user_id,
	) );

	return (bool) apply_filters( 'sz_attachments_get_user_has_cover_image', $cover_src, $user_id );
}

/**
 * Does the group has a cover image?
 *
 * @since 2.4.0
 *
 * @param int $group_id Group ID to check cover image existence for.
 * @return bool True if the group has a cover image, false otherwise.
 */
function sz_attachments_get_group_has_cover_image( $group_id = 0 ) {
	if ( empty( $group_id ) ) {
		$group_id = sz_get_current_group_id();
	}

	$cover_src = sz_attachments_get_attachment( 'url', array(
		'object_dir' => 'groups',
		'item_id'    => $group_id,
	) );

	return (bool) apply_filters( 'sz_attachments_get_user_has_cover_image', $cover_src, $group_id );
}

/**
 * Generate the cover image file.
 *
 * @since 2.4.0
 *
 * @param array                          $args {
 *     @type string $file            The absolute path to the image. Required.
 *     @type string $component       The component for the object (eg: groups, xprofile). Required.
 *     @type string $cover_image_dir The Cover image dir to write the image into. Required.
 * }
 * @param SZ_Attachment_Cover_Image|null $cover_image_class The class to use to fit the cover image.
 * @return false|array An array containing cover image data on success, false otherwise.
 */
function sz_attachments_cover_image_generate_file( $args = array(), $cover_image_class = null ) {
	// Bail if an argument is missing.
	if ( empty( $args['file'] ) || empty( $args['component'] ) || empty( $args['cover_image_dir'] ) ) {
		return false;
	}

	// Get advised dimensions for the cover image.
	$dimensions = sz_attachments_get_cover_image_dimensions( $args['component'] );

	// No dimensions or the file does not match with the cover image dir, stop!
	if ( false === $dimensions || $args['file'] !== $args['cover_image_dir'] . '/' . wp_basename( $args['file'] ) ) {
		return false;
	}

	if ( ! is_a( $cover_image_class, 'SZ_Attachment_Cover_Image' ) ) {
		$cover_image_class = new SZ_Attachment_Cover_Image();
	}

	$upload_dir = sz_attachments_cover_image_upload_dir();

	// Make sure the file is inside the Cover Image Upload path.
	if ( false === strpos( $args['file'], $upload_dir['basedir'] ) ) {
		return false;
	}

	// Resize the image so that it fit with the cover image dimensions.
	$cover_image  = $cover_image_class->fit( $args['file'], $dimensions );
	$is_too_small = false;

	// Image is too small in width and height.
	if ( empty( $cover_image ) ) {
		$cover_file = $cover_image_class->generate_filename( $args['file'] );
		@rename( $args['file'], $cover_file );

		// It's too small!
		$is_too_small = true;
	} elseif ( ! empty( $cover_image['path'] ) ) {
		$cover_file = $cover_image['path'];

		// Image is too small in width or height.
		if ( $cover_image['width'] < $dimensions['width'] || $cover_image['height'] < $dimensions['height'] ) {
			$is_too_small = true;
		}
	}

	// We were not able to generate the cover image file.
	if ( empty( $cover_file ) ) {
		return false;
	}

	// Do some clean up with old cover image, now a new one is set.
	$cover_basename = wp_basename( $cover_file );

	if ( $att_dir = opendir( $args['cover_image_dir'] ) ) {
		while ( false !== ( $attachment_file = readdir( $att_dir ) ) ) {
			// Skip directories and the new cover image.
			if ( 2 < strlen( $attachment_file ) && 0 !== strpos( $attachment_file, '.' ) && $cover_basename !== $attachment_file ) {
				@unlink( $args['cover_image_dir'] . '/' . $attachment_file );
			}
		}
	}

	// Finally return needed data.
	return array(
		'cover_file'     => $cover_file,
		'cover_basename' => $cover_basename,
		'is_too_small'   => $is_too_small
	);
}

/**
 * Ajax Upload and set a cover image
 *
 * @since 2.4.0
 *
 * @return string|null A json object containing success data if the upload succeeded,
 *                     error message otherwise.
 */
function sz_attachments_cover_image_ajax_upload() {
	if ( ! sz_is_post_request() ) {
		wp_die();
	}

	check_admin_referer( 'sz-uploader' );

	// Sending the json response will be different if the current Plupload runtime is html4.
	$is_html4 = ! empty( $_POST['html4' ] );

	if ( empty( $_POST['sz_params'] ) ) {
		sz_attachments_json_response( false, $is_html4 );
	}

	$sz_params = sz_parse_args( $_POST['sz_params'], array(
		'object'  => 'user',
		'item_id' => sz_loggedin_user_id(),
	), 'attachments_cover_image_ajax_upload' );

	$sz_params['item_id'] = (int) $sz_params['item_id'];
	$sz_params['object']  = sanitize_text_field( $sz_params['object'] );

	// We need the object to set the uploads dir filter.
	if ( empty( $sz_params['object'] ) ) {
		sz_attachments_json_response( false, $is_html4 );
	}

	// Capability check.
	if ( ! sz_attachments_current_user_can( 'edit_cover_image', $sz_params ) ) {
		sz_attachments_json_response( false, $is_html4 );
	}

	$sz          = sportszone();
	$needs_reset = array();

	// Member's cover image.
	if ( 'user' === $sz_params['object'] ) {
		$object_data = array( 'dir' => 'members', 'component' => 'xprofile' );

		if ( ! sz_displayed_user_id() && ! empty( $sz_params['item_id'] ) ) {
			$needs_reset = array( 'key' => 'displayed_user', 'value' => $sz->displayed_user );
			$sz->displayed_user->id = $sz_params['item_id'];
		}

	// Group's cover image.
	} elseif ( 'group' === $sz_params['object'] ) {
		$object_data = array( 'dir' => 'groups', 'component' => 'groups' );

		if ( ! sz_get_current_group_id() && ! empty( $sz_params['item_id'] ) ) {
			$needs_reset = array( 'component' => 'groups', 'key' => 'current_group', 'value' => $sz->groups->current_group );
			$sz->groups->current_group = groups_get_group( $sz_params['item_id'] );
		}

	// Other object's cover image.
	} else {
		$object_data = apply_filters( 'sz_attachments_cover_image_object_dir', array(), $sz_params['object'] );
	}

	// Stop here in case of a missing parameter for the object.
	if ( empty( $object_data['dir'] ) || empty( $object_data['component'] ) ) {
		sz_attachments_json_response( false, $is_html4 );
	}

	/**
	 * Filters whether or not to handle cover image uploading.
	 *
	 * If you want to override this function, make sure you return an array with the 'result' key set.
	 *
	 * @since 2.5.1
	 *
	 * @param array $value
	 * @param array $sz_params
	 * @param array $needs_reset Stores original value of certain globals we need to revert to later.
	 * @param array $object_data
	 */
	$pre_filter = apply_filters( 'sz_attachments_pre_cover_image_ajax_upload', array(), $sz_params, $needs_reset, $object_data );
	if ( isset( $pre_filter['result'] ) ) {
		sz_attachments_json_response( $pre_filter['result'], $is_html4, $pre_filter );
	}

	$cover_image_attachment = new SZ_Attachment_Cover_Image();
	$uploaded = $cover_image_attachment->upload( $_FILES );

	// Reset objects.
	if ( ! empty( $needs_reset ) ) {
		if ( ! empty( $needs_reset['component'] ) ) {
			$sz->{$needs_reset['component']}->{$needs_reset['key']} = $needs_reset['value'];
		} else {
			$sz->{$needs_reset['key']} = $needs_reset['value'];
		}
	}

	if ( ! empty( $uploaded['error'] ) ) {
		// Upload error response.
		sz_attachments_json_response( false, $is_html4, array(
			'type'    => 'upload_error',
			'message' => sprintf( __( 'Upload Failed! Error was: %s', 'sportszone' ), $uploaded['error'] ),
		) );
	}

	$error_message = __( 'There was a problem uploading the cover image.', 'sportszone' );

	$sz_attachments_uploads_dir = sz_attachments_cover_image_upload_dir();

	// The BP Attachments Uploads Dir is not set, stop.
	if ( ! $sz_attachments_uploads_dir ) {
		sz_attachments_json_response( false, $is_html4, array(
			'type'    => 'upload_error',
			'message' => $error_message,
			'more'	  => $cover_image_attachment,
		) );
	}

	$cover_subdir = $object_data['dir'] . '/' . $sz_params['item_id'] . '/cover-image';
	$cover_dir    = trailingslashit( $sz_attachments_uploads_dir['basedir'] ) . $cover_subdir;

	if ( 1 === validate_file( $cover_dir ) || ! is_dir( $cover_dir ) ) {
		// Upload error response.
		sz_attachments_json_response( false, $is_html4, array(
			'type'    => 'upload_error',
			'message' => $error_message,
			'more2'	  => $cover_dir,
		) );
	}

	/*
	 * Generate the cover image so that it fit to feature's dimensions
	 *
	 * Unlike the avatar, uploading and generating the cover image is happening during
	 * the same Ajax request, as we already instantiated the SZ_Attachment_Cover_Image
	 * class, let's use it.
	 */
	$cover = sz_attachments_cover_image_generate_file( array(
		'file'            => $uploaded['file'],
		'component'       => $object_data['component'],
		'cover_image_dir' => $cover_dir
	), $cover_image_attachment );

	if ( ! $cover ) {
		sz_attachments_json_response( false, $is_html4, array(
			'type'    => 'upload_error',
			'message' => $error_message,
			'more3'	  => $cover_dir,
		) );
	}

	$cover_url = trailingslashit( $sz_attachments_uploads_dir['baseurl'] ) . $cover_subdir . '/' . $cover['cover_basename'];

	// 1 is success.
	$feedback_code = 1;

	// 0 is the size warning.
	if ( $cover['is_too_small'] ) {
		$feedback_code = 0;
	}

	// Set the name of the file.
	$name = $_FILES['file']['name'];
	$name_parts = pathinfo( $name );
	$name = trim( substr( $name, 0, - ( 1 + strlen( $name_parts['extension'] ) ) ) );

	/**
	 * Fires if the new cover image was successfully uploaded.
	 *
	 * The dynamic portion of the hook will be xprofile in case of a user's
	 * cover image, groups in case of a group's cover image. For instance:
	 * Use add_action( 'xprofile_cover_image_uploaded' ) to run your specific
	 * code once the user has set his cover image.
	 *
	 * @since 2.4.0
	 * @since 3.0.0 Added $cover_url, $name, $feedback_code arguments.
	 *
	 * @param int    $item_id       Inform about the item id the cover image was set for.
	 * @param string $name          Filename.
	 * @param string $cover_url     URL to the image.
	 * @param int    $feedback_code If value not 1, an error occured.
	 */
	do_action(
		$object_data['component'] . '_cover_image_uploaded',
		(int) $sz_params['item_id'],
		$name,
		$cover_url,
		$feedback_code
	);

	// Finally return the cover image url to the UI.
	sz_attachments_json_response( true, $is_html4, array(
		'name'          => $name,
		'url'           => $cover_url,
		'feedback_code' => $feedback_code,
	) );
}
//add_action( 'wp_ajax_sz_cover_image_upload', 'sz_attachments_cover_image_ajax_upload' );

/**
 * Ajax delete a cover image for a given object and item id.
 *
 * @since 2.4.0
 *
 * @return string|null A json object containing success data if the cover image was deleted
 *                     error message otherwise.
 */
function sz_attachments_cover_image_ajax_delete() {
	if ( ! sz_is_post_request() ) {
		wp_send_json_error();
	}

	if ( empty( $_POST['object'] ) || empty( $_POST['item_id'] ) ) {
		wp_send_json_error();
	}

	$args = array(
		'object'  => sanitize_text_field( $_POST['object'] ),
		'item_id' => (int) $_POST['item_id'],
	);

	// Check permissions.
	check_admin_referer( 'sz_delete_cover_image', 'nonce' );
	if ( ! sz_attachments_current_user_can( 'edit_cover_image', $args ) ) {
		wp_send_json_error();
	}

	// Set object for the user's case.
	if ( 'user' === $args['object'] ) {
		$component = 'xprofile';
		$dir       = 'members';

	// Set it for any other cases.
	} else {
		$component = $args['object'] . 's';
		$dir       = $component;
	}

	// Handle delete.
	if ( sz_attachments_delete_file( array( 'item_id' => $args['item_id'], 'object_dir' => $dir, 'type' => 'cover-image' ) ) ) {
		/**
		 * Fires if the cover image was successfully deleted.
		 *
		 * The dynamic portion of the hook will be xprofile in case of a user's
		 * cover image, groups in case of a group's cover image. For instance:
		 * Use add_action( 'xprofile_cover_image_deleted' ) to run your specific
		 * code once the user has deleted his cover image.
		 *
		 * @since 2.8.0
		 *
		 * @param int $item_id Inform about the item id the cover image was deleted for.
		 */
		do_action( "{$component}_cover_image_deleted", (int) $args['item_id'] );

		$response = array(
			'reset_url'     => '',
			'feedback_code' => 3,
		);

		// Get cover image settings in case there's a default header.
		$cover_params = sz_attachments_get_cover_image_settings( $component );

		// Check if there's a default cover.
		if ( ! empty( $cover_params['default_cover'] ) ) {
			$response['reset_url'] = $cover_params['default_cover'];
		}

		wp_send_json_success( $response );

	} else {
		wp_send_json_error( array(
			'feedback_code' => 2,
		) );
	}
}
add_action( 'wp_ajax_sz_cover_image_delete', 'sz_attachments_cover_image_ajax_delete' );
