<?php
/**
 * Events classes
 *
 * @since 3.0.0
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Query to get members that are not already members of the event
 *
 * @since 3.0.0
 */
class SZ_Nouveau_Event_Invite_Query extends SZ_User_Query {
	/**
	 * Array of event member ids, cached to prevent redundant lookups
	 *
	 * @var null|array Null if not yet defined, otherwise an array of ints
	 * @since 3.0.0
	 */
	protected $event_member_ids;

	/**
	 * Set up action hooks
	 *
	 * @since 3.0.0
	 */
	public function setup_hooks() {
		add_action( 'sz_pre_user_query_construct', array( $this, 'build_exclude_args' ) );
		add_action( 'sz_pre_user_query', array( $this, 'build_meta_query' ) );
	}

	/**
	 * Exclude event members from the user query as it's not needed to invite members to join the event.
	 *
	 * @since 3.0.0
	 */
	public function build_exclude_args() {
		$this->query_vars = wp_parse_args( $this->query_vars, array(
			'event_id'     => 0,
			'is_confirmed' => true,
		) );

		$event_member_ids = $this->get_event_member_ids();

		// We want to get users that are already members of the event
		$type = 'exclude';

		// We want to get invited users who did not confirmed yet
		if ( false === $this->query_vars['is_confirmed'] ) {
			$type = 'include';
		}

		if ( ! empty( $event_member_ids ) ) {
			$this->query_vars[ $type ] = $event_member_ids;
		}
	}

	/**
	 * Get the members of the queried event
	 *
	 * @since 3.0.0
	 *
	 * @return array $ids User IDs of relevant event member ids
	 */
	protected function get_event_member_ids() {
		global $wpdb;

		if ( is_array( $this->event_member_ids ) ) {
			return $this->event_member_ids;
		}

		$sz  = sportszone();
		$sql = array(
			'select'  => "SELECT user_id FROM {$sz->events->table_name_members}",
			'where'   => array(),
			'orderby' => '',
			'order'   => '',
			'limit'   => '',
		);

		/** WHERE clauses *****************************************************/

		// Event id
		$sql['where'][] = $wpdb->prepare( 'event_id = %d', $this->query_vars['event_id'] );

		if ( false === $this->query_vars['is_confirmed'] ) {
			$sql['where'][] = $wpdb->prepare( 'is_confirmed = %d', (int) $this->query_vars['is_confirmed'] );
			$sql['where'][] = 'inviter_id != 0';
		}

		// Join the query part
		$sql['where'] = ! empty( $sql['where'] ) ? 'WHERE ' . implode( ' AND ', $sql['where'] ) : '';

		/** ORDER BY clause ***************************************************/
		$sql['orderby'] = 'ORDER BY date_modified';
		$sql['order']   = 'DESC';

		/** LIMIT clause ******************************************************/
		$this->event_member_ids = $wpdb->get_col( "{$sql['select']} {$sql['where']} {$sql['orderby']} {$sql['order']} {$sql['limit']}" );

		return $this->event_member_ids;
	}

	/**
	 * @since 3.0.0
	 */
	public function build_meta_query( SZ_User_Query $sz_user_query ) {
		if ( isset( $this->query_vars['scope'] ) && 'members' === $this->query_vars['scope'] && isset( $this->query_vars['meta_query'] ) ) {

			$invites_meta_query = new WP_Meta_Query( $this->query_vars['meta_query'] );
			$meta_sql           = $invites_meta_query->get_sql( 'user', 'u', 'ID' );

			if ( empty( $meta_sql['join'] ) || empty( $meta_sql['where'] ) ) {
				return;
			}

			$sz_user_query->uid_clauses['select'] .= ' ' . $meta_sql['join'];
			$sz_user_query->uid_clauses['where']  .= ' ' . $meta_sql['where'];
		}
	}

	/**
	 * @since 3.0.0
	 */
	public static function get_inviter_ids( $user_id = 0, $event_id = 0 ) {
		global $wpdb;

		if ( empty( $event_id ) || empty( $user_id ) ) {
			return array();
		}

		$sz = sportszone();

		return $wpdb->get_col( $wpdb->prepare( "SELECT inviter_id FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d", $user_id, $event_id ) );
	}
}

/**
 * A specific Event Nav class to make it possible to set new positions for
 * sportszone()->events->nav.
 *
 * @since 3.0.0
 */
class SZ_Nouveau_Customizer_Event_Nav extends SZ_Core_Nav {
	/**
	 * Constructor
	 *
	 * @param int $object_id Optional. The random event ID used to generate the nav.
	 */
	public function __construct( $object_id = 0 ) {
		$error = new WP_Error( 'missing_parameter' );

		if ( empty( $object_id ) || ! sz_current_user_can( 'sz_moderate' ) || ! did_action( 'admin_init' ) ) {
			return $error;
		}

		$event = events_get_event( array( 'event_id' => $object_id ) );
		if ( empty( $event->id ) ) {
			return $error;
		}

		$this->event = $event;

		parent::__construct( $event->id );
		$this->setup_nav();
	}

	/**
	 * Checks whether a property is set.
	 *
	 * Overrides SZ_Core_Nav::__isset() to avoid looking into its nav property.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key The property.
	 *
	 * @return bool True if the property is set, false otherwise.
	 */
	public function __isset( $key ) {
		return isset( $this->{$key} );
	}

