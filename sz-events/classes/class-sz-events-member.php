<?php
/**
 * SportsZone Events Classes.
 *
 * @package SportsZone
 * @subpackage EventsClasses
 * @since 1.6.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SportsZone Event Membership object.
 */
class SZ_Events_Member {

	/**
	 * ID of the membership.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $id;

	/**
	 * ID of the event associated with the membership.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $event_id;

	/**
	 * ID of the user associated with the membership.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $user_id;

	/**
	 * ID of the user whose invitation initiated the membership.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $inviter_id;

	/**
	 * Whether the member is an admin of the event.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $is_admin;

	/**
	 * Whether the member is a mod of the event.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $is_mod;

	/**
	 * Whether the member is banned from the event.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $is_banned;

	/**
	 * Title used to describe the event member's role in the event.
	 *
	 * Eg, 'Event Admin'.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $user_title;

	/**
	 * Last modified date of the membership.
	 *
	 * This value is updated when, eg, invitations are accepted.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	var $date_modified;

	/**
	 * Whether the membership has been confirmed.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $is_confirmed;

	/**
	 * Comments associated with the membership.
	 *
	 * In BP core, these are limited to the optional message users can
	 * include when requesting membership to a private event.
	 *
	 * @since 1.6.0
	 * @var string
	 */
	var $comments;

	/**
	 * Whether an invitation has been sent for this membership.
	 *
	 * The purpose of this flag is to mark when an invitation has been
	 * "drafted" (the user has been added via the interface at Send
	 * Invites), but the Send button has not been pressed, so the
	 * invitee has not yet been notified.
	 *
	 * @since 1.6.0
	 * @var int
	 */
	var $invite_sent;

	/**
	 * WP_User object representing the membership's user.
	 *
	 * @since 1.6.0
	 * @var WP_User
	 */
	protected $user;

	/**
	 * Constructor method.
	 *
	 * @since 1.6.0
	 *
	 * @param int      $user_id  Optional. Along with $event_id, can be used to
	 *                           look up a membership.
	 * @param int      $event_id Optional. Along with $user_id, can be used to
	 *                           look up a membership.
	 * @param int|bool $id       Optional. The unique ID of the membership object.
	 * @param bool     $populate Whether to populate the properties of the
	 *                           located membership. Default: true.
	 */
	public function __construct( $user_id = 0, $event_id = 0, $id = false, $populate = true ) {

		// User and event are not empty, and ID is.
		if ( !empty( $user_id ) && !empty( $event_id ) && empty( $id ) ) {
			$this->user_id  = $user_id;
			$this->event_id = $event_id;

			if ( !empty( $populate ) ) {
				$this->populate();
			}
		}

		// ID is not empty.
		if ( !empty( $id ) ) {
			$this->id = $id;

			if ( !empty( $populate ) ) {
				$this->populate();
			}
		}
	}

	/**
	 * Populate the object's properties.
	 *
	 * @since 1.6.0
	 */
	public function populate() {
		global $wpdb;

		$sz = sportszone();

		if ( $this->user_id && $this->event_id && !$this->id )
			$sql = $wpdb->prepare( "SELECT * FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d", $this->user_id, $this->event_id );

		if ( !empty( $this->id ) )
			$sql = $wpdb->prepare( "SELECT * FROM {$sz->events->table_name_members} WHERE id = %d", $this->id );

		$member = $wpdb->get_row($sql);

		if ( !empty( $member ) ) {
			$this->id            = (int) $member->id;
			$this->event_id      = (int) $member->event_id;
			$this->user_id       = (int) $member->user_id;
			$this->inviter_id    = (int) $member->inviter_id;
			$this->is_admin      = (int) $member->is_admin;
			$this->is_mod        = (int) $member->is_mod;
			$this->is_banned     = (int) $member->is_banned;
			$this->user_title    = $member->user_title;
			$this->date_modified = $member->date_modified;
			$this->is_confirmed  = (int) $member->is_confirmed;
			$this->comments      = $member->comments;
			$this->invite_sent   = (int) $member->invite_sent;
		}
	}

	/**
	 * Magic getter.
	 *
	 * @since 2.8.0
	 *
	 * @param string $key Key.
	 * @return SZ_Core_User|null
	 */
	public function __get( $key ) {
		switch ( $key ) {
			case 'user' :
				return $this->get_user_object( $this->user_id );
		}
	}

	/**
	 * Magic issetter.
	 *
	 * @since 2.8.0
	 *
	 * @param string $key Key.
	 * @return bool
	 */
	public function __isset( $key ) {
		switch ( $key ) {
			case 'user' :
				return true;

			default :
				return isset( $this->{$key} );
		}
	}

	/**
	 * Get the user object corresponding to this membership.
	 *
	 * Used for lazyloading the protected `user` property.
	 *
	 * @since 2.8.0
	 *
	 * @return SZ_Core_User
	 */
	protected function get_user_object() {
		if ( empty( $this->user ) ) {
			$this->user = new SZ_Core_User( $this->user_id );
		}

		return $this->user;
	}

