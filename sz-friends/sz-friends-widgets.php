<?php
/**
 * SportsZone Friends Widgets.
 *
 * @package SportsZone
 * @subpackage Friends
 * @since 1.9.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register the friends widget.
 *
 * @since 1.9.0
 */
function sz_friends_register_widgets() {
	if ( ! sz_is_active( 'friends' ) ) {
		return;
	}

	// The Friends widget works only when looking an a displayed user,
	// and the concept of "displayed user" doesn't exist on non-root blogs,
	// so we don't register the widget there.
	if ( ! sz_is_root_blog() ) {
		return;
	}

	add_action( 'widgets_init', function() { register_widget( 'SZ_Core_Friends_Widget' ); } );
}
add_action( 'sz_register_widgets', 'sz_friends_register_widgets' );

/** Widget AJAX ***************************************************************/

/**
 * Process AJAX pagination or filtering for the Friends widget.
 *
 * @since 1.9.0
 */
function sz_core_ajax_widget_friends() {

	check_ajax_referer( 'sz_core_widget_friends' );

	switch ( $_POST['filter'] ) {
		case 'newest-friends':
			$type = 'newest';
			break;

		case 'recently-active-friends':
			$type = 'active';
			break;

		case 'popular-friends':
			$type = 'popular';
			break;
	}

	$members_args = array(
		'user_id'         => sz_displayed_user_id(),
		'type'            => $type,
		'max'             => absint( $_POST['max-friends'] ),
		'populate_extras' => 1,
	);

	if ( sz_has_members( $members_args ) ) : ?>
		<?php echo '0[[SPLIT]]'; // Return valid result. ?>
		<?php while ( sz_members() ) : sz_the_member(); ?>
			<li class="vcard">
				<div class="item-avatar">
					<a href="<?php sz_member_permalink(); ?>"><?php sz_member_avatar(); ?></a>
				</div>

				<div class="item">
					<div class="item-title fn"><a href="<?php sz_member_permalink(); ?>"><?php sz_member_name(); ?></a></div>
					<?php if ( 'active' == $type ) : ?>
						<div class="item-meta"><span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php sz_member_last_active(); ?></span></div>
					<?php elseif ( 'newest' == $type ) : ?>
						<div class="item-meta"><span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_member_registered( array( 'relative' => false ) ) ); ?>"><?php sz_member_registered(); ?></span></div>
					<?php elseif ( sz_is_active( 'friends' ) ) : ?>
						<div class="item-meta"><span class="activity"><?php sz_member_total_friend_count(); ?></span></div>
					<?php endif; ?>
				</div>
			</li>
		<?php endwhile; ?>

	<?php else: ?>
		<?php echo "-1[[SPLIT]]<li>"; ?>
		<?php _e( 'There were no members found, please try another filter.', 'sportszone' ); ?>
		<?php echo "</li>"; ?>
	<?php endif;
}
add_action( 'wp_ajax_widget_friends', 'sz_core_ajax_widget_friends' );
add_action( 'wp_ajax_nopriv_widget_friends', 'sz_core_ajax_widget_friends' );