	/**
	 * Gets a property.
	 *
	 * Overrides SZ_Core_Nav::__isset() to avoid looking into its nav property.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key The property.
	 *
	 * @return mixed The value corresponding to the property.
	 */
	public function __get( $key ) {
		if ( ! isset( $this->{$key} ) ) {
			$this->{$key} = null;
		}

		return $this->{$key};
	}

	/**
	 * Sets a property.
	 *
	 * Overrides SZ_Core_Nav::__isset() to avoid adding a value to its nav property.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key The property.
	 *
	 * @param mixed $value The value of the property.
	 */
	public function __set( $key, $value ) {
		$this->{$key} = $value;
	}

	/**
	 * Setup a temporary nav with only the needed parameters.
	 *
	 * @since 3.0.0
	 */
	protected function setup_nav() {
		$nav_items = array(
			'root'    => array(
				'name'                => __( 'Memberships', 'sportszone' ),
				'slug'                => $this->event->slug,
				'position'            => -1,
				/** This filter is documented in sz-events/classes/class-sz-events-component.php. */
				'default_subnav_slug' => apply_filters( 'sz_events_default_extension', defined( 'SZ_EVENTS_DEFAULT_EXTENSION' ) ? SZ_EVENTS_DEFAULT_EXTENSION : 'home' ),
			),
			'home'    => array(
				'name'        => _x( 'Home', 'Event screen navigation title', 'sportszone' ),
				'slug'        => 'home',
				'parent_slug' => $this->event->slug,
				'position'    => 10,
			),
			'invites' => array(
				'name'        => _x( 'Invite', 'My Event screen nav', 'sportszone' ),
				'slug'        => 'send-invites',
				'parent_slug' => $this->event->slug,
				'position'    => 70,
			),
			'manage'  => array(
				'name'        => _x( 'Manage', 'My Event screen nav', 'sportszone' ),
				'slug'        => 'admin',
				'parent_slug' => $this->event->slug,
				'position'    => 1000,
			),
		);

		// Make sure only global front.php will be checked.
		add_filter( '_sz_nouveau_event_reset_front_template', array( $this, 'all_events_fronts' ), 10, 1 );

		$front_template = sz_events_get_front_template( $this->event );

		remove_filter( '_sz_nouveau_event_reset_front_template', array( $this, 'all_events_fronts' ), 10, 1 );

		if ( ! $front_template ) {
			if ( sz_is_active( 'activity' ) ) {
				$nav_items['home']['name'] = _x( 'Home (Activity)', 'Event screen navigation title', 'sportszone' );
			} else {
				$nav_items['home']['name'] = _x( 'Home (Members)', 'Event screen navigation title', 'sportszone' );
			}
		} else {
			if ( sz_is_active( 'activity' ) ) {
				$nav_items['activity'] = array(
					'name'        => _x( 'Activity', 'My Event screen nav', 'sportszone' ),
					'slug'        => 'activity',
					'parent_slug' => $this->event->slug,
					'position'    => 11,
				);
			}

			// Add the members one
			$nav_items['members'] = array(
				'name'        => _x( 'Members', 'My Event screen nav', 'sportszone' ),
				'slug'        => 'members',
				'parent_slug' => $this->event->slug,
				'position'    => 60,
			);
		}

		// Required params
		$required_params = array(
			'slug'              => true,
			'name'              => true,
			'nav_item_position' => true,
		);

		// Now find nav items plugins are creating within their Event extensions!
		foreach ( get_declared_classes() as $class ) {
			if ( is_subclass_of( $class, 'SZ_Event_Extension' ) ) {
				$extension = new $class;

				if ( ! empty( $extension->params ) && ! array_diff_key( $required_params, $extension->params ) ) {
					$nav_items[ $extension->params['slug'] ] = array(
						'name'        => $extension->params['name'],
						'slug'        => $extension->params['slug'],
						'parent_slug' => $this->event->slug,
						'position'    => $extension->params['nav_item_position'],
					);
				}
			}
		}

		// Now we got all, create the temporary nav.
		foreach ( $nav_items as $nav_item ) {
			$this->add_nav( $nav_item );
		}
	}

	/**
	 * Front template: do not look into event's template hierarchy.
	 *
	 * @since 3.0.0
	 *
	 * @param array $templates The list of possible event front templates.
	 *
	 * @return array The list of "global" event front templates.
	 */
	public function all_events_fronts( $templates = array() ) {
		return array_intersect( array(
			'events/single/front.php',
			'events/single/default-front.php',
		), $templates );
	}

	/**
	 * Get the original order for the event navigation.
	 *
	 * @since 3.0.0
	 *
	 * @return array a list of nav items slugs ordered.
	 */
	public function get_default_value() {
		$default_nav = $this->get_secondary( array( 'parent_slug' => $this->event->slug ) );
		return wp_list_pluck( $default_nav, 'slug' );
	}

	/**
	 * Get the list of nav items ordered according to the Site owner preferences.
	 *
	 * @since 3.0.0
	 *
	 * @return array the nav items ordered.
	 */
	public function get_event_nav() {
		// Eventually reset the order
		sz_nouveau_set_nav_item_order( $this, sz_nouveau_get_appearance_settings( 'event_nav_order' ), $this->event->slug );

		return $this->get_secondary( array( 'parent_slug' => $this->event->slug ) );
	}
}
