<?php
/**
 * SportsZone Events Classes.
 *
 * @package SportsZone
 * @subpackage EventsClasses
 * @since 1.8.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Query for the members of a event.
 *
 * Special notes about the event members data schema:
 * - *Members* are entries with is_confirmed = 1.
 * - *Pending requests* are entries with is_confirmed = 0 and inviter_id = 0.
 * - *Pending and sent invitations* are entries with is_confirmed = 0 and
 *   inviter_id != 0 and invite_sent = 1.
 * - *Pending and unsent invitations* are entries with is_confirmed = 0 and
 *   inviter_id != 0 and invite_sent = 0.
 * - *Membership requests* are entries with is_confirmed = 0 and
 *   inviter_id = 0 (and invite_sent = 0).
 *
 * @since 1.8.0
 * @since 3.0.0 $event_id now supports multiple values.
 *
 * @param array $args  {
 *     Array of arguments. Accepts all arguments from
 *     {@link SZ_User_Query}, with the following additions:
 *
 *     @type int|array|string $event_id     ID of the event to limit results to. Also accepts multiple values
 *                                          either as an array or as a comma-delimited string.
 *     @type array            $event_role   Array of event roles to match ('member', 'mod', 'admin', 'banned').
 *                                          Default: array( 'member' ).
 *     @type bool             $is_confirmed Whether to limit to confirmed members. Default: true.
 *     @type string           $type         Sort order. Accepts any value supported by {@link SZ_User_Query}, in
 *                                          addition to 'last_joined' and 'first_joined'. Default: 'last_joined'.
 * }
 */
class SZ_Event_Member_Query extends SZ_User_Query {

	/**
	 * Array of event member ids, cached to prevent redundant lookups.
	 *
	 * @since 1.8.1
	 * @var null|array Null if not yet defined, otherwise an array of ints.
	 */
	protected $event_member_ids;

	/**
	 * Set up action hooks.
	 *
	 * @since 1.8.0
	 */
	public function setup_hooks() {
		// Take this early opportunity to set the default 'type' param
		// to 'last_joined', which will ensure that SZ_User_Query
		// trusts our order and does not try to apply its own.
		if ( empty( $this->query_vars_raw['type'] ) ) {
			$this->query_vars_raw['type'] = 'last_joined';
		}

		// Set the sort order.
		add_action( 'sz_pre_user_query', array( $this, 'set_orderby' ) );

		// Set up our populate_extras method.
		add_action( 'sz_user_query_populate_extras', array( $this, 'populate_event_member_extras' ), 10, 2 );
	}

	/**
	 * Get a list of user_ids to include in the IN clause of the main query.
	 *
	 * Overrides SZ_User_Query::get_include_ids(), adding our additional
	 * event-member logic.
	 *
	 * @since 1.8.0
	 *
	 * @param array $include Existing event IDs in the $include parameter,
	 *                       as calculated in SZ_User_Query.
	 * @return array
	 */
	public function get_include_ids( $include = array() ) {
		// The following args are specific to event member queries, and
		// are not present in the query_vars of a normal SZ_User_Query.
		// We loop through to make sure that defaults are set (though
		// values passed to the constructor will, as usual, override
		// these defaults).
		$this->query_vars = sz_parse_args( $this->query_vars, array(
			'event_id'     => 0,
			'event_role'   => array( 'member' ),
			'is_confirmed' => true,
			'invite_sent'  => null,
			'inviter_id'   => null,
			'type'         => 'last_joined',
		), 'sz_event_member_query_get_include_ids' );

		$event_member_ids = $this->get_event_member_ids();

		// If the event member query returned no users, bail with an
		// array that will guarantee no matches for SZ_User_Query.
		if ( empty( $event_member_ids ) ) {
			return array( 0 );
		}

		if ( ! empty( $include ) ) {
			$event_member_ids = array_intersect( $include, $event_member_ids );
		}

		return $event_member_ids;
	}

	/**
	 * Get the members of the queried event.
	 *
	 * @since 1.8.0
	 *
	 * @return array $ids User IDs of relevant event member ids.
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
		);

		/* WHERE clauses *****************************************************/

		// Event id.
		$event_ids = wp_parse_id_list( $this->query_vars['event_id'] );
		$event_ids = implode( ',', $event_ids );
		$sql['where'][] = "event_id IN ({$event_ids})";

		// If is_confirmed.
		$is_confirmed = ! empty( $this->query_vars['is_confirmed'] ) ? 1 : 0;
		$sql['where'][] = $wpdb->prepare( "is_confirmed = %d", $is_confirmed );

		// If invite_sent.
		if ( ! is_null( $this->query_vars['invite_sent'] ) ) {
			$invite_sent = ! empty( $this->query_vars['invite_sent'] ) ? 1 : 0;
			$sql['where'][] = $wpdb->prepare( "invite_sent = %d", $invite_sent );
		}

