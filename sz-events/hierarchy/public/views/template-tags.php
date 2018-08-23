<?php
/**
 * @package   HierarchicalEventsForSZ
 * @author    dcavins
 * @license   GPL-2.0+
 * @copyright 2016 David Cavins
 */

/**
 * Output the permalink breadcrumbs for the current event in the loop.
 *
 * @since 1.0.0
 *
 * @param object|bool $event Optional. Event object.
 *                           Default: current event in loop.
 * @param string      $separator String to place between event links.
 */
function hgsz_event_permalink_breadcrumbs( $event = false, $separator = ' / ' ) {
	echo hgsz_get_event_permalink_breadcrumbs( $event, $separator );
}

	/**
	 * Return the permalink breadcrumbs for the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
     * @param string      $separator String to place between event links.
     *
	 * @return string
	 */
	function hgsz_get_event_permalink_breadcrumbs( $event = false, $separator = ' / ' ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event = $events_template->event;
		}
		$user_id = sz_loggedin_user_id();

		// Create the base event's entry.
		$item        = '<a href="' . esc_url( sz_get_event_permalink( $event ) ) . '">' . esc_html( sz_get_event_name( $event ) ) . '</a>';
		$breadcrumbs = array( $item );
		$parent_id   = hgsz_get_parent_event_id( $event->id, $user_id );

		// Add breadcrumbs for the ancestors.
		while ( $parent_id ) {
			$parent_event  = events_get_event( $parent_id );
			$breadcrumbs[] = '<a href="' . esc_url( sz_get_event_permalink( $parent_event ) ) . '">' . esc_html( sz_get_event_name( $parent_event ) ) . '</a>';
			$parent_id     = hgsz_get_parent_event_id( $parent_event->id, $user_id );
		}

		$breadcrumbs = implode( $separator, array_reverse( $breadcrumbs ) );

		/**
		 * Filters the breadcrumb trail for the current event in the loop.
		 *
		 * @since 1.0.0
		 *
		 * @param string          $breadcrumb String of breadcrumb links.
		 * @param SZ_Events_Event $event      Event object.
		 */
		return apply_filters( 'hgsz_get_event_permalink_breadcrumbs', $breadcrumbs, $event );
	}

/**
 * Output the URL of the hierarchy page of the current event in the loop.
 *
 * @since 1.0.0
 */
function hgsz_event_hierarchy_permalink( $event = false ) {
	echo esc_url( hgsz_get_event_hierarchy_permalink( $event ) );
}

	/**
	 * Generate the URL of the hierarchy page of the current event in the loop.
	 *
	 * @since 1.0.0
	 *
	 * @param object|bool $event Optional. Event object.
	 *                           Default: current event in loop.
	 * @return string
	 */
	function hgsz_get_event_hierarchy_permalink( $event = false ) {
		global $events_template;

		if ( empty( $event ) ) {
			$event =& $events_template->event;
		}

		// Filter the slug via the 'hgsz_screen_slug' filter.
		return trailingslashit( sz_get_event_permalink( $event ) . hgsz_get_hierarchy_screen_slug() );
	}

/**
 * Output the upper pagination block for a event directory list.
 *
 * @since 1.0.0
 */
function hgsz_events_loop_pagination_top() {
	return hgsz_events_loop_pagination( 'top' );
}

/**
 * Output the lower pagination block for a event directory list.
 *
 * @since 1.0.0
 */
