<?php
/**
 * Notifications template tags
 *
 * @since 3.0.0
 * @version 3.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Display the notifications filter options.
 *
 * @since 3.0.0
 */
function sz_nouveau_notifications_filters() {
	echo sz_nouveau_get_notifications_filters();
}

	/**
	 * Get the notifications filter options.
	 *
	 * @since 3.0.0
	 *
	 * @return string HTML output.
	 */
	function sz_nouveau_get_notifications_filters() {
		$output   = '';
		$filters  = sz_nouveau_notifications_sort( sz_nouveau_notifications_get_filters() );
		$selected = 0;

		if ( ! empty( $_REQUEST['type'] ) ) {
			$selected = sanitize_key( $_REQUEST['type'] );
		}

		foreach ( $filters as $filter ) {
			if ( empty( $filter['id'] ) || empty( $filter['label'] ) ) {
				continue;
			}

			$output .= sprintf( '<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( sanitize_key( $filter['id'] ) ),
				selected( $selected, $filter['id'], false ),
				esc_html( $filter['label'] )
			) . "\n";
		}

		if ( $output ) {
			$output = sprintf( '<option value="%1$s" %2$s>%3$s</option>',
				0,
				selected( $selected, 0, false ),
				esc_html__( '&mdash; Everything &mdash;', 'sportszone' )
			) . "\n" . $output;
		}

		/**
		 * Filter to edit the options output.
		 *
		 * @since 3.0.0
		 *
		 * @param string $output  The options output.
		 * @param array  $filters The sorted notifications filters.
		 */
		return apply_filters( 'sz_nouveau_get_notifications_filters', $output, $filters );
	}

/**
 * Outputs the order action links.
 *
 * @since 3.0.0
 */
function sz_nouveau_notifications_sort_order_links() {
	if ( 'unread' === sz_current_action() ) {
		$link = sz_get_notifications_unread_permalink( sz_displayed_user_id() );
	} else {
		$link = sz_get_notifications_read_permalink( sz_displayed_user_id() );
	}

	$desc = add_query_arg( 'sort_order', 'DESC', $link );
	$asc  = add_query_arg( 'sort_order', 'ASC', $link );
	?>

	<span class="notifications-order-actions">
		<a href="<?php echo esc_url( $desc ); ?>" class="sz-tooltip" data-sz-tooltip="<?php esc_attr_e( 'Newest First', 'sportszone' ); ?>" aria-label="<?php esc_attr_e( 'Newest First', 'sportszone' ); ?>" data-sz-notifications-order="DESC"><span class="dashicons dashicons-arrow-down" aria-hidden="true"></span></a>
		<a href="<?php echo esc_url( $asc ); ?>" class="sz-tooltip" data-sz-tooltip="<?php esc_attr_e( 'Oldest First', 'sportszone' ); ?>" aria-label="<?php esc_attr_e( 'Oldest First', 'sportszone' ); ?>" data-sz-notifications-order="ASC"><span class="dashicons dashicons-arrow-up" aria-hidden="true"></span></a>
	</span>

	<?php
}

/**
 * Output the dropdown for bulk management of notifications.
 *
 * @since 3.0.0
 */
function sz_nouveau_notifications_bulk_management_dropdown() {
?>

	<div class="select-wrap">

		<label class="sz-screen-reader-text" for="notification-select"><?php
			esc_html_e( 'Select Bulk Action', 'sportszone' );
		?></label>

		<select name="notification_bulk_action" id="notification-select">
			<option value="" selected="selected"><?php echo esc_html( 'Bulk Actions', 'sportszone' ); ?></option>

			<?php if ( sz_is_current_action( 'unread' ) ) : ?>
				<option value="read"><?php echo esc_html_x( 'Mark read', 'button', 'sportszone' ); ?></option>
			<?php elseif ( sz_is_current_action( 'read' ) ) : ?>
				<option value="unread"><?php echo esc_html_x( 'Mark unread', 'button', 'sportszone' ); ?></option>
			<?php endif; ?>
			<option value="delete"><?php echo esc_html_x( 'Delete', 'button', 'sportszone' ); ?></option>
		</select>

		<span class="select-arrow"></span>

	</div><!-- // .select-wrap -->

	<input type="submit" id="notification-bulk-manage" class="button action" value="<?php echo esc_attr_x( 'Apply', 'button', 'sportszone' ); ?>">
	<?php
}
