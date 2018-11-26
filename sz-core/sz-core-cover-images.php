<?php
/**
 * SportsZone Cover Images.
 *
 * @package SportsZone
 * @subpackage Core
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Set up the constants we need for cover_image support.
 *
 * @since 1.2.0
 */
function sz_core_set_cover_image_constants() {

	$sz = sportszone();

	if ( !defined( 'SZ_COVER_IMAGE_THUMB_WIDTH' ) )
		define( 'SZ_COVER_IMAGE_THUMB_WIDTH', 650 );

	if ( !defined( 'SZ_COVER_IMAGE_THUMB_HEIGHT' ) )
		define( 'SZ_COVER_IMAGE_THUMB_HEIGHT', 157 );

	if ( !defined( 'SZ_COVER_IMAGE_FULL_WIDTH' ) )
		define( 'SZ_COVER_IMAGE_FULL_WIDTH', 1300 );

	if ( !defined( 'SZ_COVER_IMAGE_FULL_HEIGHT' ) )
		define( 'SZ_COVER_IMAGE_FULL_HEIGHT', 315 );

	if ( !defined( 'SZ_COVER_IMAGE_ORIGINAL_MAX_WIDTH' ) )
		define( 'SZ_COVER_IMAGE_ORIGINAL_MAX_WIDTH', 1300 );

	if ( !defined( 'SZ_COVER_IMAGE_ORIGINAL_MAX_FILESIZE' ) ) {
		define( 'SZ_COVER_IMAGE_ORIGINAL_MAX_FILESIZE', sz_attachments_get_max_upload_file_size( 'cover_image' ) );
	}

	if ( ! defined( 'SZ_SHOW_COVER_IMAGES' ) ) {
		define( 'SZ_SHOW_COVER_IMAGES', sz_get_option( 'show_cover_images' ) );
	}
}
add_action( 'sz_init', 'sz_core_set_cover_image_constants', 3 );

/**
 * Set up global variables related to cover_images.
 *
 * @since 1.5.0
 */