function hgsz_events_loop_pagination_bottom() {
	return hgsz_events_loop_pagination( 'bottom' );
}

	/**
	 * Output the pagination block for a event directory list.
	 *
	 * @param string $location Which pagination block to produce.
	 *
	 * @since 1.0.0
	 */
	function hgsz_events_loop_pagination( $location = 'top' ) {
		if ( 'top' != $location ) {
			$location = 'bottom';
		}

		// Pagination needs to be "no-ajax" on the hierarchy screen.
		$class = '';
		if ( hgsz_is_hierarchy_screen() ) {
			$class = ' no-ajax';
		}

		/*
		 * Return typical pagination on the main event directory first load and the
		 * hierarchy screen for a single event. However, when expanding the tree,
		 * we need to not use pagination, because it conflicts with the main list's
		 * pagination. Instead, show the first 20 and provide a link to the rest.
		 */
		?>
		<div id="pag-<?php echo $location; ?>" class="pagination<?php echo $class; ?>">

			<div class="pag-count" id="event-dir-count-<?php echo $location; ?>">

				<?php sz_events_pagination_count(); ?>

			</div>

			<?php
			// Check for AJAX requests for the child events toggle.
			// Provide a link to the parent event's hierarchy screen.
			if ( isset( $_REQUEST['action'] )
				&& 'hgsz_get_child_events' == $_REQUEST['action']
				&& ! empty( $_REQUEST['parent_id'] )
				&& ( $parent_event = events_get_event( (int) $_REQUEST['parent_id'] ) )
				&& hgsz_include_event_by_context( $parent_event, sz_loggedin_user_id(), 'normal' )
				) :
			?>
				<a href="<?php hgsz_event_hierarchy_permalink( $parent_event ); ?>" class="view-all-child-events-link"><?php
					// Check for a saved option for this string first.
					$label = get_option( 'hgsz-directory-child-event-view-all-link' );
					// Next, allow translations to be applied.
					if ( empty( $label ) ) {
						$label = __( 'View all child events of %s.', 'hierarchical-events-for-sz' );
					}
					$label = sprintf( $label, sz_get_event_name( $parent_event ) );

					/**
					 * Filters the "view all subevents" link text for a event.
					 *
					 * @since 1.0.0
					 *
					 * @param string          $value        Label to use.
					 * @param SZ_Events_Event $parent_event Parent event object.
					 */
					echo esc_html( apply_filters( 'hgsz_directory_child_event_view_all_link', $label, $parent_event ) );
				?></a>
			<?php else : ?>

				<div class="pagination-links" id="event-dir-pag-<?php echo $location; ?>">

					<?php sz_events_pagination_links(); ?>

				</div>

				<?php
			endif; ?>
		</div>
		<?php
	}

/**
 * Output the child events toggle and container for a event directory list.
 *
 * @since 1.0.0
 */
function hgsz_child_event_section() {
	global $events_template;
	/*
	 * Store the $events_template global, so that the wrapper event
	 * can be restored after the has_events() loop is completed.
	 */
	$parent_events_template = $events_template;

	/*
	 * For the most accurate results, only show the 'show child events' toggle
	 * if events would be shown by a sz_has_events() loop. Keep the args simple
	 * to avoid unnecessary joins and hopefully hit the SZ_Events_Event::get()
	 * cache.
	 */
	$has_event_args = array(
		'parent_id'          => sz_get_event_id(),
		'orderby'            => 'date_created',
		'update_admin_cache' => false,
		'per_page'           => false,
	);
	if ( sz_has_events( $has_event_args ) ) :
		global $events_template;
		$number_children = $events_template->total_event_count;

		// Put the parent $events_template back.
		$events_template = $parent_events_template;
		?>
		<div class="child-events-container">
			<a href="<?php esc_url( hgsz_event_hierarchy_permalink() ); ?>" class="toggle-child-events" data-event-id="<?php sz_event_id(); ?>" aria-expanded="false" aria-controls="child-events-of-<?php sz_event_id(); ?>"><?php
				// Check for a saved option first.
				$label = sz_get_option( 'hgsz-directory-child-event-section-label' );
				// Next, allow translations to be applied.
				if ( empty( $label ) ) {
					$label = _x( 'Child events %s', 'Label for the control on hierarchical event directories that shows or hides the child events. %s will be replaced with the number of child events.', 'hierarchical-events-for-sz' );
				}
				$label = sprintf( esc_html( $label ), '<span class="count badge badge-primary badge-pill">' . $number_children . '</span>' );

				/**
				 * Filters the "Child events" toggle text for a event's entry on the
				 * hierarchical events directory.
				 *
				 * @since 1.0.0
				 *
				 * @param string $value Label to use.
				 */
				echo apply_filters( 'hgsz_directory_child_event_section_header_label', $label );
			?></a>
			<div class="child-events" id="child-events-of-<?php sz_event_id(); ?>"></div>
		</div>
	<?php else :
		$events_template = $parent_events_template;
	endif;
}

/**
 * Add breadcrumb links and a "Child Event" header to the single event hierarchy screen.
 *
 * @since 1.0.0
 */
function hgsz_single_event_hierarchy_screen_list_header() {
	if ( hgsz_is_hierarchy_screen() ) :
		// Add the parent events breadcrumb links
		if ( hgsz_get_parent_event_id( false, sz_loggedin_user_id(), 'normal' ) ) :
		?>
		<div class="parent-event-breadcrumbs">
			<h3><?php _e( 'Parent Events', 'hierarchical-events-for-sz' ); ?></h3>
			<?php hgsz_event_permalink_breadcrumbs(); ?>
		</div>
		<hr />
		<?php
		endif;
		// Add a header for the events list.
		?>
		<h3><?php _e( 'Child Events', 'hierarchical-events-for-sz' ); ?></h3>
		<?php
	endif;
}
