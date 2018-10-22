<?php
/**
 * SportsZone Events Template Functions.
 *
 * @package SportsZone
 * @subpackage EventsTemplates
 * @since 1.5.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Output the events component slug.
 *
 * @since 1.5.0
 */
function sz_events_slug() {
	echo sz_get_events_slug();
}
	/**
	 * Return the events component slug.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_events_slug() {

		/**
		 * Filters the events component slug.
		 *
		 * @since 1.5.0
		 *
		 * @param string $slug Events component slug.
		 */
		return apply_filters( 'sz_get_events_slug', sportszone()->events->slug );
	}

/**
 * Output the events component root slug.
 *
 * @since 1.5.0
 */
function sz_events_root_slug() {
	echo sz_get_events_root_slug();
}
	/**
	 * Return the events component root slug.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_events_root_slug() {

		/**
		 * Filters the events component root slug.
		 *
		 * @since 1.5.0
		 *
		 * @param string $root_slug Events component root slug.
		 */
		return apply_filters( 'sz_get_events_root_slug', sportszone()->events->root_slug );
	}

/**
 * Output the event type base slug.
 *
 * @since 2.7.0
 */
function sz_events_event_type_base() {
	echo esc_url( sz_get_events_event_type_base() );
}
	/**
	 * Get the event type base slug.
	 *
	 * The base slug is the string used as the base prefix when generating event
	 * type directory URLs. For example, in example.com/events/type/foo/, 'foo' is
	 * the event type and 'type' is the base slug.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	function sz_get_events_event_type_base() {
		/**
		 * Filters the event type URL base.
		 *
		 * @since 2.7.0
		 *
		 * @param string $base
		 */
		return apply_filters( 'sz_events_event_type_base', _x( 'type', 'event type URL base', 'sportszone' ) );
	}

/**
 * Output event directory permalink.
 *
 * @since 1.5.0
 */
function sz_events_directory_permalink() {
	echo esc_url( sz_get_events_directory_permalink() );
}
	/**
	 * Return event directory permalink.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_events_directory_permalink() {

		/**
		 * Filters the event directory permalink.
		 *
		 * @since 1.5.0
		 *
		 * @param string $value Permalink for the event directory.
		 */
		return apply_filters( 'sz_get_events_directory_permalink', trailingslashit( sz_get_root_domain() . '/' . sz_get_events_root_slug() ) );
	}

/**
 * Output event type directory permalink.
 *
 * @since 2.7.0
 *
 * @param string $event_type Optional. Event type.
 */
function sz_event_type_directory_permalink( $event_type = '' ) {
	echo esc_url( sz_get_event_type_directory_permalink( $event_type ) );
}
	/**
	 * Return event type directory permalink.
	 *
	 * @since 2.7.0
	 *
	 * @param string $event_type Optional. Event type. Defaults to current event type.
	 * @return string Event type directory URL on success, an empty string on failure.
	 */
	function sz_get_event_type_directory_permalink( $event_type = '' ) {

		if ( $event_type ) {
			$_event_type = $event_type;
		} else {
			// Fall back on the current event type.
			$_event_type = sz_get_current_event_directory_type();
		}

		$type = sz_events_get_event_type_object( $_event_type );

		// Bail when member type is not found or has no directory.
		if ( ! $type || ! $type->has_directory ) {
			return '';
		}

		/**
		 * Filters the event type directory permalink.
		 *
		 * @since 2.7.0
		 *
		 * @param string $value       Event type directory permalink.
		 * @param object $type        Event type object.
		 * @param string $member_type Event type name, as passed to the function.
		 */
		return apply_filters( 'sz_get_event_type_directory_permalink', trailingslashit( sz_get_events_directory_permalink() . sz_get_events_event_type_base() . '/' . $type->directory_slug ), $type, $event_type );
	}

/**
 * Output event type directory link.
 *
 * @since 2.7.0
 *
 * @param string $event_type Unique event type identifier as used in sz_events_register_event_type().
 */
function sz_event_type_directory_link( $event_type = '' ) {
	echo sz_get_event_type_directory_link( $event_type );
}
	/**
	 * Return event type directory link.
	 *
	 * @since 2.7.0
	 *
	 * @param string $event_type Unique event type identifier as used in sz_events_register_event_type().
	 * @return string
	 */
	function sz_get_event_type_directory_link( $event_type = '' ) {
		if ( empty( $event_type ) ) {
			return '';
		}

		return sprintf( '<a href="%s">%s</a>', esc_url( sz_get_event_type_directory_permalink( $event_type ) ), sz_events_get_event_type_object( $event_type )->labels['name'] );
	}

/**
 * Output a comma-delimited list of event types.
 *
 * @since 2.7.0
 * @see   sz_get_event_type_list() for parameter documentation.
 */
function sz_event_type_list( $event_id = 0, $r = array() ) {
	echo sz_get_event_type_list( $event_id, $r );
}
	/**
	 * Return a comma-delimited list of event types.
	 *
	 * @since 2.7.0
	 *
	 * @param int $event_id Event ID. Defaults to current event ID if on a event page.
	 * @param array|string $r {
	 *     Array of parameters. All items are optional.
	 *     @type string $parent_element Element to wrap around the list. Defaults to 'p'.
	 *     @type array  $parent_attr    Element attributes for parent element. Defaults to
	 *                                  array( 'class' => 'sz-event-type-list' ).
	 *     @type string $label          Label to add before the list. Defaults to 'Event Types:'.
	 *     @type string $label_element  Element to wrap around the label. Defaults to 'strong'.
	 *     @type array  $label_attr     Element attributes for label element. Defaults to array().
	 *     @type bool   $show_all       Whether to show all registered event types. Defaults to 'false'. If
	 *                                 'false', only shows event types with the 'show_in_list' parameter set to
	 *                                  true. See sz_events_register_event_type() for more info.
	 * }
	 * @return string
	 */
	function sz_get_event_type_list( $event_id = 0, $r = array() ) {
		if ( empty( $event_id ) ) {
			$event_id = sz_get_current_event_id();
		}

		$r = sz_parse_args( $r, array(
			'parent_element' => 'p',
			'parent_attr'    => array(
				 'class' => 'sz-event-type-list',
			),
			'label'          => __( 'Event Types:', 'sportszone' ),
			'label_element'  => 'strong',
			'label_attr'     => array(),
			'show_all'       => false,
		), 'event_type_list' );

		$retval = '';

		if ( $types = sz_events_get_event_type( $event_id, false ) ) {
			// Make sure we can show the type in the list.
			if ( false === $r['show_all'] ) {
				$types = array_intersect( sz_events_get_event_types( array( 'show_in_list' => true ) ), $types );
				if ( empty( $types ) ) {
					return $retval;
				}
			}

			$before = $after = $label = '';

			// Render parent element.
			if ( ! empty( $r['parent_element'] ) ) {
				$parent_elem = new SZ_Core_HTML_Element( array(
					'element' => $r['parent_element'],
					'attr'    => $r['parent_attr']
				) );

				// Set before and after.
				$before = $parent_elem->get( 'open_tag' );
				$after  = $parent_elem->get( 'close_tag' );
			}

			// Render label element.
			if ( ! empty( $r['label_element'] ) ) {
				$label = new SZ_Core_HTML_Element( array(
					'element'    => $r['label_element'],
					'attr'       => $r['label_attr'],
					'inner_html' => esc_html( $r['label'] )
				) );
				$label = $label->contents() . ' ';

			// No element, just the label.
			} else {
				$label = esc_html( $r['label'] );
			}

			// Comma-delimit each type into the event type directory link.
			$label .= implode( ', ', array_map( 'sz_get_event_type_directory_link', $types ) );

			// Retval time!
			$retval = $before . $label . $after;
		}

		return $retval;
	}

/**
 * Start the Events Template Loop.
 *
 * @since 1.0.0
 * @since 2.6.0 Added `$event_type`, `$event_type__in`, and `$event_type__not_in` parameters.
 * @since 2.7.0 Added `$update_admin_cache` parameter.
 *
 * @param array|string $args {
 *     Array of parameters. All items are optional.
 *     @type string       $type               Shorthand for certain orderby/order combinations. 'newest', 'active',
 *                                            'popular', 'alphabetical', 'random'. When present, will override
 *                                            orderby and order params. Default: null.
 *     @type string       $order              Sort order. 'ASC' or 'DESC'. Default: 'DESC'.
 *     @type string       $orderby            Property to sort by. 'date_created', 'last_activity',
 *                                            'total_member_count', 'name', 'random'. Default: 'last_activity'.
 *     @type int          $page               Page offset of results to return. Default: 1 (first page of results).
 *     @type int          $per_page           Number of items to return per page of results. Default: 20.
 *     @type int          $max                Does NOT affect query. May change the reported number of total events
 *                                            found, but not the actual number of found events. Default: false.
 *     @type bool         $show_hidden        Whether to include hidden events in results. Default: false.
 *     @type string       $page_arg           Query argument used for pagination. Default: 'grpage'.
 *     @type int          $user_id            If provided, results will be limited to events of which the specified
 *                                            user is a member. Default: value of sz_displayed_user_id().
 *     @type string       $slug               If provided, only the event with the matching slug will be returned.
 *                                            Default: false.
 *     @type string       $search_terms       If provided, only events whose names or descriptions match the search
 *                                            terms will be returned. Default: value of `$_REQUEST['events_search']` or
 *                                            `$_REQUEST['s']`, if present. Otherwise false.
 *     @type array|string $event_type         Array or comma-separated list of event types to limit results to.
 *     @type array|string $event_type__in     Array or comma-separated list of event types to limit results to.
 *     @type array|string $event_type__not_in Array or comma-separated list of event types that will be
 *                                            excluded from results.
 *     @type array        $meta_query         An array of meta_query conditions.
 *                                            See {@link WP_Meta_Query::queries} for description.
 *     @type array|string $include            Array or comma-separated list of event IDs. Results will be limited
 *                                            to events within the list. Default: false.
 *     @type array|string $exclude            Array or comma-separated list of event IDs. Results will exclude
 *                                            the listed events. Default: false.
 *     @type array|string $parent_id          Array or comma-separated list of event IDs. Results will include only
 *                                            child events of the listed events. Default: null.
 *     @type bool         $update_meta_cache  Whether to fetch eventmeta for queried events. Default: true.
 *     @type bool         $update_admin_cache Whether to pre-fetch event admins for queried events.
 *                                            Defaults to true when on a event directory, where this
 *                                            information is needed in the loop. Otherwise false.
 * }
 * @return bool True if there are events to display that match the params
 */
function sz_has_events( $args = '' ) {
	// TODO: Add ability to do meta query when called through ajax
	global $events_template;

	/*
	 * Defaults based on the current page & overridden by parsed $args
	 */
	$slug         = false;
	$type         = '';
	$search_terms = false;
	$loc_country  = false;
	$loc_province = false;
	$loc_city 	  = false;

	// When looking your own events, check for two action variables.
	if ( sz_is_current_action( 'my-events' ) ) {
		if ( sz_is_action_variable( 'most-popular', 0 ) ) {
			$type = 'popular';
		} elseif ( sz_is_action_variable( 'alphabetically', 0 ) ) {
			$type = 'alphabetical';
		}

	// When looking at invites, set type to invites.
	} elseif ( sz_is_current_action( 'invites' ) ) {
		$type = 'invites';

	// When looking at a single event, set the type and slug.
	} elseif ( sz_get_current_event_slug() ) {
		$type = 'single-event';
		$slug = sz_get_current_event_slug();
	}

	$event_type = sz_get_current_event_directory_type();
	if ( ! $event_type && ! empty( $_GET['event_type'] ) ) {
		if ( is_array( $_GET['event_type'] ) ) {
			$event_type = $_GET['event_type'];
		} else {
			// Can be a comma-separated list.
			$event_type = explode( ',', $_GET['event_type'] );
		}
	}

	// Default search string (too soon to escape here).
	$search_query_arg = sz_core_get_component_search_query_arg( 'events' );
	if ( ! empty( $_REQUEST[ $search_query_arg ] ) ) {
		$search_terms = stripslashes( $_REQUEST[ $search_query_arg ] );
	} elseif ( ! empty( $_REQUEST['event-filter-box'] ) ) {
		$search_terms = $_REQUEST['event-filter-box'];
	} elseif ( !empty( $_REQUEST['s'] ) ) {
		$search_terms = $_REQUEST['s'];
	}
	
	
	
	
	
	// Parse defaults and requested arguments.
	$r = sz_parse_args( $args, array(
		'type'               => $type,
		'order'              => 'DESC',
		'orderby'            => 'last_activity',
		'page'               => 1,
		'per_page'           => 20,
		'max'                => false,
		'show_hidden'        => false,
		'page_arg'           => 'grpage',
		'user_id'            => sz_displayed_user_id(),
		'slug'               => $slug,
		'search_terms'       => $search_terms,
		'event_type'         => $event_type,
		'event_type__in'     => '',
		'event_type__not_in' => '',
		'meta_query'         => false,
		'include'            => false,
		'exclude'            => false,
		'parent_id'          => null,
		'update_meta_cache'  => true,
		'update_admin_cache' => sz_is_events_directory() || sz_is_user_events(),
	), 'has_events' );
	
	if ( isset($_REQUEST['loc_country']) && !empty( $_REQUEST['loc_country'] ) ) {
		$loc_country = $_REQUEST['loc_country'];
		
		$r['meta_query'][] = array(
			'key'     => 'sz_event_country',
			'value'   => $loc_country,
			'compare' => 'LIKE',
		);
	}
	if ( isset($_REQUEST['loc_province']) &&  !empty( $_REQUEST['loc_province'] ) ) {
		$loc_province = $_REQUEST['loc_province'];
		$r['meta_query'][] = array(
			'key'     => 'sz_event_province',
			'value'   => $loc_province,
			'compare' => 'LIKE',
		);
	}
	if ( isset($_REQUEST['loc_city']) &&  !empty( $_REQUEST['loc_city'] ) ) {
		$loc_city = $_REQUEST['loc_city'];
		$r['meta_query'][] = array(
			'key'     => 'sz_event_city',
			'value'   => $loc_city,
			'compare' => 'LIKE',
		);
	}
	
	// Setup the Events template global.
	$events_template = new SZ_Events_Template( array(
		'type'               => $r['type'],
		'order'              => $r['order'],
		'orderby'            => $r['orderby'],
		'page'               => (int) $r['page'],
		'per_page'           => (int) $r['per_page'],
		'max'                => (int) $r['max'],
		'show_hidden'        => $r['show_hidden'],
		'page_arg'           => $r['page_arg'],
		'user_id'            => (int) $r['user_id'],
		'slug'               => $r['slug'],
		'search_terms'       => $r['search_terms'],
		'event_type'         => $r['event_type'],
		'event_type__in'     => $r['event_type__in'],
		'event_type__not_in' => $r['event_type__not_in'],
		'meta_query'         => $r['meta_query'],
		'include'            => $r['include'],
		'exclude'            => $r['exclude'],
		'parent_id'          => $r['parent_id'],
		'update_meta_cache'  => (bool) $r['update_meta_cache'],
		'update_admin_cache' => (bool) $r['update_admin_cache'],
	) );

	/**
	 * Filters whether or not there are events to iterate over for the events loop.
	 *
	 * @since 1.1.0
	 *
	 * @param bool               $value           Whether or not there are events to iterate over.
	 * @param SZ_Events_Template $events_template SZ_Events_Template object based on parsed arguments.
	 * @param array              $r               Array of parsed arguments for the query.
	 */
	return apply_filters( 'sz_has_events', $events_template->has_events(), $events_template, $r );
}

/**
 * Check whether there are more events to iterate over.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function sz_events() {
	global $events_template;
	return $events_template->events();
}

/**
 * Set up the current event inside the loop.
 *
 * @since 1.0.0
 *
 * @return object
 */
function sz_the_event() {
	global $events_template;
	return $events_template->the_event();
}

/**
 * Is the event accessible to the currently logged-in user?
 * Despite the name of the function, it has historically checked
 * whether a user has access to a event.
 * In BP 2.9, a property was added to the SZ_Events_Event class,
 * `is_visible`, that describes whether a user can know the event exists.
 * If you wish to check that property, use the check:
 * sz_current_user_can( 'events_see_event' ).
 *
 * @since 1.0.0
 *
 * @param SZ_Events_Event|null $event Optional. Event object. Default: current event in loop.
 * @return bool
 */
function sz_event_is_visible( $event = null ) {
	global $events_template;

	if ( sz_current_user_can( 'sz_moderate' ) ) {
		return true;
	}

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	return sz_current_user_can( 'events_access_event', array( 'event_id' => $event->id ) );
}

/**
 * Output the ID of the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object. Default: current event in loop.
 */
function sz_event_id( $event = false ) {
	echo sz_get_event_id( $event );
}
	/**
	 * Get the ID of the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return int
	 */
	function sz_get_event_id( $event = false ) {
		global $events_template;
		
		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}
		if( empty($event)) return;
		/**
		 * Filters the ID of the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param int    $id    ID of the current event in the loop.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_id', $event->id, $event );
	}

/**
 * Output the row class of the current event in the loop.
 *
 * @since 1.7.0
 *
 * @param array $classes Array of custom classes.
 */
function sz_event_class( $classes = array() ) {
	echo sz_get_event_class( $classes );
}
	/**
	 * Get the row class of the current event in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param array $classes Array of custom classes.
	 * @return string Row class of the event.
	 */
	function sz_get_event_class( $classes = array() ) {
		global $events_template;

		// Add even/odd classes, but only if there's more than 1 event.
		if ( $events_template->event_count > 1 ) {
			$pos_in_loop = (int) $events_template->current_event;
			$classes[]   = ( $pos_in_loop % 2 ) ? 'even' : 'odd';

		// If we've only one event in the loop, don't bother with odd and even.
		} else {
			$classes[] = 'sz-single-event';
		}

		// Event type - public, private, hidden.
		$classes[] = sanitize_key( $events_template->event->status );

		// Add current event types.
		if ( $event_types = sz_events_get_event_type( sz_get_event_id(), false ) ) {
			foreach ( $event_types as $event_type ) {
				$classes[] = sprintf( 'event-type-%s', esc_attr( $event_type ) );
			}
		}

		// User's event role.
		if ( sz_is_user_active() ) {

			// Admin.
			if ( sz_event_is_admin() ) {
				$classes[] = 'is-admin';
			}

			// Moderator.
			if ( sz_event_is_mod() ) {
				$classes[] = 'is-mod';
			}

			// Member.
			if ( sz_event_is_member() ) {
				$classes[] = 'is-member';
			}
		}


		/**
		 * Filters classes that will be applied to row class of the current event in the loop.
		 *
		 * @since 1.7.0
		 *
		 * @param array $classes Array of determined classes for the row.
		 */
		$classes = apply_filters( 'sz_get_event_class', $classes );
		$classes = array_merge( $classes, array() );
		$retval = 'class="' . join( ' ', $classes ) . '"';

		return $retval;
	}

/**
 * Output the name of the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_name( $event = false ) {
	echo sz_get_event_name( $event );
}
	/**
	 * Get the name of the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_name( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the name of the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $name  Name of the current event in the loop.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_name', $event->name, $event );
	}

/**
 * Output the type of the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_type( $event = false ) {
	echo sz_get_event_type( $event );
}

/**
 * Get the type of the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 * @return string
 */
