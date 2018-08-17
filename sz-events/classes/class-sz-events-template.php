<?php
/**
 * SportsZone Events Template loop class.
 *
 * @package SportsZone
 * @since 1.2.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The main Events template loop class.
 *
 * Responsible for loading a event of events into a loop for display.
 *
 * @since 1.2.0
 */
class SZ_Events_Template {

	/**
	 * The loop iterator.
	 *
	 * @var int
	 * @since 1.2.0
	 */
	public $current_event = -1;

	/**
	 * The number of events returned by the paged query.
	 *
	 * @var int
	 * @since 1.2.0
	 */
	public $event_count;

	/**
	 * Array of events located by the query.
	 *
	 * @var array
	 * @since 1.2.0
	 */
	public $events;

	/**
	 * The event object currently being iterated on.
	 *
	 * @var object
	 * @since 1.2.0
	 */
	public $event;

	/**
	 * A flag for whether the loop is currently being iterated.
	 *
	 * @var bool
	 * @since 1.2.0
	 */
	public $in_the_loop;

	/**
	 * The page number being requested.
	 *
	 * @var string
	 * @since 1.2.0
	 */
	public $pag_page;

	/**
	 * The number of items being requested per page.
	 *
	 * @var string
	 * @since 1.2.0
	 */
	public $pag_num;

	/**
	 * An HTML string containing pagination links.
	 *
	 * @var string
	 * @since 1.2.0
	 */
	public $pag_links;

	/**
	 * The total number of events matching the query parameters.
	 *
	 * @var int
	 * @since 1.2.0
	 */
	public $total_event_count;

	/**
	 * Whether the template loop is for a single event page.
	 *
	 * @var bool
	 * @since 1.2.0
	 */
	public $single_event = false;

	/**
	 * Field to sort by.
	 *
	 * @var string
	 * @since 1.2.0
	 */
	public $sort_by;

	/**
	 * Sort order.
	 *
	 * @var string
	 * @since 1.2.0
	 */
	public $order;

	/**
	 * Constructor method.
	 *
	 * @see SZ_Events_Event::get() for an in-depth description of arguments.
	 *
	 * @param array $args {
	 *     Array of arguments. Accepts all arguments accepted by
	 *     {@link SZ_Events_Event::get()}. In cases where the default
	 *     values of the params differ, they have been discussed below.
	 *     @type int $per_page Default: 20.
	 *     @type int $page Default: 1.
	 * }
	 */
	function __construct( $args = array() ){

		// Backward compatibility with old method of passing arguments.
		if ( ! is_array( $args ) || func_num_args() > 1 ) {
			_deprecated_argument( __METHOD__, '1.7', sprintf( __( 'Arguments passed to %1$s should be in an associative array. See the inline documentation at %2$s for more details.', 'sportszone' ), __METHOD__, __FILE__ ) );

			$old_args_keys = array(
				0  => 'user_id',
				1  => 'type',
				2  => 'page',
				3  => 'per_page',
				4  => 'max',
				5  => 'slug',
				6  => 'search_terms',
				7  => 'populate_extras',
				8  => 'include',
				9  => 'exclude',
				10 => 'show_hidden',
				11 => 'page_arg',
			);

			$args = sz_core_parse_args_array( $old_args_keys, func_get_args() );
		}

		$defaults = array(
			'page'               => 1,
			'per_page'           => 20,
			'page_arg'           => 'grpage',
			'max'                => false,
			'type'               => 'active',
			'order'              => 'DESC',
			'orderby'            => 'date_created',
			'show_hidden'        => false,
			'user_id'            => 0,
			'slug'               => false,
			'include'            => false,
			'exclude'            => false,
			'parent_id'          => null,
			'search_terms'       => '',
			'search_columns'     => array(),
			'event_type'         => '',
			'event_type__in'     => '',
			'event_type__not_in' => '',
			'meta_query'         => false,
			'update_meta_cache'  => true,
			'update_admin_cache' => false,
		);

		$r = sz_parse_args( $args, $defaults, 'events_template' );
		extract( $r );

		$this->pag_arg  = sanitize_key( $r['page_arg'] );
		$this->pag_page = sz_sanitize_pagination_arg( $this->pag_arg, $r['page']     );
		$this->pag_num  = sz_sanitize_pagination_arg( 'num',          $r['per_page'] );

		if ( sz_current_user_can( 'sz_moderate' ) || ( is_user_logged_in() && $user_id == sz_loggedin_user_id() ) ) {
			$show_hidden = true;
		}

		if ( 'invites' == $type ) {
			$this->events = events_get_invites_for_user( $user_id, $this->pag_num, $this->pag_page, $exclude );
		} elseif ( 'single-event' == $type ) {
			$this->single_event = true;

			if ( events_get_current_event() ) {
				$event = events_get_current_event();

			} else {
				$event = events_get_event( SZ_Events_Event::get_id_from_slug( $r['slug'] ) );
			}

			// Backwards compatibility - the 'event_id' variable is not part of the
			// SZ_Events_Event object, but we add it here for devs doing checks against it
			//
			// @see https://sportszone.trac.wordpress.org/changeset/3540
			//
			// this is subject to removal in a future release; devs should check against
			// $event->id instead.
			$event->event_id = $event->id;

			$this->events = array( $event );

		} else {
			$this->events = events_get_events( array(
				'type'               => $type,
				'order'              => $order,
				'orderby'            => $orderby,
				'per_page'           => $this->pag_num,
				'page'               => $this->pag_page,
				'user_id'            => $user_id,
				'search_terms'       => $search_terms,
				'search_columns'     => $search_columns,
				'meta_query'         => $meta_query,
				'event_type'         => $event_type,
				'event_type__in'     => $event_type__in,
				'event_type__not_in' => $event_type__not_in,
				'include'            => $include,
				'exclude'            => $exclude,
				'parent_id'          => $parent_id,
				'update_meta_cache'  => $update_meta_cache,
				'update_admin_cache' => $update_admin_cache,
				'show_hidden'        => $show_hidden,
			) );
		}

		if ( 'invites' == $type ) {
			$this->total_event_count = (int) $this->events['total'];
			$this->event_count       = (int) $this->events['total'];
			$this->events            = $this->events['events'];
		} elseif ( 'single-event' == $type ) {
			if ( empty( $event->id ) ) {
				$this->total_event_count = 0;
				$this->event_count       = 0;
			} else {
				$this->total_event_count = 1;
				$this->event_count       = 1;
			}
		} else {
			if ( empty( $max ) || $max >= (int) $this->events['total'] ) {
				$this->total_event_count = (int) $this->events['total'];
			} else {
				$this->total_event_count = (int) $max;
			}

			$this->events = $this->events['events'];

			if ( !empty( $max ) ) {
				if ( $max >= count( $this->events ) ) {
					$this->event_count = count( $this->events );
				} else {
					$this->event_count = (int) $max;
				}
			} else {
				$this->event_count = count( $this->events );
			}
		}

		// Build pagination links.
		if ( (int) $this->total_event_count && (int) $this->pag_num ) {
			$pag_args = array(
				$this->pag_arg => '%#%'
			);

			if ( defined( 'DOING_AJAX' ) && true === (bool) DOING_AJAX ) {
				$base = remove_query_arg( 's', wp_get_referer() );
			} else {
				$base = '';
			}

			$add_args = array(
				'num'     => $this->pag_num,
				'sortby'  => $this->sort_by,
				'order'   => $this->order,
			);

			if ( ! empty( $search_terms ) ) {
				$query_arg = sz_core_get_component_search_query_arg( 'events' );
				$add_args[ $query_arg ] = urlencode( $search_terms );
			}

			$this->pag_links = paginate_links( array(
				'base'      => add_query_arg( $pag_args, $base ),
				'format'    => '',
				'total'     => ceil( (int) $this->total_event_count / (int) $this->pag_num ),
				'current'   => $this->pag_page,
				'prev_text' => _x( '&larr;', 'Event pagination previous text', 'sportszone' ),
				'next_text' => _x( '&rarr;', 'Event pagination next text', 'sportszone' ),
				'mid_size'  => 1,
				'add_args'  => $add_args,
			) );
		}
	}

