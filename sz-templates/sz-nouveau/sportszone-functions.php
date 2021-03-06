<?php
/**
 * Functions of SportsZone's "Nouveau" template pack.
 *
 * @since 3.0.0
 * @version 3.1.0
 *
 * @sportszone-template-pack {
 *   Template Pack ID:       nouveau
 *   Template Pack Name:     BP Nouveau
 *   Version:                1.0.0
 *   WP required version:    4.5.0
 *   BP required version:    3.0.0
 *   Description:            A new template pack for SportsZone!
 *   Text Domain:            sz-nouveau
 *   Domain Path:            /languages/
 *   Author:                 The SportsZone community
 *   Template Pack Supports: activity, blogs, friends, groups, messages, notifications, settings, xprofile
 * }}
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/** Theme Setup ***************************************************************/

/**
 * Loads SportsZone Nouveau Template pack functionality.
 *
 * See @link SZ_Theme_Compat() for more.
 *
 * @since 3.0.0
 */
class SZ_Nouveau extends SZ_Theme_Compat {
	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	/**
	 * Return the instance of this class.
	 *
	 * @since 3.0.0
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * The BP Nouveau constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		parent::start();

		$this->includes();
		$this->setup_support();
		
		add_filter( 'template_include', array( $this, 'template_loader' ) );
		add_filter( 'the_content', array( $this, 'match_content' ) );
		//add_filter( 'the_content', array( $this, 'calendar_content' ) );
		//add_filter( 'the_content', array( $this, 'club_content' ) );
		//add_filter( 'the_content', array( $this, 'team_content' ) );
		//add_filter( 'the_content', array( $this, 'table_content' ) );
		//add_filter( 'the_content', array( $this, 'player_content' ) );
		//add_filter( 'the_content', array( $this, 'list_content' ) );
		//add_filter( 'the_content', array( $this, 'staff_content' ) );
	}
	
	public function add_content( $content, $type, $position = 10, $caption = null ) {
		
		if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
		if ( ! in_the_loop() ) return; // Return if not in main loop

		// Return password form if required
		if ( post_password_required() ) {
			echo get_the_password_form();
			return;
		}
		
		// Prepend caption to content if given
		if ( $content ) {
			if ( $caption ) {
				$content = '<h3 class="sz-post-caption">' . $caption . '</h3>' . $content;
			}

			$content = '<div class="sz-post-content">' . $content . '</div>';
		}
		
		// Get layout setting
		$layout = (array) get_option( 'sportszone_' . $type . '_template_order', array() );
		
		// Get templates
		$templates = sportszone()->templates->$type;
		
		
		// Combine layout setting with available templates
		$templates = array_merge( array_flip( $layout ), $templates );
		
		$templates = apply_filters( 'sportszone_' . $type . '_templates', $templates );

		// Split templates into sections and tabs
		$slice = array_search( 'tabs', array_keys( $templates ) );
		if ( $slice ) {
			$section_templates = array_slice( $templates, 0, $slice );
			$tab_templates = array_slice( $templates, $slice );
		} else {
			$section_templates = $templates;
			$tab_templates = array();
		}

		ob_start();

		// Before template hook
		do_action( 'sportszone_before_single_' . $type );
		
		
			
		
		// Loop through sections
		if ( ! empty( $section_templates ) ) {
			foreach ( $section_templates as $key => $template ) {
				// Ignore templates that are unavailable or that have been turned off
				if ( ! is_array( $template ) ) continue;
				if ( ! isset( $template['option'] ) ) continue;
				if ( 'yes' !== get_option( $template['option'], sz_array_value( $template, 'default', 'yes' ) ) ) continue;
				
				// Render the template
				echo '<div class="sz-section-content sz-section-content-' . $key . '">';
				if ( 'content' === $key ) {
					echo $content;
					// Template content hook
					do_action( 'sportszone_single_' . $type . '_content' );
				} else {
					do_action( 'sportszone_' . $type . '_' . $key . '_before' );
					call_user_func( $template['action'] );
					do_action( 'sportszone_' . $type . '_' .$key . '_after' );
				}
				echo '</div>';
			}
		}
		
	
		// After template hook
		do_action( 'sportszone_after_single_' . $type );
		
		$ob = ob_get_clean();
		
		$tabs = '';
		
		if ( ! empty( $tab_templates ) ) {
			$i = 0;
			$tab_content = '';

			foreach ( $tab_templates as $key => $template ) {
				// Ignore templates that are unavailable or that have been turned off
				if ( ! is_array( $template ) ) continue;
				if ( ! isset( $template['option'] ) ) continue;
				if ( 'yes' !== get_option( $template['option'], sz_array_value( $template, 'default', 'yes' ) ) ) continue;
				
				// Put tab content into buffer
				ob_start();
				if ( 'content' === $key ) {
					echo $content;
				} else {
					call_user_func( $template['action'] );
				}
				$buffer = ob_get_clean();

				// Trim whitespace from buffer
				$buffer = trim( $buffer );
				
				// Continue if tab content is empty
				if ( empty( $buffer ) ) continue;
				
				// Get template label
				$label = sz_array_value( $template, 'label', $template['title'] );
				
				// Add to tabs
				$tabs .= '<li class="sz-tab-menu-item' . ( 0 === $i ? ' sz-tab-menu-item-active' : '' ) . '"><a href="#sz-tab-content-' . $key . '" data-sz-tab="' . $key . '">' . apply_filters( 'gettext', $label, $label, 'sportszone' ) . '</a></li>';
				
				// Render the template
				$tab_content .= '<div class="sz-tab-content sp-tab-content-' . $key . '" id="sz-tab-content-' . $key . '"' . ( 0 === $i ? ' style="display: block;"' : '' ) . '>' . $buffer . '</div>';

				$i++;
			}
			
			$ob .= '<div class="sz-tab-group">';
		
			if ( ! empty( $tabs ) ) {
				$ob .= '<ul class="sz-tab-menu">' . $tabs . '</ul>';
			}

			$ob .= $tab_content;
			
			$ob .= '</div>';
		}
		
		return $ob;
	}
	
	public function match_content( $content ) {
		if ( is_singular( 'sz_match' ) ) {
			$status = sz_get_status( get_the_ID() );
			if ( 'results' == $status ) {
				$caption = __( 'Recap', 'sportszone' );
			} else {
				$caption = __( 'Preview', 'sportszone' );
			}
			$content = self::add_content( $content, 'match', apply_filters( 'sportszone_match_content_priority', 10 ), $caption );
		}
		return $content;
	}

	/**
	 * BP Nouveau global variables.
	 *
	 * @since 3.0.0
	 */
	protected function setup_globals() {
		$sz = sportszone();

		foreach ( $sz->theme_compat->packages['nouveau'] as $property => $value ) {
			$this->{$property} = $value;
		}

		$this->includes_dir  = trailingslashit( $this->dir ) . 'includes/';
		$this->directory_nav = new SZ_Core_Nav();
	}