function sz_get_event_type( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( 'public' == $event->status ) {
		$type = __( "Public Event", 'sportszone' );
	} elseif ( 'hidden' == $event->status ) {
		$type = __( "Hidden Event", 'sportszone' );
	} elseif ( 'private' == $event->status ) {
		$type = __( "Private Event", 'sportszone' );
	} else {
		$type = ucwords( $event->status ) . ' ' . __( 'Event', 'sportszone' );
	}

	/**
	 * Filters the type for the current event in the loop.
	 *
	 * @since 1.0.0
	 * @since 2.5.0 Added the `$event` parameter.
	 *
	 * @param string $type  Type for the current event in the loop.
	 * @param object $event Event object.
	 */
	return apply_filters( 'sz_get_event_type', $type, $event );
}
/**
 * Output the status of the current event in the loop.
 *
 * @since 1.1.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_status( $event = false ) {
	echo sz_get_event_status( $event );
}
	/**
	 * Get the status of the current event in the loop.
	 *
	 * @since 1.1.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_status( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the status of the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $status Status of the current event in the loop.
		 * @param object $event  Event object.
		 */
		return apply_filters( 'sz_get_event_status', $event->status, $event );
	}

/**
 * Output the event avatar while in the events loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *      See {@link sz_get_event_avatar()} for description of arguments.
 * }
 */
function sz_event_avatar( $args = '' ) {
	echo sz_get_event_avatar( $args );
}
	/**
	 * Get a event's avatar.
	 *
	 * @since 1.0.0
	 *
	 * @see sz_core_fetch_avatar() For a description of arguments and return values.
	 *
	 * @param array|string $args {
	 *     Arguments are listed here with an explanation of their defaults.
	 *     For more information about the arguments, see {@link sz_core_fetch_avatar()}.
	 *
	 *     @type string   $alt     Default: 'Event logo of [event name]'.
	 *     @type string   $class   Default: 'avatar'.
	 *     @type string   $type    Default: 'full'.
	 *     @type int|bool $width   Default: false.
	 *     @type int|bool $height  Default: false.
	 *     @type bool     $id      Passed to `$css_id` parameter.
	 * }
	 * @return string Event avatar string.
	 */
	function sz_get_event_avatar( $args = '' ) {
		global $events_template;


		// Parse the arguments.
		$r = sz_parse_args( $args, array(
			'type'   => 'full',
			'width'  => false,
			'height' => false,
			'class'  => 'avatar',
			'id'     => false,
			'alt'    => sprintf( __( 'Event logo of %s', 'sportszone' ), $events_template->event->name )
		) );

		// Fetch the avatar from the folder.
		$avatar = sz_core_fetch_avatar( array(
			'item_id'    => $events_template->event->id,
			'avatar_dir' => 'event-avatars',
			'object'     => 'event',
			'type'       => $r['type'],
			'alt'        => $r['alt'],
			'css_id'     => $r['id'],
			'class'      => $r['class'],
			'width'      => $r['width'],
			'height'     => $r['height'],
		) );

		// If No avatar found, provide some backwards compatibility.
		if ( empty( $avatar ) ) {
			$avatar = '<img src="' . esc_url( $events_template->event->avatar_thumb ) . '" class="avatar" alt="' . esc_attr( $events_template->event->name ) . '" />';
		}

		/**
		 * Filters the event avatar while in the events loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $avatar HTML image element holding the event avatar.
		 * @param array  $r      Array of parsed arguments for the event avatar.
		 */
		return apply_filters( 'sz_get_event_avatar', $avatar, $r );
	}

/**
 * Output the event avatar thumbnail while in the events loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_avatar_thumb( $event = false ) {
	echo sz_get_event_avatar_thumb( $event );
}
	/**
	 * Return the event avatar thumbnail while in the events loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_avatar_thumb( $event = false ) {
		return sz_get_event_avatar( array(
			'type' => 'thumb',
			'id'   => ! empty( $event->id ) ? $event->id : false
		) );
	}









/*--------------------------------*/









/**
 * Output the event cover_image while in the events loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *      See {@link sz_get_event_cover_image()} for description of arguments.
 * }
 */
function sz_event_cover_image( $args = '' ) {
	echo sz_get_event_cover_image( $args );
}
	/**
	 * Get a event's cover_image.
	 *
	 * @since 1.0.0
	 *
	 * @see sz_core_fetch_cover_image() For a description of arguments and return values.
	 *
	 * @param array|string $args {
	 *     Arguments are listed here with an explanation of their defaults.
	 *     For more information about the arguments, see {@link sz_core_fetch_cover_image()}.
	 *
	 *     @type string   $alt     Default: 'Event logo of [event name]'.
	 *     @type string   $class   Default: 'avatar'.
	 *     @type string   $type    Default: 'full'.
	 *     @type int|bool $width   Default: false.
	 *     @type int|bool $height  Default: false.
	 *     @type bool     $id      Passed to `$css_id` parameter.
	 * }
	 * @return string Event avatar string.
	 */
	function sz_get_event_cover_image( $args = '' ) {
		global $events_template;
		// Bail if avatars are turned off.
		//var_dump(sportszone()->cover_image->show_cover_images);
		/*if ( sz_disable_event_cover_image_uploads() || ! sportszone()->cover_image->show_cover_images ) {
			return false;
		}*/

		// Parse the arguments.
		$r = sz_parse_args( $args, array(
			'type'   => 'full',
			'width'  => false,
			'height' => false,
			'class'  => 'cover-image',
			'id'     => false,
			'alt'    => sprintf( __( 'Event cover of %s', 'sportszone' ), $events_template->event->name )
		) );

		// Fetch the cover_image from the folder.
		$cover_image = sz_core_fetch_cover_image( array(
			'item_id'    => $events_template->event->id,
			'cover_image_dir' => 'event-cover-images',
			'object'     => 'event',
			'type'       => $r['type'],
			'alt'        => $r['alt'],
			'css_id'     => $r['id'],
			'class'      => $r['class'],
			'width'      => $r['width'],
			'height'     => $r['height'],
		) );

		// If No avatar found, provide some backwards compatibility.
		if ( empty( $cover_image ) ) {
			$cover_image = '<img src="' . esc_url( $events_template->event->cover_image_thumb ) . '" class="cover-image" alt="' . esc_attr( $events_template->event->name ) . '" />';
		}

		/**
		 * Filters the event cover_image while in the events loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $avatar HTML image element holding the event cover_image.
		 * @param array  $r      Array of parsed arguments for the event cover_image.
		 */
		return apply_filters( 'sz_get_event_cover_image', $cover_image, $r );
	}

/**
 * Output the event cover_image thumbnail while in the events loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_cover_image_thumb( $event = false ) {
	echo sz_get_event_cover_image_thumb( $event );
}
	/**
	 * Return the event cover_image thumbnail while in the events loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_cover_image_thumb( $event = false ) {
		return sz_get_event_cover_image( array(
			'type' => 'thumb',
			'id'   => ! empty( $event->id ) ? $event->id : false
		) );
	}



/**
 * Return whether a event has an avatar.
 *
 * @since 1.1.0
 *
 * @param int|bool $event_id Event ID to check.
 * @return boolean
 */
function sz_get_event_has_cover_image( $event_id = false ) {

	if ( false === $event_id ) {
		$event_id = sz_get_current_event_id();
	}

	$cover_image_args = array(
		'item_id' => $event_id,
		'object'  => 'event',
		'no_grav' => true,
		'html'    => false,
		'type'    => 'thumb',
	);

	$event_cover_image = sz_core_fetch_cover_image( $cover_image_args ); 
	if ( sz_core_cover_image_default( 'local', $cover_image_args ) === $event_cover_image ) {
		return false;
	}

	return true;
}






/*--------------------------------*/
	
	
	



/**
 * Output the miniature event avatar thumbnail while in the events loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_avatar_mini( $event = false ) {
	echo sz_get_event_avatar_mini( $event );
}
	/**
	 * Return the miniature event avatar thumbnail while in the events loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_avatar_mini( $event = false ) {
		return sz_get_event_avatar( array(
			'type'   => 'thumb',
			'width'  => 30,
			'height' => 30,
			'id'     => ! empty( $event->id ) ? $event->id : false
		) );
	}

/** Event cover image *********************************************************/

/**
 * Should we use the event's cover image header.
 *
 * @since 2.4.0
 *
 * @return bool True if the displayed user has a cover image,
 *              False otherwise
 */
function sz_event_use_cover_image_header() {
	return (bool) sz_is_active( 'events', 'cover_image' ) && ! sz_disable_event_cover_image_uploads() && sz_attachments_is_wp_version_supported();
}

/**
 * Output the 'last active' string for the current event in the loop.
 *
 * @since 1.0.0
 * @since 2.7.0 Added $args as a parameter.
 *
 * @param object|bool  $event Optional. Event object. Default: current event in loop.
 * @param array|string $args Optional. {@see sz_get_event_last_active()}.
 */
function sz_event_last_active( $event = false, $args = array() ) {
	echo sz_get_event_last_active( $event, $args );
}
	/**
	 * Return the 'last active' string for the current event in the loop.
	 *
	 * @since 1.0.0
	 * @since 2.7.0 Added $args as a parameter.
	 *
	 * @param object|bool  $event Optional. Event object. Default: current event in loop.
	 * @param array|string $args {
	 *     Array of optional parameters.
	 *
	 *     @type bool $relative Optional. If true, returns relative activity date. eg. active 5 months ago.
	 *                          If false, returns active date value from database. Default: true.
	 * }
	 * @return string
	 */
	function sz_get_event_last_active( $event = false, $args = array() ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		$r = sz_parse_args( $args, array(
			'relative' => true,
		), 'event_last_active' );

		$last_active = $event->last_activity;
		if ( ! $last_active ) {
			$last_active = events_get_eventmeta( $event->id, 'last_activity' );
		}

		// We do not want relative time, so return now.
		// @todo Should the 'sz_get_event_last_active' filter be applied here?
		if ( ! $r['relative'] ) {
			return esc_attr( $last_active );
		}

		if ( empty( $last_active ) ) {
			return __( 'not yet active', 'sportszone' );
		} else {

			/**
			 * Filters the 'last active' string for the current event in the loop.
			 *
			 * @since 1.0.0
			 * @since 2.5.0 Added the `$event` parameter.
			 *
			 * @param string $value Determined last active value for the current event.
			 * @param object $event Event object.
			 */
			return apply_filters( 'sz_get_event_last_active', sz_core_time_since( $last_active ), $event );
		}
	}

/**
 * Output the permalink for the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param SZ_Events_Event|null $event Optional. Event object. Default: current event in loop.
 */
function sz_event_permalink( $event = null ) {
	echo sz_get_event_permalink( $event );
}
	/**
	 * Return the permalink for the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param SZ_Events_Event|null $event Optional. Event object. Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_permalink( $event = null ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the permalink for the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Permalink for the current event in the loop.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_permalink', trailingslashit( sz_get_events_directory_permalink() . sz_get_event_slug( $event ) . '/' ), $event );
	}

/**
 * Output an HTML-formatted link for the current event in the loop.
 *
 * @since 2.9.0
 *
 * @param SZ_Events_Event|null $event Optional. Event object.
 *                                    Default: current event in loop.
 */
function sz_event_link( $event = null ) {
	echo sz_get_event_link( $event );
}
	/**
	 * Return an HTML-formatted link for the current event in the loop.
	 *
	 * @since 2.9.0
	 *
	 * @param SZ_Events_Event|null $event Optional. Event object.
	 *                                    Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_link( $event = null ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		$link = sprintf(
			'<a href="%s" class="sz-event-home-link %s-home-link">%s</a>',
			esc_url( sz_get_event_permalink( $event ) ),
			esc_attr( sz_get_event_slug( $event ) ),
			esc_html( sz_get_event_name( $event ) )
		);

		/**
		 * Filters the HTML-formatted link for the current event in the loop.
		 *
		 * @since 2.9.0
		 *
		 * @param string          $value HTML-formatted link for the
		 *                               current event in the loop.
		 * @param SZ_Events_Event $event The current event object.
		 */
		return apply_filters( 'sz_get_event_link', $link, $event );
	}

/**
 * Output the permalink for the admin section of the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_admin_permalink( $event = false ) {
	echo sz_get_event_admin_permalink( $event );
}
	/**
	 * Return the permalink for the admin section of the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_admin_permalink( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the permalink for the admin section of the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Permalink for the admin section of the current event in the loop.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_admin_permalink', trailingslashit( sz_get_event_permalink( $event ) . 'admin' ), $event );
	}

/**
 * Return the slug for the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_slug( $event = false ) {
	echo sz_get_event_slug( $event );
}
	/**
	 * Return the slug for the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_slug( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the slug for the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $slug  Slug for the current event in the loop.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_slug', $event->slug, $event );
	}

/**
 * Output the description for the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_description( $event = false ) {
	echo sz_get_event_description( $event );
}
	/**
	 * Return the description for the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_description( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the description for the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Description for the current event.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_description', stripslashes( $event->description ), $event );
	}

/**
 * Output the description for the current event in the loop, for use in a textarea.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_description_editable( $event = false ) {
	echo sz_get_event_description_editable( $event );
}
	/**
	 * Return the permalink for the current event in the loop, for use in a textarea.
	 *
	 * 'sz_get_event_description_editable' does not have the formatting
	 * filters that 'sz_get_event_description' has, which makes it
	 * appropriate for "raw" editing.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_description_editable( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the permalink for the current event in the loop, for use in a textarea.
		 *
		 * 'sz_get_event_description_editable' does not have the formatting filters that
		 * 'sz_get_event_description' has, which makes it appropriate for "raw" editing.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $description Description for the current event in the loop.
		 * @param object $event       Event object.
		 */
		return apply_filters( 'sz_get_event_description_editable', $event->description, $event );
	}

/**
 * Output an excerpt of the event description.
 *
 * @since 1.0.0
 *
 * @param object|bool $event  Optional. The event being referenced.
 *                            Defaults to the event currently being
 *                            iterated on in the events loop.
 * @param int         $length Optional. Length of returned string, including ellipsis.
 *                            Default: 225.
 */
function sz_event_description_excerpt( $event = false, $length = 225 ) {
	echo sz_get_event_description_excerpt( $event, $length );
}
	/**
	 * Get an excerpt of a event description.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event  Optional. The event being referenced.
	 *                            Defaults to the event currently being
	 *                            iterated on in the events loop.
	 * @param int         $length Optional. Length of returned string, including ellipsis.
	 *                            Default: 225.
	 * @return string Excerpt.
	 */
	function sz_get_event_description_excerpt( $event = false, $length = 225 ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the excerpt of a event description.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Excerpt of a event description.
		 * @param object $event Object for event whose description is made into an excerpt.
		 */
		return apply_filters( 'sz_get_event_description_excerpt', sz_create_excerpt( $event->description, $length ), $event );
	}

/**
 * Output the status of the current event in the loop.
 *
 * Either 'Public' or 'Private'.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_public_status( $event = false ) {
	echo sz_get_event_public_status( $event );
}
	/**
	 * Return the status of the current event in the loop.
	 *
	 * Either 'Public' or 'Private'.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_public_status( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		if ( $event->is_public ) {
			return __( 'Public', 'sportszone' );
		} else {
			return __( 'Private', 'sportszone' );
		}
	}

/**
 * Output whether the current event in the loop is public.
 *
 * No longer used in SportsZone.
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_is_public( $event = false ) {
	echo sz_get_event_is_public( $event );
}
	/**
	 * Return whether the current event in the loop is public.
	 *
	 * No longer used in SportsZone.
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return mixed
	 */
	function sz_get_event_is_public( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters whether the current event in the loop is public.
		 *
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param bool   $public True if the event is public.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_is_public', $event->is_public, $event );
	}

/**
 * Output the created date of the current event in the loop.
 *
 * @since 1.0.0
 * @since 2.7.0 Added $args as a parameter.
 *
 * @param object|bool  $event Optional. Event object. Default: current event in loop.
 * @param array|string $args  {@see sz_get_event_date_created()}.
 */
function sz_event_date_created( $event = false, $args = array() ) {
	echo sz_get_event_date_created( $event, $args );
}
	/**
	 * Return the created date of the current event in the loop.
	 *
	 * @since 1.0.0
	 * @since 2.7.0 Added $args as a parameter.
	 *
	 * @param object|bool  $event Optional. Event object. Default: current event in loop.
	 * @param array|string $args {
	 *     Array of optional parameters.
	 *
	 *     @type bool $relative Optional. If true, returns relative created date. eg. active 5 months ago.
	 *                          If false, returns created date value from database. Default: true.
	 * }
	 * @return string
	 */
	function sz_get_event_date_created( $event = false, $args = array() ) {
		global $events_template;

		$r = sz_parse_args( $args, array(
			'relative' => true,
		), 'event_date_created' );

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		// We do not want relative time, so return now.
		// @todo Should the 'sz_get_event_date_created' filter be applied here?
		if ( ! $r['relative'] ) {
			return esc_attr( $event->date_created );
		}

		/**
		 * Filters the created date of the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Created date for the current event.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_date_created', sz_core_time_since( $event->date_created ), $event );
	}

/**
 * Output the username of the creator of the current event in the loop.
 *
 * @since 1.7.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_creator_username( $event = false ) {
	echo sz_get_event_creator_username( $event );
}
	/**
	 * Return the username of the creator of the current event in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_creator_username( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the username of the creator of the current event in the loop.
		 *
		 * @since 1.7.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Username of the event creator.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_creator_username', sz_core_get_user_displayname( $event->creator_id ), $event );
	}

/**
 * Output the user ID of the creator of the current event in the loop.
 *
 * @since 1.7.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_creator_id( $event = false ) {
	echo sz_get_event_creator_id( $event );
}
	/**
	 * Return the user ID of the creator of the current event in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return int
	 */
	function sz_get_event_creator_id( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the user ID of the creator of the current event in the loop.
		 *
		 * @since 1.7.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param int $creator_id User ID of the event creator.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_creator_id', $event->creator_id, $event );
	}

/**
 * Output the permalink of the creator of the current event in the loop.
 *
 * @since 1.7.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_creator_permalink( $event = false ) {
	echo sz_get_event_creator_permalink( $event );
}
	/**
	 * Return the permalink of the creator of the current event in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_creator_permalink( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the permalink of the creator of the current event in the loop.
		 *
		 * @since 1.7.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Permalink of the event creator.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_creator_permalink', sz_core_get_user_domain( $event->creator_id ), $event );
	}

/**
 * Determine whether a user is the creator of the current event in the loop.
 *
 * @since 1.7.0
 *
 * @param SZ_Events_Event|null $event   Optional. Event object. Default: current event in loop.
 * @param int                  $user_id ID of the user.
 * @return bool
 */
function sz_is_event_creator( $event = null, $user_id = 0 ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( empty( $user_id ) ) {
		$user_id = sz_loggedin_user_id();
	}

	return (bool) ( $event->creator_id == $user_id );
}

/**
 * Output the avatar of the creator of the current event in the loop.
 *
 * @since 1.7.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 * @param array       $args {
 *     Array of optional arguments. See {@link sz_get_event_creator_avatar()}
 *     for description.
 * }
 */
function sz_event_creator_avatar( $event = false, $args = array() ) {
	echo sz_get_event_creator_avatar( $event, $args );
}
	/**
	 * Return the avatar of the creator of the current event in the loop.
	 *
	 * @since 1.7.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @param array       $args {
	 *     Array of optional arguments. See {@link sz_core_fetch_avatar()}
	 *     for detailed description of arguments.
	 *     @type string $type   Default: 'full'.
	 *     @type int    $width  Default: false.
	 *     @type int    $height Default: false.
	 *     @type int    $class  Default: 'avatar'.
	 *     @type string $id     Passed to 'css_id'. Default: false.
	 *     @type string $alt    Alt text. Default: 'Event creator profile
	 *                          photo of [user display name]'.
	 * }
	 * @return string
	 */
	function sz_get_event_creator_avatar( $event = false, $args = array() ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		$r = sz_parse_args( $args, array(
			'type'   => 'full',
			'width'  => false,
			'height' => false,
			'class'  => 'avatar',
			'id'     => false,
			'alt'    => sprintf( __( 'Event creator profile photo of %s', 'sportszone' ),  sz_core_get_user_displayname( $event->creator_id ) )
		), 'event_creator_avatar' );
		extract( $r, EXTR_SKIP );

		$avatar = sz_core_fetch_avatar( array( 'item_id' => $event->creator_id, 'type' => $type, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height, 'alt' => $alt ) );

		/**
		 * Filters the avatar of the creator of the current event in the loop.
		 *
		 * @since 1.7.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $avatar Avatar of the event creator.
		 * @param object $event  Event object.
		 */
		return apply_filters( 'sz_get_event_creator_avatar', $avatar, $event );
	}

/**
 * Determine whether the current user is the admin of the current event.
 *
 * Alias of {@link sz_is_item_admin()}.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function sz_event_is_admin() {
	return sz_is_item_admin();
}

/**
 * Determine whether the current user is a mod of the current event.
 *
 * Alias of {@link sz_is_item_mod()}.
 *
 * @since 1.1.0
 *
 * @return bool
 */
function sz_event_is_mod() {
	return sz_is_item_mod();
}

/**
 * Output markup listing event admins.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_list_admins( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( ! empty( $event->admins ) ) { ?>
		<ul id="event-admins">
			<?php foreach( (array) $event->admins as $admin ) { ?>
				<li>
					<a href="<?php echo sz_core_get_user_domain( $admin->user_id, $admin->user_nicename, $admin->user_login ) ?>" class="sz-tooltip" data-sz-tooltip="<?php printf( ('%s'),  sz_core_get_user_displayname( $admin->user_id ) ); ?>"><?php echo sz_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'email' => $admin->user_email, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $admin->user_id ) ) ) ) ?></a>
				</li>
			<?php } ?>
		</ul>
	<?php } else { ?>
		<span class="activity"><?php _e( 'No Admins', 'sportszone' ) ?></span>
	<?php } ?>
<?php
}

/**
 * Output markup listing event mod.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 */
function sz_event_list_mods( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( ! empty( $event->mods ) ) : ?>

		<ul id="event-mods">

			<?php foreach( (array) $event->mods as $mod ) { ?>

				<li>
					<a href="<?php echo sz_core_get_user_domain( $mod->user_id, $mod->user_nicename, $mod->user_login ) ?>" class="sz-tooltip" data-sz-tooltip="<?php printf( ('%s'),  sz_core_get_user_displayname( $mod->user_id ) ); ?>"><?php echo sz_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'email' => $mod->user_email, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $mod->user_id ) ) ) ) ?></a>
				</li>

			<?php } ?>

		</ul>