	/**
	 * Save the membership data to the database.
	 *
	 * @since 1.6.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		global $wpdb;

		$sz = sportszone();

		$this->user_id       = apply_filters( 'events_member_user_id_before_save',       $this->user_id,       $this->id );
		$this->event_id      = apply_filters( 'events_member_event_id_before_save',      $this->event_id,      $this->id );
		$this->inviter_id    = apply_filters( 'events_member_inviter_id_before_save',    $this->inviter_id,    $this->id );
		$this->is_admin      = apply_filters( 'events_member_is_admin_before_save',      $this->is_admin,      $this->id );
		$this->is_mod        = apply_filters( 'events_member_is_mod_before_save',        $this->is_mod,        $this->id );
		$this->is_banned     = apply_filters( 'events_member_is_banned_before_save',     $this->is_banned,     $this->id );
		$this->user_title    = apply_filters( 'events_member_user_title_before_save',    $this->user_title,    $this->id );
		$this->date_modified = apply_filters( 'events_member_date_modified_before_save', $this->date_modified, $this->id );
		$this->is_confirmed  = apply_filters( 'events_member_is_confirmed_before_save',  $this->is_confirmed,  $this->id );
		$this->comments      = apply_filters( 'events_member_comments_before_save',      $this->comments,      $this->id );
		$this->invite_sent   = apply_filters( 'events_member_invite_sent_before_save',   $this->invite_sent,   $this->id );

		/**
		 * Fires before the current event membership item gets saved.
		 *
		 * Please use this hook to filter the properties above. Each part will be passed in.
		 *
		 * @since 1.0.0
		 *
		 * @param SZ_Events_Member $this Current instance of the event membership item being saved. Passed by reference.
		 */
		do_action_ref_array( 'events_member_before_save', array( &$this ) );

		// The following properties are required; bail if not met.
		if ( empty( $this->user_id ) || empty( $this->event_id ) ) {
			return false;
		}

		if ( !empty( $this->id ) ) {
			$sql = $wpdb->prepare( "UPDATE {$sz->events->table_name_members} SET inviter_id = %d, is_admin = %d, is_mod = %d, is_banned = %d, user_title = %s, date_modified = %s, is_confirmed = %d, comments = %s, invite_sent = %d WHERE id = %d", $this->inviter_id, $this->is_admin, $this->is_mod, $this->is_banned, $this->user_title, $this->date_modified, $this->is_confirmed, $this->comments, $this->invite_sent, $this->id );
		} else {
			// Ensure that user is not already a member of the event before inserting.
			if ( $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 1 LIMIT 1", $this->user_id, $this->event_id ) ) ) {
				return false;
			}

			$sql = $wpdb->prepare( "INSERT INTO {$sz->events->table_name_members} ( user_id, event_id, inviter_id, is_admin, is_mod, is_banned, user_title, date_modified, is_confirmed, comments, invite_sent ) VALUES ( %d, %d, %d, %d, %d, %d, %s, %s, %d, %s, %d )", $this->user_id, $this->event_id, $this->inviter_id, $this->is_admin, $this->is_mod, $this->is_banned, $this->user_title, $this->date_modified, $this->is_confirmed, $this->comments, $this->invite_sent );
		}

		if ( !$wpdb->query( $sql ) )
			return false;

		$this->id = $wpdb->insert_id;

		// Update the user's event count.
		self::refresh_total_event_count_for_user( $this->user_id );

		// Update the event's member count.
		self::refresh_total_member_count_for_event( $this->event_id );

		/**
		 * Fires after the current event membership item has been saved.
		 *
		 * Please use this hook to filter the properties above. Each part will be passed in.
		 *
		 * @since 1.0.0
		 *
		 * @param SZ_Events_Member $this Current instance of the event membership item has been saved. Passed by reference.
		 */
		do_action_ref_array( 'events_member_after_save', array( &$this ) );

