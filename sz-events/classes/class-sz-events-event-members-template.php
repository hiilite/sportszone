<?php
/**
 * SportsZone Events event members loop template class.
 *
 * @package SportsZone
 * @since 1.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Event Members Loop template class.
 *
 * @since 1.0.0
 */
class SZ_Events_Event_Members_Template {

	/**
	 * @since 1.0.0
	 * @var int
	 */
	public $current_member = -1;

	/**
	 * @since 1.0.0
	 * @var int
	 */
	public $member_count;

	/**
	 * @since 1.0.0
	 * @var array
	 */
	public $members;

	/**
	 * @since 1.0.0
	 * @var object
	 */
	public $member;

	/**
	 * @since 1.0.0
	 * @var bool
	 */
	public $in_the_loop;

	/**
	 * @since 1.0.0
	 * @var int
	 */
	public $pag_page;

	/**
	 * @since 1.0.0
	 * @var int
	 */
	public $pag_num;

	/**
	 * @since 1.0.0
	 * @var array|string|void
	 */
	public $pag_links;

	/**
	 * @since 1.0.0
	 * @var int
	 */
	public $total_event_count;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $args {
	 *     An array of optional arguments.
	 *     @type int      $event_id           ID of the event whose members are being
	 *                                        queried. Default: current event ID.
	 *     @type int      $page               Page of results to be queried. Default: 1.
	 *     @type int      $per_page           Number of items to return per page of
	 *                                        results. Default: 20.
	 *     @type int      $max                Optional. Max number of items to return.
	 *     @type array    $exclude            Optional. Array of user IDs to exclude.
	 *     @type bool|int $exclude_admin_mods True (or 1) to exclude admins and mods from
	 *                                        results. Default: 1.
	 *     @type bool|int $exclude_banned     True (or 1) to exclude banned users from results.
	 *                                        Default: 1.
	 *     @type array    $event_role         Optional. Array of event roles to include.
	 *     @type string   $search_terms       Optional. Search terms to match.
	 * }
	 */
	public function __construct( $args = array() ) {

		// Backward compatibility with old method of passing arguments.
		if ( ! is_array( $args ) || func_num_args() > 1 ) {
			_deprecated_argument( __METHOD__, '2.0.0', sprintf( __( 'Arguments passed to %1$s should be in an associative array. See the inline documentation at %2$s for more details.', 'sportszone' ), __METHOD__, __FILE__ ) );

			$old_args_keys = array(
				0 => 'event_id',
				1 => 'per_page',
				2 => 'max',
				3 => 'exclude_admins_mods',
				4 => 'exclude_banned',
				5 => 'exclude',
				6 => 'event_role',
			);

			$args = sz_core_parse_args_array( $old_args_keys, func_get_args() );
		}

		$r = sz_parse_args( $args, array(
			'event_id'            => sz_get_current_event_id(),
			'page'                => 1,
			'per_page'            => 20,
			'page_arg'            => 'mlpage',
			'max'                 => false,
			'exclude'             => false,
			'exclude_admins_mods' => 1,
			'exclude_banned'      => 1,
			'event_role'          => false,
			'search_terms'        => false,
			'type'                => 'last_joined',
		), 'event_members_template' );

		$this->pag_arg  = sanitize_key( $r['page_arg'] );
		$this->pag_page = sz_sanitize_pagination_arg( $this->pag_arg, $r['page']     );
		$this->pag_num  = sz_sanitize_pagination_arg( 'num',          $r['per_page'] );

		/**
		 * Check the current event is the same as the supplied event ID.
		 * It can differ when using {@link sz_event_has_members()} outside the Events screens.
		 */
		$current_event = events_get_current_event();
		if ( empty( $current_event ) || ( $current_event && $current_event->id !== sz_get_current_event_id() ) ) {
			$current_event = events_get_event( $r['event_id'] );
		}

		// Assemble the base URL for pagination.
		$base_url = trailingslashit( sz_get_event_permalink( $current_event ) . sz_current_action() );
		if ( sz_action_variable() ) {
			$base_url = trailingslashit( $base_url . sz_action_variable() );
		}

		$members_args = $r;

		$members_args['page']     = $this->pag_page;
		$members_args['per_page'] = $this->pag_num;

		// Get event members for this loop.
		$this->members = events_get_event_members( $members_args );

		if ( empty( $r['max'] ) || ( $r['max'] >= (int) $this->members['count'] ) ) {
			$this->total_member_count = (int) $this->members['count'];
		} else {
			$this->total_member_count = (int) $r['max'];
		}

		// Reset members array for subsequent looping.
		$this->members = $this->members['members'];

		if ( empty( $r['max'] ) || ( $r['max'] >= count( $this->members ) ) ) {
			$this->member_count = (int) count( $this->members );
		} else {
			$this->member_count = (int) $r['max'];
		}

		$this->pag_links = paginate_links( array(
			'base'      => add_query_arg( array( $this->pag_arg => '%#%' ), $base_url ),
			'format'    => '',
			'total'     => ! empty( $this->pag_num ) ? ceil( $this->total_member_count / $this->pag_num ) : $this->total_member_count,
			'current'   => $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size'  => 1,
			'add_args'  => array(),
		) );
	}

	/**
	 * Whether or not there are members to display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_members() {
		if ( ! empty( $this->member_count ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Increments to the next member to display.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public function next_member() {
		$this->current_member++;
		$this->member = $this->members[ $this->current_member ];

		return $this->member;
	}

	/**
	 * Rewinds to the first member to display.
	 *
	 * @since 1.0.0
	 */
	public function rewind_members() {
		$this->current_member = -1;
		if ( $this->member_count > 0 ) {
			$this->member = $this->members[0];
		}
	}

	/**
	 * Finishes up the members for display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function members() {
		$tick = intval( $this->current_member + 1 );
		if ( $tick < $this->member_count ) {
			return true;
		} elseif ( $tick == $this->member_count ) {

			/**
			 * Fires right before the rewinding of members list.
			 *
			 * @since 1.0.0
			 * @since 2.3.0 `$this` parameter added.
			 * @since 2.7.0 Action renamed from `loop_end`.
			 *
			 * @param SZ_Events_Event_Members_Template $this Instance of the current Members template.
			 */
			do_action( 'event_members_loop_end', $this );

			// Do some cleaning up after the loop.
			$this->rewind_members();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Sets up the member to display.
	 *
	 * @since 1.0.0
	 */
	public function the_member() {
		$this->in_the_loop = true;
		$this->member      = $this->next_member();

		// Loop has just started.
		if ( 0 == $this->current_member ) {

			/**
			 * Fires if the current member item is the first in the members list.
			 *
			 * @since 1.0.0
			 * @since 2.3.0 `$this` parameter added.
			 * @since 2.7.0 Action renamed from `loop_start`.
			 *
			 * @param SZ_Events_Event_Members_Template $this Instance of the current Members template.
			 */
			do_action( 'event_members_loop_start', $this );
		}
	}
}