<?php else : ?>

		<span class="activity"><?php _e( 'No Mods', 'sportszone' ) ?></span>

<?php endif;

}

/**
 * Return a list of user IDs for a event's admins.
 *
 * @since 1.5.0
 *
 * @param SZ_Events_Event|bool $event     Optional. The event being queried. Defaults
 *                                        to the current event in the loop.
 * @param string               $format    Optional. 'string' to get a comma-separated string,
 *                                        'array' to get an array.
 * @return mixed               $admin_ids A string or array of user IDs.
 */
function sz_event_admin_ids( $event = false, $format = 'string' ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	$admin_ids = array();

	if ( $event->admins ) {
		foreach( $event->admins as $admin ) {
			$admin_ids[] = $admin->user_id;
		}
	}

	if ( 'string' == $format ) {
		$admin_ids = implode( ',', $admin_ids );
	}

	/**
	 * Filters a list of user IDs for a event's admins.
	 *
	 * This filter may return either an array or a comma separated string.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 Added the `$event` parameter.
	 *
	 * @param array|string $admin_ids List of user IDs for a event's admins.
	 * @param object       $event     Event object.
	 */
	return apply_filters( 'sz_event_admin_ids', $admin_ids, $event );
}

/**
 * Return a list of user IDs for a event's moderators.
 *
 * @since 1.5.0
 *
 * @param SZ_Events_Event|bool $event   Optional. The event being queried.
 *                                      Defaults to the current event in the loop.
 * @param string               $format  Optional. 'string' to get a comma-separated string,
 *                                      'array' to get an array.
 * @return mixed               $mod_ids A string or array of user IDs.
 */
function sz_event_mod_ids( $event = false, $format = 'string' ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	$mod_ids = array();

	if ( $event->mods ) {
		foreach( $event->mods as $mod ) {
			$mod_ids[] = $mod->user_id;
		}
	}

	if ( 'string' == $format ) {
		$mod_ids = implode( ',', $mod_ids );
	}

	/**
	 * Filters a list of user IDs for a event's moderators.
	 *
	 * This filter may return either an array or a comma separated string.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 Added the `$event` parameter.
	 *
	 * @param array|string $admin_ids List of user IDs for a event's moderators.
	 * @param object       $event     Event object.
	 */
	return apply_filters( 'sz_event_mod_ids', $mod_ids, $event );
}

/**
 * Output the permalink of the current event's Members page.
 *
 * @since 1.0.0
 */
function sz_event_all_members_permalink() {
	echo sz_get_event_all_members_permalink();
}
	/**
	 * Return the permalink of the Members page of the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_all_members_permalink( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the permalink of the Members page for the current event in the loop.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Permalink of the Members page for the current event.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_all_members_permalink', trailingslashit( sz_get_event_permalink( $event ) . 'members' ), $event );
	}

/**
 * Display a Events search form.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 */
function sz_event_search_form() {

	$action = sz_displayed_user_domain() . sz_get_events_slug() . '/my-events/search/';
	$label = __('Filter Events', 'sportszone');
	$name = 'event-filter-box';

	$search_form_html = '<form action="' . $action . '" id="event-search-form" method="post">
		<label for="'. $name .'" id="'. $name .'-label">'. $label .'</label>
		<input type="search" name="'. $name . '" id="'. $name .'" value="'. $value .'"'.  $disabled .' />

		'. wp_nonce_field( 'event-filter-box', '_wpnonce_event_filter', true, false ) .'
		</form>';

	echo apply_filters( 'sz_event_search_form', $search_form_html );
}

/**
 * Determine whether the displayed user has no events.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 *
 * @return bool True if the displayed user has no events, otherwise false.
 */
function sz_event_show_no_events_message() {
	if ( !events_total_events_for_user( sz_displayed_user_id() ) ) {
		return true;
	}

	return false;
}

/**
 * Determine whether the current page is a event activity permalink.
 *
 * No longer used in SportsZone.
 *
 * @todo Deprecate.
 *
 * @return bool True if this is a event activity permalink, otherwise false.
 */
function sz_event_is_activity_permalink() {

	if ( !sz_is_single_item() || !sz_is_events_component() || !sz_is_current_action( sz_get_activity_slug() ) ) {
		return false;
	}

	return true;
}

/**
 * Output the pagination HTML for a event loop.
 *
 * @since 1.2.0
 */
function sz_events_pagination_links() {
	echo sz_get_events_pagination_links();
}
	/**
	 * Get the pagination HTML for a event loop.
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	function sz_get_events_pagination_links() {
		global $events_template;

		/**
		 * Filters the pagination HTML for a event loop.
		 *
		 * @since 1.2.0
		 *
		 * @param string $pag_links HTML markup for the pagination links.
		 */
		return apply_filters( 'sz_get_events_pagination_links', $events_template->pag_links );
	}

/**
 * Output the "Viewing x-y of z events" pagination message.
 *
 * @since 1.2.0
 */
function sz_events_pagination_count() {
	echo sz_get_events_pagination_count();
}
	/**
	 * Generate the "Viewing x-y of z events" pagination message.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_events_pagination_count() {
		global $events_template;

		$start_num = intval( ( $events_template->pag_page - 1 ) * $events_template->pag_num ) + 1;
		$from_num  = sz_core_number_format( $start_num );
		$to_num    = sz_core_number_format( ( $start_num + ( $events_template->pag_num - 1 ) > $events_template->total_event_count ) ? $events_template->total_event_count : $start_num + ( $events_template->pag_num - 1 ) );
		$total     = sz_core_number_format( $events_template->total_event_count );

		if ( 1 == $events_template->total_event_count ) {
			$message = __( 'Viewing 1 event', 'sportszone' );
		} else {
			$message = sprintf( _n( 'Viewing %1$s - %2$s of %3$s event', 'Viewing %1$s - %2$s of %3$s events', $events_template->total_event_count, 'sportszone' ), $from_num, $to_num, $total );
		}

		/**
		 * Filters the "Viewing x-y of z events" pagination message.
		 *
		 * @since 1.5.0
		 *
		 * @param string $message  "Viewing x-y of z events" text.
		 * @param string $from_num Total amount for the low value in the range.
		 * @param string $to_num   Total amount for the high value in the range.
		 * @param string $total    Total amount of events found.
		 */
		return apply_filters( 'sz_get_events_pagination_count', $message, $from_num, $to_num, $total );
	}

/**
 * Determine whether events auto-join is enabled.
 *
 * "Auto-join" is the toggle that determines whether users are joined to a
 * public event automatically when creating content in that event.
 *
 * @since 1.2.6
 *
 * @return bool
 */
function sz_events_auto_join() {

	/**
	 * Filters whether events auto-join is enabled.
	 *
	 * @since 1.2.6
	 *
	 * @param bool $value Enabled status.
	 */
	return apply_filters( 'sz_events_auto_join', (bool) sportszone()->events->auto_join );
}

/**
 * Output the total member count for a event.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object. Default: current event in loop.
 */
function sz_event_total_members( $event = false ) {
	echo sz_get_event_total_members( $event );
}
	/**
	 * Get the total member count for a event.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return int
	 */
	function sz_get_event_total_members( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the total member count for a event.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param int    $total_member_count Total member count for a event.
		 * @param object $event              Event object.
		 */
		return apply_filters( 'sz_get_event_total_members', $event->total_member_count, $event );
	}

/**
 * Output the "x members" count string for a event.
 *
 * @since 1.2.0
 */
function sz_event_member_count() {
	echo sz_get_event_member_count();
}
	/**
	 * Generate the "x members" count string for a event.
	 *
	 * @since 1.2.0
	 *
	 * @return string
	 */
	function sz_get_event_member_count() {
		global $events_template;

		if ( isset( $events_template->event->total_member_count ) ) {
			$count = (int) $events_template->event->total_member_count;
		} else {
			$count = 0;
		}

		$count_string = sprintf( _n( '%s member', '%s members', $count, 'sportszone' ), sz_core_number_format( $count ) );

		/**
		 * Filters the "x members" count string for a event.
		 *
		 * @since 1.2.0
		 *
		 * @param string $count_string The "x members" count string for a event.
		 */
		return apply_filters( 'sz_get_event_member_count', $count_string );
	}

/**
 * Output the URL of the Forum page of the current event in the loop.
 *
 * @since 1.0.0
 */
function sz_event_forum_permalink() {
	echo sz_get_event_forum_permalink();
}
	/**
	 * Generate the URL of the Forum page of a event.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function sz_get_event_forum_permalink( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the URL of the Forum page of a event.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value URL permalink for the Forum Page.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_forum_permalink', trailingslashit( sz_get_event_permalink( $event ) . 'forum' ), $event );
	}

/**
 * Determine whether forums are enabled for a event.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object. Default: current event in loop.
 * @return bool
 */
function sz_event_is_forum_enabled( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( ! empty( $event->enable_forum ) ) {
		return true;
	}

	return false;
}

/**
 * Output the 'checked' attribute for the event forums settings UI.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object. Default: current event in loop.
 */
function sz_event_show_forum_setting( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( $event->enable_forum ) {
		echo ' checked="checked"';
	}
}

/**
 * Output the 'checked' attribute for a given status in the settings UI.
 *
 * @since 1.0.0
 *
 * @param string      $setting Event status. 'public', 'private', 'hidden'.
 * @param object|bool $event   Optional. Event object. Default: current event in loop.
 */
function sz_event_show_status_setting( $setting, $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( $setting == $event->status ) {
		echo ' checked="checked"';
	}
}

/**
 * Output the 'checked' value, if needed, for a given invite_status on the event create/admin screens
 *
 * @since 1.5.0
 *
 * @param string      $setting The setting you want to check against ('members',
 *                             'mods', or 'admins').
 * @param object|bool $event   Optional. Event object. Default: current event in loop.
 */
function sz_event_show_invite_status_setting( $setting, $event = false ) {
	$event_id = isset( $event->id ) ? $event->id : false;

	$invite_status = sz_event_get_invite_status( $event_id );

	if ( $setting == $invite_status ) {
		echo ' checked="checked"';
	}
}

/**
 * Get the invite status of a event.
 *
 * 'invite_status' became part of SportsZone in BP 1.5. In order to provide
 * backward compatibility with earlier installations, events without a status
 * set will default to 'members', ie all members in a event can send
 * invitations. Filter 'sz_event_invite_status_fallback' to change this
 * fallback behavior.
 *
 * This function can be used either in or out of the loop.
 *
 * @since 1.5.0
 *
 * @param int|bool $event_id Optional. The ID of the event whose status you want to
 *                           check. Default: the displayed event, or the current event
 *                           in the loop.
 * @return bool|string Returns false when no event can be found. Otherwise
 *                     returns the event invite status, from among 'members',
 *                     'mods', and 'admins'.
 */
function sz_event_get_invite_status( $event_id = false ) {
	global $events_template;

	if ( !$event_id ) {
		$sz = sportszone();

		if ( isset( $sz->events->current_event->id ) ) {
			// Default to the current event first.
			$event_id = $sz->events->current_event->id;
		} elseif ( isset( $events_template->event->id ) ) {
			// Then see if we're in the loop.
			$event_id = $events_template->event->id;
		} else {
			return false;
		}
	}

	$invite_status = events_get_eventmeta( $event_id, 'invite_status' );

	// Backward compatibility. When 'invite_status' is not set, fall back to a default value.
	if ( !$invite_status ) {
		$invite_status = apply_filters( 'sz_event_invite_status_fallback', 'members' );
	}

	/**
	 * Filters the invite status of a event.
	 *
	 * Invite status in this case means who from the event can send invites.
	 *
	 * @since 1.5.0
	 *
	 * @param string $invite_status Membership level needed to send an invite.
	 * @param int    $event_id      ID of the event whose status is being checked.
	 */
	return apply_filters( 'sz_event_get_invite_status', $invite_status, $event_id );
}

/**
 * Can a user send invitations in the specified event?
 *
 * @since 1.5.0
 * @since 2.2.0 Added the $user_id parameter.
 *
 * @param int $event_id The event ID to check.
 * @param int $user_id  The user ID to check.
 * @return bool
 */
function sz_events_user_can_send_invites( $event_id = 0, $user_id = 0 ) {
	$can_send_invites = false;
	$invite_status    = false;

	// If $user_id isn't specified, we check against the logged-in user.
	if ( ! $user_id ) {
		$user_id = sz_loggedin_user_id();
	}

	// If $event_id isn't specified, use existing one if available.
	if ( ! $event_id ) {
		$event_id = sz_get_current_event_id();
	}

	if ( $user_id ) {
		$can_send_invites = sz_user_can( $user_id, 'events_send_invitation', array( 'event_id' => $event_id ) );
	}

	/**
	 * Filters whether a user can send invites in a event.
	 *
	 * @since 1.5.0
	 * @since 2.2.0 Added the $user_id parameter.
	 *
	 * @param bool $can_send_invites Whether the user can send invites
	 * @param int  $event_id         The event ID being checked
	 * @param bool $invite_status    The event's current invite status
	 * @param int  $user_id          The user ID being checked
	 */
	return apply_filters( 'sz_events_user_can_send_invites', $can_send_invites, $event_id, $invite_status, $user_id );
}

/**
 * Since SportsZone 1.0, this generated the event settings admin/member screen.
 * As of SportsZone 1.5 (r4489), and because this function outputs HTML, it was moved into /sz-default/events/single/admin.php.
 *
 * @deprecated 1.5
 * @deprecated No longer used.
 * @since 1.0.0
 * @todo Remove in 1.4
 *
 * @param bool $admin_list
 * @param bool $event
 */
function sz_event_admin_memberlist( $admin_list = false, $event = false ) {
	global $events_template;

	_deprecated_function( __FUNCTION__, '1.5', 'No longer used. See /sz-default/events/single/admin.php' );

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}


	if ( $admins = events_get_event_admins( $event->id ) ) : ?>

		<ul id="admins-list" class="item-list<?php if ( !empty( $admin_list ) ) : ?> single-line<?php endif; ?>">

		<?php foreach ( (array) $admins as $admin ) { ?>

			<?php if ( !empty( $admin_list ) ) : ?>

			<li>

				<?php echo sz_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $admin->user_id ) ) ) ) ?>

				<h5>

					<?php echo sz_core_get_userlink( $admin->user_id ); ?>

					<span class="small">
						<a class="button confirm admin-demote-to-member" href="<?php sz_event_member_demote_link($admin->user_id) ?>"><?php _e( 'Demote to Member', 'sportszone' ) ?></a>
					</span>
				</h5>
			</li>

			<?php else : ?>

			<li>

				<?php echo sz_core_fetch_avatar( array( 'item_id' => $admin->user_id, 'type' => 'thumb', 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $admin->user_id ) ) ) ) ?>

				<h5><?php echo sz_core_get_userlink( $admin->user_id ) ?></h5>
				<span class="activity">
					<?php echo sz_core_get_last_activity( strtotime( $admin->date_modified ), __( 'joined %s', 'sportszone') ); ?>
				</span>

				<?php if ( sz_is_active( 'friends' ) ) : ?>

					<div class="action">

						<?php sz_add_friend_button( $admin->user_id ); ?>

					</div>

				<?php endif; ?>

			</li>

			<?php endif;
		} ?>

		</ul>

	<?php else : ?>

		<div id="message" class="info">
			<p><?php _e( 'This event has no administrators', 'sportszone' ); ?></p>
		</div>

	<?php endif;
}

/**
 * Generate the HTML for a list of event moderators.
 *
 * No longer used.
 *
 * @todo Deprecate.
 *
 * @param bool $admin_list
 * @param bool $event
 */