		return true;
	}

	/**
	 * Promote a member to a new status.
	 *
	 * @since 1.6.0
	 *
	 * @param string $status The new status. 'mod' or 'admin'.
	 * @return bool True on success, false on failure.
	 */
	public function promote( $status = 'mod' ) {
		if ( 'mod' == $status ) {
			$this->is_admin   = 0;
			$this->is_mod     = 1;
			$this->user_title = __( 'Event Mod', 'sportszone' );
		}

		if ( 'admin' == $status ) {
			$this->is_admin   = 1;
			$this->is_mod     = 0;
			$this->user_title = __( 'Event Admin', 'sportszone' );
		}

		return $this->save();
	}

	/**
	 * Demote membership to Member status (non-admin, non-mod).
	 *
	 * @since 1.6.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function demote() {
		$this->is_mod     = 0;
		$this->is_admin   = 0;
		$this->user_title = false;

		return $this->save();
	}

	/**
	 * Ban the user from the event.
	 *
	 * @since 1.6.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function ban() {
		if ( !empty( $this->is_admin ) )
			return false;

		$this->is_mod = 0;
		$this->is_banned = 1;

		return $this->save();
	}

	/**
	 * Unban the user from the event.
	 *
	 * @since 1.6.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function unban() {
		if ( !empty( $this->is_admin ) )
			return false;

		$this->is_banned = 0;

		return $this->save();
	}

	/**
	 * Mark a pending invitation as accepted.
	 *
	 * @since 1.6.0
	 */
	public function accept_invite() {
		$this->inviter_id    = 0;
		$this->is_confirmed  = 1;
		$this->date_modified = sz_core_current_time();
	}

	/**
	 * Confirm a membership request.
	 *
	 * @since 1.6.0
	 */
	public function accept_request() {
		$this->is_confirmed = 1;
		$this->date_modified = sz_core_current_time();
	}

	/**
	 * Remove the current membership.
	 *
	 * @since 1.6.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public function remove() {
		global $wpdb;

		/**
		 * Fires before a member is removed from a event.
		 *
		 * @since 2.3.0
		 *
		 * @param SZ_Events_Member $this Current event membership object.
		 */
		do_action_ref_array( 'events_member_before_remove', array( $this ) );

		$sz  = sportszone();
		$sql = $wpdb->prepare( "DELETE FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d", $this->user_id, $this->event_id );

		if ( !$result = $wpdb->query( $sql ) )
			return false;

		// Update the user's event count.
		self::refresh_total_event_count_for_user( $this->user_id );

		// Update the event's member count.
		self::refresh_total_member_count_for_event( $this->event_id );

		/**
		 * Fires after a member is removed from a event.
		 *
		 * @since 2.3.0
		 *
		 * @param SZ_Events_Member $this Current event membership object.
		 */
		do_action_ref_array( 'events_member_after_remove', array( $this ) );

		return $result;
	}

	/** Static Methods ****************************************************/

	/**
	 * Refresh the total_event_count for a user.
	 *
	 * @since 1.8.0
	 *
	 * @param int $user_id ID of the user.
	 * @return bool True on success, false on failure.
	 */
	public static function refresh_total_event_count_for_user( $user_id ) {
		return sz_update_user_meta( $user_id, 'total_event_count', (int) self::total_event_count( $user_id ) );
	}

	/**
	 * Refresh the total_member_count for a event.
	 *
	 * @since 1.8.0
	 *
	 * @param int $event_id ID of the event.
	 * @return bool|int True on success, false on failure.
	 */
	public static function refresh_total_member_count_for_event( $event_id ) {
		return events_update_eventmeta( $event_id, 'total_member_count', (int) SZ_Events_Event::get_total_member_count( $event_id ) );
	}

	/**
	 * Delete a membership, based on user + event IDs.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id  ID of the user.
	 * @param int $event_id ID of the event.
	 * @return True on success, false on failure.
	 */
	public static function delete( $user_id, $event_id ) {
		global $wpdb;

		/**
		 * Fires before a event membership is deleted.
		 *
		 * @since 2.3.0
		 *
		 * @param int $user_id  ID of the user.
		 * @param int $event_id ID of the event.
		 */
		do_action( 'sz_events_member_before_delete', $user_id, $event_id );

		$sz = sportszone();
		$remove = $wpdb->query( $wpdb->prepare( "DELETE FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d", $user_id, $event_id ) );

		// Update the user's event count.
		self::refresh_total_event_count_for_user( $user_id );

		// Update the event's member count.
		self::refresh_total_member_count_for_event( $event_id );

		/**
		 * Fires after a member is removed from a event.
		 *
		 * @since 2.3.0
		 *
		 * @param int $user_id  ID of the user.
		 * @param int $event_id ID of the event.
		 */
		do_action( 'sz_events_member_after_delete', $user_id, $event_id );

		return $remove;
	}

	/**
	 * Get the IDs of the events of which a specified user is a member.
	 *
	 * @since 1.6.0
	 *
	 * @param int      $user_id ID of the user.
	 * @param int|bool $limit   Optional. Max number of results to return.
	 *                          Default: false (no limit).
	 * @param int|bool $page    Optional. Page offset of results to return.
	 *                          Default: false (no limit).
	 * @return array {
	 *     @type array $events Array of events returned by paginated query.
	 *     @type int   $total  Count of events matching query.
	 * }
	 */
	public static function get_event_ids( $user_id, $limit = false, $page = false ) {
		global $wpdb;

		$pag_sql = '';
		if ( !empty( $limit ) && !empty( $page ) )
			$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		$sz = sportszone();

		// If the user is logged in and viewing their own events, we can show hidden and private events.
		if ( $user_id != sz_loggedin_user_id() ) {
			$event_sql = $wpdb->prepare( "SELECT DISTINCT m.event_id FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0{$pag_sql}", $user_id );
			$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", $user_id ) );
		} else {
			$event_sql = $wpdb->prepare( "SELECT DISTINCT event_id FROM {$sz->events->table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0{$pag_sql}", $user_id );
			$total_events = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT event_id) FROM {$sz->events->table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0", $user_id ) );
		}

		$events = $wpdb->get_col( $event_sql );

		return array( 'events' => $events, 'total' => (int) $total_events );
	}

	/**
	 * Get the IDs of the events of which a specified user is a member, sorted by the date joined.
	 *
	 * @since 1.6.0
	 *
	 * @param int         $user_id ID of the user.
	 * @param int|bool    $limit   Optional. Max number of results to return.
	 *                             Default: false (no limit).
	 * @param int|bool    $page    Optional. Page offset of results to return.
	 *                             Default: false (no limit).
	 * @param string|bool $filter  Optional. Limit results to events whose name or
	 *                             description field matches search terms.
	 * @return array {
	 *     @type array $events Array of events returned by paginated query.
	 *     @type int   $total  Count of events matching query.
	 * }
	 */
	public static function get_recently_joined( $user_id, $limit = false, $page = false, $filter = false ) {
		global $wpdb;

		$user_id_sql = $pag_sql = $hidden_sql = $filter_sql = '';

		$user_id_sql = $wpdb->prepare( 'm.user_id = %d', $user_id );

		if ( !empty( $limit ) && !empty( $page ) )
			$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !empty( $filter ) ) {
			$search_terms_like = '%' . sz_esc_like( $filter ) . '%';
			$filter_sql = $wpdb->prepare( " AND ( g.name LIKE %s OR g.description LIKE %s )", $search_terms_like, $search_terms_like );
		}

		if ( $user_id != sz_loggedin_user_id() )
			$hidden_sql = " AND g.status != 'hidden'";

		$sz = sportszone();

		$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$sz->events->table_name_eventmeta} gm1, {$sz->events->table_name_eventmeta} gm2, {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'{$hidden_sql}{$filter_sql} AND {$user_id_sql} AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY m.date_modified DESC {$pag_sql}" );
		$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE m.event_id = g.id{$hidden_sql}{$filter_sql} AND {$user_id_sql} AND m.is_banned = 0 AND m.is_confirmed = 1 ORDER BY m.date_modified DESC" );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	/**
	 * Get the IDs of the events of which a specified user is an admin.
	 *
	 * @since 1.6.0
	 *
	 * @param int         $user_id ID of the user.
	 * @param int|bool    $limit   Optional. Max number of results to return.
	 *                             Default: false (no limit).
	 * @param int|bool    $page    Optional. Page offset of results to return.
	 *                             Default: false (no limit).
	 * @param string|bool $filter  Optional. Limit results to events whose name or
	 *                             description field matches search terms.
	 * @return array {
	 *     @type array $events Array of events returned by paginated query.
	 *     @type int   $total  Count of events matching query.
	 * }
	 */
	public static function get_is_admin_of( $user_id, $limit = false, $page = false, $filter = false ) {
		global $wpdb;

		$user_id_sql = $pag_sql = $hidden_sql = $filter_sql = '';

		$user_id_sql = $wpdb->prepare( 'm.user_id = %d', $user_id );

		if ( !empty( $limit ) && !empty( $page ) )
			$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !empty( $filter ) ) {
			$search_terms_like = '%' . sz_esc_like( $filter ) . '%';
			$filter_sql = $wpdb->prepare( " AND ( g.name LIKE %s OR g.description LIKE %s )", $search_terms_like, $search_terms_like );
		}

		if ( $user_id != sz_loggedin_user_id() )
			$hidden_sql = " AND g.status != 'hidden'";

		$sz = sportszone();

		$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$sz->events->table_name_eventmeta} gm1, {$sz->events->table_name_eventmeta} gm2, {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'{$hidden_sql}{$filter_sql} AND {$user_id_sql} AND m.is_confirmed = 1 AND m.is_banned = 0 AND m.is_admin = 1 ORDER BY m.date_modified ASC {$pag_sql}" );
		$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE m.event_id = g.id{$hidden_sql}{$filter_sql} AND {$user_id_sql} AND m.is_confirmed = 1 AND m.is_banned = 0 AND m.is_admin = 1 ORDER BY date_modified ASC" );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	/**
	 * Get the IDs of the events of which a specified user is a moderator.
	 *
	 * @since 1.6.0
	 *
	 * @param int         $user_id ID of the user.
	 * @param int|bool    $limit   Optional. Max number of results to return.
	 *                             Default: false (no limit).
	 * @param int|bool    $page    Optional. Page offset of results to return.
	 *                             Default: false (no limit).
	 * @param string|bool $filter  Optional. Limit results to events whose name or
	 *                             description field matches search terms.
	 * @return array {
	 *     @type array $events Array of events returned by paginated query.
	 *     @type int   $total  Count of events matching query.
	 * }
	 */
	public static function get_is_mod_of( $user_id, $limit = false, $page = false, $filter = false ) {
		global $wpdb;

		$user_id_sql = $pag_sql = $hidden_sql = $filter_sql = '';

		$user_id_sql = $wpdb->prepare( 'm.user_id = %d', $user_id );

		if ( !empty( $limit ) && !empty( $page ) )
			$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		if ( !empty( $filter ) ) {
			$search_terms_like = '%' . sz_esc_like( $filter ) . '%';
			$filter_sql = $wpdb->prepare( " AND ( g.name LIKE %s OR g.description LIKE %s )", $search_terms_like, $search_terms_like );
		}

		if ( $user_id != sz_loggedin_user_id() )
			$hidden_sql = " AND g.status != 'hidden'";

		$sz = sportszone();

		$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$sz->events->table_name_eventmeta} gm1, {$sz->events->table_name_eventmeta} gm2, {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'{$hidden_sql}{$filter_sql} AND {$user_id_sql} AND m.is_confirmed = 1 AND m.is_banned = 0 AND m.is_mod = 1 ORDER BY m.date_modified ASC {$pag_sql}" );
		$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE m.event_id = g.id{$hidden_sql}{$filter_sql} AND {$user_id_sql} AND m.is_confirmed = 1 AND m.is_banned = 0 AND m.is_mod = 1 ORDER BY date_modified ASC" );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	/**
	 * Get the events of which a specified user is banned from.
	 *
	 * @since 2.4.0
	 *
	 * @param int         $user_id ID of the user.
	 * @param int|bool    $limit   Optional. Max number of results to return.
	 *                             Default: false (no limit).
	 * @param int|bool    $page    Optional. Page offset of results to return.
	 *                             Default: false (no limit).
	 * @param string|bool $filter  Optional. Limit results to events whose name or
	 *                             description field matches search terms.
	 * @return array {
	 *     @type array $events Array of events returned by paginated query.
	 *     @type int   $total  Count of events matching query.
	 * }
	 */
	public static function get_is_banned_of( $user_id, $limit = false, $page = false, $filter = false ) {
		global $wpdb;

		$sz = sportszone();

		$user_id_sql = $pag_sql = $hidden_sql = $filter_sql = '';
		$user_id_sql = $wpdb->prepare( 'm.user_id = %d', $user_id );

		if ( $limit && $page ) {
			$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit ), intval( $limit ) );
		}

		if ( $filter ) {
			$search_terms_like = '%' . sz_esc_like( $filter ) . '%';
			$filter_sql        = $wpdb->prepare( " AND ( g.name LIKE %s OR g.description LIKE %s )", $search_terms_like, $search_terms_like );
		}

		if ( $user_id !== sz_loggedin_user_id() && ! sz_current_user_can( 'sz_moderate' ) ) {
			$hidden_sql = " AND g.status != 'hidden'";
		}

		$paged_events = $wpdb->get_results( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$sz->events->table_name_eventmeta} gm1, {$sz->events->table_name_eventmeta} gm2, {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count'{$hidden_sql}{$filter_sql} AND {$user_id_sql} AND m.is_banned = 1  ORDER BY m.date_modified ASC {$pag_sql}" );
		$total_events = $wpdb->get_var( "SELECT COUNT(DISTINCT m.event_id) FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE m.event_id = g.id{$hidden_sql}{$filter_sql} AND {$user_id_sql} AND m.is_banned = 1 ORDER BY date_modified ASC" );

		return array( 'events' => $paged_events, 'total' => $total_events );
	}

	/**
	 * Get the count of events of which the specified user is a member.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id Optional. Default: ID of the displayed user.
	 * @return int Event count.
	 */
	public static function total_event_count( $user_id = 0 ) {
		global $wpdb;

		if ( empty( $user_id ) )
			$user_id = sz_displayed_user_id();

		$sz = sportszone();

		if ( $user_id != sz_loggedin_user_id() && !sz_current_user_can( 'sz_moderate' ) ) {
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE m.event_id = g.id AND g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", $user_id ) );
		} else {
			return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE m.event_id = g.id AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0", $user_id ) );
		}
	}

	/**
	 * Get a user's outstanding event invitations.
	 *
	 * @since 1.6.0
	 *
	 * @param int               $user_id ID of the invitee.
	 * @param int|bool          $limit   Optional. Max number of results to return.
	 *                                   Default: false (no limit).
	 * @param int|bool          $page    Optional. Page offset of results to return.
	 *                                   Default: false (no limit).
	 * @param string|array|bool $exclude Optional. Array or comma-separated list
	 *                                   of event IDs to exclude from results.
	 * @return array {
	 *     @type array $events Array of events returned by paginated query.
	 *     @type int   $total  Count of events matching query.
	 * }
	 */
	public static function get_invites( $user_id, $limit = false, $page = false, $exclude = false ) {
		global $wpdb;

		$pag_sql = ( !empty( $limit ) && !empty( $page ) ) ? $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) ) : '';

		if ( !empty( $exclude ) ) {
			$exclude     = implode( ',', wp_parse_id_list( $exclude ) );
			$exclude_sql = " AND g.id NOT IN ({$exclude})";
		} else {
			$exclude_sql = '';
		}

		$sz = sportszone();

		$paged_events = $wpdb->get_results( $wpdb->prepare( "SELECT g.*, gm1.meta_value as total_member_count, gm2.meta_value as last_activity FROM {$sz->events->table_name_eventmeta} gm1, {$sz->events->table_name_eventmeta} gm2, {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE g.id = m.event_id AND g.id = gm1.event_id AND g.id = gm2.event_id AND gm2.meta_key = 'last_activity' AND gm1.meta_key = 'total_member_count' AND m.is_confirmed = 0 AND m.inviter_id != 0 AND m.invite_sent = 1 AND m.user_id = %d {$exclude_sql} ORDER BY m.date_modified ASC {$pag_sql}", $user_id ) );

		return array( 'events' => $paged_events, 'total' => self::get_invite_count_for_user( $user_id ) );
	}

	/**
	 * Gets the total event invite count for a user.
	 *
	 * @since 2.0.0
	 *
	 * @param int $user_id The user ID.
	 * @return int
	 */
	public static function get_invite_count_for_user( $user_id = 0 ) {
		global $wpdb;

		$sz = sportszone();

		$count = wp_cache_get( $user_id, 'sz_event_invite_count' );

		if ( false === $count ) {
			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT m.event_id) FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE m.event_id = g.id AND m.is_confirmed = 0 AND m.inviter_id != 0 AND m.invite_sent = 1 AND m.user_id = %d", $user_id ) );
			wp_cache_set( $user_id, $count, 'sz_event_invite_count' );
		}

		return $count;
	}

	/**
	 * Check whether a user has an outstanding invitation to a given event.
	 *
	 * @since 1.6.0
	 *
	 * @param int    $user_id  ID of the potential invitee.
	 * @param int    $event_id ID of the event.
	 * @param string $type     If 'sent', results are limited to those invitations
	 *                         that have actually been sent (non-draft). Default: 'sent'.
	 * @return int|null The ID of the invitation if found; null if not found.
	 */
	public static function check_has_invite( $user_id, $event_id, $type = 'sent' ) {
		global $wpdb;

		if ( empty( $user_id ) )
			return false;

		$sz  = sportszone();
		$sql = "SELECT id FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND inviter_id != 0";

		if ( 'sent' == $type )
			$sql .= " AND invite_sent = 1";

		$query = $wpdb->get_var( $wpdb->prepare( $sql, $user_id, $event_id ) );

		return is_numeric( $query ) ? (int) $query : $query;
	}

	/**
	 * Delete an invitation, by specifying user ID and event ID.
	 *
	 * @since 1.6.0
	 *
	 * @global WPDB $wpdb
	 *
	 * @param  int $user_id  ID of the user.
	 * @param  int $event_id ID of the event.
	 * @return int Number of records deleted.
	 */
	public static function delete_invite( $user_id, $event_id ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			return false;
		}

		/**
		 * Fires before a event invitation is deleted.
		 *
		 * @since 2.6.0
		 *
		 * @param int $user_id  ID of the user.
		 * @param int $event_id ID of the event.
		 */
		do_action( 'sz_events_member_before_delete_invite', $user_id, $event_id );

		$table_name = sportszone()->events->table_name_members;

		$sql = "DELETE FROM {$table_name}
				WHERE user_id = %d
					AND event_id = %d
					AND is_confirmed = 0
					AND inviter_id != 0";

		$prepared = $wpdb->prepare( $sql, $user_id, $event_id );

		return $wpdb->query( $prepared );
	}

	/**
	 * Delete an unconfirmed membership request, by user ID and event ID.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id  ID of the user.
	 * @param int $event_id ID of the event.
	 * @return int Number of records deleted.
	 */
	public static function delete_request( $user_id, $event_id ) {
		global $wpdb;

		if ( empty( $user_id ) )
			return false;

		$sz = sportszone();

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND inviter_id = 0 AND invite_sent = 0", $user_id, $event_id ) );
	}

	/**
	 * Check whether a user is an admin of a given event.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id  ID of the user.
	 * @param int $event_id ID of the event.
	 * @return mixed
	 */
	public static function check_is_admin( $user_id, $event_id ) {
		global $wpdb;

		if ( empty( $user_id ) )
			return false;

		$sz = sportszone();

		return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_admin = 1 AND is_banned = 0", $user_id, $event_id ) );
	}

	/**
	 * Check whether a user is a mod of a given event.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id  ID of the user.
	 * @param int $event_id ID of the event.
	 * @return mixed
	 */
	public static function check_is_mod( $user_id, $event_id ) {
		global $wpdb;

		if ( empty( $user_id ) )
			return false;

		$sz = sportszone();

		return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_mod = 1 AND is_banned = 0", $user_id, $event_id ) );
	}

	/**
	 * Check whether a user is a member of a given event.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id  ID of the user.
	 * @param int $event_id ID of the event.
	 * @return mixed
	 */
	public static function check_is_member( $user_id, $event_id ) {
		global $wpdb;

		if ( empty( $user_id ) )
			return false;

		$sz = sportszone();

		return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 1 AND is_banned = 0", $user_id, $event_id ) );
	}

	/**
	 * Check whether a user is banned from a given event.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id  ID of the user.
	 * @param int $event_id ID of the event.
	 * @return int|null int 1 if user is banned; int 0 if user is not banned;
	 *                  null if user is not part of the event or if event doesn't exist.
	 */
	public static function check_is_banned( $user_id, $event_id ) {
		global $wpdb;

		if ( empty( $user_id ) )
			return false;

		$sz = sportszone();

		$query = $wpdb->get_var( $wpdb->prepare( "SELECT is_banned FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d", $user_id, $event_id ) );

		return is_numeric( $query ) ? (int) $query : $query;
	}

	/**
	 * Is the specified user the creator of the event?
	 *
	 * @since 1.2.6
	 *
	 * @param int $user_id  ID of the user.
	 * @param int $event_id ID of the event.
	 * @return int|null int of event ID if user is the creator; null on failure.
	 */
	public static function check_is_creator( $user_id, $event_id ) {
		global $wpdb;

		if ( empty( $user_id ) )
			return false;

		$sz = sportszone();

		$query = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$sz->events->table_name} WHERE creator_id = %d AND id = %d", $user_id, $event_id ) );

		return is_numeric( $query ) ? (int) $query : $query;
	}

	/**
	 * Check whether a user has an outstanding membership request for a given event.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id  ID of the user.
	 * @param int $event_id ID of the event.
	 * @return int Database ID of the membership if found; int 0 on failure.
	 */
	public static function check_for_membership_request( $user_id, $event_id ) {
		global $wpdb;

		if ( empty( $user_id ) )
			return false;

		$sz = sportszone();

		return $wpdb->query( $wpdb->prepare( "SELECT id FROM {$sz->events->table_name_members} WHERE user_id = %d AND event_id = %d AND is_confirmed = 0 AND is_banned = 0 AND inviter_id = 0", $user_id, $event_id ) );
	}

	/**
	 * Get a list of randomly selected IDs of events that the member belongs to.
	 *
	 * @since 1.6.0
	 *
	 * @param int $user_id      ID of the user.
	 * @param int $total_events Max number of event IDs to return. Default: 5.
	 * @return array Event IDs.
	 */
	public static function get_random_events( $user_id = 0, $total_events = 5 ) {
		global $wpdb;

		$sz = sportszone();

		// If the user is logged in and viewing their random events, we can show hidden and private events.
		if ( sz_is_my_profile() ) {
			return array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT event_id FROM {$sz->events->table_name_members} WHERE user_id = %d AND is_confirmed = 1 AND is_banned = 0 ORDER BY rand() LIMIT %d", $user_id, $total_events ) ) );
		} else {
			return array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT m.event_id FROM {$sz->events->table_name_members} m, {$sz->events->table_name} g WHERE m.event_id = g.id AND g.status != 'hidden' AND m.user_id = %d AND m.is_confirmed = 1 AND m.is_banned = 0 ORDER BY rand() LIMIT %d", $user_id, $total_events ) ) );
		}
	}

	/**
	 * Get the IDs of all a given event's members.
	 *
	 * @since 1.6.0
	 *
	 * @param int $event_id ID of the event.
	 * @return array IDs of all event members.
	 */
	public static function get_event_member_ids( $event_id ) {
		global $wpdb;

		$sz = sportszone();

		return array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$sz->events->table_name_members} WHERE event_id = %d AND is_confirmed = 1 AND is_banned = 0", $event_id ) ) );
	}

	/**
	 * Get a list of all a given event's admins.
	 *
	 * @since 1.6.0
	 *
	 * @param int $event_id ID of the event.
	 * @return array Info about event admins (user_id + date_modified).
	 */
	public static function get_event_administrator_ids( $event_id ) {
		global $wpdb;

		if ( empty( $event_id ) ) {
			return array();
		}

		$event_admins = wp_cache_get( $event_id, 'sz_event_admins' );

		if ( false === $event_admins ) {
			self::prime_event_admins_mods_cache( array( $event_id ) );
			$event_admins = wp_cache_get( $event_id, 'sz_event_admins' );
		}

		if ( false === $event_admins ) {
			// The wp_cache_get is still coming up empty. Return an empty array.
			$event_admins = array();
		} else {
			// Cast the user_id property as an integer.
			foreach ( (array) $event_admins as $key => $data ) {
				$event_admins[ $key ]->user_id = (int) $event_admins[ $key ]->user_id;
			}
		}

		return $event_admins;
	}

	/**
	 * Prime the sz_event_admins cache for one or more events.
	 *
	 * @since 2.7.0
	 *
	 * @param array $event_ids IDs of the events.
	 * @return bool True on success.
	 */
	public static function prime_event_admins_mods_cache( $event_ids ) {
		global $wpdb;

		$uncached = sz_get_non_cached_ids( $event_ids, 'sz_event_admins' );

		if ( $uncached ) {
			$sz = sportszone();
			$uncached_sql = implode( ',', array_map( 'intval', $uncached ) );
			$event_admin_mods = $wpdb->get_results( "SELECT user_id, event_id, date_modified, is_admin, is_mod FROM {$sz->events->table_name_members} WHERE event_id IN ({$uncached_sql}) AND ( is_admin = 1 OR is_mod = 1 ) AND is_banned = 0" );

			$admins = $mods = array();
			if ( $event_admin_mods ) {
				foreach ( $event_admin_mods as $event_admin_mod ) {
					$obj = new stdClass();
					$obj->user_id = $event_admin_mod->user_id;
					$obj->date_modified = $event_admin_mod->date_modified;

					if ( $event_admin_mod->is_admin ) {
						$admins[ $event_admin_mod->event_id ][] = $obj;
					} else {
						$mods[ $event_admin_mod->event_id ][] = $obj;
					}
				}
			}

			// Prime cache for all events, even those with no matches.
			foreach ( $uncached as $event_id ) {
				$event_admins = isset( $admins[ $event_id ] ) ? $admins[ $event_id ] : array();
				wp_cache_set( $event_id, $event_admins, 'sz_event_admins' );

				$event_mods = isset( $mods[ $event_id ] ) ? $mods[ $event_id ] : array();
				wp_cache_set( $event_id, $event_mods, 'sz_event_mods' );
			}
		}
	}

	/**
	 * Get a list of all a given event's moderators.
	 *
	 * @since 1.6.0
	 *
	 * @param int $event_id ID of the event.
	 * @return array Info about event mods (user_id + date_modified).
	 */
	public static function get_event_moderator_ids( $event_id ) {
		global $wpdb;

		if ( empty( $event_id ) ) {
			return array();
		}

		$event_mods = wp_cache_get( $event_id, 'sz_event_mods' );

		if ( false === $event_mods ) {
			self::prime_event_admins_mods_cache( array( $event_id ) );
			$event_mods = wp_cache_get( $event_id, 'sz_event_mods' );
		}

		if ( false === $event_mods ) {
			// The wp_cache_get is still coming up empty. Return an empty array.
			$event_mods = array();
		} else {
			// Cast the user_id property as an integer.
			foreach ( (array) $event_mods as $key => $data ) {
				$event_mods[ $key ]->user_id = (int) $event_mods[ $key ]->user_id;
			}
		}

		return $event_mods;
	}

	/**
	 * Get event membership objects by ID (or an array of IDs).
	 *
	 * @since 2.6.0
	 *
	 * @param int|string|array $membership_ids Single membership ID or comma-separated/array list of membership IDs.
	 * @return array
	 */
	public static function get_memberships_by_id( $membership_ids ) {
		global $wpdb;

		$sz = sportszone();

		$membership_ids = implode( ',', wp_parse_id_list( $membership_ids ) );
		return $wpdb->get_results( "SELECT * FROM {$sz->events->table_name_members} WHERE id IN ({$membership_ids})" );
	}

	/**
	 * Get the IDs users with outstanding membership requests to the event.
	 *
	 * @since 1.6.0
	 *
	 * @param int $event_id ID of the event.
	 * @return array IDs of users with outstanding membership requests.
	 */
	public static function get_all_membership_request_user_ids( $event_id ) {
		global $wpdb;

		$sz = sportszone();

		return array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$sz->events->table_name_members} WHERE event_id = %d AND is_confirmed = 0 AND inviter_id = 0", $event_id ) ) );
	}

	/**
	 * Get members of a event.
	 *
	 * @deprecated 1.6.0
	 *
	 * @param int        $event_id            ID of the event being queried for.
	 * @param bool|int   $limit               Max amount to return.
	 * @param bool|int   $page                Pagination value.
	 * @param bool       $exclude_admins_mods Whether or not to exclude admins and moderators.
	 * @param bool       $exclude_banned      Whether or not to exclude banned members.
	 * @param bool|array $exclude             Array of user IDs to exclude.
	 * @return false|array
	 */
	public static function get_all_for_event( $event_id, $limit = false, $page = false, $exclude_admins_mods = true, $exclude_banned = true, $exclude = false ) {
		global $wpdb;

		_deprecated_function( __METHOD__, '1.8', 'SZ_Event_Member_Query' );

		$pag_sql = '';
		if ( !empty( $limit ) && !empty( $page ) )
			$pag_sql = $wpdb->prepare( "LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );

		$exclude_admins_sql = '';
		if ( !empty( $exclude_admins_mods ) )
			$exclude_admins_sql = "AND is_admin = 0 AND is_mod = 0";

		$banned_sql = '';
		if ( !empty( $exclude_banned ) )
			$banned_sql = " AND is_banned = 0";

		$exclude_sql = '';
		if ( !empty( $exclude ) ) {
			$exclude     = implode( ',', wp_parse_id_list( $exclude ) );
			$exclude_sql = " AND m.user_id NOT IN ({$exclude})";
		}

		$sz = sportszone();

		if ( sz_is_active( 'xprofile' ) ) {

			/**
			 * Filters the SQL prepared statement used to fetch event members.
			 *
			 * @since 1.5.0
			 *
			 * @param string $value SQL prepared statement for fetching event members.
			 */
			$members = $wpdb->get_results( apply_filters( 'sz_event_members_user_join_filter', $wpdb->prepare( "SELECT m.user_id, m.date_modified, m.is_banned, u.user_login, u.user_nicename, u.user_email, pd.value as display_name FROM {$sz->events->table_name_members} m, {$wpdb->users} u, {$sz->profile->table_name_data} pd WHERE u.ID = m.user_id AND u.ID = pd.user_id AND pd.field_id = 1 AND event_id = %d AND is_confirmed = 1 {$banned_sql} {$exclude_admins_sql} {$exclude_sql} ORDER BY m.date_modified DESC {$pag_sql}", $event_id ) ) );
		} else {

			/** This filter is documented in sz-events/sz-events-classes */
			$members = $wpdb->get_results( apply_filters( 'sz_event_members_user_join_filter', $wpdb->prepare( "SELECT m.user_id, m.date_modified, m.is_banned, u.user_login, u.user_nicename, u.user_email, u.display_name FROM {$sz->events->table_name_members} m, {$wpdb->users} u WHERE u.ID = m.user_id AND event_id = %d AND is_confirmed = 1 {$banned_sql} {$exclude_admins_sql} {$exclude_sql} ORDER BY m.date_modified DESC {$pag_sql}", $event_id ) ) );
		}

		if ( empty( $members ) ) {
			return false;
		}

		if ( empty( $pag_sql ) ) {
			$total_member_count = count( $members );
		} else {

			/**
			 * Filters the SQL prepared statement used to fetch event members total count.
			 *
			 * @since 1.5.0
			 *
			 * @param string $value SQL prepared statement for fetching event member count.
			 */
			$total_member_count = $wpdb->get_var( apply_filters( 'sz_event_members_count_user_join_filter', $wpdb->prepare( "SELECT COUNT(user_id) FROM {$sz->events->table_name_members} m WHERE event_id = %d AND is_confirmed = 1 {$banned_sql} {$exclude_admins_sql} {$exclude_sql}", $event_id ) ) );
		}

		// Fetch whether or not the user is a friend.
		foreach ( (array) $members as $user )
			$user_ids[] = $user->user_id;

		$user_ids = implode( ',', wp_parse_id_list( $user_ids ) );

		if ( sz_is_active( 'friends' ) ) {
			$friend_status = $wpdb->get_results( $wpdb->prepare( "SELECT initiator_user_id, friend_user_id, is_confirmed FROM {$sz->friends->table_name} WHERE (initiator_user_id = %d AND friend_user_id IN ( {$user_ids} ) ) OR (initiator_user_id IN ( {$user_ids} ) AND friend_user_id = %d )", sz_loggedin_user_id(), sz_loggedin_user_id() ) );
			for ( $i = 0, $count = count( $members ); $i < $count; ++$i ) {
				foreach ( (array) $friend_status as $status ) {
					if ( $status->initiator_user_id == $members[$i]->user_id || $status->friend_user_id == $members[$i]->user_id ) {
						$members[$i]->is_friend = $status->is_confirmed;
					}
				}
			}
		}

		return array( 'members' => $members, 'count' => $total_member_count );
	}

	/**
	 * Get all membership IDs for a user.
	 *
	 * @since 2.6.0
	 *
	 * @param int $user_id ID of the user.
	 * @return array
	 */
	public static function get_membership_ids_for_user( $user_id ) {
		global $wpdb;

		$sz = sportszone();

		$event_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$sz->events->table_name_members} WHERE user_id = %d ORDER BY id ASC", $user_id ) );

		return $event_ids;
	}

	/**
	 * Delete all memberships for a given event.
	 *
	 * @since 1.6.0
	 *
	 * @param int $event_id ID of the event.
	 * @return int Number of records deleted.
	 */
	public static function delete_all( $event_id ) {
		global $wpdb;

		$sz = sportszone();

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$sz->events->table_name_members} WHERE event_id = %d", $event_id ) );
	}

	/**
	 * Delete all event membership information for the specified user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id ID of the user.
	 * @return mixed
	 */
	public static function delete_all_for_user( $user_id ) {
		global $wpdb;

		$sz = sportszone();

		// Get all the event ids for the current user's events and update counts.
		$event_ids = SZ_Events_Member::get_event_ids( $user_id );
		foreach ( $event_ids['events'] as $event_id ) {
			events_update_eventmeta( $event_id, 'total_member_count', events_get_total_member_count( $event_id ) - 1 );

			// If current user is the creator of a event and is the sole admin, delete that event to avoid counts going out-of-sync.
			if ( events_is_user_admin( $user_id, $event_id ) && count( events_get_event_admins( $event_id ) ) < 2 && events_is_user_creator( $user_id, $event_id ) )
				events_delete_event( $event_id );
		}

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$sz->events->table_name_members} WHERE user_id = %d", $user_id ) );
	}
}
