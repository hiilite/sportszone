<?php
/**
 * SportsZone Events Widgets
 *
 * @package SportsZone
 * @subpackage EventsWidgets
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register widgets for events component.
 *
 * @since 1.0.0
 */
function events_register_widgets() {
	add_action( 'widgets_init', function() { register_widget( 'SZ_Events_Widget' ); } );
}
add_action( 'sz_register_widgets', 'events_register_widgets' );

/**
 * AJAX callback for the Events List widget.
 *
 * @since 1.0.0
 */
function events_ajax_widget_events_list() {

	check_ajax_referer( 'events_widget_events_list' );

	switch ( $_POST['filter'] ) {
		case 'newest-events':
			$type = 'newest';
		break;
		case 'recently-active-events':
			$type = 'active';
		break;
		case 'popular-events':
			$type = 'popular';
		break;
		case 'alphabetical-events':
			$type = 'alphabetical';
		break;
	}

	$per_page = isset( $_POST['max_events'] ) ? intval( $_POST['max_events'] ) : 5;

	$events_args = array(
		'user_id'  => 0,
		'type'     => $type,
		'per_page' => $per_page,
		'max'      => $per_page,
	);

	if ( sz_has_events( $events_args ) ) : ?>
		<?php echo "0[[SPLIT]]"; ?>
		<?php while ( sz_events() ) : sz_the_event(); ?>
			<li <?php sz_event_class(); ?>>
				<div class="item-avatar">
					<a href="<?php sz_event_permalink() ?>"><?php sz_event_avatar_thumb() ?></a>
				</div>

				<div class="item">
					<div class="item-title"><?php sz_event_link(); ?></div>
					<div class="item-meta">
						<?php if ( 'newest-events' === $_POST['filter'] ) : ?>
							<span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_event_date_created( 0, array( 'relative' => false ) ) ); ?>"><?php printf( __( 'created %s', 'sportszone' ), sz_get_event_date_created() ); ?></span>
						<?php elseif ( 'popular-events' === $_POST['filter'] ) : ?>
							<span class="activity"><?php sz_event_member_count(); ?></span>
						<?php else : ?>
							<span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_event_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( __( 'active %s', 'sportszone' ), sz_get_event_last_active() ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			</li>
		<?php endwhile; ?>

		<?php wp_nonce_field( 'events_widget_events_list', '_wpnonce-events' ); ?>
		<input type="hidden" name="events_widget_max" id="events_widget_max" value="<?php echo esc_attr( $_POST['max_events'] ); ?>" />

	<?php else: ?>

		<?php echo "-1[[SPLIT]]<li>" . __( "No events matched the current filter.", 'sportszone' ); ?>

	<?php endif;

}
add_action( 'wp_ajax_widget_events_list',        'events_ajax_widget_events_list' );
add_action( 'wp_ajax_nopriv_widget_events_list', 'events_ajax_widget_events_list' );