function sz_event_mod_memberlist( $admin_list = false, $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( $event_mods = events_get_event_mods( $event->id ) ) { ?>

		<ul id="mods-list" class="item-list<?php if ( $admin_list ) { ?> single-line<?php } ?>">

		<?php foreach ( (array) $event_mods as $mod ) { ?>

			<?php if ( !empty( $admin_list ) ) { ?>

			<li>

				<?php echo sz_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $mod->user_id ) ) ) ) ?>

				<h5>
					<?php echo sz_core_get_userlink( $mod->user_id ); ?>

					<span class="small">
						<a href="<?php sz_event_member_promote_admin_link( array( 'user_id' => $mod->user_id ) ) ?>" class="button confirm mod-promote-to-admin"><?php _e( 'Promote to Admin', 'sportszone' ); ?></a>
						<a class="button confirm mod-demote-to-member" href="<?php sz_event_member_demote_link($mod->user_id) ?>"><?php _e( 'Demote to Member', 'sportszone' ) ?></a>
					</span>
				</h5>
			</li>

			<?php } else { ?>

			<li>

				<?php echo sz_core_fetch_avatar( array( 'item_id' => $mod->user_id, 'type' => 'thumb', 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $mod->user_id ) ) ) ) ?>

				<h5><?php echo sz_core_get_userlink( $mod->user_id ) ?></h5>

				<span class="activity"><?php echo sz_core_get_last_activity( strtotime( $mod->date_modified ), __( 'joined %s', 'sportszone') ); ?></span>

				<?php if ( sz_is_active( 'friends' ) ) : ?>

					<div class="action">
						<?php sz_add_friend_button( $mod->user_id ) ?>
					</div>

				<?php endif; ?>

			</li>

			<?php } ?>
		<?php } ?>

		</ul>

	<?php } else { ?>

		<div id="message" class="info">
			<p><?php _e( 'This event has no moderators', 'sportszone' ); ?></p>
		</div>

	<?php }
}

/**
 * Determine whether a event has moderators.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object. Default: current event in loop.
 * @return array Info about event admins (user_id + date_modified).
 */
function sz_event_has_moderators( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	/**
	 * Filters whether a event has moderators.
	 *
	 * @since 1.0.0
	 * @since 2.5.0 Added the `$event` parameter.
	 *
	 * @param array  $value Array of user IDs who are a moderator of the provided event.
	 * @param object $event Event object.
	 */
	return apply_filters( 'sz_event_has_moderators', events_get_event_mods( $event->id ), $event );
}

/**
 * Output a URL for promoting a user to moderator.
 *
 * @since 1.1.0
 *
 * @param array|string $args See {@link sz_get_event_member_promote_mod_link()}.
 */
function sz_event_member_promote_mod_link( $args = '' ) {
	echo sz_get_event_member_promote_mod_link( $args );
}
	/**
	 * Generate a URL for promoting a user to moderator.
	 *
	 * @since 1.1.0
	 *
	 * @param array|string $args {
	 *     @type int    $user_id ID of the member to promote. Default:
	 *                           current member in a event member loop.
	 *     @type object $event   Event object. Default: current event.
	 * }
	 * @return string
	 */
	function sz_get_event_member_promote_mod_link( $args = '' ) {
		global $members_template, $events_template;

		$r = sz_parse_args( $args, array(
			'user_id' => $members_template->member->user_id,
			'event'   => &$events_template->event
		), 'event_member_promote_mod_link' );
		extract( $r, EXTR_SKIP );

		/**
		 * Filters a URL for promoting a user to moderator.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value URL to use for promoting a user to moderator.
		 */
		return apply_filters( 'sz_get_event_member_promote_mod_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'admin/manage-members/promote/mod/' . $user_id ), 'events_promote_member' ) );
	}

/**
 * Output a URL for promoting a user to admin.
 *
 * @since 1.1.0
 *
 * @param array|string $args See {@link sz_get_event_member_promote_admin_link()}.
 */
function sz_event_member_promote_admin_link( $args = '' ) {
	echo sz_get_event_member_promote_admin_link( $args );
}
	/**
	 * Generate a URL for promoting a user to admin.
	 *
	 * @since 1.1.0
	 *
	 * @param array|string $args {
	 *     @type int    $user_id ID of the member to promote. Default:
	 *                           current member in a event member loop.
	 *     @type object $event   Event object. Default: current event.
	 * }
	 * @return string
	 */
	function sz_get_event_member_promote_admin_link( $args = '' ) {
		global $members_template, $events_template;

		$r = sz_parse_args( $args, array(
			'user_id' => !empty( $members_template->member->user_id ) ? $members_template->member->user_id : false,
			'event'   => &$events_template->event
		), 'event_member_promote_admin_link' );
		extract( $r, EXTR_SKIP );

		/**
		 * Filters a URL for promoting a user to admin.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value URL to use for promoting a user to admin.
		 */
		return apply_filters( 'sz_get_event_member_promote_admin_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'admin/manage-members/promote/admin/' . $user_id ), 'events_promote_member' ) );
	}

/**
 * Output a URL for demoting a user to member.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the member to demote. Default: current member in
 *                     a member loop.
 */
function sz_event_member_demote_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id ) {
		$user_id = $members_template->member->user_id;
	}

	echo sz_get_event_member_demote_link( $user_id );
}
	/**
	 * Generate a URL for demoting a user to member.
	 *
	 * @since 1.0.0
	 *
	 * @param int         $user_id ID of the member to demote. Default: current
	 *                             member in a member loop.
	 * @param object|bool $event   Optional. Event object. Default: current event.
	 * @return string
	 */
	function sz_get_event_member_demote_link( $user_id = 0, $event = false ) {
		global $members_template, $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		if ( !$user_id ) {
			$user_id = $members_template->member->user_id;
		}

		/**
		 * Filters a URL for demoting a user to member.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value URL to use for demoting a user to member.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_member_demote_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'admin/manage-members/demote/' . $user_id ), 'events_demote_member' ), $event );
	}

/**
 * Output a URL for banning a member from a event.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the member to ban.
 *                     Default: current member in a member loop.
 */
function sz_event_member_ban_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id ) {
		$user_id = $members_template->member->user_id;
	}

	echo sz_get_event_member_ban_link( $user_id );
}
	/**
	 * Generate a URL for banning a member from a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int         $user_id ID of the member to ban.
	 *                             Default: current member in a member loop.
	 * @param object|bool $event   Optional. Event object. Default: current event.
	 * @return string
	 */
	function sz_get_event_member_ban_link( $user_id = 0, $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters a URL for banning a member from a event.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value URL to use for banning a member.
		 */
		return apply_filters( 'sz_get_event_member_ban_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'admin/manage-members/ban/' . $user_id ), 'events_ban_member' ) );
	}

/**
 * Output a URL for unbanning a member from a event.
 *
 * @since 1.0.0
 *
 * @param int $user_id ID of the member to unban.
 *                     Default: current member in a member loop.
 */
function sz_event_member_unban_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id ) {
		$user_id = $members_template->member->user_id;
	}

	echo sz_get_event_member_unban_link( $user_id );
}
	/**
	 * Generate a URL for unbanning a member from a event.
	 *
	 * @since 1.0.0
	 *
	 * @param int         $user_id ID of the member to unban.
	 *                             Default: current member in a member loop.
	 * @param object|bool $event   Optional. Event object. Default: current event.
	 * @return string
	 */
	function sz_get_event_member_unban_link( $user_id = 0, $event = false ) {
		global $members_template, $events_template;

		if ( !$user_id ) {
			$user_id = $members_template->member->user_id;
		}

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters a URL for unbanning a member from a event.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value URL to use for unbanning a member.
		 */
		return apply_filters( 'sz_get_event_member_unban_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'admin/manage-members/unban/' . $user_id ), 'events_unban_member' ) );
	}

/**
 * Output a URL for removing a member from a event.
 *
 * @since 1.2.6
 *
 * @param int $user_id ID of the member to remove.
 *                     Default: current member in a member loop.
 */
function sz_event_member_remove_link( $user_id = 0 ) {
	global $members_template;

	if ( !$user_id ) {
		$user_id = $members_template->member->user_id;
	}

	echo sz_get_event_member_remove_link( $user_id );
}
	/**
	 * Generate a URL for removing a member from a event.
	 *
	 * @since 1.2.6
	 *
	 * @param int         $user_id ID of the member to remove.
	 *                             Default: current member in a member loop.
	 * @param object|bool $event   Optional. Event object. Default: current event.
	 * @return string
	 */
	function sz_get_event_member_remove_link( $user_id = 0, $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters a URL for removing a member from a event.
		 *
		 * @since 1.2.6
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value URL to use for removing a member.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_member_remove_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'admin/manage-members/remove/' . $user_id ), 'events_remove_member' ), $event );
	}

/**
 * HTML admin subnav items for event pages.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in the loop.
 */
function sz_event_admin_tabs( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event = ( $events_template->event ) ? $events_template->event : events_get_current_event();
	}

	$css_id = 'manage-members';

	if ( 'private' == $event->status ) {
		$css_id = 'membership-requests';
	}

	add_filter( "sz_get_options_nav_{$css_id}", 'sz_event_admin_tabs_backcompat', 10, 3 );

	sz_get_options_nav( $event->slug . '_manage' );

	remove_filter( "sz_get_options_nav_{$css_id}", 'sz_event_admin_tabs_backcompat', 10 );
}

/**
 * BackCompat for plugins/themes directly hooking events_admin_tabs
 * without using the Events Extension API.
 *
 * @since 2.2.0
 *
 * @param  string $subnav_output Subnav item output.
 * @param  string $subnav_item   subnav item params.
 * @param  string $selected_item Surrent selected tab.
 * @return string HTML output
 */
function sz_event_admin_tabs_backcompat( $subnav_output = '', $subnav_item = '', $selected_item = '' ) {
	if ( ! has_action( 'events_admin_tabs' ) ) {
		return $subnav_output;
	}

	$event = events_get_current_event();

	ob_start();

	do_action( 'events_admin_tabs', $selected_item, $event->slug );

	$admin_tabs_backcompat = trim( ob_get_contents() );
	ob_end_clean();

	if ( ! empty( $admin_tabs_backcompat ) ) {
		_doing_it_wrong( "do_action( 'events_admin_tabs' )", __( 'This action should not be used directly. Please use the SportsZone Event Extension API to generate Manage tabs.', 'sportszone' ), '2.2.0' );
		$subnav_output .= $admin_tabs_backcompat;
	}

	return $subnav_output;
}

/**
 * Output the event count for the displayed user.
 *
 * @since 1.1.0
 */
function sz_event_total_for_member() {
	echo sz_get_event_total_for_member();
}
	/**
	 * Get the event count for the displayed user.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	function sz_get_event_total_for_member() {

		/**
		 * FIlters the event count for a displayed user.
		 *
		 * @since 1.1.0
		 *
		 * @param int $value Total event count for a displayed user.
		 */
		return apply_filters( 'sz_get_event_total_for_member', SZ_Events_Member::total_event_count() );
	}

/**
 * Output the 'action' attribute for a event form.
 *
 * @since 1.0.0
 *
 * @param string $page Page slug.
 */
function sz_event_form_action( $page ) {
	echo sz_get_event_form_action( $page );
}
	/**
	 * Generate the 'action' attribute for a event form.
	 *
	 * @since 1.0.0
	 *
	 * @param string      $page  Page slug.
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in the loop.
	 * @return string
	 */
	function sz_get_event_form_action( $page, $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the 'action' attribute for a event form.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Action attribute for a event form.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_event_form_action', trailingslashit( sz_get_event_permalink( $event ) . $page ), $event );
	}

/**
 * Output the 'action' attribute for a event admin form.
 *
 * @since 1.0.0
 *
 * @param string|bool $page Optional. Page slug.
 */
function sz_event_admin_form_action( $page = false ) {
	echo sz_get_event_admin_form_action( $page );
}
	/**
	 * Generate the 'action' attribute for a event admin form.
	 *
	 * @since 1.0.0
	 *
	 * @param string|bool $page  Optional. Page slug.
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in the loop.
	 * @return string
	 */
	function sz_get_event_admin_form_action( $page = false, $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		if ( empty( $page ) ) {
			$page = sz_action_variable( 0 );
		}

		/**
		 * Filters the 'action' attribute for a event admin form.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Action attribute for a event admin form.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_event_admin_form_action', trailingslashit( sz_get_event_permalink( $event ) . 'admin/' . $page ), $event );
	}

/**
 * Determine whether the logged-in user has requested membership to a event.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in the loop.
 * @return bool
 */
function sz_event_has_requested_membership( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	if ( events_check_for_membership_request( sz_loggedin_user_id(), $event->id ) ) {
		return true;
	}

	return false;
}

/**
 * Check if current user is member of a event.
 *
 * @since 1.0.0
 *
 * @global object $events_template
 *
 * @param object|bool $event Optional. Event to check is_member.
 *                           Default: current event in the loop.
 * @return bool If user is member of event or not.
 */
function sz_event_is_member( $event = false ) {
	global $events_template;

	// Site admins always have access.
	if ( sz_current_user_can( 'sz_moderate' ) ) {
		return true;
	}

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	/**
	 * Filters whether current user is member of a event.
	 *
	 * @since 1.2.4
	 * @since 2.5.0 Added the `$event` parameter.
	 *
	 * @param bool   $is_member If user is a member of event or not.
	 * @param object $event     Event object.
	 */
	return apply_filters( 'sz_event_is_member', ! empty( $event->is_member ), $event );
}

/**
 * Check whether the current user has an outstanding invite to the current event in the loop.
 *
 * @since 2.1.0
 *
 * @param object|bool $event Optional. Event data object.
 *                           Default: the current event in the events loop.
 * @return bool True if the user has an outstanding invite, otherwise false.
 */
function sz_event_is_invited( $event = false ) {
	global $events_template;

	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	/**
	 * Filters whether current user has an outstanding invite to current event in loop.
	 *
	 * @since 2.1.0
	 * @since 2.5.0 Added the `$event` parameter.
	 *
	 * @param bool   $is_invited If user has an outstanding event invite.
	 * @param object $event      Event object.
	 */
	return apply_filters( 'sz_event_is_invited', ! empty( $event->is_invited ), $event );
}

/**
 * Check if a user is banned from a event.
 *
 * If this function is invoked inside the events template loop, then we check
 * $events_template->event->is_banned instead of using {@link events_is_user_banned()}
 * and making another SQL query.
 *
 * In SportsZone 2.1, to standardize this function, we are defaulting the
 * return value to a boolean.  In previous versions, using this function would
 * return either a string of the integer (0 or 1) or null if a result couldn't
 * be found from the database.  If the logged-in user had the 'sz_moderate'
 * capability, the return value would be boolean false.
 *
 * @since 1.5.0
 *
 * @global SZ_Events_Template $events_template Event template loop object.
 *
 * @param SZ_Events_Event|bool $event   Event to check if user is banned.
 * @param int                  $user_id The user ID to check.
 * @return bool True if user is banned.  False if user isn't banned.
 */
function sz_event_is_user_banned( $event = false, $user_id = 0 ) {
	global $events_template;

	// Site admins always have access.
	if ( sz_current_user_can( 'sz_moderate' ) ) {
		return false;
	}

	// Check events loop first
	// @see SZ_Events_Event::get_event_extras().
	if ( ! empty( $events_template->in_the_loop ) && isset( $events_template->event->is_banned ) ) {
		$retval = $events_template->event->is_banned;

	// Not in loop.
	} else {
		// Default to not banned.
		$retval = false;

		if ( empty( $event ) ) {
			$event = $events_template->event;
		}

		if ( empty( $user_id ) ) {
			$user_id = sz_loggedin_user_id();
		}

		if ( ! empty( $user_id ) && ! empty( $event->id ) ) {
			$retval = events_is_user_banned( $user_id, $event->id );
		}
	}

	/**
	 * Filters whether current user has been banned from current event in loop.
	 *
	 * @since 1.5.0
	 * @since 2.5.0 Added the `$event` parameter.
	 *
	 * @param bool   $is_invited If user has been from current event.
	 * @param object $event      Event object.
	 */
	return (bool) apply_filters( 'sz_event_is_user_banned', $retval, $event );
}

/**
 * Output the URL for accepting an invitation to the current event in the loop.
 *
 * @since 1.0.0
 */
function sz_event_accept_invite_link() {
	echo sz_get_event_accept_invite_link();
}
	/**
	 * Generate the URL for accepting an invitation to a event.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: Current event in the loop.
	 * @return string
	 */
	function sz_get_event_accept_invite_link( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		$sz = sportszone();

		/**
		 * Filters the URL for accepting an invitation to a event.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value URL for accepting an invitation to a event.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_accept_invite_link', wp_nonce_url( trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() . '/invites/accept/' . $event->id ), 'events_accept_invite' ), $event );
	}

/**
 * Output the URL for accepting an invitation to the current event in the loop.
 *
 * @since 1.0.0
 */
function sz_event_reject_invite_link() {
	echo sz_get_event_reject_invite_link();
}
	/**
	 * Generate the URL for rejecting an invitation to a event.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: Current event in the loop.
	 * @return string
	 */
	function sz_get_event_reject_invite_link( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		$sz = sportszone();

		/**
		 * Filters the URL for rejecting an invitation to a event.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value URL for rejecting an invitation to a event.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_reject_invite_link', wp_nonce_url( trailingslashit( sz_loggedin_user_domain() . sz_get_events_slug() . '/invites/reject/' . $event->id ), 'events_reject_invite' ), $event );
	}

/**
 * Output the URL for confirming a request to leave a event.
 *
 * @since 1.0.0
 */
function sz_event_leave_confirm_link() {
	echo sz_get_event_leave_confirm_link();
}
	/**
	 * Generate the URL for confirming a request to leave a event.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: Current event in the loop.
	 * @return string
	 */
	function sz_get_event_leave_confirm_link( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the URL for confirming a request to leave a event.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value URL for confirming a request to leave a event.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_event_leave_confirm_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'leave-event/yes' ), 'events_leave_event' ), $event );
	}

/**
 * Output the URL for rejecting a request to leave a event.
 *
 * @since 1.0.0
 */
function sz_event_leave_reject_link() {
	echo sz_get_event_leave_reject_link();
}
	/**
	 * Generate the URL for rejecting a request to leave a event.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: Current event in the loop.
	 * @return string
	 */
	function sz_get_event_leave_reject_link( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the URL for rejecting a request to leave a event.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value URL for rejecting a request to leave a event.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_get_event_leave_reject_link', sz_get_event_permalink( $event ), $event );
	}

/**
 * Output the 'action' attribute for a event send invite form.
 *
 * @since 1.0.0
 */
function sz_event_send_invite_form_action() {
	echo sz_get_event_send_invite_form_action();
}
	/**
	 * Output the 'action' attribute for a event send invite form.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in the loop.
	 * @return string
	 */
	function sz_get_event_send_invite_form_action( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		/**
		 * Filters the 'action' attribute for a event send invite form.
		 *
		 * @since 1.0.0
		 * @since 2.5.0 Added the `$event` parameter.
		 *
		 * @param string $value Action attribute for a event send invite form.
		 * @param object $event Event object.
		 */
		return apply_filters( 'sz_event_send_invite_form_action', trailingslashit( sz_get_event_permalink( $event ) . 'send-invites/send' ), $event );
	}


/**
 * Output button to join a event.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Single event object.
 */
