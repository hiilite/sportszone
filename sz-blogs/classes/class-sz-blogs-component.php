<?php
/**
 * SportsZone Blogs Loader
 *
 * The blogs component tracks posts and comments to member activity streams,
 * shows blogs the member can post to in their profiles, and caches useful
 * information from those blogs to make querying blogs in bulk more performant.
 *
 * @package SportsZone
 * @subpackage BlogsCore
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Creates our Blogs component.
 */
class SZ_Blogs_Component extends SZ_Component {

	/**
	 * Start the blogs component creation process.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		parent::start(
			'blogs',
			__( 'Site Directory', 'sportszone' ),
			sportszone()->plugin_dir,
			array(
				'adminbar_myaccount_order' => 30,
				'search_query_arg' => 'sites_search',
				'features' => array( 'site-icon' )
			)
		);
	}

	/**
	 * Set up global settings for the blogs component.
	 *
	 * The SZ_BLOGS_SLUG constant is deprecated, and only used here for
	 * backwards compatibility.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_globals() for description of parameters.
	 *
	 * @param array $args See {@link SZ_Component::setup_globals()}.
	 */
	public function setup_globals( $args = array() ) {
		$sz = sportszone();

		if ( ! defined( 'SZ_BLOGS_SLUG' ) ) {
			define ( 'SZ_BLOGS_SLUG', $this->id );
		}

		// Global tables for messaging component.
		$global_tables = array(
			'table_name'          => $sz->table_prefix . 'sz_user_blogs',
			'table_name_blogmeta' => $sz->table_prefix . 'sz_user_blogs_blogmeta',
		);

		$meta_tables = array(
			'blog' => $sz->table_prefix . 'sz_user_blogs_blogmeta',
		);

		// Fetch the default directory title.
		$default_directory_titles = sz_core_get_directory_page_default_titles();
		$default_directory_title  = $default_directory_titles[$this->id];

		// All globals for blogs component.
		$args = array(
			'slug'                  => SZ_BLOGS_SLUG,
			'root_slug'             => isset( $sz->pages->blogs->slug ) ? $sz->pages->blogs->slug : SZ_BLOGS_SLUG,
			'has_directory'         => is_multisite(), // Non-multisite installs don't need a top-level Sites directory, since there's only one site.
			'directory_title'       => isset( $sz->pages->blogs->title ) ? $sz->pages->blogs->title : $default_directory_title,
			'notification_callback' => 'sz_blogs_format_notifications',
			'search_string'         => __( 'Search sites...', 'sportszone' ),
			'autocomplete_all'      => defined( 'SZ_MESSAGES_AUTOCOMPLETE_ALL' ),
			'global_tables'         => $global_tables,
			'meta_tables'           => $meta_tables,
		);

		// Setup the globals.
		parent::setup_globals( $args );

		/**
		 * Filters if a blog is public.
		 *
		 * In case the config is not multisite, the blog_public option is ignored.
		 *
		 * @since 2.3.0
		 *
		 * @param int $value Whether or not the blog is public.
		 */
		if ( 0 !== apply_filters( 'sz_is_blog_public', (int) get_option( 'blog_public' ) ) || ! is_multisite() ) {

			/**
			 * Filters the post types to track for the Blogs component.
			 *
			 * @since 1.5.0
			 * @deprecated 2.3.0
			 *
			 * @param array $value Array of post types to track.
			 */
			$post_types = apply_filters( 'sz_blogs_record_post_post_types', array( 'post' ) );

			foreach ( $post_types as $post_type ) {
				add_post_type_support( $post_type, 'sportszone-activity' );
			}
		}
	}

	/**
	 * Include sz-blogs files.
	 *
	 * @see SZ_Component::includes() for description of parameters.
	 *
	 * @param array $includes See {@link SZ_Component::includes()}.
	 */
	public function includes( $includes = array() ) {

		// Files to include.
		$includes = array(
			'cache',
			'template',
			'filters',
			'functions',
		);

		if ( sz_is_active( 'activity' ) ) {
			$includes[] = 'activity';
		}

		if ( is_multisite() ) {
			$includes[] = 'widgets';
		}

		// Include the files.
		parent::includes( $includes );
	}

	/**
	 * Late includes method.
	 *
	 * Only load up certain code when on specific pages.
	 *
	 * @since 3.0.0
	 */
	public function late_includes() {
		// Bail if PHPUnit is running.
		if ( defined( 'SZ_TESTS_DIR' ) ) {
			return;
		}

		// Bail if not on a blogs page or not multisite.
		if ( ! sz_is_blogs_component() || ! is_multisite() ) {
			return;
		}

		// Actions.
		if ( isset( $_GET['random-blog'] ) ) {
			require $this->path . 'sz-blogs/actions/random.php';
		}

		// Screens.
		if ( sz_is_user() ) {
			require $this->path . 'sz-blogs/screens/my-blogs.php';
		} else {
			if ( sz_is_blogs_directory() ) {
				require $this->path . 'sz-blogs/screens/directory.php';
			}

			if ( is_user_logged_in() && sz_is_current_action( 'create' ) ) {
				require $this->path . 'sz-blogs/screens/create.php';
			}

			// Theme compatibility.
			new SZ_Blogs_Theme_Compat();
		}
	}