		// If inviter_id.
		if ( ! is_null( $this->query_vars['inviter_id'] ) ) {
			$inviter_id = $this->query_vars['inviter_id'];

			// Empty: inviter_id = 0. (pass false, 0, or empty array).
			if ( empty( $inviter_id ) ) {
				$sql['where'][] = "inviter_id = 0";

			// The string 'any' matches any non-zero value (inviter_id != 0).
			} elseif ( 'any' === $inviter_id ) {
				$sql['where'][] = "inviter_id != 0";

			// Assume that a list of inviter IDs has been passed.
			} else {
				// Parse and sanitize.
				$inviter_ids = wp_parse_id_list( $inviter_id );
				if ( ! empty( $inviter_ids ) ) {
					$inviter_ids_sql = implode( ',', $inviter_ids );
					$sql['where'][] = "inviter_id IN ({$inviter_ids_sql})";
				}
			}
		}

		// Role information is stored as follows: admins have
		// is_admin = 1, mods have is_mod = 1, banned have is_banned =
		// 1, and members have all three set to 0.
		$roles = !empty( $this->query_vars['event_role'] ) ? $this->query_vars['event_role'] : array();
		if ( is_string( $roles ) ) {
			$roles = explode( ',', $roles );
		}

		// Sanitize: Only 'admin', 'mod', 'member', and 'banned' are valid.
		$allowed_roles = array( 'admin', 'mod', 'member', 'banned' );
		foreach ( $roles as $role_key => $role_value ) {
			if ( ! in_array( $role_value, $allowed_roles ) ) {
				unset( $roles[ $role_key ] );
			}
		}

		$roles = array_unique( $roles );

		// When querying for a set of roles containing 'member' (for
		// which there is no dedicated is_ column), figure out a list
		// of columns *not* to match.
		$roles_sql = '';
		if ( in_array( 'member', $roles ) ) {
			$role_columns = array();
			foreach ( array_diff( $allowed_roles, $roles ) as $excluded_role ) {
				$role_columns[] = 'is_' . $excluded_role . ' = 0';
			}

			if ( ! empty( $role_columns ) ) {
				$roles_sql = '(' . implode( ' AND ', $role_columns ) . ')';
			}

		// When querying for a set of roles *not* containing 'member',
		// simply construct a list of is_* = 1 clauses.
		} else {
			$role_columns = array();
			foreach ( $roles as $role ) {
				$role_columns[] = 'is_' . $role . ' = 1';
			}

			if ( ! empty( $role_columns ) ) {
				$roles_sql = '(' . implode( ' OR ', $role_columns ) . ')';
			}
		}

		if ( ! empty( $roles_sql ) ) {
			$sql['where'][] = $roles_sql;
		}

		$sql['where'] = ! empty( $sql['where'] ) ? 'WHERE ' . implode( ' AND ', $sql['where'] ) : '';

		// We fetch event members in order of last_joined, regardless
		// of 'type'. If the 'type' value is not 'last_joined' or
		// 'first_joined', the order will be overridden in
		// SZ_Event_Member_Query::set_orderby().
		$sql['orderby'] = "ORDER BY date_modified";
		$sql['order']   = 'first_joined' === $this->query_vars['type'] ? 'ASC' : 'DESC';

		$this->event_member_ids = $wpdb->get_col( "{$sql['select']} {$sql['where']} {$sql['orderby']} {$sql['order']}" );

		/**
		 * Filters the member IDs for the current event member query.
		 *
		 * Use this filter to build a custom query (such as when you've
		 * defined a custom 'type').
		 *
		 * @since 2.0.0
		 *
		 * @param array                 $event_member_ids Array of associated member IDs.
		 * @param SZ_Event_Member_Query $this             Current SZ_Event_Member_Query instance.
		 */
		$this->event_member_ids = apply_filters( 'sz_event_member_query_event_member_ids', $this->event_member_ids, $this );