	/**
	 * Whether there are events available in the loop.
	 *
	 * @since 1.2.0
	 *
	 * @see sz_has_events()
	 *
	 * @return bool True if there are items in the loop, otherwise false.
	 */
	function has_events() {
		if ( $this->event_count ) {
			return true;
		}

		return false;
	}

	/**
	 * Set up the next event and iterate index.
	 *
	 * @since 1.2.0
	 *
	 * @return object The next event to iterate over.
	 */
	function next_event() {
		$this->current_event++;
		$this->event = $this->events[$this->current_event];

		return $this->event;
	}

	/**
	 * Rewind the events and reset member index.
	 *
	 * @since 1.2.0
	 */
	function rewind_events() {
		$this->current_event = -1;
		if ( $this->event_count > 0 ) {
			$this->event = $this->events[0];
		}
	}

	/**
	 * Whether there are events left in the loop to iterate over.
	 *
	 * This method is used by {@link sz_events()} as part of the while loop
	 * that controls iteration inside the events loop, eg:
	 *     while ( sz_events() ) { ...
	 *
	 * @since 1.2.0
	 *
	 * @see sz_events()
	 *
	 * @return bool True if there are more events to show, otherwise false.
	 */
	function events() {
		if ( $this->current_event + 1 < $this->event_count ) {
			return true;
		} elseif ( $this->current_event + 1 == $this->event_count ) {

			/**
			 * Fires right before the rewinding of events list.
			 *
			 * @since 1.5.0
			 */
			do_action('event_loop_end');
			// Do some cleaning up after the loop.
			$this->rewind_events();
		}

		$this->in_the_loop = false;
		return false;
	}

	/**
	 * Set up the current event inside the loop.
	 *
	 * Used by {@link sz_the_event()} to set up the current event data
	 * while looping, so that template tags used during that iteration make
	 * reference to the current member.
	 *
	 * @since 1.2.0
	 *
	 * @see sz_the_event()
	 */
	function the_event() {
		$this->in_the_loop = true;
		$this->event       = $this->next_event();

		if ( 0 == $this->current_event ) {

			/**
			 * Fires if the current event item is the first in the loop.
			 *
			 * @since 1.1.0
			 */
			do_action( 'event_loop_start' );
		}
	}
}