	/**
	 * Set up component navigation for sz-blogs.
	 *
	 * @see SZ_Component::setup_nav() for a description of arguments.
	 *
	 * @param array $main_nav Optional. See SZ_Component::setup_nav() for
	 *                        description.
	 * @param array $sub_nav  Optional. See SZ_Component::setup_nav() for
	 *                        description.
	 */
	public function setup_nav( $main_nav = array(), $sub_nav = array() ) {

		/**
		 * Blog/post/comment menus should not appear on single WordPress setups.
		 * Although comments and posts made by users will still show on their
		 * activity stream.
		 */
		if ( ! is_multisite() ) {
			return false;
		}

		// Determine user to use.
		if ( sz_displayed_user_domain() ) {
			$user_domain = sz_displayed_user_domain();
		} elseif ( sz_loggedin_user_domain() ) {
			$user_domain = sz_loggedin_user_domain();
		} else {
			return;
		}

		$slug       = sz_get_blogs_slug();
		$parent_url = trailingslashit( $user_domain . $slug );

		// Add 'Sites' to the main navigation.
		$count    = (int) sz_get_total_blog_count_for_user();
		$class    = ( 0 === $count ) ? 'no-count' : 'count';
		$nav_text = sprintf(
			/* translators: %s: Site count for the current user */
			__( 'Sites %s', 'sportszone' ),
			sprintf(
				'<span class="%s">%s</span>',
				esc_attr( $class ),
				sz_core_number_format( $count )
			)
		);
		$main_nav = array(
			'name'                => $nav_text,
			'slug'                => $slug,
			'position'            => 30,
			'screen_function'     => 'sz_blogs_screen_my_blogs',
			'default_subnav_slug' => 'my-sites',
			'item_css_id'         => $this->id
		);

		$sub_nav[] = array(
			'name'            => __( 'My Sites', 'sportszone' ),
			'slug'            => 'my-sites',
			'parent_url'      => $parent_url,
			'parent_slug'     => $slug,
			'screen_function' => 'sz_blogs_screen_my_blogs',
			'position'        => 10
		);

		// Setup navigation.
		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up sz-blogs integration with the WordPress admin bar.
	 *
	 * @since 1.5.0
	 *
	 * @see SZ_Component::setup_admin_bar() for a description of arguments.
	 *
	 * @param array $wp_admin_nav See SZ_Component::setup_admin_bar()
	 *                            for description.
	 * @return bool
	 */
	public function setup_admin_bar( $wp_admin_nav = array() ) {

		/**
		 * Site/post/comment menus should not appear on single WordPress setups.
		 *
		 * Comments and posts made by users will still show in their activity.
		 */
		if ( ! is_multisite() ) {
			return false;
		}

		// Menus for logged in user.
		if ( is_user_logged_in() ) {

			// Setup the logged in user variables.
			$blogs_link = trailingslashit( sz_loggedin_user_domain() . sz_get_blogs_slug() );

			// Add the "Sites" sub menu.
			$wp_admin_nav[] = array(
				'parent' => sportszone()->my_account_menu_id,
				'id'     => 'my-account-' . $this->id,
				'title'  => __( 'Sites', 'sportszone' ),
				'href'   => $blogs_link
			);

			// My Sites.
			$wp_admin_nav[] = array(
				'parent'   => 'my-account-' . $this->id,
				'id'       => 'my-account-' . $this->id . '-my-sites',
				'title'    => __( 'My Sites', 'sportszone' ),
				'href'     => $blogs_link,
				'position' => 10
			);

			// Create a Site.
			if ( sz_blog_signup_enabled() ) {
				$wp_admin_nav[] = array(
					'parent'   => 'my-account-' . $this->id,
					'id'       => 'my-account-' . $this->id . '-create',
					'title'    => __( 'Create a Site', 'sportszone' ),
					'href'     => trailingslashit( sz_get_blogs_directory_permalink() . 'create' ),
					'position' => 99
				);
			}
		}

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Set up the title for pages and <title>.
	 */
	public function setup_title() {

		// Set up the component options navigation for Site.
		if ( sz_is_blogs_component() ) {
			$sz = sportszone();

			if ( sz_is_my_profile() ) {
				if ( sz_is_active( 'xprofile' ) ) {
					$sz->sz_options_title = __( 'My Sites', 'sportszone' );
				}

			// If we are not viewing the logged in user, set up the current
			// users avatar and name.
			} else {
				$sz->sz_options_avatar = sz_core_fetch_avatar( array(
					'item_id' => sz_displayed_user_id(),
					'type'    => 'thumb',
					'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_get_displayed_user_fullname() )
				) );
				$sz->sz_options_title = sz_get_displayed_user_fullname();
			}
		}

		parent::setup_title();
	}

	/**
	 * Setup cache groups
	 *
	 * @since 2.2.0
	 */
	public function setup_cache_groups() {

		// Global groups.
		wp_cache_add_global_groups( array(
			'blog_meta'
		) );

		parent::setup_cache_groups();
	}
}