function sz_event_join_button( $event = false ) {
	echo sz_get_event_join_button( $event );
}
	/**
	 * Return button to join a event.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Single event object.
	 * @return false|string
	 */
	function sz_get_event_join_button( $event = false ) {
		global $events_template;

		// Set event to current loop event if none passed.
		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		// Don't show button if not logged in or previously banned.
		if ( ! is_user_logged_in() || sz_event_is_user_banned( $event ) ) {
			return false;
		}

		// Event creation was not completed or status is unknown.
		if ( empty( $event->status ) ) {
			return false;
		}

		// Already a member.
		if ( ! empty( $event->is_member ) ) {

			// Stop sole admins from abandoning their event.
			$event_admins = events_get_event_admins( $event->id );
			if ( ( 1 == count( $event_admins ) ) && ( sz_loggedin_user_id() === (int) $event_admins[0]->user_id ) ) {
				return false;
			}

			// Setup button attributes.
			$button = array(
				'id'                => 'leave_event',
				'component'         => 'events',
				'must_be_logged_in' => true,
				'block_self'        => false,
				'wrapper_class'     => 'event-button ' . $event->status,
				'wrapper_id'        => 'eventbutton-' . $event->id,
				'link_href'         => wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'leave-event' ), 'events_leave_event' ),
				'link_text'         => __( 'Leave Event', 'sportszone' ),
				'link_class'        => 'event-button leave-event',
			);

		// Not a member.
		} else {

			// Show different buttons based on event status.
			switch ( $event->status ) {
				case 'hidden' :
					return false;

				case 'public':
					$button = array(
						'id'                => 'join_event',
						'component'         => 'events',
						'must_be_logged_in' => true,
						'block_self'        => false,
						'wrapper_class'     => 'event-button ' . $event->status,
						'wrapper_id'        => 'eventbutton-' . $event->id,
						'link_href'         => wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'join' ), 'events_join_event' ),
						'link_text'         => __( 'Join Event', 'sportszone' ),
						'link_class'        => 'event-button join-event',
					);
					break;

				case 'private' :

					// Member has outstanding invitation -
					// show an "Accept Invitation" button.
					if ( $event->is_invited ) {
						$button = array(
							'id'                => 'accept_invite',
							'component'         => 'events',
							'must_be_logged_in' => true,
							'block_self'        => false,
							'wrapper_class'     => 'event-button ' . $event->status,
							'wrapper_id'        => 'eventbutton-' . $event->id,
							'link_href'         => add_query_arg( 'redirect_to', sz_get_event_permalink( $event ), sz_get_event_accept_invite_link( $event ) ),
							'link_text'         => __( 'Accept Invitation', 'sportszone' ),
							'link_class'        => 'event-button accept-invite',
						);

					// Member has requested membership but request is pending -
					// show a "Request Sent" button.
					} elseif ( $event->is_pending ) {
						$button = array(
							'id'                => 'membership_requested',
							'component'         => 'events',
							'must_be_logged_in' => true,
							'block_self'        => false,
							'wrapper_class'     => 'event-button pending ' . $event->status,
							'wrapper_id'        => 'eventbutton-' . $event->id,
							'link_href'         => sz_get_event_permalink( $event ),
							'link_text'         => __( 'Request Sent', 'sportszone' ),
							'link_class'        => 'event-button pending membership-requested',
						);

					// Member has not requested membership yet -
					// show a "Request Membership" button.
					} else {
						$button = array(
							'id'                => 'request_membership',
							'component'         => 'events',
							'must_be_logged_in' => true,
							'block_self'        => false,
							'wrapper_class'     => 'event-button ' . $event->status,
							'wrapper_id'        => 'eventbutton-' . $event->id,
							'link_href'         => wp_nonce_url( trailingslashit( sz_get_event_permalink( $event ) . 'request-membership' ), 'events_request_membership' ),
							'link_text'         => __( 'Request Membership', 'sportszone' ),
							'link_class'        => 'event-button request-membership',
						);
					}

					break;
			}
		}

		/**
		 * Filters the HTML button for joining a event.
		 *
		 * @since 1.2.6
		 * @since 2.4.0 Added $event parameter to filter args.
		 *
		 * @param string $button HTML button for joining a event.
		 * @param object $event SportsZone event object
		 */
		return sz_get_button( apply_filters( 'sz_get_event_join_button', $button, $event ) );
	}

/**
 * Output the Create a Event button.
 *
 * @since 2.0.0
 */
function sz_event_create_button() {
	echo sz_get_event_create_button();
}
	/**
	 * Get the Create a Event button.
	 *
	 * @since 2.0.0
	 *
	 * @return false|string
	 */
	function sz_get_event_create_button() {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! sz_user_can_create_events() ) {
			// TODO: Return message for why user cannot create an event
			return false;
		}
		
		$button_args = array(
			'id'         => 'create_event',
			'component'  => 'events',
			'link_text'  => __( 'Create a Event', 'sportszone' ),
			'link_class' => 'event-create no-ajax',
			'link_href'  => trailingslashit( sz_get_events_directory_permalink() . 'create' ),
			'wrapper'    => false,
			'block_self' => false,
		);

		/**
		 * Filters the HTML button for creating a event.
		 *
		 * @since 2.0.0
		 *
		 * @param string $button HTML button for creating a event.
		 */
		return sz_get_button( apply_filters( 'sz_get_event_create_button', $button_args ) );
	}

/**
 * Output the Create a Event nav item.
 *
 * @since 2.2.0
 */
function sz_event_create_nav_item() {
	echo sz_get_event_create_nav_item();
}

	/**
	 * Get the Create a Event nav item.
	 *
	 * @since 2.2.0
	 *
	 * @return string
	 */
	function sz_get_event_create_nav_item() {
		// Get the create a event button.
		$create_event_button = sz_get_event_create_button();

		// Make sure the button is available.
		if ( empty( $create_event_button ) ) {
			return;
		}

		$output = '<li id="event-create-nav">' . $create_event_button . '</li>';

		/**
		 * Filters the Create a Event nav item.
		 *
		 * @since 2.2.0
		 *
		 * @param string $output HTML output for nav item.
		 */
		return apply_filters( 'sz_get_event_create_nav_item', $output );
	}

/**
 * Checks if a specific theme is still filtering the Events directory title
 * if so, transform the title button into a Events directory nav item.
 *
 * @since 2.2.0
 *
 * @return string|null HTML Output
 */
function sz_event_backcompat_create_nav_item() {
	// Bail if the Events nav item is already used by sz-legacy.
	if ( has_action( 'sz_events_directory_event_filter', 'sz_legacy_theme_event_create_nav', 999 ) ) {
		return;
	}

	// Bail if the theme is not filtering the Events directory title.
	if ( ! has_filter( 'sz_events_directory_header' ) ) {
		return;
	}

	sz_event_create_nav_item();
}
add_action( 'sz_events_directory_event_filter', 'sz_event_backcompat_create_nav_item', 1000 );

/**
 * Prints a message if the event is not visible to the current user (it is a
 * hidden or private event, and the user does not have access).
 *
 * @since 1.0.0
 *
 * @global SZ_Events_Template $events_template Events template object.
 *
 * @param object|null $event Event to get status message for. Optional; defaults to current event.
 */
function sz_event_status_message( $event = null ) {
	global $events_template;

	// Event not passed so look for loop.
	if ( empty( $event ) ) {
		$event =& $events_template->event;
	}

	// Event status is not set (maybe outside of event loop?).
	if ( empty( $event->status ) ) {
		$message = __( 'This event is not currently accessible.', 'sportszone' );

	// Event has a status.
	} else {
		switch( $event->status ) {

			// Private event.
			case 'private' :
				if ( ! sz_event_has_requested_membership( $event ) ) {
					if ( is_user_logged_in() ) {
						if ( sz_event_is_invited( $event ) ) {
							$message = __( 'You must accept your pending invitation before you can access this private event.', 'sportszone' );
						} else {
							$message = __( 'This is a private event and you must request event membership in order to join.', 'sportszone' );
						}
					} else {
						$message = __( 'This is a private event. To join you must be a registered site member and request event membership.', 'sportszone' );
					}
				} else {
					$message = __( 'This is a private event. Your membership request is awaiting approval from the event administrator.', 'sportszone' );
				}

				break;

			// Hidden event.
			case 'hidden' :
			default :
				$message = __( 'This is a hidden event and only invited members can join.', 'sportszone' );
				break;
		}
	}

	/**
	 * Filters a message if the event is not visible to the current user.
	 *
	 * This will be true if it is a hidden or private event, and the user does not have access.
	 *
	 * @since 1.6.0
	 *
	 * @param string $message Message to display to the current user.
	 * @param object $event   Event to get status message for.
	 */
	echo apply_filters( 'sz_event_status_message', $message, $event );
}

/**
 * Output hidden form fields for event.
 *
 * This function is no longer used, but may still be used by older themes.
 *
 * @since 1.0.0
 */
function sz_event_hidden_fields() {
	$query_arg = sz_core_get_component_search_query_arg( 'events' );

	if ( isset( $_REQUEST[ $query_arg ] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . esc_attr( $_REQUEST[ $query_arg ] ) . '" name="search_terms" />';
	}

	if ( isset( $_REQUEST['letter'] ) ) {
		echo '<input type="hidden" id="selected_letter" value="' . esc_attr( $_REQUEST['letter'] ) . '" name="selected_letter" />';
	}

	if ( isset( $_REQUEST['events_search'] ) ) {
		echo '<input type="hidden" id="search_terms" value="' . esc_attr( $_REQUEST['events_search'] ) . '" name="search_terms" />';
	}
}

/**
 * Output the total number of events.
 *
 * @since 1.0.0
 */
function sz_total_event_count() {
	echo sz_get_total_event_count();
}
	/**
	 * Return the total number of events.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	function sz_get_total_event_count() {

		/**
		 * Filters the total number of events.
		 *
		 * @since 1.0.0
		 *
		 * @param int $value Total number of events found.
		 */
		return apply_filters( 'sz_get_total_event_count', events_get_total_event_count() );
	}

/**
 * Output the total number of events a user belongs to.
 *
 * @since 1.0.0
 *
 * @param int $user_id User ID to get event membership count.
 */
function sz_total_event_count_for_user( $user_id = 0 ) {
	echo sz_get_total_event_count_for_user( $user_id );
}
	/**
	 * Return the total number of events a user belongs to.
	 *
	 * Filtered by `sz_core_number_format()` by default
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id User ID to get event membership count.
	 * @return string
	 */
	function sz_get_total_event_count_for_user( $user_id = 0 ) {
		$count = events_total_events_for_user( $user_id );

		/**
		 * Filters the total number of events a user belongs to.
		 *
		 * @since 1.2.0
		 *
		 * @param int $count   Total number of events for the user.
		 * @param int $user_id ID of the user being checked.
		 */
		return apply_filters( 'sz_get_total_event_count_for_user', $count, $user_id );
	}

/* Event Members *************************************************************/

/**
 * Initialize a event member query loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *     An array of optional arguments.
 *     @type int      $event_id           ID of the event whose members are being queried.
 *                                        Default: current event ID.
 *     @type int      $page               Page of results to be queried. Default: 1.
 *     @type int      $per_page           Number of items to return per page of results.
 *                                        Default: 20.
 *     @type int      $max                Optional. Max number of items to return.
 *     @type array    $exclude            Optional. Array of user IDs to exclude.
 *     @type bool|int $exclude_admin_mods True (or 1) to exclude admins and mods from results.
 *                                        Default: 1.
 *     @type bool|int $exclude_banned     True (or 1) to exclude banned users from results.
 *                                        Default: 1.
 *     @type array    $event_role         Optional. Array of event roles to include.
 *     @type string   $type               Optional. Sort order of results. 'last_joined',
 *                                        'first_joined', or any of the $type params available in
 *                                        {@link SZ_User_Query}. Default: 'last_joined'.
 *     @type string   $search_terms       Optional. Search terms to match. Pass an
 *                                        empty string to force-disable search, even in
 *                                        the presence of $_REQUEST['s']. Default: false.
 * }
 *
 * @return bool
 */
function sz_event_has_members( $args = '' ) {
	global $members_template;

	$exclude_admins_mods = 1;

	if ( sz_is_event_members() ) {
		$exclude_admins_mods = 0;
	}

	/*
	 * Use false as the search_terms default so that SZ_User_Query
	 * doesn't add a search clause.
	 */
	$search_terms_default = false;
	$search_query_arg = sz_core_get_component_search_query_arg( 'members' );
	if ( ! empty( $_REQUEST[ $search_query_arg ] ) ) {
		$search_terms_default = stripslashes( $_REQUEST[ $search_query_arg ] );
	}

	$r = sz_parse_args( $args, array(
		'event_id'            => sz_get_current_event_id(),
		'page'                => 1,
		'per_page'            => 20,
		'max'                 => false,
		'exclude'             => false,
		'exclude_admins_mods' => $exclude_admins_mods,
		'exclude_banned'      => 1,
		'event_role'          => false,
		'search_terms'        => $search_terms_default,
		'type'                => 'last_joined',
	), 'event_has_members' );

	/*
	 * If an empty search_terms string has been passed,
	 * the developer is force-disabling search.
	 */
	if ( '' === $r['search_terms'] ) {
		// Set the search_terms to false for SZ_User_Query efficiency.
		$r['search_terms'] = false;
	} elseif ( ! empty( $_REQUEST['s'] ) ) {
		$r['search_terms'] = $_REQUEST['s'];
	}

	$members_template = new SZ_Events_Event_Members_Template( $r );

	/**
	 * Filters whether or not a event member query has members to display.
	 *
	 * @since 1.1.0
	 *
	 * @param bool                             $value            Whether there are members to display.
	 * @param SZ_Events_Event_Members_Template $members_template Object holding the member query results.
	 */
	return apply_filters( 'sz_event_has_members', $members_template->has_members(), $members_template );
}

/**
 * @since 1.0.0
 *
 * @return mixed
 */
function sz_event_members() {
	global $members_template;

	return $members_template->members();
}

/**
 * @since 1.0.0
 *
 * @return mixed
 */
function sz_event_the_member() {
	global $members_template;

	return $members_template->the_member();
}

/**
 * Output the event member avatar while in the events members loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {@see sz_core_fetch_avatar()}.
 */
function sz_event_member_avatar( $args = '' ) {
	echo sz_get_event_member_avatar( $args );
}
	/**
	 * Return the event member avatar while in the events members loop.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $args {@see sz_core_fetch_avatar()}.
	 * @return string
	 */
	function sz_get_event_member_avatar( $args = '' ) {
		global $members_template;

		$r = sz_parse_args( $args, array(
			'item_id' => $members_template->member->user_id,
			'type'    => 'full',
			'email'   => $members_template->member->user_email,
			'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), $members_template->member->display_name )
		) );

		/**
		 * Filters the event member avatar while in the events members loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value HTML markup for event member avatar.
		 * @param array  $r     Parsed args used for the avatar query.
		 */
		return apply_filters( 'sz_get_event_member_avatar', sz_core_fetch_avatar( $r ), $r );
	}

/**
 * Output the event member avatar while in the events members loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {@see sz_core_fetch_avatar()}.
 */
function sz_event_member_avatar_thumb( $args = '' ) {
	echo sz_get_event_member_avatar_thumb( $args );
}
	/**
	 * Return the event member avatar while in the events members loop.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $args {@see sz_core_fetch_avatar()}.
	 * @return string
	 */
	function sz_get_event_member_avatar_thumb( $args = '' ) {
		global $members_template;

		$r = sz_parse_args( $args, array(
			'item_id' => $members_template->member->user_id,
			'type'    => 'thumb',
			'email'   => $members_template->member->user_email,
			'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), $members_template->member->display_name )
		) );

		/**
		 * Filters the event member avatar thumb while in the events members loop.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value HTML markup for event member avatar thumb.
		 * @param array  $r     Parsed args used for the avatar query.
		 */
		return apply_filters( 'sz_get_event_member_avatar_thumb', sz_core_fetch_avatar( $r ), $r );
	}

/**
 * Output the event member avatar while in the events members loop.
 *
 * @since 1.0.0
 *
 * @param int $width  Width of avatar to fetch.
 * @param int $height Height of avatar to fetch.
 */
function sz_event_member_avatar_mini( $width = 30, $height = 30 ) {
	echo sz_get_event_member_avatar_mini( $width, $height );
}
	/**
	 * Output the event member avatar while in the events members loop.
	 *
	 * @since 1.0.0
	 *
	 * @param int $width  Width of avatar to fetch.
	 * @param int $height Height of avatar to fetch.
	 * @return string
	 */
	function sz_get_event_member_avatar_mini( $width = 30, $height = 30 ) {
		global $members_template;

		$r = sz_parse_args( array(), array(
			'item_id' => $members_template->member->user_id,
			'type'    => 'thumb',
			'email'   => $members_template->member->user_email,
			'alt'     => sprintf( __( 'Profile picture of %s', 'sportszone' ), $members_template->member->display_name ),
			'width'   => absint( $width ),
			'height'  => absint( $height )
		) );

		/**
		 * Filters the event member avatar mini while in the events members loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value HTML markup for event member avatar mini.
		 * @param array  $r     Parsed args used for the avatar query.
		 */
		return apply_filters( 'sz_get_event_member_avatar_mini', sz_core_fetch_avatar( $r ), $r );
	}

/**
 * @since 1.0.0
 */
function sz_event_member_name() {
	echo sz_get_event_member_name();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_name() {
		global $members_template;

		/**
		 * Filters the event member display name of the current user in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $display_name Display name of the current user.
		 */
		return apply_filters( 'sz_get_event_member_name', $members_template->member->display_name );
	}

/**
 * @since 1.0.0
 */
function sz_event_member_url() {
	echo sz_get_event_member_url();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_url() {
		global $members_template;

		/**
		 * Filters the event member url for the current user in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value URL for the current user.
		 */
		return apply_filters( 'sz_get_event_member_url', sz_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) );
	}

/**
 * @since 1.0.0
 */
function sz_event_member_link() {
	echo sz_get_event_member_link();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_link() {
		global $members_template;

		/**
		 * Filters the event member HTML link for the current user in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value HTML link for the current user.
		 */
		return apply_filters( 'sz_get_event_member_link', '<a href="' . sz_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) . '">' . $members_template->member->display_name . '</a>' );
	}

/**
 * @since 1.2.0
 */
function sz_event_member_domain() {
	echo sz_get_event_member_domain();
}

	/**
	 * @since 1.2.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_domain() {
		global $members_template;

		/**
		 * Filters the event member domain for the current user in the loop.
		 *
		 * @since 1.2.0
		 *
		 * @param string $value Domain for the current user.
		 */
		return apply_filters( 'sz_get_event_member_domain', sz_core_get_user_domain( $members_template->member->user_id, $members_template->member->user_nicename, $members_template->member->user_login ) );
	}

/**
 * @since 1.2.0
 */
function sz_event_member_is_friend() {
	echo sz_get_event_member_is_friend();
}

	/**
	 * @since 1.2.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_is_friend() {
		global $members_template;

		if ( !isset( $members_template->member->is_friend ) ) {
			$friend_status = 'not_friends';
		} else {
			$friend_status = ( 0 == $members_template->member->is_friend )
				? 'pending'
				: 'is_friend';
		}

		/**
		 * Filters the friendship status between current user and displayed user in event member loop.
		 *
		 * @since 1.2.0
		 *
		 * @param string $friend_status Current status of the friendship.
		 */
		return apply_filters( 'sz_get_event_member_is_friend', $friend_status );
	}

/**
 * @since 1.0.0
 */