		return $this->event_member_ids;
	}

	/**
	 * Tell SZ_User_Query to order by the order of our query results.
	 *
	 * We only override SZ_User_Query's native ordering in case of the
	 * 'last_joined' and 'first_joined' $type parameters.
	 *
	 * @since 1.8.1
	 *
	 * @param SZ_User_Query $query SZ_User_Query object.
	 */
	public function set_orderby( $query ) {
		$gm_ids = $this->get_event_member_ids();
		if ( empty( $gm_ids ) ) {
			$gm_ids = array( 0 );
		}

		// For 'last_joined', 'first_joined', and 'event_activity'
		// types, we override the default orderby clause of
		// SZ_User_Query. In the case of 'event_activity', we perform
		// a separate query to get the necessary order. In the case of
		// 'last_joined' and 'first_joined', we can trust the order of
		// results from  SZ_Event_Member_Query::get_event_members().
		// In all other cases, we fall through and let SZ_User_Query
		// do its own (non-event-specific) ordering.
		if ( in_array( $query->query_vars['type'], array( 'last_joined', 'first_joined', 'event_activity' ) ) ) {

			// Event Activity DESC.
			if ( 'event_activity' == $query->query_vars['type'] ) {
				$gm_ids = $this->get_gm_ids_ordered_by_activity( $query, $gm_ids );
			}

			// The first param in the FIELD() clause is the sort column id.
			$gm_ids = array_merge( array( 'u.id' ), wp_parse_id_list( $gm_ids ) );
			$gm_ids_sql = implode( ',', $gm_ids );

			$query->uid_clauses['orderby'] = "ORDER BY FIELD(" . $gm_ids_sql . ")";
		}

		// Prevent this filter from running on future SZ_User_Query
		// instances on the same page.
		remove_action( 'sz_pre_user_query', array( $this, 'set_orderby' ) );
	}

	/**
	 * Fetch additional data required in sz_event_has_members() loops.
	 *
	 * Additional data fetched:
	 *      - is_banned
	 *      - date_modified
	 *
	 * @since 1.8.0
	 *
	 * @param SZ_User_Query $query        SZ_User_Query object. Because we're
	 *                                    filtering the current object, we use
	 *                                    $this inside of the method instead.
	 * @param string        $user_ids_sql Sanitized, comma-separated string of
	 *                                    the user ids returned by the main query.
	 */
	public function populate_event_member_extras( $query, $user_ids_sql ) {
		global $wpdb;

		$sz     = sportszone();
		$extras = $wpdb->get_results( $wpdb->prepare( "SELECT id, user_id, date_modified, is_admin, is_mod, comments, user_title, invite_sent, is_confirmed, inviter_id, is_banned FROM {$sz->events->table_name_members} WHERE user_id IN ({$user_ids_sql}) AND event_id = %d", $this->query_vars['event_id'] ) );

		foreach ( (array) $extras as $extra ) {
			if ( isset( $this->results[ $extra->user_id ] ) ) {
				// The user_id is provided for backward compatibility.
				$this->results[ $extra->user_id ]->user_id       = (int) $extra->user_id;
				$this->results[ $extra->user_id ]->is_admin      = (int) $extra->is_admin;
				$this->results[ $extra->user_id ]->is_mod        = (int) $extra->is_mod;
				$this->results[ $extra->user_id ]->is_banned     = (int) $extra->is_banned;
				$this->results[ $extra->user_id ]->date_modified = $extra->date_modified;
				$this->results[ $extra->user_id ]->user_title    = $extra->user_title;
				$this->results[ $extra->user_id ]->comments      = $extra->comments;
				$this->results[ $extra->user_id ]->invite_sent   = (int) $extra->invite_sent;
				$this->results[ $extra->user_id ]->inviter_id    = (int) $extra->inviter_id;
				$this->results[ $extra->user_id ]->is_confirmed  = (int) $extra->is_confirmed;
				$this->results[ $extra->user_id ]->membership_id = (int) $extra->id;
			}
		}

		// Don't filter other SZ_User_Query objects on the same page.
		remove_action( 'sz_user_query_populate_extras', array( $this, 'populate_event_member_extras' ), 10 );
	}

	/**
	 * Sort user IDs by how recently they have generated activity within a given event.
	 *
	 * @since 2.1.0
	 *
	 * @param SZ_User_Query $query  SZ_User_Query object.
	 * @param array         $gm_ids array of event member ids.
	 * @return array
	 */
	public function get_gm_ids_ordered_by_activity( $query, $gm_ids = array() ) {
		global $wpdb;

		if ( empty( $gm_ids ) ) {
			return $gm_ids;
		}

		if ( ! sz_is_active( 'activity' ) ) {
			return $gm_ids;
		}

		$activity_table = sportszone()->activity->table_name;

		$sql = array(
			'select'  => "SELECT user_id, max( date_recorded ) as date_recorded FROM {$activity_table}",
			'where'   => array(),
			'eventby' => 'EVENT BY user_id',
			'orderby' => 'ORDER BY date_recorded',
			'order'   => 'DESC',
		);

		$sql['where'] = array(
			'user_id IN (' . implode( ',', wp_parse_id_list( $gm_ids ) ) . ')',
			'item_id = ' . absint( $query->query_vars['event_id'] ),
			$wpdb->prepare( "component = %s", sportszone()->events->id ),
		);

		$sql['where'] = 'WHERE ' . implode( ' AND ', $sql['where'] );

		$event_user_ids = $wpdb->get_results( "{$sql['select']} {$sql['where']} {$sql['eventby']} {$sql['orderby']} {$sql['order']}" );

		return wp_list_pluck( $event_user_ids, 'user_id' );
	}
}