function sz_core_set_cover_image_globals() {
	$sz = sportszone();

	$sz->cover_image        = new stdClass;
	$sz->cover_image->thumb = new stdClass;
	$sz->cover_image->full  = new stdClass;

	// Dimensions.
	$sz->cover_image->thumb->width  = SZ_COVER_IMAGE_THUMB_WIDTH;
	$sz->cover_image->thumb->height = SZ_COVER_IMAGE_THUMB_HEIGHT;
	$sz->cover_image->full->width   = SZ_COVER_IMAGE_FULL_WIDTH;
	$sz->cover_image->full->height  = SZ_COVER_IMAGE_FULL_HEIGHT;

	// Upload maximums.
	$sz->cover_image->original_max_width    = SZ_COVER_IMAGE_ORIGINAL_MAX_WIDTH;
	$sz->cover_image->original_max_filesize = SZ_COVER_IMAGE_ORIGINAL_MAX_FILESIZE;

	// Defaults.
	$sz->cover_image->thumb->default = sz_core_cover_image_default_thumb();
	$sz->cover_image->full->default  = sz_core_cover_image_default();

	// These have to be set on page load in order to avoid infinite filter loops at runtime.
	$sz->cover_image->upload_path = sz_core_cover_image_upload_path();
	$sz->cover_image->url = sz_core_cover_image_url();

	// Cache the root blog's show_cover_images setting, to avoid unnecessary
	// calls to switch_to_blog().
	$sz->cover_image->show_cover_images = (bool) SZ_SHOW_COVER_IMAGES;

	// Backpat for pre-1.5.
	if ( ! defined( 'SZ_COVER_IMAGE_UPLOAD_PATH' ) )
		define( 'SZ_COVER_IMAGE_UPLOAD_PATH', $sz->cover_image->upload_path );

	// Backpat for pre-1.5.
	if ( ! defined( 'SZ_COVER_IMAGE_URL' ) )
		define( 'SZ_COVER_IMAGE_URL', $sz->cover_image->url );

	/**
	 * Fires at the end of the core cover_image globals setup.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_core_set_cover_image_globals' );
}
add_action( 'sz_setup_globals', 'sz_core_set_cover_image_globals' );

/**
 * Get an cover_image for a SportsZone object.
 *
 * Supports cover_images for users, groups, and blogs by default, but can be
 * extended to support custom components as well.
 *
 * This function gives precedence to locally-uploaded cover_images. When a local
 * cover_image is not found, Gravatar is queried. To disable Gravatar fallbacks
 * locally:
 *    add_filter( 'sz_core_fetch_cover_image_no_grav', '__return_true' );
 *
 * @since 1.1.0
 * @since 2.4.0 Added 'extra_attr', 'scheme', 'rating' and 'force_default' for $args.
 *              These are inherited from WordPress 4.2.0. See {@link get_cover_image()}.
 *
 * @param array|string $args {
 *     An array of arguments. All arguments are technically optional; some
 *     will, if not provided, be auto-detected by sz_core_fetch_cover_image(). This
 *     auto-detection is described more below, when discussing specific
 *     arguments.
 *
 *     @type int|bool    $item_id    The numeric ID of the item for which you're requesting
 *                                   an avatar (eg, a user ID). If no 'item_id' is present,
 *                                   the function attempts to infer an ID from the 'object' + the
 *                                   current context: if 'object' is 'user' and the current page is a
 *                                   user page, 'item_id' will default to the displayed user ID; if
 *                                   'group' and on a group page, to the current group ID; if 'blog',
 *                                   to the current blog's ID. If no 'item_id' can be determined in
 *                                   this way, the function returns false. Default: false.
 *     @type string      $object     The kind of object for which you're getting an
 *                                   cover_image. SportsZone natively supports three options: 'user',
 *                                   'group', 'blog'; a plugin may register more.  Default: 'user'.
 *     @type string      $type       When a new cover_image is uploaded to BP, 'thumb' and
 *                                   'full' versions are saved. This parameter specifies whether you'd
 *                                   like the 'full' or smaller 'thumb' cover_image. Default: 'thumb'.
 *     @type string|bool $cover_image_dir The name of the subdirectory where the
 *                                   requested cover_image should be found. If no value is passed,
 *                                   'cover_image_dir' is inferred from 'object': 'user' becomes 'cover_images',
 *                                   'group' becomes 'group-cover-images', 'blog' becomes 'blog-cover-images'.
 *                                   Remember that this string denotes a subdirectory of BP's main
 *                                   cover_image directory (usually based on {@link wp_upload_dir()}); it's a
 *                                   string like 'group-cover-images' rather than the full directory path.
 *                                   Generally, it'll only be necessary to override the default value if
 *                                   storing cover_images in a non-default location. Defaults to false
 *                                   (auto-detected).
 *     @type int|bool    $width      Requested cover_image width. The unit is px. This value
 *                                   is used to build the 'width' attribute for the <img> element. If
 *                                   no value is passed, BP uses the global cover_image width for this
 *                                   cover_image type. Default: false (auto-detected).
 *     @type int|bool    $height     Requested cover_image height. The unit is px. This
 *                                   value is used to build the 'height' attribute for the <img>
 *                                   element. If no value is passed, BP uses the global cover_image height
 *                                   for this cover_image type. Default: false (auto-detected).
 *     @type string      $class      The CSS class for the <img> element. Note that BP
 *                                   uses the 'cover_image' class fairly extensively in its default styling,
 *                                   so if you plan to pass a custom value, consider appending it to
 *                                   'cover_image' (eg 'cover_image foo') rather than replacing it altogether.
 *                                   Default: 'cover_image'.
 *     @type string|bool $css_id     The CSS id for the <img> element.
 *                                   Default: false.
 *     @type string      $title      The title attribute for the <img> element.
 *                                   Default: false.
 *     @type string      $alt        The alt attribute for the <img> element. In BP, this
 *                                   value is generally passed by the wrapper functions, where the data
 *                                   necessary for concatenating the string is at hand; see
 *                                   {@link sz_get_activity_cover_image()} for an example. Default: ''.
 *     @type string|bool $email      An email to use in Grcover_image queries. Unless
 *                                   otherwise configured, BP uses Grcover_image as a fallback for cover_images
 *                                   that are not provided locally. Grcover_image's API requires using a hash
 *                                   of the user's email address; this argument provides it. If not
 *                                   provided, the function will infer it: for users, by getting the
 *                                   user's email from the database, for groups/blogs, by concatenating
 *                                   "{$item_id}-{$object}@{sz_get_root_domain()}". The user query adds
 *                                   overhead, so it's recommended that wrapper functions provide a
 *                                   value for 'email' when querying user IDs. Default: false.
 *     @type bool       $no_grav     Whether to disable the default Grcover_image fallback.
 *                                   By default, BP will fall back on Grcover_image when it cannot find a
 *                                   local cover_image. In some cases, this may be undesirable, in which
 *                                   case 'no_grav' should be set to true. To disable Grcover_image
 *                                   fallbacks globally, see the 'sz_core_fetch_cover_image_no_grav' filter.
 *                                   Default: true for groups, otherwise false.
 *     @type bool       $html        Whether to return an <img> HTML element, vs a raw URL
 *                                   to an cover_image. If false, <img>-specific arguments (like 'css_id')
 *                                   will be ignored. Default: true.
 *     @type string     $extra_attr  HTML attributes to insert in the IMG element. Not sanitized. Default: ''.
 *     @type string     $scheme      URL scheme to use. See set_url_scheme() for accepted values.
 *                                   Default null.
 *     @type string     $rating      What rating to display Grcover_images for. Accepts 'G', 'PG', 'R', 'X'.
 *                                   Default is the value of the 'cover_image_rating' option.
 *     @type bool       $force_default Used when creating the Grcover_image URL. Whether to force the default
 *                                     image regardless if the Grcover_image exists. Default: false.
 * }
 * @return string Formatted HTML <img> element, or raw cover_image URL based on $html arg.
 */
function sz_core_fetch_cover_image( $args = '' ) {
	$sz = sportszone();
	
	// If cover_images are disabled for the root site, obey that request and bail.
	/*if ( ! $sz->cover_image->show_cover_images ) {
		return;
	}*/
	global $current_blog;

	// Set the default variables array and parse it against incoming $args array.
	$params = wp_parse_args( $args, array(
		'item_id'       => false,
		'object'        => 'user',
		'type'          => 'thumb',
		'cover_image_dir'    => false,
		'width'         => false,
		'height'        => false,
		'class'         => 'cover_image',
		'css_id'        => false,
		'alt'           => '',
		'email'         => false,
		'no_grav'       => null,
		'html'          => true,
		'title'         => '',
		'extra_attr'    => '',
		'scheme'        => null,
		'rating'        => get_option( 'cover_image_rating' ),
		'force_default' => false,
	) );

	/* Set item_id ***********************************************************/

	if ( empty( $params['item_id'] ) ) {

		switch ( $params['object'] ) {

			case 'blog'  :
				$params['item_id'] = $current_blog->id;
				break;

			case 'group' :
				if ( sz_is_active( 'groups' ) ) {
					$params['item_id'] = $sz->groups->current_group->id;
				} else {
					$params['item_id'] = false;
				}

				break;

			case 'user'  :
			default      :
				$params['item_id'] = sz_displayed_user_id();
				break;
		}

		/**
		 * Filters the ID of the item being requested.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value  ID of cover_image item being requested.
		 * @param string $value  Avatar type being requested.
		 * @param array  $params Array of parameters for the request.
		 */
		$params['item_id'] = apply_filters( 'sz_core_cover_image_item_id', $params['item_id'], $params['object'], $params );

		if ( empty( $params['item_id'] ) ) {
			return false;
		}
	}

	/* Set cover_image_dir ********************************************************/

	if ( empty( $params['cover_image_dir'] ) ) {

		switch ( $params['object'] ) {

			case 'blog'  :
				$params['cover_image_dir'] = 'blog-cover-images';
				break;

			case 'group' :
				if ( sz_is_active( 'groups' ) ) {
					$params['cover_image_dir'] = 'group-cover-images';
				} else {
					$params['cover_image_dir'] = false;
				}

				break;

			case 'user'  :
			default      :
				$params['cover_image_dir'] = 'user-cover-images';
				break;
		}

		/**
		 * Filters the cover_image directory to use.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value  Name of the subdirectory where the requested cover_image should be found.
		 * @param string $value  Avatar type being requested.
		 * @param array  $params Array of parameters for the request.
		 */
		$params['cover_image_dir'] = apply_filters( 'sz_core_cover_image_dir', $params['cover_image_dir'], $params['object'], $params );

		if ( empty( $params['cover_image_dir'] ) ) {
			return false;
		}
	}

	/* <img> alt *************************************************************/

	if ( false !== strpos( $params['alt'], '%s' ) || false !== strpos( $params['alt'], '%1$s' ) ) {

		switch ( $params['object'] ) {

			case 'blog'  :
				$item_name = get_blog_option( $params['item_id'], 'blogname' );
				break;

			case 'group' :
				$item_name = sz_get_group_name( groups_get_group( $params['item_id'] ) );
				break;

			case 'user'  :
			default :
				$item_name = sz_core_get_user_displayname( $params['item_id'] );
				break;
		}

		/**
		 * Filters the alt attribute value to be applied to cover_image.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value  alt to be applied to cover_image.
		 * @param string $value  ID of cover_image item being requested.
		 * @param string $value  Avatar type being requested.
		 * @param array  $params Array of parameters for the request.
		 */
		$item_name = apply_filters( 'sz_core_cover_image_alt', $item_name, $params['item_id'], $params['object'], $params );
		$params['alt'] = sprintf( $params['alt'], $item_name );
	}

	/* Sanity Checks *********************************************************/

	// Get a fallback for the 'alt' parameter, create html output.
	if ( empty( $params['alt'] ) ) {
		$params['alt'] = __( 'Profile Photo', 'sportszone' );
	}
	$html_alt = ' alt="' . esc_attr( $params['alt'] ) . '"';

	// Filter image title and create html string.
	$html_title = '';

	/**
	 * Filters the title attribute value to be applied to cover_image.
	 *
	 * @since 1.5.0
	 *
	 * @param string $value  Title to be applied to cover_image.
	 * @param string $value  ID of cover_image item being requested.
	 * @param string $value  Avatar type being requested.
	 * @param array  $params Array of parameters for the request.
	 */
	$params['title'] = apply_filters( 'sz_core_cover_image_title', $params['title'], $params['item_id'], $params['object'], $params );

	if ( ! empty( $params['title'] ) ) {
		$html_title = ' title="' . esc_attr( $params['title'] ) . '"';
	}

	// Extra attributes.
	$extra_attr = ! empty( $args['extra_attr'] ) ? ' ' . $args['extra_attr'] : '';

	// Set CSS ID and create html string.
	$html_css_id = '';

	/**
	 * Filters the ID attribute to be applied to cover_image.
	 *
	 * @since 2.2.0
	 *
	 * @param string $value  ID to be applied to cover_image.
	 * @param string $value  ID of cover_image item being requested.
	 * @param string $value  Avatar type being requested.
	 * @param array  $params Array of parameters for the request.
	 */
	$params['css_id'] = apply_filters( 'sz_core_css_id', $params['css_id'], $params['item_id'], $params['object'], $params );

	if ( ! empty( $params['css_id'] ) ) {
		$html_css_id = ' id="' . esc_attr( $params['css_id'] ) . '"';
	}

	// Set image width.
	if ( false !== $params['width'] ) {
		// Width has been specified. No modification necessary.
	} elseif ( 'thumb' == $params['type'] ) {
		$params['width'] = sz_core_cover_image_thumb_width();
	} else {
		$params['width'] = sz_core_cover_image_full_width();
	}
	$html_width = ' width="' . $params['width'] . '"';

	// Set image height.
	if ( false !== $params['height'] ) {
		// Height has been specified. No modification necessary.
	} elseif ( 'thumb' == $params['type'] ) {
		$params['height'] = sz_core_cover_image_thumb_height();
	} else {
		$params['height'] = sz_core_cover_image_full_height();
	}
	$html_height = ' height="' . $params['height'] . '"';

	/**
	 * Filters the classes to be applied to the cover_image.
	 *
	 * @since 1.6.0
	 *
	 * @param array|string $value  Class(es) to be applied to the cover_image.
	 * @param string       $value  ID of the cover_image item being requested.
	 * @param string       $value  Avatar type being requested.
	 * @param array        $params Array of parameters for the request.
	 */
	$params['class'] = apply_filters( 'sz_core_cover_image_class', $params['class'], $params['item_id'], $params['object'], $params );

	// Use an alias to leave the param unchanged.
	$cover_image_classes = $params['class'];
	if ( ! is_array( $cover_image_classes ) ) {
		$cover_image_classes = explode( ' ', $cover_image_classes );
	}

	// Merge classes.
	$cover_image_classes = array_merge( $cover_image_classes, array(
		$params['object'] . '-' . $params['item_id'] . '-cover-image',
		'cover-image-' . $params['width'],
	) );

	// Sanitize each class.
	$cover_image_classes = array_map( 'sanitize_html_class', $cover_image_classes );

	// Populate the class attribute.
	$html_class = ' class="' . join( ' ', $cover_image_classes ) . ' photo"';

	// Set img URL and DIR based on prepopulated constants.
	$cover_image_loc        = new stdClass();
	$cover_image_loc->path  = trailingslashit( sz_core_cover_image_upload_path() );
	$cover_image_loc->url   = trailingslashit( sz_core_cover_image_url() );

	$cover_image_loc->dir   = trailingslashit( $params['cover_image_dir'] );

	/**
	 * Filters the cover_image folder directory URL.
	 *
	 * @since 1.1.0
	 *
	 * @param string $value Path to the cover_image folder URL.
	 * @param int    $value ID of the cover_image item being requested.
	 * @param string $value Avatar type being requested.
	 * @param string $value Subdirectory where the requested cover_image should be found.
	 */
	$cover_image_folder_url = apply_filters( 'sz_core_cover_image_folder_url', ( $cover_image_loc->url  . $cover_image_loc->dir . $params['item_id'] ), $params['item_id'], $params['object'], $params['cover_image_dir'] );

	/**
	 * Filters the cover_image folder directory path.
	 *
	 * @since 1.1.0
	 *
	 * @param string $value Path to the cover_image folder directory.
	 * @param int    $value ID of the cover_image item being requested.
	 * @param string $value Avatar type being requested.
	 * @param string $value Subdirectory where the requested cover_image should be found.
	 */
	$cover_image_folder_dir = apply_filters( 'sz_core_cover_image_folder_dir', ( $cover_image_loc->path . $cover_image_loc->dir . $params['item_id'] ), $params['item_id'], $params['object'], $params['cover_image_dir'] );

	/**
	 * Look for uploaded cover_image first. Use it if it exists.
	 * Set the file names to search for, to select the full size
	 * or thumbnail image.
	 */
	$cover_image_size              = ( 'full' == $params['type'] ) ? '-bpfull' : '-bpthumb';
	$legacy_user_cover_image_name  = ( 'full' == $params['type'] ) ? '-cover-image2' : '-cover-image1';
	$legacy_group_cover_image_name = ( 'full' == $params['type'] ) ? '-groupcover-image-full' : '-groupcover-image-thumb';

	// Check for directory.
	if ( file_exists( $cover_image_folder_dir ) ) {

		// Open directory.
		if ( $av_dir = opendir( $cover_image_folder_dir ) ) {

			// Stash files in an array once to check for one that matches.
			$cover_image_files = array();
			while ( false !== ( $cover_image_file = readdir( $av_dir ) ) ) {
				// Only add files to the array (skip directories).
				if ( 2 < strlen( $cover_image_file ) ) {
					$cover_image_files[] = $cover_image_file;
				}
			}

			// Check for array.
			if ( 0 < count( $cover_image_files ) ) {

				// Check for current cover-image.
				foreach( $cover_image_files as $key => $value ) {
					if ( strpos ( $value, $cover_image_size )!== false ) {
						$cover_image_url = $cover_image_folder_url . '/' . $cover_image_files[$key];
					}
				}

				// Legacy cover-image check.
				if ( !isset( $cover_image_url ) ) {
					foreach( $cover_image_files as $key => $value ) {
						if ( strpos ( $value, $legacy_user_cover_image_name )!== false ) {
							$cover_image_url = $cover_image_folder_url . '/' . $cover_image_files[$key];
						}
					}

					// Legacy group cover-image check.
					if ( !isset( $cover_image_url ) ) {
						foreach( $cover_image_files as $key => $value ) {
							if ( strpos ( $value, $legacy_group_cover_image_name )!== false ) {
								$cover_image_url = $cover_image_folder_url . '/' . $cover_image_files[$key];
							}
						}
					}
				}
			}
		}

		// Close the cover-image directory.
		closedir( $av_dir );

		// If we found a locally uploaded cover-image.
		if ( isset( $cover_image_url ) ) {
			// Support custom scheme.
			$cover_image_url = set_url_scheme( $cover_image_url, $params['scheme'] );

			// Return it wrapped in an <img> element.
			if ( true === $params['html'] ) {

				/**
				 * Filters an cover-image URL wrapped in an <img> element.
				 *
				 * @since 1.1.0
				 *
				 * @param string $value             Full <img> element for an cover-image.
				 * @param array  $params            Array of parameters for the request.
				 * @param string $value             ID of the item requested.
				 * @param string $value             Subdirectory where the requested cover-image should be found.
				 * @param string $html_css_id       ID attribute for cover-image.
				 * @param string $html_width        Width attribute for cover-image.
				 * @param string $html_height       Height attribute for cover-image.
				 * @param string $cover_image_folder_url Avatar URL path.
				 * @param string $cover_image_folder_dir Avatar DIR path.
				 */
				return apply_filters( 'sz_core_fetch_cover_image', '<img src="' . $cover_image_url . '"' . $html_class . $html_css_id  . $html_width . $html_height . $html_alt . $html_title . $extra_attr . ' />', $params, $params['item_id'], $params['cover_image_dir'], $html_css_id, $html_width, $html_height, $cover_image_folder_url, $cover_image_folder_dir );

			// ...or only the URL
			} else {

				/**
				 * Filters a locally uploaded cover-image URL.
				 *
				 * @since 1.2.5
				 *
				 * @param string $cover_image_url URL for a locally uploaded cover-image.
				 * @param array  $params     Array of parameters for the request.
				 */
				return apply_filters( 'sz_core_fetch_cover_image_url', $cover_image_url, $params );
			}
		}
	}

	
		/**
		 * Filters the avatar default when Gravatar is not used.
		 *
		 * This is a variable filter dependent on the avatar type being requested.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value  Default avatar for non-gravatar requests.
		 * @param array  $params Array of parameters for the avatar request.
		 */
	$gravatar = apply_filters( 'sz_core_default_cover_image_' . $params['object'], sz_core_cover_image_default( 'local', $params ), $params );
	

	if ( true === $params['html'] ) {

		/** This filter is documented in sz-core/sz-core-cover-images.php */
		return apply_filters( 'sz_core_fetch_cover_image', '<img src="' . $gravatar . '"' . $html_css_id . $html_class . $html_width . $html_height . $html_alt . $html_title . $extra_attr . ' />', $params, $params['item_id'], $params['cover_image_dir'], $html_css_id, $html_width, $html_height, $cover_image_folder_url, $cover_image_folder_dir );
	} else {

		/** This filter is documented in sz-core/sz-core-cover-images.php */
		return apply_filters( 'sz_core_fetch_cover_image_url', $gravatar, $params );
	}
}

/**
 * Delete an existing cover-image.
 *
 * @since 1.1.0
 *
 * @param array|string $args {
 *     Array of function parameters.
 *     @type bool|int    $item_id    ID of the item whose cover-image you're deleting.
 *                                   Defaults to the current item of type $object.
 *     @type string      $object     Object type of the item whose cover-image you're
 *                                   deleting. 'user', 'group', 'blog', or custom.
 *                                   Default: 'user'.
 *     @type bool|string $cover_image_dir Subdirectory where cover-image is located.
 *                                   Default: false, which falls back on the default location
 *                                   corresponding to the $object.
 * }
 * @return bool True on success, false on failure.
 */
function sz_core_delete_existing_cover_image( $args = '' ) {

	$defaults = array(
		'item_id'    => false,
		'object'     => 'user', // User OR group OR blog OR custom type (if you use filters).
		'cover_image_dir' => false
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	/**
	 * Filters whether or not to handle deleting an existing cover-image.
	 *
	 * If you want to override this function, make sure you return false.
	 *
	 * @since 2.5.1
	 *
	 * @param bool  $value Whether or not to delete the cover-image.
	 * @param array $args {
	 *     Array of function parameters.
	 *
	 *     @type bool|int    $item_id    ID of the item whose cover-image you're deleting.
	 *                                   Defaults to the current item of type $object.
	 *     @type string      $object     Object type of the item whose cover-image you're
	 *                                   deleting. 'user', 'group', 'blog', or custom.
	 *                                   Default: 'user'.
	 *     @type bool|string $cover_image_dir Subdirectory where cover-image is located.
	 *                                   Default: false, which falls back on the default location
	 *                                   corresponding to the $object.
	 * }
	 */
	if ( ! apply_filters( 'sz_core_pre_delete_existing_cover_image', true, $args ) ) {
		return true;
	}

	if ( empty( $item_id ) ) {
		if ( 'user' == $object )
			$item_id = sz_displayed_user_id();
		elseif ( 'group' == $object )
			$item_id = sportszone()->groups->current_group->id;
		elseif ( 'blog' == $object )
			$item_id = $current_blog->id;

		/** This filter is documented in sz-core/sz-core-cover-images.php */
		$item_id = apply_filters( 'sz_core_cover_image_item_id', $item_id, $object );

		if ( !$item_id ) return false;
	}

	if ( empty( $cover_image_dir ) ) {
		if ( 'user' == $object )
			$cover_image_dir = 'user-cover-images';
		elseif ( 'group' == $object )
			$cover_image_dir = 'group-cover-images';
		elseif ( 'blog' == $object )
			$cover_image_dir = 'blog-cover-images';

		/** This filter is documented in sz-core/sz-core-cover-images.php */
		$cover_image_dir = apply_filters( 'sz_core_cover_image_dir', $cover_image_dir, $object );

		if ( !$cover_image_dir ) return false;
	}

	/** This filter is documented in sz-core/sz-core-cover-images.php */
	$cover_image_folder_dir = apply_filters( 'sz_core_cover_image_folder_dir', sz_core_cover_image_upload_path() . '/' . $cover_image_dir . '/' . $item_id, $item_id, $object, $cover_image_dir );

	if ( !file_exists( $cover_image_folder_dir ) )
		return false;

	if ( $av_dir = opendir( $cover_image_folder_dir ) ) {
		while ( false !== ( $cover_image_file = readdir($av_dir) ) ) {
			if ( ( preg_match( "/-bpfull/", $cover_image_file ) || preg_match( "/-bpthumb/", $cover_image_file ) ) && '.' != $cover_image_file && '..' != $cover_image_file )
				@unlink( $cover_image_folder_dir . '/' . $cover_image_file );
		}
	}
	closedir($av_dir);

	@rmdir( $cover_image_folder_dir );

	/**
	 * Fires after deleting an existing cover-image.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args Array of arguments used for cover-image deletion.
	 */
	do_action( 'sz_core_delete_existing_cover_image', $args );

	return true;
}

/**
 * Ajax delete an cover-image for a given object and item id.
 *
 * @since 2.3.0
 *
 * @return string|null A JSON object containing success data if the cover-image was deleted,
 *                     error message otherwise.
 */
function sz_cover_image_ajax_delete() {
	if ( ! sz_is_post_request() ) {
		wp_send_json_error();
	}

	$cover_image_data = $_POST;

	if ( empty( $cover_image_data['object'] ) || empty( $cover_image_data['item_id'] ) ) {
		wp_send_json_error();
	}

	$nonce = 'sz_delete_cover_image_link';
	if ( 'group' === $cover_image_data['object'] ) {
		$nonce = 'sz_group_cover_image_delete';
	}

	// Check the nonce.
	check_admin_referer( $nonce, 'nonce' );

	// Capability check.
	if ( ! sz_attachments_current_user_can( 'edit_cover_image', $cover_image_data ) ) {
		wp_send_json_error();
	}

	// Handle delete.
	if ( sz_core_delete_existing_cover_image( array( 'item_id' => $cover_image_data['item_id'], 'object' => $cover_image_data['object'] ) ) ) {
		$return = array(
			'cover_image' => html_entity_decode( sz_core_fetch_cover_image( array(
				'object'  => $cover_image_data['object'],
				'item_id' => $cover_image_data['item_id'],
				'html'    => false,
				'type'    => 'full',
			) ) ),
			'feedback_code' => 4,
			'item_id'       => $cover_image_data['item_id'],
		);

		wp_send_json_success( $return );
	} else {
		wp_send_json_error( array(
			'feedback_code' => 3,
		) );
	}
}
add_action( 'wp_ajax_sz_cover_image_delete', 'sz_cover_image_ajax_delete' );

/**
 * Handle cover-image uploading.
 *
 * The functions starts off by checking that the file has been uploaded
 * properly using sz_core_check_cover_image_upload(). It then checks that the file
 * size is within limits, and that it has an accepted file extension (jpg, gif,
 * png). If everything checks out, crop the image and move it to its real
 * location.
 *
 * @since 1.1.0
 *
 * @see sz_core_check_cover_image_upload()
 * @see sz_core_check_cover_image_type()
 *
 * @param array  $file              The appropriate entry the from $_FILES superglobal.
 * @param string $upload_dir_filter A filter to be applied to 'upload_dir'.
 * @return bool True on success, false on failure.
 */
function sz_core_cover_image_handle_upload( $file, $upload_dir_filter ) {
	/**
	 * Filters whether or not to handle uploading.
	 *
	 * If you want to override this function, make sure you return false.
	 *
	 * @since 1.2.4
	 *
	 * @param bool   $value             Whether or not to crop.
	 * @param array  $file              Appropriate entry from $_FILES superglobal.
	 * @parma string $upload_dir_filter A filter to be applied to 'upload_dir'.
	 */
	if ( ! apply_filters( 'sz_core_pre_cover_image_handle_upload', true, $file, $upload_dir_filter ) ) {
		return true;
	}

	// Setup some variables.
	$sz          = sportszone();
	$upload_path = sz_core_cover_image_upload_path();
	
	// Upload the file.
	$cover_image_attachment = new SZ_Attachment_Cover_Image();
	
	$sz->cover_image_admin->original = $cover_image_attachment->upload( $file, $upload_dir_filter );
	// In case of an error, stop the process and display a feedback to the user.
	if ( ! empty( $sz->cover_image_admin->original['error'] ) ) {
		sz_core_add_message( sprintf( __( 'Upload Failed! Error was: %s', 'sportszone' ), $sz->cover_image_admin->original['error'] ), 'error' );
		return false;
	}

	// The Avatar UI available width.
	$ui_available_width = 0;
	// Try to set the ui_available_width using the cover_image_admin global.
	if ( isset( $sz->cover_image_admin->ui_available_width ) ) {
		$ui_available_width = $sz->cover_image_admin->ui_available_width;
	}
	
	// Maybe resize.
	$sz->cover_image_admin->resized = $cover_image_attachment->shrink( $sz->cover_image_admin->original['file'], $ui_available_width );
	$sz->cover_image_admin->image   = new stdClass();
	
	// We only want to handle one image after resize.
	if ( empty( $sz->cover_image_admin->resized ) ) {
		$sz->cover_image_admin->image->file = $sz->cover_image_admin->original['file'];
		$sz->cover_image_admin->image->dir  = str_replace( $upload_path, '', $sz->cover_image_admin->original['file'] );
	} else {
		$sz->cover_image_admin->image->file = $sz->cover_image_admin->resized['path'];
		$sz->cover_image_admin->image->dir  = str_replace( $upload_path, '', $sz->cover_image_admin->resized['path'] );
		@unlink( $sz->cover_image_admin->original['file'] );
	}
	
	// Check for WP_Error on what should be an image.
	if ( is_wp_error( $sz->cover_image_admin->image->dir ) ) {
		sz_core_add_message( sprintf( __( 'Upload failed! Error was: %s', 'sportszone' ), $sz->cover_image_admin->image->dir->get_error_message() ), 'error' );
		return false;
	}

	// If the uploaded image is smaller than the "full" dimensions, throw a warning.
	if ( $cover_image_attachment->is_too_small( $sz->cover_image_admin->image->file ) ) {
		sz_core_add_message( sprintf( __( 'You have selected an image that is smaller than recommended. For best results, upload a picture larger than %d x %d pixels.', 'sportszone' ), sz_core_cover_image_full_width(), sz_core_cover_image_full_height() ), 'error' );
	}

	// Set the url value for the image.
	$sz->cover_image_admin->image->url = sz_core_cover_image_url() . $sz->cover_image_admin->image->dir;

	return true;
}

/**
 * Ajax upload an cover_image.
 *
 * @since 2.3.0
 *
 * @return string|null A JSON object containing success data if the upload succeeded
 *                     error message otherwise.
 */
function sz_cover_image_ajax_upload() {
	if ( ! sz_is_post_request() ) {
		wp_die();
	}

	/**
	 * Sending the json response will be different if
	 * the current Plupload runtime is html4.
	 */
	$is_html4 = false;
	if ( ! empty( $_POST['html4' ] ) ) {
		$is_html4 = true;
	}

	// Check the nonce.
	check_admin_referer( 'sz-uploader' );

	// Init the SportsZone parameters.
	$sz_params = array();

	// We need it to carry on.
	if ( ! empty( $_POST['sz_params' ] ) ) {
		$sz_params = $_POST['sz_params' ];
	} else {
		sz_attachments_json_response( false, $is_html4, array(
			'type'	=> 'upload_error',
			'message'	=> 'params empty'
		)  );
	}

	// We need the object to set the uploads dir filter.
	if ( empty( $sz_params['object'] ) ) {
		sz_attachments_json_response( false, $is_html4, array(
			'type'	=> 'upload_error',
			'message'	=> 'empty object'
		)  );
	}

	// Capability check.
	if ( ! sz_attachments_current_user_can( 'edit_cover_image', $sz_params ) ) {
		sz_attachments_json_response( false, $is_html4, array(
			'type'	=> 'upload_error',
			'message'	=> 'edit cover image'
		)  );
	}

	$sz = sportszone();
	$sz_params['upload_dir_filter'] = '';
	$needs_reset = array();

	if ( 'user' === $sz_params['object'] && sz_is_active( 'xprofile' ) ) {
		$sz_params['upload_dir_filter'] = 'sz_attachments_cover_image_upload_dir';

		if ( ! sz_displayed_user_id() && ! empty( $sz_params['item_id'] ) ) {
			$needs_reset = array( 'key' => 'displayed_user', 'value' => $sz->displayed_user );
			$sz->displayed_user->id = $sz_params['item_id'];
		}
	} elseif ( 'group' === $sz_params['object'] && sz_is_active( 'groups' ) ) {
		$sz_params['upload_dir_filter'] = 'groups_cover_image_upload_dir';

		if ( ! sz_get_current_group_id() && ! empty( $sz_params['item_id'] ) ) {
			$needs_reset = array( 'component' => 'groups', 'key' => 'current_group', 'value' => $sz->groups->current_group );
			$sz->groups->current_group = groups_get_group( $sz_params['item_id'] );
		}
	} else {
		/**
		 * Filter here to deal with other components.
		 *
		 * @since 2.3.0
		 *
		 * @var array $sz_params the SportsZone Ajax parameters.
		 */
		$sz_params = apply_filters( 'sz_core_cover_image_ajax_upload_params', $sz_params );
	}

	if ( ! isset( $sz->cover_image_admin ) ) {
		$sz->cover_image_admin = new stdClass();
	}

	/**
	 * The SportsZone upload parameters is including the Avatar UI Available width,
	 * add it to the cover_image_admin global for a later use.
	 */
	if ( isset( $sz_params['ui_available_width'] ) ) {
		$sz->cover_image_admin->ui_available_width =  (int) $sz_params['ui_available_width'];
	}

	// Upload the cover_image.
	$cover_image = sz_core_cover_image_handle_upload( $_FILES, $sz_params['upload_dir_filter'] );

	// Reset objects.
	if ( ! empty( $needs_reset ) ) {
		if ( ! empty( $needs_reset['component'] ) ) {
			$sz->{$needs_reset['component']}->{$needs_reset['key']} = $needs_reset['value'];
		} else {
			$sz->{$needs_reset['key']} = $needs_reset['value'];
		}
	}

	// Init the feedback message.
	$feedback_message = false;

	if ( ! empty( $sz->template_message ) ) {
		$feedback_message = $sz->template_message;

		// Remove template message.
		$sz->template_message      = false;
		$sz->template_message_type = false;

		@setcookie( 'sz-message', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
		@setcookie( 'sz-message-type', false, time() - 1000, COOKIEPATH, COOKIE_DOMAIN, is_ssl() );
	}

	if ( empty( $cover_image ) ) {
		// Default upload error.
		$message = __( 'Upload failed.', 'sportszone' );

		// Use the template message if set.
		if ( ! empty( $feedback_message ) ) {
			$message = $feedback_message;
		}

		// Upload error reply.
		sz_attachments_json_response( false, $is_html4, array(
			'type'    => 'upload_error',
			'message' => $message,
			'more'	  => $cover_image,
		) );
	}

	if ( empty( $sz->cover_image_admin->image->file ) ) {
		sz_attachments_json_response( false, $is_html4, array(
			'type'	=> 'upload_error',
			'message'	=> 'file empty',
			'file'		=> $sz->cover_image_admin->image
		) );
	}

	$uploaded_image = @getimagesize( $sz->cover_image_admin->image->file );

	// Set the name of the file.
	$name = $_FILES['file']['name'];
	$name_parts = pathinfo( $name );
	$name = trim( substr( $name, 0, - ( 1 + strlen( $name_parts['extension'] ) ) ) );

	// Finally return the cover_image to the editor.
	sz_attachments_json_response( true, $is_html4, array(
		'name'      => $name,
		'url'       => $sz->cover_image_admin->image->url,
		'width'     => $uploaded_image[0],
		'height'    => $uploaded_image[1],
		'feedback'  => $feedback_message,
		'image'		=> $sz->cover_image_admin->image,
	) );
}
add_action( 'wp_ajax_sz_cover_image_upload', 'sz_cover_image_ajax_upload' );

/**
 * Handle cover_image webcam capture.
 *
 * @since 2.3.0
 *
 * @param string $data    Base64 encoded image.
 * @param int    $item_id Item to associate.
 * @return bool True on success, false on failure.
 */
function sz_cover_image_handle_capture( $data = '', $item_id = 0 ) {
	if ( empty( $data ) || empty( $item_id ) ) {
		return false;
	}

	/**
	 * Filters whether or not to handle cover_image webcam capture.
	 *
	 * If you want to override this function, make sure you return false.
	 *
	 * @since 2.5.1
	 *
	 * @param bool   $value   Whether or not to crop.
	 * @param string $data    Base64 encoded image.
	 * @param int    $item_id Item to associate.
	 */
	if ( ! apply_filters( 'sz_cover_image_pre_handle_capture', true, $data, $item_id ) ) {
		return true;
	}

	$cover_image_dir = sz_core_cover_image_upload_path() . '/cover-images';

	// It's not a regular upload, we may need to create this folder.
	if ( ! file_exists( $cover_image_dir ) ) {
		if ( ! wp_mkdir_p( $cover_image_dir ) ) {
			return false;
		}
	}

	/**
	 * Filters the Avatar folder directory.
	 *
	 * @since 2.3.0
	 *
	 * @param string $cover_image_dir Directory for storing cover_images.
	 * @param int    $item_id    ID of the item being acted on.
	 * @param string $value      Avatar type.
	 * @param string $value      Cover Images word.
	 */
	$cover_image_folder_dir = apply_filters( 'sz_core_cover_image_folder_dir', $cover_image_dir . '/' . $item_id, $item_id, 'user', 'cover-images' );

	// It's not a regular upload, we may need to create this folder.
	if( ! is_dir( $cover_image_folder_dir ) ) {
		if ( ! wp_mkdir_p( $cover_image_folder_dir ) ) {
			return false;
		}
	}

	$original_file = $cover_image_folder_dir . '/webcam-capture-' . $item_id . '.png';

	if ( file_put_contents( $original_file, $data ) ) {
		$cover_image_to_crop = str_replace( sz_core_cover_image_upload_path(), '', $original_file );

		// Crop to default values.
		$crop_args = array( 'item_id' => $item_id, 'original_file' => $cover_image_to_crop, 'crop_x' => 0, 'crop_y' => 0 );

		return sz_core_cover_image_handle_crop( $crop_args );
	} else {
		return false;
	}
}

/**
 * Crop an uploaded cover_image.
 *
 * @since 1.1.0
 *
 * @param array|string $args {
 *     Array of function parameters.
 *
 *     @type string      $object        Object type of the item whose cover_image you're
 *                                      handling. 'user', 'group', 'blog', or custom.
 *                                      Default: 'user'.
 *     @type string      $cover_image_dir    Subdirectory where cover_image should be stored.
 *                                      Default: 'cover_images'.
 *     @type bool|int    $item_id       ID of the item that the cover_image belongs to.
 *     @type bool|string $original_file Absolute path to the original cover_image file.
 *     @type int         $crop_w        Crop width. Default: the global 'full' cover_image width,
 *                                      as retrieved by sz_core_cover_image_full_width().
 *     @type int         $crop_h        Crop height. Default: the global 'full' cover_image height,
 *                                      as retrieved by sz_core_cover_image_full_height().
 *     @type int         $crop_x        The horizontal starting point of the crop. Default: 0.
 *     @type int         $crop_y        The vertical starting point of the crop. Default: 0.
 * }
 * @return bool True on success, false on failure.
 */
function sz_core_cover_image_handle_crop( $args = '' ) {

	$r = wp_parse_args( $args, array(
		'object'        => 'user',
		'cover_image_dir'    => 'cover_images',
		'item_id'       => false,
		'original_file' => false,
		'crop_w'        => sz_core_cover_image_full_width(),
		'crop_h'        => sz_core_cover_image_full_height(),
		'crop_x'        => 0,
		'crop_y'        => 0
	) );

	/**
	 * Filters whether or not to handle cropping.
	 *
	 * If you want to override this function, make sure you return false.
	 *
	 * @since 1.2.4
	 *
	 * @param bool  $value Whether or not to crop.
	 * @param array $r     Array of parsed arguments for function.
	 */
	if ( ! apply_filters( 'sz_core_pre_cover_image_handle_crop', true, $r ) ) {
		return true;
	}
	
	// Crop the file.
	$cover_image_attachment = new SZ_Attachment_Cover_Image();
	$cropped           = $cover_image_attachment->crop( $r );
	
	// Check for errors.
	if ( empty( $cropped['full'] ) || empty( $cropped['thumb'] ) || is_wp_error( $cropped['full'] ) || is_wp_error( $cropped['thumb'] ) ) {
		return $cropped;
	}

	return true;
}

/**
 * Ajax set an cover_image for a given object and item id.
 *
 * @since 2.3.0
 *
 * @return string|null A JSON object containing success data if the crop/capture succeeded
 *                     error message otherwise.
 */
function sz_cover_image_ajax_set() {
	if ( ! sz_is_post_request() ) {
		wp_send_json_error();
	}

	// Check the nonce.
	check_admin_referer( 'sz_cover_image_cropstore', 'nonce' );

	$cover_image_data = wp_parse_args( $_POST, array(
		'crop_w' => sz_core_cover_image_full_width(),
		'crop_h' => sz_core_cover_image_full_height(),
		'crop_x' => 0,
		'crop_y' => 0
	) );

	if ( empty( $cover_image_data['object'] ) || empty( $cover_image_data['item_id'] ) || empty( $cover_image_data['original_file'] ) ) {
		wp_send_json_error();
	}

	// Capability check.
	if ( ! sz_attachments_current_user_can( 'edit_cover_image', $cover_image_data ) ) {
		wp_send_json_error();
	}

	if ( ! empty( $cover_image_data['type'] ) && 'camera' === $cover_image_data['type'] && 'user' === $cover_image_data['object'] ) {
		$webcam_cover_image = false;

		if ( ! empty( $cover_image_data['original_file'] ) ) {
			$webcam_cover_image = str_replace( array( 'data:image/png;base64,', ' ' ), array( '', '+' ), $cover_image_data['original_file'] );
			$webcam_cover_image = base64_decode( $webcam_cover_image );
		}

		if ( ! sz_cover_image_handle_capture( $webcam_cover_image, $cover_image_data['item_id'] ) ) {
			wp_send_json_error( array(
				'feedback_code' => 1
			) );

		} else {
			$return = array(
				'cover_image' => html_entity_decode( sz_core_fetch_cover_image( array(
					'object'  => $cover_image_data['object'],
					'item_id' => $cover_image_data['item_id'],
					'html'    => false,
					'type'    => 'full',
				) ) ),
				'feedback_code' => 2,
				'item_id'       => $cover_image_data['item_id'],
			);

			/**
			 * Fires if the new cover_image was successfully captured.
			 *
			 * @since 1.1.0 Used to inform the cover_image was successfully cropped
			 * @since 2.3.4 Add two new parameters to inform about the user id and
			 *              about the way the cover_image was set (eg: 'crop' or 'camera')
		 *              Move the action at the right place, once the cover_image is set
			 * @since 2.8.0 Added the `$cover_image_data` parameter.
			 *
			 * @param string $item_id     Inform about the user id the cover_image was set for.
			 * @param string $type        Inform about the way the cover_image was set ('camera').
			 * @param array  $cover_image_data Array of parameters passed to the cover_image handler.
			 */
			do_action( 'xprofile_cover_image_uploaded', (int) $cover_image_data['item_id'], $cover_image_data['type'], $cover_image_data );

			wp_send_json_success( $return );
		}

		return;
	}

	$original_file = str_replace( sz_core_cover_image_url(), '', $cover_image_data['original_file'] );

	// Set cover_images dir & feedback part.
	if ( 'user' === $cover_image_data['object'] ) {
		$cover_image_dir = sanitize_key( $cover_image_data['object'] ) . '-cover-images';

	// Defaults to object-cover-images dir.
	} else {
		$cover_image_dir = sanitize_key( $cover_image_data['object'] ) . '-cover-images';
	}

	// Crop args.
	$r = array(
		'item_id'       => $cover_image_data['item_id'],
		'object'        => $cover_image_data['object'],
		'cover_image_dir'    => $cover_image_dir,
		'original_file' => $original_file,
		'crop_w'        => $cover_image_data['crop_w'],
		'crop_h'        => $cover_image_data['crop_h'],
		'crop_x'        => $cover_image_data['crop_x'],
		'crop_y'        => $cover_image_data['crop_y']
	);

	// Handle crop.
	if ( sz_core_cover_image_handle_crop( $r ) ) {
		$return = array(
			'cover_image' => html_entity_decode( sz_core_fetch_cover_image( array(
				'object'  => $cover_image_data['object'],
				'item_id' => $cover_image_data['item_id'],
				'html'    => false,
				'type'    => 'full',
			) ) ),
			'feedback_code' => 2,
			'item_id'       => $cover_image_data['item_id'],
			'crop_return'	=> sz_core_cover_image_handle_crop( $r )
		);

		if ( 'user' === $cover_image_data['object'] ) {
			/** This action is documented in sz-core/sz-core-cover-images.php */
			do_action( 'xprofile_cover_image_uploaded', (int) $cover_image_data['item_id'], $cover_image_data['type'], $r );
		} elseif ( 'group' === $cover_image_data['object'] ) {
			/** This action is documented in sz-groups/sz-groups-screens.php */
			do_action( 'groups_cover_image_uploaded', (int) $cover_image_data['item_id'], $cover_image_data['type'], $r );
		}

		wp_send_json_success( $return );
	} else {
		wp_send_json_error( array(
			'feedback_code' => 1,
			'args'			=> $r,
			'return'		=> sz_core_cover_image_handle_crop( $r )
		) );
	}
}
add_action( 'wp_ajax_sz_cover_image_set', 'sz_cover_image_ajax_set' );

/**
 * Filter {@link get_cover_image_url()} to use the SportsZone user cover_image URL.
 *
 * @since 2.9.0
 *
 * @param  string $retval      The URL of the cover_image.
 * @param  mixed  $id_or_email The Grcover_image to retrieve. Accepts a user_id, grcover_image md5 hash,
 *                             user email, WP_User object, WP_Post object, or WP_Comment object.
 * @param  array  $args        Arguments passed to get_cover_image_data(), after processing.
 * @return string
 */
function sz_core_get_cover_image_data_url_filter( $retval, $id_or_email, $args ) {
	$user = null;

	// Ugh, hate duplicating code; process the user identifier.
	if ( is_numeric( $id_or_email ) ) {
		$user = get_user_by( 'id', absint( $id_or_email ) );
	} elseif ( $id_or_email instanceof WP_User ) {
		// User Object
		$user = $id_or_email;
	} elseif ( $id_or_email instanceof WP_Post ) {
		// Post Object
		$user = get_user_by( 'id', (int) $id_or_email->post_author );
	} elseif ( $id_or_email instanceof WP_Comment ) {
		if ( ! empty( $id_or_email->user_id ) ) {
			$user = get_user_by( 'id', (int) $id_or_email->user_id );
		}
	} elseif ( is_email( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
	}

	// No user, so bail.
	if ( false === $user instanceof WP_User ) {
		return $retval;
	}

	// Set SportsZone-specific cover_image args.
	$args['item_id'] = $user->ID;
	$args['html']    = false;

	// Use the 'full' type if size is larger than BP's thumb width.
	if ( (int) $args['size'] > sz_core_cover_image_thumb_width() ) {
		$args['type'] = 'full';
	}

	// Get the SportsZone cover_image URL.
	if ( $sz_cover_image = sz_core_fetch_cover_image( $args ) ) {
		return $sz_cover_image;
	}

	return $retval;
}
add_filter( 'get_cover_image_url', 'sz_core_get_cover_image_data_url_filter', 10, 3 );

/**
 * Is the current cover_image upload error-free?
 *
 * @since 1.0.0
 *
 * @param array $file The $_FILES array.
 * @return bool True if no errors are found. False if there are errors.
 */
function sz_core_check_cover_image_upload( $file ) {
	if ( isset( $file['error'] ) && $file['error'] )
		return false;

	return true;
}

/**
 * Is the file size of the current cover_image upload permitted?
 *
 * @since 1.0.0
 *
 * @param array $file The $_FILES array.
 * @return bool True if the cover_image is under the size limit, otherwise false.
 */
function sz_core_check_cover_image_size( $file ) {
	if ( $file['file']['size'] > sz_core_cover_image_original_max_filesize() )
		return false;

	return true;
}

/**
 * Get allowed cover_image types.
 *
 * @since 2.3.0
 *
 * @return array
 */
function sz_core_get_allowed_cover_image_types() {
	$allowed_types = sz_attachments_get_allowed_types( 'cover_image' );

	/**
	 * Filters the list of allowed image types.
	 *
	 * @since 2.3.0
	 *
	 * @param array $allowed_types List of image types.
	 */
	$cover_image_types = (array) apply_filters( 'sz_core_get_allowed_cover_image_types', $allowed_types );

	if ( empty( $cover_image_types ) ) {
		$cover_image_types = $allowed_types;
	} else {
		$cover_image_types = array_intersect( $allowed_types, $cover_image_types );
	}

	return array_values( $cover_image_types );
}

/**
 * Get allowed cover_image mime types.
 *
 * @since 2.3.0
 *
 * @return array
 */
function sz_core_get_allowed_cover_image_mimes() {
	$allowed_types  = sz_core_get_allowed_cover_image_types();

	return sz_attachments_get_allowed_mimes( 'cover_image', $allowed_types );
}

/**
 * Does the current cover_image upload have an allowed file type?
 *
 * Permitted file types are JPG, GIF and PNG.
 *
 * @since 1.0.0
 *
 * @param array $file The $_FILES array.
 * @return bool True if the file extension is permitted, otherwise false.
 */
function sz_core_check_cover_image_type( $file ) {
	return sz_attachments_check_filetype( $file['file']['tmp_name'], $file['file']['name'], sz_core_get_allowed_cover_image_mimes() );
}

/**
 * Fetch data from the BP root blog's upload directory.
 *
 * @since 1.8.0
 *
 * @param string $type The variable we want to return from the $sz->avatars object.
 *                     Only 'upload_path' and 'url' are supported. Default: 'upload_path'.
 * @return string The avatar upload directory path.
 */
function sz_core_get_cover_image_upload_dir( $type = 'upload_path' ) {
	$sz = sportszone();

	switch ( $type ) {
		case 'upload_path' :
			$constant = 'SZ_COVER_IMAGE_UPLOAD_PATH';
			$key      = 'basedir';

			break;

		case 'url' :
			$constant = 'SZ_COVER_IMAGE_URL';
			$key      = 'baseurl';

			break;

		default :
			return false;

			break;
	}

	// See if the value has already been calculated and stashed in the $sz global.
	if ( isset( $sz->cover_image->$type ) ) {
		$retval = $sz->cover_image->$type;
	} else {
		// If this value has been set in a constant, just use that.
		if ( defined( $constant ) ) {
			$retval = constant( $constant );
		} else {

			// Use cached upload dir data if available.
			if ( ! empty( $sz->cover_image->upload_dir ) ) {
				$upload_dir = $sz->cover_image->upload_dir;

			// No cache, so query for it.
			} else {

				// Get upload directory information from current site.
				$upload_dir = sz_upload_dir();

				// Stash upload directory data for later use.
				$sz->cover_image->upload_dir = $upload_dir;
			}

			// Directory does not exist and cannot be created.
			if ( ! empty( $upload_dir['error'] ) ) {
				$retval = 'dir_error';
				
			} else {
				$retval = $upload_dir[$key];

				// If $key is 'baseurl', check to see if we're on SSL
				// Workaround for WP13941, WP15928, WP19037.
				if ( $key == 'baseurl' && is_ssl() ) {
					$retval = str_replace( 'http://', 'https://', $retval );
				}
			}

		}

		// Stash in $sz for later use.
		$sz->cover_image->$type = $retval;
	}

	return $retval;
}

/**
 * Get the absolute upload path for the WP installation.
 *
 * @since 1.2.0
 *
 * @return string Absolute path to WP upload directory.
 */
function sz_core_cover_image_upload_path() {

	/**
	 * Filters the absolute upload path for the WP installation.
	 *
	 * @since 1.2.0
	 *
	 * @param string $value Absolute upload path for the WP installation.
	 */
	return apply_filters( 'sz_core_cover_image_upload_path', sz_core_get_cover_image_upload_dir() );
}

/**
 * Get the raw base URL for root site upload location.
 *
 * @since 1.2.0
 *
 * @return string Full URL to current upload location.
 */
function sz_core_cover_image_url() {

	/**
	 * Filters the raw base URL for root site upload location.
	 *
	 * @since 1.2.0
	 *
	 * @param string $value Raw base URL for the root site upload location.
	 */
	
	return apply_filters( 'sz_core_cover_image_url', sz_core_get_cover_image_upload_dir( 'url' ) );
}

/**
 * Check if a given user ID has an uploaded cover_image.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the user whose cover_image is being checked.
 * @return bool True if the user has uploaded a local cover_image. Otherwise false.
 */
function sz_get_user_has_cover_image( $user_id = 0 ) {

	if ( empty( $user_id ) )
		$user_id = sz_displayed_user_id();

	$retval = false;
	if ( sz_core_fetch_cover_image( array( 'item_id' => $user_id, 'no_grav' => true, 'html' => false, 'type' => 'full' ) ) != sz_core_cover_image_default( 'local' ) )
		$retval = true;

	/**
	 * Filters whether or not a user has an uploaded cover_image.
	 *
	 * @since 1.6.0
	 *
	 * @param bool $retval  Whether or not a user has an uploaded cover_image.
	 * @param int  $user_id ID of the user being checked.
	 */
	return (bool) apply_filters( 'sz_get_user_has_cover_image', $retval, $user_id );
}

/**
 * Utility function for fetching an cover_image dimension setting.
 *
 * @since 1.5.0
 *
 * @param string $type   Dimension type you're fetching dimensions for. 'thumb'
 *                       or 'full'. Default: 'thumb'.
 * @param string $h_or_w Which dimension is being fetched. 'height' or 'width'.
 *                       Default: 'height'.
 * @return int|bool $dim The dimension.
 */
function sz_core_cover_image_dimension( $type = 'thumb', $h_or_w = 'height' ) {
	$sz  = sportszone();
	$dim = isset( $sz->cover_image->{$type}->{$h_or_w} ) ? (int) $sz->cover_image->{$type}->{$h_or_w} : false;

	/**
	 * Filters the cover_image dimension setting.
	 *
	 * @since 1.5.0
	 *
	 * @param int|bool $dim    Dimension setting for the type.
	 * @param string   $type   The type of cover_image whose dimensions are requested. Default 'thumb'.
	 * @param string   $h_or_w The dimension parameter being requested. Default 'height'.
	 */
	return apply_filters( 'sz_core_cover_image_dimension', $dim, $type, $h_or_w );
}

/**
 * Get the 'thumb' cover_image width setting.
 *
 * @since 1.5.0
 *
 * @return int The 'thumb' width.
 */
function sz_core_cover_image_thumb_width() {

	/**
	 * Filters the 'thumb' cover_image width setting.
	 *
	 * @since 1.5.0
	 *
	 * @param int $value Value for the 'thumb' cover_image width setting.
	 */
	return apply_filters( 'sz_core_cover_image_thumb_width', sz_core_cover_image_dimension( 'thumb', 'width' ) );
}

/**
 * Get the 'thumb' cover_image height setting.
 *
 * @since 1.5.0
 *
 * @return int The 'thumb' height.
 */
function sz_core_cover_image_thumb_height() {

	/**
	 * Filters the 'thumb' cover_image height setting.
	 *
	 * @since 1.5.0
	 *
	 * @param int $value Value for the 'thumb' cover_image height setting.
	 */
	return apply_filters( 'sz_core_cover_image_thumb_height', sz_core_cover_image_dimension( 'thumb', 'height' ) );
}

/**
 * Get the 'full' cover_image width setting.
 *
 * @since 1.5.0
 *
 * @return int The 'full' width.
 */
function sz_core_cover_image_full_width() {

	/**
	 * Filters the 'full' cover_image width setting.
	 *
	 * @since 1.5.0
	 *
	 * @param int $value Value for the 'full' cover_image width setting.
	 */
	return apply_filters( 'sz_core_cover_image_full_width', sz_core_cover_image_dimension( 'full', 'width' ) );
}

/**
 * Get the 'full' cover_image height setting.
 *
 * @since 1.5.0
 *
 * @return int The 'full' height.
 */
function sz_core_cover_image_full_height() {

	/**
	 * Filters the 'full' cover_image height setting.
	 *
	 * @since 1.5.0
	 *
	 * @param int $value Value for the 'full' cover_image height setting.
	 */
	return apply_filters( 'sz_core_cover_image_full_height', sz_core_cover_image_dimension( 'full', 'height' ) );
}

/**
 * Get the max width for original cover_image uploads.
 *
 * @since 1.5.0
 *
 * @return int The max width for original cover_image uploads.
 */
function sz_core_cover_image_original_max_width() {

	/**
	 * Filters the max width for original cover_image uploads.
	 *
	 * @since 1.5.0
	 *
	 * @param int $value Value for the max width.
	 */
	return apply_filters( 'sz_core_cover_image_original_max_width', (int) sportszone()->cover_image->original_max_width );
}

/**
 * Get the max filesize for original cover_image uploads.
 *
 * @since 1.5.0
 *
 * @return int The max filesize for original cover_image uploads.
 */
function sz_core_cover_image_original_max_filesize() {

	/**
	 * Filters the max filesize for original cover_image uploads.
	 *
	 * @since 1.5.0
	 *
	 * @param int $value Value for the max filesize.
	 */
	return apply_filters( 'sz_core_cover_image_original_max_filesize', (int) sportszone()->cover_image->original_max_filesize );
}

/**
 * Get the URL of the 'full' default cover_image.
 *
 * @since 1.5.0
 * @since 2.6.0 Introduced `$params` and `$object_type` parameters.
 *
 * @param string $type   'local' if the fallback should be the locally-hosted version
 *                       of the mystery person, 'grcover_image' if the fallback should be
 *                       Grcover_image's version. Default: 'grcover_image'.
 * @param array  $params Parameters passed to sz_core_fetch_cover_image().
 * @return string The URL of the default cover_image.
 */
function sz_core_cover_image_default( $type = 'local', $params = array() ) {
	// Local override.
	if ( defined( 'SZ_COVER_IMAGE_DEFAULT' ) ) {
		$cover_image = SZ_COVER_IMAGE_DEFAULT;

	// Use the local default image.
	} elseif ( 'local' === $type ) { 
		$size = '';
		if (
			( isset( $params['type'] ) && 'thumb' === $params['type'] && sz_core_cover_image_thumb_width() <= 50 ) ||
			( isset( $params['width'] ) && $params['width'] <= 50 )
		) {

			$size = '-50';
		}

		$cover_image = sportszone()->plugin_url . "sz-core/images/rugby-group.jpg";
	} 

	/**
	 * Filters the URL of the 'full' default cover_image.
	 *
	 * @since 1.5.0
	 * @since 2.6.0 Added `$params`.
	 *
	 * @param string $cover_image URL of the default cover_image.
	 * @param array  $params Params provided to sz_core_fetch_cover_image().
	 */
	return apply_filters( 'sz_core_cover_image_default', $cover_image, $params );
}

/**
 * Get the URL of the 'thumb' default cover_image.
 *
 * Uses Gravatar's mystery-person avatar, unless SZ_COVER_IMAGE_DEFAULT_THUMB has been
 * defined.
 *
 * @since 1.5.0
 * @since 2.6.0 Introduced `$object_type` parameter.
 *
 * @param string $type   'local' if the fallback should be the locally-hosted version
 *                       of the mystery person, 'gravatar' if the fallback should be
 *                       Gravatar's version. Default: 'gravatar'.
 * @param array  $params Parameters passed to sz_core_fetch_cover_image().
 * @return string The URL of the default avatar thumb.
 */
function sz_core_cover_image_default_thumb( $type = 'local', $params = array() ) {
	// Local override.
	if ( defined( 'SZ_COVER_IMAGE_DEFAULT_THUMB' ) ) {
		$cover_image = SZ_COVER_IMAGE_DEFAULT_THUMB;

	// Use the local default image.
	} elseif ( 'local' === $type ) {
		$cover_image = sportszone()->plugin_url . 'sz-core/images/mystery-man-50.jpg';

	}

	/**
	 * Filters the URL of the 'thumb' default avatar.
	 *
	 * @since 1.5.0
	 * @since 2.6.0 Added `$params`.
	 *
	 * @param string $cover_image URL of the default avatar.
	 * @param string $params Params provided to sz_core_fetch_cover_image().
	 */
	return apply_filters( 'sz_core_cover_image_thumb', $cover_image, $params );
}

/**
 * Reset the week parameter of the WordPress main query if needed.
 *
 * When cropping an cover_image, a $_POST['w'] var is sent, setting the 'week'
 * parameter of the WordPress main query to this posted var. To avoid
 * notices, we need to make sure this 'week' query var is reset to 0.
 *
 * @since 2.2.0
 *
 * @param WP_Query|null $posts_query The main query object.
 */
function sz_core_cover_image_reset_query( $posts_query = null ) {
	$reset_w = false;

	// Group's cover_image edit screen.
	if ( sz_is_group_admin_page() ) {
		$reset_w = sz_is_group_admin_screen( 'group-cover-image' );

	// Group's cover_image create screen.
	} elseif ( sz_is_group_create() ) {
		/**
		 * We can't use sz_get_groups_current_create_step().
		 * as it's not set yet
		 */
		$reset_w = 'group-cover-image' === sz_action_variable( 1 );

	// User's change cover-image screen.
	} else {
		$reset_w = sz_is_user_change_cover_image();
	}

	// A user or a group is cropping an cover_image.
	if ( true === $reset_w && isset( $_POST['cover-image-crop-submit'] ) ) {
		$posts_query->set( 'w', 0 );
	}
}
add_action( 'sz_parse_query', 'sz_core_cover_image_reset_query', 10, 1 );

/**
 * Checks whether Avatar UI should be loaded.
 *
 * @since 2.3.0
 *
 * @return bool True if Avatar UI should load, false otherwise.
 */
function sz_cover_image_is_front_edit() {
	$retval = false;

	// No need to carry on if the current WordPress version is not supported.
	if ( ! sz_attachments_is_wp_version_supported() ) {
		return $retval;
	}

	if ( sz_is_user_change_cover_image() && 'crop-image' !== sz_get_cover_image_admin_step() ) {
		$retval = ! sz_core_get_root_option( 'sz-disable-cover-image-uploads' );
	}

	if ( sz_is_active( 'groups' ) ) {
		// Group creation.
		if ( sz_is_group_create() && sz_is_group_creation_step( 'group-cover-image' ) && 'crop-image' !== sz_get_cover_image_admin_step() ) {
			$retval = ! sz_disable_group_cover_image_uploads();

		// Group Manage.
		} elseif ( sz_is_group_admin_page() && sz_is_group_admin_screen( 'group-cover-image' ) && 'crop-image' !== sz_get_cover_image_admin_step() ) {
			$retval = ! sz_disable_group_cover_image_uploads();
		}
	}

	/**
	 * Use this filter if you need to :
	 * - Load the avatar UI for a component that is !groups or !user (return true regarding your conditions)
	 * - Completely disable the avatar UI introduced in 2.3 (eg: __return_false())
	 *
	 * @since 2.3.0
	 *
	 * @param bool $retval Whether or not to load the Avatar UI.
	 */
	return apply_filters( 'sz_cover_image_is_front_edit', $retval );
}

/**
 * Checks whether the Webcam Avatar UI part should be loaded.
 *
 * @since 2.3.0
 *
 * @global $is_safari
 * @global $is_IE
 *
 * @return bool True to load the Webcam Avatar UI part. False otherwise.
 */
function sz_cover_image_use_webcam() {
	global $is_safari, $is_IE, $is_chrome;

	/**
	 * Do not use the webcam feature for mobile devices
	 * to avoid possible confusions.
	 */
	if ( wp_is_mobile() ) {
		return false;
	}

	/**
	 * Bail when the browser does not support getUserMedia.
	 *
	 * @see http://caniuse.com/#feat=stream
	 */
	if ( $is_safari || $is_IE || ( $is_chrome && ! is_ssl() ) ) {
		return false;
	}

	/**
	 * Use this filter if you need to disable the webcam capture feature
	 * by returning false.
	 *
	 * @since 2.3.0
	 *
	 * @param bool $value Whether or not to load Webcam Avatar UI part.
	 */
	return apply_filters( 'sz_cover_image_use_webcam', true );
}

/**
 * Template function to load the Avatar UI javascript templates.
 *
 * @since 2.3.0
 */
function sz_cover_image_get_templates() {
	if ( ! sz_cover_image_is_front_edit() ) {
		return;
	}
	
	sz_attachments_get_template_part( 'cover-images/index' );
}

/**
 * Trick to check if the theme's SportsZone templates are up to date.
 *
 * If the "cover_image templates" are not including the new template tag, this will
 * help users to get the cover_image UI.
 *
 * @since 2.3.0
 */
function sz_cover_image_template_check() {
	if ( ! sz_cover_image_is_front_edit() ) {
		return;
	}

	if ( ! did_action( 'sz_attachments_cover_image_check_template' ) ) {
		sz_attachments_get_template_part( 'cover-images/index' );
	}
}