function sz_event_member_is_banned() {
	echo sz_get_event_member_is_banned();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_is_banned() {
		global $members_template;

		/**
		 * Filters whether the member is banned from the current event.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $is_banned Whether or not the member is banned.
		 */
		return apply_filters( 'sz_get_event_member_is_banned', $members_template->member->is_banned );
	}

/**
 * @since 1.2.6
 */
function sz_event_member_css_class() {
	global $members_template;

	if ( $members_template->member->is_banned ) {

		/**
		 * Filters the class to add to the HTML if member is banned.
		 *
		 * @since 1.2.6
		 *
		 * @param string $value HTML class to add.
		 */
		echo apply_filters( 'sz_event_member_css_class', 'banned-user' );
	}
}

/**
 * Output the joined date for the current member in the event member loop.
 *
 * @since 1.0.0
 * @since 2.7.0 Added $args as a parameter.
 *
 * @param array|string $args {@see sz_get_event_member_joined_since()}
 * @return string|null
 */
function sz_event_member_joined_since( $args = array() ) {
	echo sz_get_event_member_joined_since( $args );
}
	/**
	 * Return the joined date for the current member in the event member loop.
	 *
	 * @since 1.0.0
	 * @since 2.7.0 Added $args as a parameter.
	 *
	 * @param array|string $args {
	 *     Array of optional parameters.
	 *
	 *     @type bool $relative Optional. If true, returns relative joined date. eg. joined 5 months ago.
	 *                          If false, returns joined date value from database. Default: true.
	 * }
	 * @return string
	 */
	function sz_get_event_member_joined_since( $args = array() ) {
		global $members_template;

		$r = sz_parse_args( $args, array(
			'relative' => true,
		), 'event_member_joined_since' );

		// We do not want relative time, so return now.
		// @todo Should the 'sz_get_event_member_joined_since' filter be applied here?
		if ( ! $r['relative'] ) {
			return esc_attr( $members_template->member->date_modified );
		}

		/**
		 * Filters the joined since time for the current member in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Joined since time.
		 */
		return apply_filters( 'sz_get_event_member_joined_since', sz_core_get_last_activity( $members_template->member->date_modified, __( 'joined %s', 'sportszone') ) );
	}

/**
 * @since 1.0.0
 */
function sz_event_member_id() {
	echo sz_get_event_member_id();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_id() {
		global $members_template;

		/**
		 * Filters the member's user ID for event members loop.
		 *
		 * @since 1.0.0
		 *
		 * @param int $user_id User ID of the member.
		 */
		return apply_filters( 'sz_get_event_member_id', $members_template->member->user_id );
	}

/**
 * @since 1.0.0
 *
 * @return bool
 */
function sz_event_member_needs_pagination() {
	global $members_template;

	if ( $members_template->total_member_count > $members_template->pag_num ) {
		return true;
	}

	return false;
}

/**
 * @since 1.0.0
 */
function sz_event_pag_id() {
	echo sz_get_event_pag_id();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_pag_id() {

		/**
		 * Filters the string to be used as the event pag id.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value Value to use for the pag id.
		 */
		return apply_filters( 'sz_get_event_pag_id', 'pag' );
	}

/**
 * @since 1.0.0
 */
function sz_event_member_pagination() {
	echo sz_get_event_member_pagination();
	wp_nonce_field( 'sz_events_member_list', '_member_pag_nonce' );
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_pagination() {
		global $members_template;

		/**
		 * Filters the HTML markup to be used for event member listing pagination.
		 *
		 * @since 1.0.0
		 *
		 * @param string $pag_links HTML markup for the pagination.
		 */
		return apply_filters( 'sz_get_event_member_pagination', $members_template->pag_links );
	}

/**
 * @since 1.0.0
 */
function sz_event_member_pagination_count() {
	echo sz_get_event_member_pagination_count();
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_member_pagination_count() {
		global $members_template;

		$start_num = intval( ( $members_template->pag_page - 1 ) * $members_template->pag_num ) + 1;
		$from_num  = sz_core_number_format( $start_num );
		$to_num    = sz_core_number_format( ( $start_num + ( $members_template->pag_num - 1 ) > $members_template->total_member_count ) ? $members_template->total_member_count : $start_num + ( $members_template->pag_num - 1 ) );
		$total     = sz_core_number_format( $members_template->total_member_count );

		if ( 1 == $members_template->total_member_count ) {
			$message = __( 'Viewing 1 member', 'sportszone' );
		} else {
			$message = sprintf( _n( 'Viewing %1$s - %2$s of %3$s member', 'Viewing %1$s - %2$s of %3$s members', $members_template->total_member_count, 'sportszone' ), $from_num, $to_num, $total );
		}

		/**
		 * Filters the "Viewing x-y of z members" pagination message.
		 *
		 * @since 1.0.0
		 *
		 * @param string $value    "Viewing x-y of z members" text.
		 * @param string $from_num Total amount for the low value in the range.
		 * @param string $to_num   Total amount for the high value in the range.
		 * @param string $total    Total amount of members found.
		 */
		return apply_filters( 'sz_get_event_member_pagination_count', $message, $from_num, $to_num, $total );
	}

/**
 * @since 1.0.0
 */
function sz_event_member_admin_pagination() {
	echo sz_get_event_member_admin_pagination();
	wp_nonce_field( 'sz_events_member_admin_list', '_member_admin_pag_nonce' );
}

	/**
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	function sz_get_event_member_admin_pagination() {
		global $members_template;

		return $members_template->pag_links;
	}

/**
 * Output the contents of the current event's home page.
 *
 * You should only use this when on a single event page.
 *
 * @since 2.4.0
 */
function sz_events_front_template_part() {
	$located = sz_events_get_front_template();

	if ( false !== $located ) {
		$slug = str_replace( '.php', '', $located );

		/**
		 * Let plugins adding an action to sz_get_template_part get it from here
		 *
		 * @param string $slug Template part slug requested.
		 * @param string $name Template part name requested.
		 */
		do_action( 'get_template_part_' . $slug, $slug, false );

		load_template( $located, true );

	} else if ( sz_is_active( 'activity' ) ) {
		sz_get_template_part( 'events/single/activity' );

	} else if ( sz_is_active( 'members'  ) ) {
		sz_events_members_template_part();
	}

	return $located;
}

/**
 * Locate a custom event front template if it exists.
 *
 * @since 2.4.0
 * @since 2.6.0 Adds the Event Type to the front template hierarchy.
 *
 * @param  SZ_Events_Event|null $event Optional. Falls back to current event if not passed.
 * @return string|bool                 Path to front template on success; boolean false on failure.
 */
function sz_events_get_front_template( $event = null ) {
	if ( ! is_a( $event, 'SZ_Events_Event' ) ) {
		$event = events_get_current_event();
	}

	if ( ! isset( $event->id ) ) {
		return false;
	}

	if ( isset( $event->front_template ) ) {
		return $event->front_template;
	}

	$template_names = array(
		'events/single/front-id-'     . sanitize_file_name( $event->id )     . '.php',
		'events/single/front-slug-'   . sanitize_file_name( $event->slug )   . '.php',
	);

	if ( sz_events_get_event_types() ) {
		$event_type = sz_events_get_event_type( $event->id );
		if ( ! $event_type ) {
			$event_type = 'none';
		}

		$template_names[] = 'events/single/front-event-type-' . sanitize_file_name( $event_type )   . '.php';
	}

	$template_names = array_merge( $template_names, array(
		'events/single/front-status-' . sanitize_file_name( $event->status ) . '.php',
		'events/single/front.php'
	) );

	/**
	 * Filters the hierarchy of event front templates corresponding to a specific event.
	 *
	 * @since 2.4.0
	 * @since 2.5.0 Added the `$event` parameter.
	 *
	 * @param array  $template_names Array of template paths.
	 * @param object $event          Event object.
	 */
	return sz_locate_template( apply_filters( 'sz_events_get_front_template', $template_names, $event ), false, true );
}

/**
 * Output the Event members template
 *
 * @since 2.0.0
 */
function sz_events_members_template_part() {
	?>
	<div class="item-list-tabs" id="subnav" aria-label="<?php esc_attr_e( 'Event secondary navigation', 'sportszone' ); ?>" role="navigation">
		<ul>
			<li class="events-members-search" role="search">
				<?php sz_directory_members_search_form(); ?>
			</li>

			<?php sz_events_members_filter(); ?>
			<?php

			/**
			 * Fires at the end of the event members search unordered list.
			 *
			 * Part of sz_events_members_template_part().
			 *
			 * @since 1.5.0
			 */
			do_action( 'sz_members_directory_member_sub_types' ); ?>

		</ul>
	</div>

	<h2 class="sz-screen-reader-text"><?php
		/* translators: accessibility text */
		_e( 'Members', 'sportszone' );
	?></h2>

	<div id="members-event-list" class="event_members dir-list">

		<?php sz_get_template_part( 'events/single/members' ); ?>

	</div>
	<?php
}

/**
 * Output the Event members filters
 *
 * @since 2.0.0
 */
function sz_events_members_filter() {
	?>
	<li id="event_members-order-select" class="last filter">
		<label for="event_members-order-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
		<select id="event_members-order-by">
			<option value="last_joined"><?php _e( 'Newest', 'sportszone' ); ?></option>
			<option value="first_joined"><?php _e( 'Oldest', 'sportszone' ); ?></option>

			<?php if ( sz_is_active( 'activity' ) ) : ?>
				<option value="event_activity"><?php _e( 'Event Activity', 'sportszone' ); ?></option>
			<?php endif; ?>

			<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

			<?php

			/**
			 * Fires at the end of the Event members filters select input.
			 *
			 * Useful for plugins to add more filter options.
			 *
			 * @since 2.0.0
			 */
			do_action( 'sz_events_members_order_options' ); ?>

		</select>
	</li>
	<?php
}

/*
 * Event Creation Process Template Tags
 */

/**
 * Determine if the current logged in user can create events.
 *
 * @since 1.5.0
 *
 * @return bool True if user can create events. False otherwise.
 */
function sz_user_can_create_events() {

	// Super admin can always create events.
	if ( sz_current_user_can( 'sz_moderate' ) ) {
		return true;
	}

	// Get event creation option, default to 0 (allowed).
	$restricted = (int) sz_get_option( 'sz_restrict_event_creation', 0 );

	// Allow by default.
	$can_create = true;

	// Are regular users restricted?
	if ( $restricted ) {
		$can_create = false;
	}
	$user_id = get_current_user_id();
	$is_admin_of = SZ_Groups_Member::get_is_admin_of( $user_id ); // Get list of all groups user is a admin of
			
	// if user is admin of any groups
	if(is_array($is_admin_of)){
		$accepted_types = array('team', 'club', 'union');
		$can_create = false;
		
		// Loop through each group and check its type
		foreach($is_admin_of['groups'] as $group){
			$type = sz_groups_get_group_type($group->id);
			if(in_array($type, $accepted_types) ) {
				$can_create = true;
			}
		}
		
	}
	/**
	 * Filters if the current logged in user can create events.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $can_create Whether the person can create events.
	 * @param int  $restricted Whether or not event creation is restricted.
	 */
	return apply_filters( 'sz_user_can_create_events', $can_create, $restricted );
}

/**
 * @since 1.0.0
 *
 * @return bool
 */
function sz_event_creation_tabs() {
	$sz = sportszone();

	if ( !is_array( $sz->events->event_creation_steps ) ) {
		return false;
	}

	if ( !sz_get_events_current_create_step() ) {
		$keys = array_keys( $sz->events->event_creation_steps );
		$sz->events->current_create_step = array_shift( $keys );
	}

	$counter = 1;

	foreach ( (array) $sz->events->event_creation_steps as $slug => $step ) {
		$is_enabled = sz_are_previous_event_creation_steps_complete( $slug ); ?>

		<li<?php if ( sz_get_events_current_create_step() == $slug ) : ?> class="current"<?php endif; ?>><?php if ( $is_enabled ) : ?><a href="<?php sz_events_directory_permalink(); ?>create/step/<?php echo $slug ?>/"><?php else: ?><span><?php endif; ?><?php echo $counter ?>. <?php echo $step['name'] ?><?php if ( $is_enabled ) : ?></a><?php else: ?></span><?php endif ?></li><?php
		$counter++;
	}

	unset( $is_enabled );

	/**
	 * Fires at the end of the creation of the event tabs.
	 *
	 * @since 1.0.0
	 */
	do_action( 'events_creation_tabs' );
}

/**
 * @since 1.0.0
 */
function sz_event_creation_stage_title() {
	$sz = sportszone();

	/**
	 * Filters the event creation stage title.
	 *
	 * @since 1.1.0
	 *
	 * @param string $value HTML markup for the event creation stage title.
	 */
	echo apply_filters( 'sz_event_creation_stage_title', '<span>&mdash; ' . $sz->events->event_creation_steps[sz_get_events_current_create_step()]['name'] . '</span>' );
}

/**
 * @since 1.1.0
 */
function sz_event_creation_form_action() {
	echo sz_get_event_creation_form_action();
}

/**
 * @since 1.1.0
 *
 * @return mixed|void
 */
	function sz_get_event_creation_form_action() {
		$sz = sportszone();

		if ( !sz_action_variable( 1 ) ) {
			$keys = array_keys( $sz->events->event_creation_steps );
			$sz->action_variables[1] = array_shift( $keys );
		}

		/**
		 * Filters the event creation form action.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Action to be used with event creation form.
		 */
		return apply_filters( 'sz_get_event_creation_form_action', trailingslashit( sz_get_events_directory_permalink() . 'create/step/' . sz_action_variable( 1 ) ) );
	}

/**
 * @since 1.1.0
 *
 * @param string $step_slug
 *
 * @return bool
 */
function sz_is_event_creation_step( $step_slug ) {

	// Make sure we are in the events component.
	if ( ! sz_is_events_component() || ! sz_is_current_action( 'create' ) ) {
		return false;
	}

	$sz = sportszone();

	// If this the first step, we can just accept and return true.
	$keys = array_keys( $sz->events->event_creation_steps );
	if ( !sz_action_variable( 1 ) && array_shift( $keys ) == $step_slug ) {
		return true;
	}

	// Before allowing a user to see a event creation step we must make sure
	// previous steps are completed.
	if ( !sz_is_first_event_creation_step() ) {
		if ( !sz_are_previous_event_creation_steps_complete( $step_slug ) ) {
			return false;
		}
	}

	// Check the current step against the step parameter.
	if ( sz_is_action_variable( $step_slug ) ) {
		return true;
	}

	return false;
}

/**
 * @since 1.1.0
 *
 * @param array $step_slugs
 *
 * @return bool
 */
function sz_is_event_creation_step_complete( $step_slugs ) {
	$sz = sportszone();

	if ( !isset( $sz->events->completed_create_steps ) ) {
		return false;
	}

	if ( is_array( $step_slugs ) ) {
		$found = true;

		foreach ( (array) $step_slugs as $step_slug ) {
			if ( !in_array( $step_slug, $sz->events->completed_create_steps ) ) {
				$found = false;
			}
		}

		return $found;
	} else {
		return in_array( $step_slugs, $sz->events->completed_create_steps );
	}

	return true;
}

/**
 * @since 1.1.0
 *
 * @param string $step_slug
 *
 * @return bool
 */
function sz_are_previous_event_creation_steps_complete( $step_slug ) {
	$sz = sportszone();

	// If this is the first event creation step, return true.
	$keys = array_keys( $sz->events->event_creation_steps );
	if ( array_shift( $keys ) == $step_slug ) {
		return true;
	}

	reset( $sz->events->event_creation_steps );

	$previous_steps = array();

	// Get previous steps.
	foreach ( (array) $sz->events->event_creation_steps as $slug => $name ) {
		if ( $slug === $step_slug ) {
			break;
		}

		$previous_steps[] = $slug;
	}

	return sz_is_event_creation_step_complete( $previous_steps );
}

/**
 * @since 1.1.0
 */
function sz_new_event_id() {
	echo sz_get_new_event_id();
}

	/**
	 * @since 1.1.0
	 *
	 * @return int
	 */
	function sz_get_new_event_id() {
		$sz           = sportszone();
		$new_event_id = isset( $sz->events->new_event_id )
			? $sz->events->new_event_id
			: 0;

		/**
		 * Filters the new event ID.
		 *
		 * @since 1.1.0
		 *
		 * @param int $new_event_id ID of the new event.
		 */
		return (int) apply_filters( 'sz_get_new_event_id', $new_event_id );
	}

/**
 * @since 1.1.0
 */
function sz_new_event_name() {
	echo sz_get_new_event_name();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_new_event_name() {
		$sz   = sportszone();
		$name = isset( $sz->events->current_event->name )
			? $sz->events->current_event->name
			: '';

		/**
		 * Filters the new event name.
		 *
		 * @since 1.1.0
		 *
		 * @param string $name Name of the new event.
		 */
		return apply_filters( 'sz_get_new_event_name', $name );
	}

/**
 * @since 1.1.0
 */
function sz_new_event_description() {
	echo sz_get_new_event_description();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_new_event_description() {
		$sz          = sportszone();
		$description = isset( $sz->events->current_event->description )
			? $sz->events->current_event->description
			: '';

		/**
		 * Filters the new event description.
		 *
		 * @since 1.1.0
		 *
		 * @param string $name Description of the new event.
		 */
		return apply_filters( 'sz_get_new_event_description', $description );
	}

/**
 * @since 1.1.0
 */
function sz_new_event_enable_forum() {
	echo sz_get_new_event_enable_forum();
}

	/**
	 * @since 1.1.0
	 *
	 * @return int
	 */
	function sz_get_new_event_enable_forum() {
		$sz    = sportszone();
		$forum = isset( $sz->events->current_event->enable_forum )
			? $sz->events->current_event->enable_forum
			: false;

		/**
		 * Filters whether or not to enable forums for the new event.
		 *
		 * @since 1.1.0
		 *
		 * @param int $forum Whether or not to enable forums.
		 */
		return (int) apply_filters( 'sz_get_new_event_enable_forum', $forum );
	}

/**
 * @since 1.1.0
 */
function sz_new_event_status() {
	echo sz_get_new_event_status();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_new_event_status() {
		$sz     = sportszone();
		$status = isset( $sz->events->current_event->status )
			? $sz->events->current_event->status
			: 'public';

		/**
		 * Filters the new event status.
		 *
		 * @since 1.1.0
		 *
		 * @param string $status Status for the new event.
		 */
		return apply_filters( 'sz_get_new_event_status', $status );
	}

/**
 * Output the avatar for the event currently being created
 *
 * @since 1.1.0
 *
 * @see sz_core_fetch_avatar() For more information on accepted arguments
 *
 * @param array|string $args See sz_core_fetch_avatar().
 */
function sz_new_event_avatar( $args = '' ) {
	echo sz_get_new_event_avatar( $args );
}
	/**
	 * Return the avatar for the event currently being created
	 *
	 * @since 1.1.0
	 *
	 * @see sz_core_fetch_avatar() For a description of arguments and return values.
	 *
	 * @param array|string $args {
	 *     Arguments are listed here with an explanation of their defaults.
	 *     For more information about the arguments, see {@link sz_core_fetch_avatar()}.
	 *
	 *     @type string   $alt     Default: 'Event photo'.
	 *     @type string   $class   Default: 'avatar'.
	 *     @type string   $type    Default: 'full'.
	 *     @type int|bool $width   Default: false.
	 *     @type int|bool $height  Default: false.
	 *     @type string   $id      Passed to $css_id parameter. Default: 'avatar-crop-preview'.
	 * }
	 * @return string       The avatar for the event being created
	 */
	function sz_get_new_event_avatar( $args = '' ) {

		// Parse arguments.
		$r = sz_parse_args( $args, array(
			'type'    => 'full',
			'width'   => false,
			'height'  => false,
			'class'   => 'avatar',
			'id'      => 'avatar-crop-preview',
			'alt'     => __( 'Event photo', 'sportszone' ),
		), 'get_new_event_avatar' );

		// Merge parsed arguments with object specific data.
		$r = array_merge( $r, array(
			'item_id'    => sz_get_current_event_id(),
			'object'     => 'event',
			'avatar_dir' => 'event-avatars',
		) );

		// Get the avatar.
		$avatar = sz_core_fetch_avatar( $r );

		/**
		 * Filters the new event avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $avatar HTML markup for the new event avatar.
		 * @param array  $r      Array of parsed arguments for the event avatar.
		 * @param array  $args   Array of original arguments passed to the function.
		 */
		return apply_filters( 'sz_get_new_event_avatar', $avatar, $r, $args );
	}
	
	
/**
 * Output the cover_image for the event currently being created
 *
 * @since 1.1.0
 *
 * @see sz_core_fetch_cover_image() For more information on accepted arguments
 *
 * @param array|string $args See sz_core_fetch_cover_image().
 */
function sz_new_event_cover_image( $args = '' ) {
	echo sz_get_new_event_cover_image( $args );
}
	/**
	 * Return the cover_image for the event currently being created
	 *
	 * @since 1.1.0
	 *
	 * @see sz_core_fetch_cover_image() For a description of arguments and return values.
	 *
	 * @param array|string $args {
	 *     Arguments are listed here with an explanation of their defaults.
	 *     For more information about the arguments, see {@link sz_core_fetch_avatar()}.
	 *
	 *     @type string   $alt     Default: 'Event photo'.
	 *     @type string   $class   Default: 'avatar'.
	 *     @type string   $type    Default: 'full'.
	 *     @type int|bool $width   Default: false.
	 *     @type int|bool $height  Default: false.
	 *     @type string   $id      Passed to $css_id parameter. Default: 'avatar-crop-preview'.
	 * }
	 * @return string       The cover_image for the event being created
	 */
	function sz_get_new_event_cover_image( $args = '' ) {

		// Parse arguments.
		$r = sz_parse_args( $args, array(
			'type'    => 'full',
			'width'   => false,
			'height'  => false,
			'class'   => 'cover-image',
			'id'      => 'cover-image-crop-preview',
			'alt'     => __( 'Event Cover Photo', 'sportszone' ),
		), 'get_new_event_cover_image' );

		// Merge parsed arguments with object specific data.
		$r = array_merge( $r, array(
			'item_id'    => sz_get_current_event_id(),
			'object'     => 'event',
			'cover_image_dir' => 'event-cover-images',
		) );

		// Get the cover image.
		$cover_image = sz_core_fetch_cover_image( $r );

		/**
		 * Filters the new event avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $avatar HTML markup for the new event avatar.
		 * @param array  $r      Array of parsed arguments for the event avatar.
		 * @param array  $args   Array of original arguments passed to the function.
		 */
		return apply_filters( 'sz_get_new_event_cover_image', $cover_image, $r, $args );
	}

/**
 * Escape & output the URL to the previous event creation step
 *
 * @since 1.1.0
 */
function sz_event_creation_previous_link() {
	echo esc_url( sz_get_event_creation_previous_link() );
}
	/**
	 * Return the URL to the previous event creation step
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	function sz_get_event_creation_previous_link() {
		$sz    = sportszone();
		$steps = array_keys( $sz->events->event_creation_steps );

		// Loop through steps.
		foreach ( $steps as $slug ) {

			// Break when the current step is found.
			if ( sz_is_action_variable( $slug ) ) {
				break;
			}

			// Add slug to previous steps.
			$previous_steps[] = $slug;
		}

		// Generate the URL for the previous step.
		$event_directory = sz_get_events_directory_permalink();
		$create_step     = 'create/step/';
		$previous_step   = array_pop( $previous_steps );
		$url             = trailingslashit( $event_directory . $create_step . $previous_step );

		/**
		 * Filters the permalink for the previous step with the event creation process.
		 *
		 * @since 1.1.0
		 *
		 * @param string $url Permalink for the previous step.
		 */
		return apply_filters( 'sz_get_event_creation_previous_link', $url );
	}

/**
 * Echoes the current event creation step.
 *
 * @since 1.6.0
 */
function sz_events_current_create_step() {
	echo sz_get_events_current_create_step();
}
	/**
	 * Returns the current event creation step. If none is found, returns an empty string.
	 *
	 * @since 1.6.0
	 *
	 *
	 * @return string $current_create_step
	 */
	function sz_get_events_current_create_step() {
		$sz = sportszone();

		if ( !empty( $sz->events->current_create_step ) ) {
			$current_create_step = $sz->events->current_create_step;
		} else {
			$current_create_step = '';
		}

		/**
		 * Filters the current event creation step.
		 *
		 * If none is found, returns an empty string.
		 *
		 * @since 1.6.0
		 *
		 * @param string $current_create_step Current step in the event creation process.
		 */
		return apply_filters( 'sz_get_events_current_create_step', $current_create_step );
	}

/**
 * Is the user looking at the last step in the event creation process.
 *
 * @since 1.1.0
 *
 * @param string $step Step to compare.
 * @return bool True if yes, False if no
 */
function sz_is_last_event_creation_step( $step = '' ) {

	// Use current step, if no step passed.
	if ( empty( $step ) ) {
		$step = sz_get_events_current_create_step();
	}

	// Get the last step.
	$sz     = sportszone();
	$steps  = array_keys( $sz->events->event_creation_steps );
	$l_step = array_pop( $steps );

	// Compare last step to step.
	$retval = ( $l_step === $step );

	/**
	 * Filters whether or not user is looking at last step in event creation process.
	 *
	 * @since 2.4.0
	 *
	 * @param bool   $retval Whether or not we are looking at last step.
	 * @param array  $steps  Array of steps from the event creation process.
	 * @param string $step   Step to compare.
	 */
	return (bool) apply_filters( 'sz_is_last_event_creation_step', $retval, $steps, $step );
}

/**
 * Is the user looking at the first step in the event creation process
 *
 * @since 1.1.0
 *
 * @param string $step Step to compare.
 * @return bool True if yes, False if no
 */
function sz_is_first_event_creation_step( $step = '' ) {

	// Use current step, if no step passed.
	if ( empty( $step ) ) {
		$step = sz_get_events_current_create_step();
	}

	// Get the first step.
	$sz     = sportszone();
	$steps  = array_keys( $sz->events->event_creation_steps );
	$f_step = array_shift( $steps );

	// Compare first step to step.
	$retval = ( $f_step === $step );

	/**
	 * Filters whether or not user is looking at first step in event creation process.
	 *
	 * @since 2.4.0
	 *
	 * @param bool   $retval Whether or not we are looking at first step.
	 * @param array  $steps  Array of steps from the event creation process.
	 * @param string $step   Step to compare.
	 */
	return (bool) apply_filters( 'sz_is_first_event_creation_step', $retval, $steps, $step );
}

/**
 * Output a list of friends who can be invited to a event
 *
 * @since 1.0.0
 *
 * @param array $args Array of arguments for friends list output.
 */
function sz_new_event_invite_friend_list( $args = array() ) {
	echo sz_get_new_event_invite_friend_list( $args );
}
	/**
	 * Return a list of friends who can be invited to a event
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Array of arguments for friends list output.
	 * @return false|string HTML list of checkboxes, or false
	 */
	function sz_get_new_event_invite_friend_list( $args = array() ) {

		// Bail if no friends component.
		if ( ! sz_is_active( 'friends' ) ) {
			return false;
		}

		// Parse arguments.
		$r = sz_parse_args( $args, array(
			'user_id'   => sz_loggedin_user_id(),
			'event_id'  => false,
			'before'    => '',
			'separator' => 'li',
			'after'     => '',
		), 'event_invite_friend_list' );

		// No event passed, so look for new or current event ID's.
		if ( empty( $r['event_id'] ) ) {
			$sz            = sportszone();
			$r['event_id'] = ! empty( $sz->events->new_event_id )
				? $sz->events->new_event_id
				: $sz->events->current_event->id;
		}

		// Setup empty items array.
		$items = array();

		// Build list markup parent elements.
		$before = '';
		if ( ! empty( $r['before'] ) ) {
			$before = $r['before'];
		}

		$after = '';
		if ( ! empty( $r['after'] ) ) {
			$after = $r['after'];
		}

		// Get user's friends who are not in this event already.
		$friends = friends_get_friends_invite_list( $r['user_id'], $r['event_id'] );

		if ( ! empty( $friends ) ) {

			// Get already invited users.
			$invites = events_get_invites_for_event( $r['user_id'], $r['event_id'] );

			for ( $i = 0, $count = count( $friends ); $i < $count; ++$i ) {
				$checked = in_array( (int) $friends[ $i ]['id'], (array) $invites );
				$items[] = '<' . $r['separator'] . '><label for="f-' . esc_attr( $friends[ $i ]['id'] ) . '"><input' . checked( $checked, true, false ) . ' type="checkbox" name="friends[]" id="f-' . esc_attr( $friends[ $i ]['id'] ) . '" value="' . esc_attr( $friends[ $i ]['id'] ) . '" /> ' . esc_html( $friends[ $i ]['full_name'] ) . '</label></' . $r['separator'] . '>';
			}
		}

		/**
		 * Filters the array of friends who can be invited to a event.
		 *
		 * @since 2.4.0
		 *
		 * @param array $items Array of friends.
		 * @param array $r     Parsed arguments from sz_get_new_event_invite_friend_list()
		 * @param array $args  Unparsed arguments from sz_get_new_event_invite_friend_list()
		 */
		$invitable_friends = apply_filters( 'sz_get_new_event_invite_friend_list', $items, $r, $args );

		if ( ! empty( $invitable_friends ) && is_array( $invitable_friends ) ) {
			$retval = $before . implode( "\n", $invitable_friends ) . $after;
		} else {
			$retval = false;
		}

		return $retval;
	}

/**
 * @since 1.0.0
 */
function sz_directory_events_search_form() {

	$query_arg = sz_core_get_component_search_query_arg( 'events' );

	if ( ! empty( $_REQUEST[ $query_arg ] ) ) {
		$search_value = stripslashes( $_REQUEST[ $query_arg ] );
	} else {
		$search_value = sz_get_search_default_text( 'events' );
	}

	$search_form_html = '<form action="" method="get" id="search-events-form">
		<label for="events_search"><input type="text" name="' . esc_attr( $query_arg ) . '" id="events_search" placeholder="'. esc_attr( $search_value ) .'" /></label>
		<input type="submit" id="events_search_submit" name="events_search_submit" value="'. __( 'Search', 'sportszone' ) .'" />
	</form>';

	/**
	 * Filters the HTML markup for the events search form.
	 *
	 * @since 1.9.0
	 *
	 * @param string $search_form_html HTML markup for the search form.
	 */
	echo apply_filters( 'sz_directory_events_search_form', $search_form_html );

}

/**
 * Displays event header tabs.
 *
 * @since 1.0.0
 *
 * @todo Deprecate?
 */
function sz_events_header_tabs() {
	$user_events = sz_displayed_user_domain() . sz_get_events_slug(); ?>

	<li<?php if ( !sz_action_variable( 0 ) || sz_is_action_variable( 'recently-active', 0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_events . '/my-events/recently-active' ); ?>"><?php _e( 'Recently Active', 'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'recently-joined', 0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_events . '/my-events/recently-joined' ); ?>"><?php _e( 'Recently Joined',  'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'most-popular',    0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_events . '/my-events/most-popular'    ); ?>"><?php _e( 'Most Popular',     'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'admin-of',        0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_events . '/my-events/admin-of'        ); ?>"><?php _e( 'Administrator Of', 'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'mod-of',          0 ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_events . '/my-events/mod-of'          ); ?>"><?php _e( 'Moderator Of',     'sportszone' ); ?></a></li>
	<li<?php if ( sz_is_action_variable( 'alphabetically'     ) ) : ?> class="current"<?php endif; ?>><a href="<?php echo trailingslashit( $user_events . '/my-events/alphabetically'  ); ?>"><?php _e( 'Alphabetically',   'sportszone' ); ?></a></li>

<?php
	do_action( 'events_header_tabs' );
}

/**
 * Displays event filter titles.
 *
 * @since 1.0.0
 *
 * @todo Deprecate?
 */
function sz_events_filter_title() {
	$current_filter = sz_action_variable( 0 );

	switch ( $current_filter ) {
		case 'recently-active': default:
			_e( 'Recently Active', 'sportszone' );
			break;
		case 'recently-joined':
			_e( 'Recently Joined', 'sportszone' );
			break;
		case 'most-popular':
			_e( 'Most Popular', 'sportszone' );
			break;
		case 'admin-of':
			_e( 'Administrator Of', 'sportszone' );
			break;
		case 'mod-of':
			_e( 'Moderator Of', 'sportszone' );
			break;
		case 'alphabetically':
			_e( 'Alphabetically', 'sportszone' );
		break;
	}
	do_action( 'sz_events_filter_title' );
}

/**
 * Echo the current event type message.
 *
 * @since 2.7.0
 */
function sz_current_event_directory_type_message() {
	echo sz_get_current_event_directory_type_message();
}
	/**
	 * Generate the current event type message.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	function sz_get_current_event_directory_type_message() {
		$type_object = sz_events_get_event_type_object( sz_get_current_event_directory_type() );

		$message = sprintf( __( 'Viewing events of the type: %s', 'sportszone' ), '<strong>' . $type_object->labels['singular_name'] . '</strong>' );

		/**
		 * Filters the current event type message.
		 *
		 * @since 2.7.0
		 *
		 * @param string $message Message to filter.
		 */
		return apply_filters( 'sz_get_current_event_type_message', $message );
	}

/**
 * Is the current page a specific event admin screen?
 *
 * @since 1.1.0
 *
 * @param string $slug Admin screen slug.
 * @return bool
 */
function sz_is_event_admin_screen( $slug = '' ) {
	return (bool) ( sz_is_event_admin_page() && sz_is_action_variable( $slug ) );
}

/**
 * Echoes the current event admin tab slug.
 *
 * @since 1.6.0
 */
function sz_event_current_admin_tab() {
	echo sz_get_event_current_admin_tab();
}
	/**
	 * Returns the current event admin tab slug.
	 *
	 * @since 1.6.0
	 *
	 *
	 * @return string $tab The current tab's slug.
	 */
	function sz_get_event_current_admin_tab() {
		if ( sz_is_events_component() && sz_is_current_action( 'admin' ) ) {
			$tab = sz_action_variable( 0 );
		} else {
			$tab = '';
		}

		/**
		 * Filters the current event admin tab slug.
		 *
		 * @since 1.6.0
		 *
		 * @param string $tab Current event admin tab slug.
		 */
		return apply_filters( 'sz_get_current_event_admin_tab', $tab );
	}

/** Event Avatar Template Tags ************************************************/

/**
 * Outputs the current event avatar.
 *
 * @since 1.0.0
 *
 * @param string $type Thumb or full.
 */
function sz_event_current_avatar( $type = 'thumb' ) {
	echo sz_get_event_current_avatar( $type );
}
	/**
	 * Returns the current event avatar.
	 *
	 * @since 2.0.0
	 *
	 * @param string $type Thumb or full.
	 * @return string $tab The current tab's slug.
	 */
	function sz_get_event_current_avatar( $type = 'thumb' ) {

		$event_avatar = sz_core_fetch_avatar( array(
			'item_id'    => sz_get_current_event_id(),
			'object'     => 'event',
			'type'       => $type,
			'avatar_dir' => 'event-avatars',
			'alt'        => __( 'Event avatar', 'sportszone' ),
			'class'      => 'avatar'
		) );

		/**
		 * Filters the current event avatar.
		 *
		 * @since 2.0.0
		 *
		 * @param string $event_avatar HTML markup for current event avatar.
		 */
		return apply_filters( 'sz_get_event_current_avatar', $event_avatar );
	}

/**
 * Return whether a event has an avatar.
 *
 * @since 1.1.0
 *
 * @param int|bool $event_id Event ID to check.
 * @return boolean
 */
function sz_get_event_has_avatar( $event_id = false ) {

	if ( false === $event_id ) {
		$event_id = sz_get_current_event_id();
	}

	$avatar_args = array(
		'item_id' => $event_id,
		'object'  => 'event',
		'no_grav' => true,
		'html'    => false,
		'type'    => 'thumb',
	);

	$event_avatar = sz_core_fetch_avatar( $avatar_args );

	if ( sz_core_avatar_default( 'local', $avatar_args ) === $event_avatar ) {
		return false;
	}

	return true;
}

/**
 * @since 1.1.0
 */
function sz_event_avatar_delete_link() {
	echo sz_get_event_avatar_delete_link();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_avatar_delete_link() {
		$sz = sportszone();

		/**
		 * Filters the URL to delete the event avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value URL to delete the event avatar.
		 */
		return apply_filters( 'sz_get_event_avatar_delete_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $sz->events->current_event ) . 'admin/event-avatar/delete' ), 'sz_event_avatar_delete' ) );
	}
	
/**
 * @since 3.1.0
 */
function sz_event_cover_image_delete_link() {
	echo sz_get_event_cover_image_delete_link();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_cover_image_delete_link() {
		$sz = sportszone();

		/**
		 * Filters the URL to delete the event avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value URL to delete the event avatar.
		 */
		return apply_filters( 'sz_get_event_cover_image_delete_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( $sz->events->current_event ) . 'admin/event-cover-image/delete' ), 'sz_event_cover_image_delete' ) );
	}

/**
 * @since 1.0.0
 */
function sz_custom_event_boxes() {
	do_action( 'events_custom_event_boxes' );
}

/**
 * @since 1.0.0
 */
function sz_custom_event_admin_tabs() {
	do_action( 'events_custom_event_admin_tabs' );
}

/**
 * @since 1.0.0
 */
function sz_custom_event_fields_editable() {
	do_action( 'events_custom_event_fields_editable' );
}

/**
 * @since 1.0.0
 */
function sz_custom_event_fields() {
	do_action( 'events_custom_event_fields' );
}

/* Event Membership Requests *************************************************/

/**
 * Initialize a event membership request template loop.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *     @type int $event_id ID of the event. Defaults to current event.
 *     @type int $per_page Number of records to return per page. Default: 10.
 *     @type int $page     Page of results to return. Default: 1.
 *     @type int $max      Max number of items to return. Default: false.
 * }
 * @return bool True if there are requests, otherwise false.
 */
function sz_event_has_membership_requests( $args = '' ) {
	global $requests_template;

	$r = sz_parse_args( $args, array(
		'event_id' => sz_get_current_event_id(),
		'per_page' => 10,
		'page'     => 1,
		'max'      => false
	), 'event_has_membership_requests' );

	$requests_template = new SZ_Events_Membership_Requests_Template( $r );

	/**
	 * Filters whether or not a event membership query has requests to display.
	 *
	 * @since 1.1.0
	 *
	 * @param bool                                   $value             Whether there are requests to display.
	 * @param SZ_Events_Membership_Requests_Template $requests_template Object holding the requests query results.
	 */
	return apply_filters( 'sz_event_has_membership_requests', $requests_template->has_requests(), $requests_template );
}

/**
 * @since 1.0.0
 *
 * @return mixed
 */
function sz_event_membership_requests() {
	global $requests_template;

	return $requests_template->requests();
}

/**
 * @since 1.0.0
 *
 * @return mixed
 */
function sz_event_the_membership_request() {
	global $requests_template;

	return $requests_template->the_request();
}

/**
 * @since 1.0.0
 */
function sz_event_request_user_avatar_thumb() {
	global $requests_template;

	/**
	 * Filters the requesting user's avatar thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value HTML markup for the user's avatar thumbnail.
	 */
	echo apply_filters( 'sz_event_request_user_avatar_thumb', sz_core_fetch_avatar( array( 'item_id' => $requests_template->request->user_id, 'type' => 'thumb', 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_core_get_user_displayname( $requests_template->request->user_id ) ) ) ) );
}

/**
 * @since 1.0.0
 */
function sz_event_request_reject_link() {
	echo sz_get_event_request_reject_link();
}

	/**
	 * @since 1.2.6
	 *
	 * @return mixed|void
	 */
	function sz_get_event_request_reject_link() {
		global $requests_template;

		/**
		 * Filters the URL to use to reject a membership request.
		 *
		 * @since 1.2.6
		 *
		 * @param string $value URL to use to reject a membership request.
		 */
		return apply_filters( 'sz_get_event_request_reject_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( events_get_current_event() ) . 'admin/membership-requests/reject/' . $requests_template->request->membership_id ), 'events_reject_membership_request' ) );
	}

/**
 * @since 1.0.0
 */
function sz_event_request_accept_link() {
	echo sz_get_event_request_accept_link();
}

	/**
	 * @since 1.2.6
	 * @return mixed|void
	 */
	function sz_get_event_request_accept_link() {
		global $requests_template;

		/**
		 * Filters the URL to use to accept a membership request.
		 *
		 * @since 1.2.6
		 *
		 * @param string $value URL to use to accept a membership request.
		 */
		return apply_filters( 'sz_get_event_request_accept_link', wp_nonce_url( trailingslashit( sz_get_event_permalink( events_get_current_event() ) . 'admin/membership-requests/accept/' . $requests_template->request->membership_id ), 'events_accept_membership_request' ) );
	}

/**
 * @since 1.0.0
 */
function sz_event_request_user_link() {
	echo sz_get_event_request_user_link();
}

	/**
	 * @since 1.2.6
	 *
	 * @return mixed|void
	 */
	function sz_get_event_request_user_link() {
		global $requests_template;

		/**
		 * Filters the URL for the user requesting membership.
		 *
		 * @since 1.2.6
		 *
		 * @param string $value URL for the user requestion membership.
		 */
		return apply_filters( 'sz_get_event_request_user_link', sz_core_get_userlink( $requests_template->request->user_id ) );
	}

/**
 * @since 1.0.0
 */
function sz_event_request_time_since_requested() {
	global $requests_template;

	/**
	 * Filters the formatted time since membership was requested.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Formatted time since membership was requested.
	 */
	echo apply_filters( 'sz_event_request_time_since_requested', sprintf( __( 'requested %s', 'sportszone' ), sz_core_time_since( $requests_template->request->date_modified ) ) );
}

/**
 * @since 1.0.0
 */
function sz_event_request_comment() {
	global $requests_template;

	/**
	 * Filters the membership request comment left by user.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Membership request comment left by user.
	 */
	echo apply_filters( 'sz_event_request_comment', strip_tags( stripslashes( $requests_template->request->comments ) ) );
}

/**
 * Output pagination links for event membership requests.
 *
 * @since 2.0.0
 */
function sz_event_requests_pagination_links() {
	echo sz_get_event_requests_pagination_links();
}
	/**
	 * Get pagination links for event membership requests.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function sz_get_event_requests_pagination_links() {
		global $requests_template;

		/**
		 * Filters pagination links for event membership requests.
		 *
		 * @since 2.0.0
		 *
		 * @param string $value Pagination links for event membership requests.
		 */
		return apply_filters( 'sz_get_event_requests_pagination_links', $requests_template->pag_links );
	}

/**
 * Output pagination count text for event membership requests.
 *
 * @since 2.0.0
 */
function sz_event_requests_pagination_count() {
	echo sz_get_event_requests_pagination_count();
}
	/**
	 * Get pagination count text for event membership requests.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function sz_get_event_requests_pagination_count() {
		global $requests_template;

		$start_num = intval( ( $requests_template->pag_page - 1 ) * $requests_template->pag_num ) + 1;
		$from_num  = sz_core_number_format( $start_num );
		$to_num    = sz_core_number_format( ( $start_num + ( $requests_template->pag_num - 1 ) > $requests_template->total_request_count ) ? $requests_template->total_request_count : $start_num + ( $requests_template->pag_num - 1 ) );
		$total     = sz_core_number_format( $requests_template->total_request_count );

		if ( 1 == $requests_template->total_request_count ) {
			$message = __( 'Viewing 1 request', 'sportszone' );
		} else {
			$message = sprintf( _n( 'Viewing %1$s - %2$s of %3$s request', 'Viewing %1$s - %2$s of %3$s requests', $requests_template->total_request_count, 'sportszone' ), $from_num, $to_num, $total );
		}

		/**
		 * Filters pagination count text for event membership requests.
		 *
		 * @since 2.0.0
		 *
		 * @param string $message  Pagination count text for event membership requests.
		 * @param string $from_num Total amount for the low value in the range.
		 * @param string $to_num   Total amount for the high value in the range.
		 * @param string $total    Total amount of members found.
		 */
		return apply_filters( 'sz_get_event_requests_pagination_count', $message, $from_num, $to_num, $total );
	}

/** Event Invitations *********************************************************/

/**
 * Whether or not there are invites.
 *
 * @since 1.1.0
 *
 * @param string $args
 * @return bool|mixed|void
 */
function sz_event_has_invites( $args = '' ) {
	global $invites_template, $event_id;

	$r = sz_parse_args( $args, array(
		'event_id' => false,
		'user_id'  => sz_loggedin_user_id(),
		'per_page' => false,
		'page'     => 1,
	), 'event_has_invites' );

	if ( empty( $r['event_id'] ) ) {
		if ( events_get_current_event() ) {
			$r['event_id'] = sz_get_current_event_id();
		} elseif ( ! empty( sportszone()->events->new_event_id ) ) {
			$r['event_id'] = sportszone()->events->new_event_id;
		}
	}

	// Set the global (for use in SZ_Events_Invite_Template::the_invite()).
	if ( empty( $event_id ) ) {
		$event_id = $r['event_id'];
	}

	if ( ! $event_id ) {
		return false;
	}

	$invites_template = new SZ_Events_Invite_Template( $r );

	/**
	 * Filters whether or not a event invites query has invites to display.
	 *
	 * @since 1.1.0
	 *
	 * @param bool                      $value            Whether there are requests to display.
	 * @param SZ_Events_Invite_Template $invites_template Object holding the invites query results.
	 */
	return apply_filters( 'sz_event_has_invites', $invites_template->has_invites(), $invites_template );
}

/**
 * @since 1.1.0
 *
 * @return mixed
 */
function sz_event_invites() {
	global $invites_template;

	return $invites_template->invites();
}

/**
 * @since 1.1.0
 *
 * @return mixed
 */
function sz_event_the_invite() {
	global $invites_template;

	return $invites_template->the_invite();
}

/**
 * @since 1.1.0
 */
function sz_event_invite_item_id() {
	echo sz_get_event_invite_item_id();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_invite_item_id() {
		global $invites_template;

		/**
		 * Filters the event invite item ID.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Event invite item ID.
		 */
		return apply_filters( 'sz_get_event_invite_item_id', 'uid-' . $invites_template->invite->user->id );
	}

/**
 * @since 1.1.0
 */
function sz_event_invite_user_avatar() {
	echo sz_get_event_invite_user_avatar();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_invite_user_avatar() {
		global $invites_template;

		/**
		 * Filters the event invite user avatar.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Event invite user avatar.
		 */
		return apply_filters( 'sz_get_event_invite_user_avatar', $invites_template->invite->user->avatar_thumb );
	}

/**
 * @since 1.1.0
 */
function sz_event_invite_user_link() {
	echo sz_get_event_invite_user_link();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_invite_user_link() {
		global $invites_template;

		/**
		 * Filters the event invite user link.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Event invite user link.
		 */
		return apply_filters( 'sz_get_event_invite_user_link', sz_core_get_userlink( $invites_template->invite->user->id ) );
	}

/**
 * @since 1.1.0
 */
function sz_event_invite_user_last_active() {
	echo sz_get_event_invite_user_last_active();
}

	/**
	 * @since 1.1.0
	 *
	 * @return mixed|void
	 */
	function sz_get_event_invite_user_last_active() {
		global $invites_template;

		/**
		 * Filters the event invite user's last active time.
		 *
		 * @since 1.1.0
		 *
		 * @param string $value Event invite user's last active time.
		 */
		return apply_filters( 'sz_get_event_invite_user_last_active', $invites_template->invite->user->last_active );
	}

/**
 * @since 1.1.0
 */
function sz_event_invite_user_remove_invite_url() {
	echo sz_get_event_invite_user_remove_invite_url();
}

	/**
	 * @since 1.1.0
	 *
	 * @return string
	 */
	function sz_get_event_invite_user_remove_invite_url() {
		global $invites_template;

		$user_id = intval( $invites_template->invite->user->id );

		if ( sz_is_current_action( 'create' ) ) {
			$uninvite_url = sz_get_events_directory_permalink() . 'create/step/event-invites/?user_id=' . $user_id;
		} else {
			$uninvite_url = trailingslashit( sz_get_event_permalink( events_get_current_event() ) . 'send-invites/remove/' . $user_id );
		}

		return wp_nonce_url( $uninvite_url, 'events_invite_uninvite_user' );
	}

/**
 * Output pagination links for event invitations.
 *
 * @since 2.0.0
 */
function sz_event_invite_pagination_links() {
	echo sz_get_event_invite_pagination_links();
}

	/**
	 * Get pagination links for event invitations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function sz_get_event_invite_pagination_links() {
		global $invites_template;

		/**
		 * Filters the pagination links for event invitations.
		 *
		 * @since 2.0.0
		 *
		 * @param string $value Pagination links for event invitations.
		 */
		return apply_filters( 'sz_get_event_invite_pagination_links', $invites_template->pag_links );
	}

/**
 * Output pagination count text for event invitations.
 *
 * @since 2.0.0
 */
function sz_event_invite_pagination_count() {
	echo sz_get_event_invite_pagination_count();
}
	/**
	 * Get pagination count text for event invitations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	function sz_get_event_invite_pagination_count() {
		global $invites_template;

		$start_num = intval( ( $invites_template->pag_page - 1 ) * $invites_template->pag_num ) + 1;
		$from_num  = sz_core_number_format( $start_num );
		$to_num    = sz_core_number_format( ( $start_num + ( $invites_template->pag_num - 1 ) > $invites_template->total_invite_count ) ? $invites_template->total_invite_count : $start_num + ( $invites_template->pag_num - 1 ) );
		$total     = sz_core_number_format( $invites_template->total_invite_count );

		if ( 1 == $invites_template->total_invite_count ) {
			$message = __( 'Viewing 1 invitation', 'sportszone' );
		} else {
			$message = sprintf( _n( 'Viewing %1$s - %2$s of %3$s invitation', 'Viewing %1$s - %2$s of %3$s invitations', $invites_template->total_invite_count, 'sportszone' ), $from_num, $to_num, $total );
		}

		/** This filter is documented in sz-events/sz-events-template.php */
		return apply_filters( 'sz_get_events_pagination_count', $message, $from_num, $to_num, $total );
	}

/** Event RSS *****************************************************************/

/**
 * Hook event activity feed to <head>.
 *
 * @since 1.5.0
 */
function sz_events_activity_feed() {

	// Bail if not viewing a single event or activity is not active.
	if ( ! sz_is_active( 'events' ) || ! sz_is_active( 'activity' ) || ! sz_is_event() ) {
		return;
	} ?>

	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo( 'name' ) ?> | <?php sz_current_event_name() ?> | <?php _e( 'Event Activity RSS Feed', 'sportszone' ) ?>" href="<?php sz_event_activity_feed_link() ?>" />

<?php
}
add_action( 'sz_head', 'sz_events_activity_feed' );

/**
 * Output the current event activity-stream RSS URL.
 *
 * @since 1.5.0
 */
function sz_event_activity_feed_link() {
	echo sz_get_event_activity_feed_link();
}
	/**
	 * Return the current event activity-stream RSS URL.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	function sz_get_event_activity_feed_link() {
		$current_event = events_get_current_event();
		$event_link    = sz_get_event_permalink( $current_event ) . 'feed';
		$feed_link     = trailingslashit( $event_link );

		/**
		 * Filters the current event activity-stream RSS URL.
		 *
		 * @since 1.2.0
		 *
		 * @param string $feed_link Current event activity-stream RSS URL.
		 */
		return apply_filters( 'sz_get_event_activity_feed_link', $feed_link );
	}

/** Current Event *************************************************************/

/**
 * Echoes the output of sz_get_current_event_id().
 *
 * @since 1.5.0
 */
function sz_current_event_id() {
	echo sz_get_current_event_id();
}
	/**
	 * Returns the ID of the current event.
	 *
	 * @since 1.5.0
	 *
	 * @return int $current_event_id The id of the current event, if there is one.
	 */
	function sz_get_current_event_id() {
		$current_event    = events_get_current_event();
		$current_event_id = isset( $current_event->id ) ? (int) $current_event->id : 0;

		/**
		 * Filters the ID of the current event.
		 *
		 * @since 1.5.0
		 *
		 * @param int    $current_event_id ID of the current event.
		 * @param object $current_event    Instance holding the current event.
		 */
		return apply_filters( 'sz_get_current_event_id', $current_event_id, $current_event );
	}

/**
 * Echoes the output of sz_get_current_event_slug().
 *
 * @since 1.5.0
 */
function sz_current_event_slug() {
	echo sz_get_current_event_slug();
}
	/**
	 * Returns the slug of the current event.
	 *
	 * @since 1.5.0
	 *
	 * @return string $current_event_slug The slug of the current event, if there is one.
	 */
	function sz_get_current_event_slug() {
		$current_event      = events_get_current_event();
		$current_event_slug = isset( $current_event->slug ) ? $current_event->slug : '';

		/**
		 * Filters the slug of the current event.
		 *
		 * @since 1.5.0
		 *
		 * @param string $current_event_slug Slug of the current event.
		 * @param object $current_event      Instance holding the current event.
		 */
		return apply_filters( 'sz_get_current_event_slug', $current_event_slug, $current_event );
	}

/**
 * Echoes the output of sz_get_current_event_name().
 *
 * @since 1.5.0
 */
function sz_current_event_name() {
	echo sz_get_current_event_name();
}
	/**
	 * Returns the name of the current event.
	 *
	 * @since 1.5.0
	 *
	 * @return string The name of the current event, if there is one.
	 */
	function sz_get_current_event_name() {
		$current_event = events_get_current_event();
		$current_name  = sz_get_event_name( $current_event );

		/**
		 * Filters the name of the current event.
		 *
		 * @since 1.2.0
		 *
		 * @param string $current_name  Name of the current event.
		 * @param object $current_event Instance holding the current event.
		 */
		return apply_filters( 'sz_get_current_event_name', $current_name, $current_event );
	}

/**
 * Echoes the output of sz_get_current_event_description().
 *
 * @since 2.1.0
 */
function sz_current_event_description() {
	echo sz_get_current_event_description();
}
	/**
	 * Returns the description of the current event.
	 *
	 * @since 2.1.0
	 *                       this output.
	 *
	 * @return string The description of the current event, if there is one.
	 */
	function sz_get_current_event_description() {
		$current_event      = events_get_current_event();
		$current_event_desc = isset( $current_event->description ) ? $current_event->description : '';

		/**
		 * Filters the description of the current event.
		 *
		 * This filter is used to apply extra filters related to formatting.
		 *
		 * @since 1.0.0
		 *
		 * @param string $current_event_desc Description of the current event.
		 */
		$desc               = apply_filters( 'sz_get_event_description', $current_event_desc );

		/**
		 * Filters the description of the current event.
		 *
		 * @since 2.1.0
		 *
		 * @param string $desc Description of the current event.
		 */
		return apply_filters( 'sz_get_current_event_description', $desc );
	}

/**
 * Output a URL for a event component action.
 *
 * @since 1.2.0
 *
 * @param string $action
 * @param string $query_args
 * @param bool $nonce
 * @return string|null
 */
function sz_events_action_link( $action = '', $query_args = '', $nonce = false ) {
	echo sz_get_events_action_link( $action, $query_args, $nonce );
}
	/**
	 * Get a URL for a event component action.
	 *
	 * @since 1.2.0
	 *
	 * @param string $action
	 * @param string $query_args
	 * @param bool $nonce
	 * @return string
	 */
	function sz_get_events_action_link( $action = '', $query_args = '', $nonce = false ) {

		$current_event = events_get_current_event();
		$url           = '';

		// Must be a event.
		if ( ! empty( $current_event->id ) ) {

			// Append $action to $url if provided
			if ( !empty( $action ) ) {
				$url = sz_get_event_permalink( $current_event ) . $action;
			} else {
				$url = sz_get_event_permalink( $current_event );
			}

			// Add a slash at the end of our user url.
			$url = trailingslashit( $url );

			// Add possible query args.
			if ( !empty( $query_args ) && is_array( $query_args ) ) {
				$url = add_query_arg( $query_args, $url );
			}

			// To nonce, or not to nonce...
			if ( true === $nonce ) {
				$url = wp_nonce_url( $url );
			} elseif ( is_string( $nonce ) ) {
				$url = wp_nonce_url( $url, $nonce );
			}
		}

		/**
		 * Filters a URL for a event component action.
		 *
		 * @since 2.1.0
		 *
		 * @param string $url        URL for a event component action.
		 * @param string $action     Action being taken for the event.
		 * @param string $query_args Query arguments being passed.
		 * @param bool   $nonce      Whether or not to add a nonce.
		 */
		return apply_filters( 'sz_get_events_action_link', $url, $action, $query_args, $nonce );
	}

/** Stats **********************************************************************/

/**
 * Display the number of events in user's profile.
 *
 * @since 2.0.0
 *
 * @param array|string $args before|after|user_id
 *
 */
function sz_events_profile_stats( $args = '' ) {
	echo sz_events_get_profile_stats( $args );
}
add_action( 'sz_members_admin_user_stats', 'sz_events_profile_stats', 8, 1 );

/**
 * Return the number of events in user's profile.
 *
 * @since 2.0.0
 *
 * @param array|string $args before|after|user_id
 * @return string HTML for stats output.
 */
function sz_events_get_profile_stats( $args = '' ) {

	// Parse the args
	$r = sz_parse_args( $args, array(
		'before'  => '<li class="sz-events-profile-stats">',
		'after'   => '</li>',
		'user_id' => sz_displayed_user_id(),
		'events'  => 0,
		'output'  => ''
	), 'events_get_profile_stats' );

	// Allow completely overloaded output
	if ( empty( $r['output'] ) ) {

		// Only proceed if a user ID was passed
		if ( ! empty( $r['user_id'] ) ) {

			// Get the user events
			if ( empty( $r['events'] ) ) {
				$r['events'] = absint( sz_get_total_event_count_for_user( $r['user_id'] ) );
			}

			// If events exist, show some formatted output
			$r['output'] = $r['before'] . sprintf( _n( '%s event', '%s events', $r['events'], 'sportszone' ), '<strong>' . $r['events'] . '</strong>' ) . $r['after'];
		}
	}

	/**
	 * Filters the number of events in user's profile.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value HTML for stats output.
	 * @param array  $r     Array of parsed arguments for query.
	 */
	return apply_filters( 'sz_events_get_profile_stats', $r['output'], $r );
}