	/**
	 * Includes!
	 *
	 * @since 3.0.0
	 */
	protected function includes() {
		require $this->includes_dir . 'functions.php';
		require $this->includes_dir . 'classes.php';
		require $this->includes_dir . 'template-tags.php';

		// Test suite requires the AJAX functions early.
		if ( function_exists( 'tests_add_filter' ) ) {
			require $this->includes_dir . 'ajax.php';

		// Load AJAX code only on AJAX requests.
		} else {
			add_action( 'admin_init', function() {
				if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX ) {
					require $this->includes_dir . 'ajax.php';
				}
			}, 0 );
		}

		add_action( 'sz_customize_register', function() {
			if ( sz_is_root_blog() && current_user_can( 'customize' ) ) {
				require $this->includes_dir . 'customizer.php';
			}
		}, 0 );

		foreach ( sz_core_get_packaged_component_ids() as $component ) {
			$component_loader = trailingslashit( $this->includes_dir ) . $component . '/loader.php';

			if ( ! sz_is_active( $component ) || ! file_exists( $component_loader ) ) {
				continue;
			}

			require( $component_loader );
		}

		/**
		 * Fires after all of the SportsZone Nouveau includes have been loaded. Passed by reference.
		 *
		 * @since 3.0.0
		 *
		 * @param SZ_Nouveau $value Current SZ_Nouveau instance.
		 */
		do_action_ref_array( 'sz_nouveau_includes', array( &$this ) );
	}

	/**
	 * Setup the Template Pack features support.
	 *
	 * @since 3.0.0
	 */
	protected function setup_support() {
		$width         = 1300;
		$top_offset    = 150;

		/** This filter is documented in sz-core/sz-core-avatars.php. */
		$avatar_height = apply_filters( 'sz_core_avatar_full_height', $top_offset );
		$cover_image_height = apply_filters( 'sz_core_cover_image_full_height', 315 );
		if ( $avatar_height > $top_offset ) {
			$top_offset = $avatar_height;
		}

		sz_set_theme_compat_feature( $this->id, array(
			'name'     => 'cover_image',
			'settings' => array(
				'components'   => array( 'xprofile', 'groups' ),
				'width'        => $width,
				'height'       => $cover_image_height,
				'callback'     => 'sz_nouveau_theme_cover_image',
				'theme_handle' => 'sz-nouveau',
			),
		) );
	}

	/**
	 * Setup the Template Pack common actions.
	 *
	 * @since 3.0.0
	 */
	protected function setup_actions() {
		// Filter SportsZone template hierarchy and look for page templates.
		add_filter( 'sz_get_sportszone_template', array( $this, 'theme_compat_page_templates' ), 10, 1 );

		// Add our "sportszone" div wrapper to theme compat template parts.
		add_filter( 'sz_replace_the_content', array( $this, 'theme_compat_wrapper' ), 999 );

		// We need to neutralize the SportsZone core "sz_core_render_message()" once it has been added.
		add_action( 'sz_actions', array( $this, 'neutralize_core_template_notices' ), 6 );

		// Scripts
		add_action( 'sz_enqueue_scripts', array( $this, 'register_scripts' ), 2 ); // Register theme JS
		remove_action( 'sz_enqueue_scripts', 'sz_core_confirmation_js' );
		add_action( 'sz_enqueue_scripts', array( $this, 'enqueue_styles' ) ); // Enqueue theme CSS
		add_action( 'sz_enqueue_scripts', array( $this, 'enqueue_scripts' ) ); // Enqueue theme JS
		add_filter( 'sz_enqueue_scripts', array( $this, 'localize_scripts' ) ); // Enqueue theme script localization

		// Body no-js class
		add_filter( 'body_class', array( $this, 'add_nojs_body_class' ), 20, 1 );

		// Ajax querystring
		add_filter( 'sz_ajax_querystring', 'sz_nouveau_ajax_querystring', 10, 2 );

		// Register directory nav items
		add_action( 'sz_screens', array( $this, 'setup_directory_nav' ), 15 );

		// Register the Default front pages Dynamic Sidebars
		add_action( 'widgets_init', 'sz_nouveau_register_sidebars', 11 );

		// Register the Primary Object nav widget
		add_action( 'sz_widgets_init', array( 'SZ_Nouveau_Object_Nav_Widget', 'register_widget' ) );

		// Set the BP Uri for the Ajax customizer preview
		add_filter( 'sz_uri', array( $this, 'customizer_set_uri' ), 10, 1 );

		/** Override **********************************************************/

		/**
		 * Fires after all of the SportsZone theme compat actions have been added.
		 *
		 * @since 3.0.0
		 *
		 * @param SZ_Nouveau $this Current SZ_Nouveau instance.
		 */
		do_action_ref_array( 'sz_theme_compat_actions', array( &$this ) );

	}
	

	/**
	 * Enqueue the template pack css files
	 *
	 * @since 3.0.0
	 */
	public function enqueue_styles() {
		$min = sz_core_get_minified_asset_suffix();
		$rtl = '';

		if ( is_rtl() ) {
			$rtl = '-rtl';
		}

		/**
		 * Filters the SportsZone Nouveau CSS dependencies.
		 *
		 * @since 3.0.0
		 *
		 * @param array $value Array of style dependencies. Default Dashicons.
		 */
		$css_dependencies = apply_filters( 'sz_nouveau_css_dependencies', array( 'dashicons', 'bootstrap' ) );

		/**
		 * Filters the styles to enqueue for SportsZone Nouveau.
		 *
		 * This filter provides a multidimensional array that will map to arguments used for wp_enqueue_style().
		 * The primary index should have the stylesheet handle to use, and be assigned an array that has indexes for
		 * file location, dependencies, and version.
		 *
		 * @since 3.0.0
		 *
		 * @param array $value Array of styles to enqueue.
		 */
		$styles = apply_filters( 'sz_nouveau_enqueue_styles', array(
			'sz-nouveau' => array(
				'file' => 'css/sportszone%1$s%2$s.css', 'dependencies' => $css_dependencies, 'version' => $this->version,
			),
		) );

		if ( $styles ) {

			foreach ( $styles as $handle => $style ) {
				if ( ! isset( $style['file'] ) ) {
					continue;
				}

				$file = sprintf( $style['file'], $rtl, $min );
				
				//Remove .min
				$file = str_replace( '.min', '', $file );

				// Locate the asset if needed.
				if ( false === strpos( $style['file'], '://' ) ) {
					$asset = sz_locate_template_asset( $file );

					if ( empty( $asset['uri'] ) || false === strpos( $asset['uri'], '://' ) ) {
						continue;
					}

					$file = $asset['uri'];
				}

				$data = wp_parse_args( $style, array(
					'dependencies' => array(),
					'version'      => $this->version,
					'type'         => 'screen',
				) );

				wp_enqueue_style( $handle, $file, $data['dependencies'], $data['version'], $data['type'] );

				if ( $min ) {
					wp_style_add_data( $handle, 'suffix', $min );
				}
			}
		}
	}

	/**
	 * Register Template Pack JavaScript files
	 *
	 * @since 3.0.0
	 */
	public function register_scripts() {
		$min          = sz_core_get_minified_asset_suffix();
		$dependencies = sz_core_get_js_dependencies();
		$sz_confirm   = array_search( 'sz-confirm', $dependencies );

		unset( $dependencies[ $sz_confirm ] );

		/**
		 * Filters the scripts to enqueue for SportsZone Nouveau.
		 *
		 * This filter provides a multidimensional array that will map to arguments used for wp_register_script().
		 * The primary index should have the script handle to use, and be assigned an array that has indexes for
		 * file location, dependencies, version and if it should load in the footer or not.
		 *
		 * @since 3.0.0
		 *
		 * @param array $value Array of scripts to register.
		 */
		$scripts = apply_filters( 'sz_nouveau_register_scripts', array(
			'sz-nouveau' => array(
				'file'         => 'js/sportszone-nouveau.js',
				'dependencies' => $dependencies,
				'version'      => $this->version,
				'footer'       => true,
			),
		) );

		// Bail if no scripts
		if ( empty( $scripts ) ) {
			return;
		}

		// Add The password verify if needed.
		if ( sz_is_active( 'settings' ) || sz_get_signup_allowed() ) {
			$scripts['sz-nouveau-password-verify'] = array(
				'file'         => 'js/password-verify%s.js',
				'dependencies' => array( 'sz-nouveau', 'password-strength-meter' ),
				'footer'       => true,
			);
		}

		foreach ( $scripts as $handle => $script ) {
			if ( ! isset( $script['file'] ) ) {
				continue;
			}

			$file = sprintf( $script['file'], $min );

			// Locate the asset if needed.
			if ( false === strpos( $script['file'], '://' ) ) {
				$asset = sz_locate_template_asset( $file );

				if ( empty( $asset['uri'] ) || false === strpos( $asset['uri'], '://' ) ) {
					continue;
				}

				$file = $asset['uri'];
			}

			$data = wp_parse_args( $script, array(
				'dependencies' => array(),
				'version'      => $this->version,
				'footer'       => false,
			) );

			wp_register_script( $handle, $file, $data['dependencies'], $data['version'], $data['footer'] );
		}
	}

	/**
	 * Enqueue the required JavaScript files
	 *
	 * @since 3.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'sz-nouveau' );

		if ( sz_is_register_page() || sz_is_user_settings_general() ) {
			wp_enqueue_script( 'sz-nouveau-password-verify' );
		}

		if ( is_singular() && sz_is_blog_page() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		/**
		 * Fires after all of the SportsZone Nouveau scripts have been enqueued.
		 *
		 * @since 3.0.0
		 */
		do_action( 'sz_nouveau_enqueue_scripts' );
	}

	/**
	 * Adds the no-js class to the body tag.
	 *
	 * This function ensures that the <body> element will have the 'no-js' class by default. If you're
	 * using JavaScript for some visual functionality in your theme, and you want to provide noscript
	 * support, apply those styles to body.no-js.
	 *
	 * The no-js class is removed by the JavaScript created in sportszone.js.
	 *
	 * @since 3.0.0
	 *
	 * @param array $classes Array of classes to append to body tag.
	 *
	 * @return array $classes
	 */
	public function add_nojs_body_class( $classes ) {
		$classes[] = 'no-js';
		return array_unique( $classes );
	}

	/**
	 * Load localizations for topic script.
	 *
	 * These localizations require information that may not be loaded even by init.
	 *
	 * @since 3.0.0
	 */
	public function localize_scripts() {
		$params = array(
			'ajaxurl'             => sz_core_ajax_url(),
			'accepted'            => __( 'Accepted', 'sportszone' ),
			'close'               => __( 'Close', 'sportszone' ),
			'comments'            => __( 'comments', 'sportszone' ),
			'leave_group_confirm' => __( 'Are you sure you want to leave this group?', 'sportszone' ),
			'confirm'             => __( 'Are you sure?', 'sportszone' ),
			'my_favs'             => __( 'My Favorites', 'sportszone' ),
			'rejected'            => __( 'Rejected', 'sportszone' ),
			'show_all'            => __( 'Show all', 'sportszone' ),
			'show_all_comments'   => __( 'Show all comments for this thread', 'sportszone' ),
			'show_x_comments'     => __( 'Show all %d comments', 'sportszone' ),
			'unsaved_changes'     => __( 'Your profile has unsaved changes. If you leave the page, the changes will be lost.', 'sportszone' ),
			'view'                => __( 'View', 'sportszone' ),
			'object_nav_parent'   => '#sportszone',
		);

		// If the Object/Item nav are in the sidebar
		if ( sz_nouveau_is_object_nav_in_sidebar() ) {
			$params['object_nav_parent'] = '.sportszone_object_nav';
		}

		/**
		 * Filters the supported SportsZone Nouveau components.
		 *
		 * @since 3.0.0
		 *
		 * @param array $value Array of supported components.
		 */
		$supported_objects = (array) apply_filters( 'sz_nouveau_supported_components', sz_core_get_packaged_component_ids() );
		$object_nonces     = array();

		foreach ( $supported_objects as $key_object => $object ) {
			if ( ! sz_is_active( $object ) || 'forums' === $object ) {
				unset( $supported_objects[ $key_object ] );
				continue;
			}

			if ( 'groups' === $object ) {
				$supported_objects = array_merge( $supported_objects, array( 'group_members', 'group_requests' ) );
			} elseif ( 'events' === $object ) {
				$supported_objects = array_merge( $supported_objects, array( 'event_members', 'event_requests' ) );
			}

			$object_nonces[ $object ] = wp_create_nonce( 'sz_nouveau_' . $object );
		}

		// Add components & nonces
		$params['objects'] = $supported_objects;
		$params['nonces']  = $object_nonces;

		// Used to transport the settings inside the Ajax requests
		if ( is_customize_preview() ) {
			$params['customizer_settings'] = sz_nouveau_get_temporary_setting( 'any' );
		}

		/**
		 * Filters core JavaScript strings for internationalization before AJAX usage.
		 *
		 * @since 3.0.0
		 *
		 * @param array $params Array of key/value pairs for AJAX usage.
		 */
		wp_localize_script( 'sz-nouveau', 'SZ_Nouveau', apply_filters( 'sz_core_get_js_strings', $params ) );
	}

	/**
	 * Filter the default theme compatibility root template hierarchy, and prepend
	 * a page template to the front if it's set.
	 *
	 * @see https://sportszone.trac.wordpress.org/ticket/6065
	 *
	 * @since 3.0.0
	 *
	 * @param array $templates Array of templates.
	 *
	 * @return array
	 */
	public function theme_compat_page_templates( $templates = array() ) {
		/**
		 * Filters whether or not we are looking at a directory to determine if to return early.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $value Whether or not we are viewing a directory.
		 */
		if ( true === (bool) apply_filters( 'sz_nouveau_theme_compat_page_templates_directory_only', ! sz_is_directory() ) ) {
			return $templates;
		}

		// No page ID yet.
		$page_id = 0;

		// Get the WordPress Page ID for the current view.
		foreach ( (array) sportszone()->pages as $component => $sz_page ) {

			// Handles the majority of components.
			if ( sz_is_current_component( $component ) ) {
				$page_id = (int) $sz_page->id;
			}

			// Stop if not on a user page.
			if ( ! sz_is_user() && ! empty( $page_id ) ) {
				break;
			}

			// The Members component requires an explicit check due to overlapping components.
			if ( sz_is_user() && ( 'members' === $component ) ) {
				$page_id = (int) $sz_page->id;
				break;
			}
		}

		// Bail if no directory page set.
		if ( 0 === $page_id ) {
			return $templates;
		}

		// Check for page template.
		$page_template = get_page_template_slug( $page_id );

		// Add it to the beginning of the templates array so it takes precedence over the default hierarchy.
		if ( ! empty( $page_template ) ) {

			/**
			 * Check for existence of template before adding it to template
			 * stack to avoid accidentally including an unintended file.
			 *
			 * @see https://sportszone.trac.wordpress.org/ticket/6190
			 */
			if ( '' !== locate_template( $page_template ) ) {
				array_unshift( $templates, $page_template );
			}
		}

		return $templates;
	}

	/**
	 * Add our special 'sportszone' div wrapper to the theme compat template part.
	 *
	 * @since 3.0.0
	 *
	 * @see sz_buffer_template_part()
	 *
	 * @param string $retval Current template part contents.
	 *
	 * @return string
	 */
	public function theme_compat_wrapper( $retval ) {
		if ( false !== strpos( $retval, '<div id="sportszone"' ) ) {
			return $retval;
		}

		// Add our 'sportszone' div wrapper.
		return sprintf(
			'<div id="sportszone" class="%1$s">%2$s</div><!-- #sportszone -->%3$s',
			esc_attr( sz_nouveau_get_container_classes() ),
			$retval,  // Constructed HTML.
			"\n"
		);
	}

	/**
	 * Define the directory nav items
	 *
	 * @since 3.0.0
	 */
	public function setup_directory_nav() {
		$nav_items = array();

		if ( sz_is_members_directory() ) {
			$nav_items = sz_nouveau_get_members_directory_nav_items();
		} elseif ( sz_is_activity_directory() ) {
			$nav_items = sz_nouveau_get_activity_directory_nav_items();
		} elseif ( sz_is_groups_directory() ) {
			$nav_items = sz_nouveau_get_groups_directory_nav_items();
		} elseif ( sz_is_events_directory() ) {
			$nav_items = sz_nouveau_get_events_directory_nav_items();
		} elseif ( sz_is_blogs_directory() ) {
			$nav_items = sz_nouveau_get_blogs_directory_nav_items();
		}

		if ( empty( $nav_items ) ) {
			return;
		}

		foreach ( $nav_items as $nav_item ) {
			if ( empty( $nav_item['component'] ) || $nav_item['component'] !== sz_current_component() ) {
				continue;
			}

			// Define the primary nav for the current component's directory
			$this->directory_nav->add_nav( $nav_item );
		}
	}

	/**
	 * We'll handle template notices from BP Nouveau.
	 *
	 * @since 3.0.0
	 */
	public function neutralize_core_template_notices() {
		remove_action( 'template_notices', 'sz_core_render_message' );
	}

	/**
	 * Set the BP Uri for the customizer in case of Ajax requests.
	 *
	 * @since 3.0.0
	 *
	 * @param  string $path the BP Uri.
	 * @return string       the BP Uri.
	 */
	public function customizer_set_uri( $path ) {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			return $path;
		}

		$uri = parse_url( $path );

		if ( false === strpos( $uri['path'], 'customize.php' ) ) {
			return $path;
		} else {
			$vars = wp_parse_args( $uri['query'], array() );

			if ( ! empty( $vars['url'] ) ) {
				$path = str_replace( get_site_url(), '', urldecode( $vars['url'] ) );
			}
		}

		return $path;
	}
	
	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. sportszone looks for theme
	 * overrides in /theme/sportszone/ by default
	 *
	 * For beginners, it also looks for a sportszone.php template last. If the user adds
	 * this to the theme (containing a sportszone() inside) this will be used as a
	 * fallback for all sportszone templates.
	 *
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader( $template ) {
		$find = array();
		$file = '';

		if ( is_single() ):

			$post_type = get_post_type();
		
			if ( is_sz_post_type( $post_type ) ):
				$file = 'single-' . str_replace( 'sz_', '', $post_type ) . '.php';
				$find[] = $file;
				$find[] = SZ_TEMPLATE_PATH . $file;
			endif;

		elseif ( is_tax() ):

			$term = get_queried_object();

			switch( $term->taxonomy ):
				case 'sz_venue':
				$file = 'taxonomy-venue.php';
				$find[] 	= 'taxonomy-venue-' . $term->slug . '.php';
				$find[] 	= SZ_TEMPLATE_PATH . 'taxonomy-venue-' . $term->slug . '.php';
				$find[] 	= $file;
				$find[] 	= SZ_TEMPLATE_PATH . $file;
			endswitch;

		endif;

		$find[] = 'sportszone.php';

		if ( $file ):
			$located       = locate_template( $find );
			if ( $located ):
				$template = $located;
			endif;
		endif;

		return $template;
	}
}

/**
 * Get a unique instance of BP Nouveau
 *
 * @since 3.0.0
 *
 * @return SZ_Nouveau the main instance of the class
 */
function sz_nouveau() {
	return SZ_Nouveau::get_instance();
}

/**
 * Launch BP Nouveau!
 */
sz_nouveau();
