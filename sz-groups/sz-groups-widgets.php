<?php
/**
 * SportsZone Groups Widgets
 *
 * @package SportsZone
 * @subpackage GroupsWidgets
 * @since 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Register widgets for groups component.
 *
 * @since 1.0.0
 */
function groups_register_widgets() {
	add_action( 'widgets_init', function() { register_widget( 'SZ_Groups_Widget' ); } );
}
add_action( 'sz_register_widgets', 'groups_register_widgets' );

/**
 * AJAX callback for the Groups List widget.
 *
 * @since 1.0.0
 */
function groups_ajax_widget_groups_list() {

	check_ajax_referer( 'groups_widget_groups_list' );

	switch ( $_POST['filter'] ) {
		case 'newest-groups':
			$type = 'newest';
		break;
		case 'recently-active-groups':
			$type = 'active';
		break;
		case 'popular-groups':
			$type = 'popular';
		break;
		case 'alphabetical-groups':
			$type = 'alphabetical';
		break;
	}

	$per_page = isset( $_POST['max_groups'] ) ? intval( $_POST['max_groups'] ) : 5;

	$groups_args = array(
		'user_id'  => 0,
		'type'     => $type,
		'per_page' => $per_page,
		'max'      => $per_page,
	);

	if ( sz_has_groups( $groups_args ) ) : ?>
		<?php echo "0[[SPLIT]]"; ?>
		<?php while ( sz_groups() ) : sz_the_group(); ?>
			<li <?php sz_group_class(); ?>>
				<div class="item-avatar">
					<a href="<?php sz_group_permalink() ?>"><?php sz_group_avatar_thumb() ?></a>
				</div>

				<div class="item">
					<div class="item-title"><?php sz_group_link(); ?></div>
					<div class="item-meta">
						<?php if ( 'newest-groups' === $_POST['filter'] ) : ?>
							<span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_group_date_created( 0, array( 'relative' => false ) ) ); ?>"><?php printf( __( 'created %s', 'sportszone' ), sz_get_group_date_created() ); ?></span>
						<?php elseif ( 'popular-groups' === $_POST['filter'] ) : ?>
							<span class="activity"><?php sz_group_member_count(); ?></span>
						<?php else : ?>
							<span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( __( 'active %s', 'sportszone' ), sz_get_group_last_active() ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			</li>
		<?php endwhile; ?>

		<?php wp_nonce_field( 'groups_widget_groups_list', '_wpnonce-groups' ); ?>
		<input type="hidden" name="groups_widget_max" id="groups_widget_max" value="<?php echo esc_attr( $_POST['max_groups'] ); ?>" />

	<?php else: ?>

		<?php echo "-1[[SPLIT]]<li>" . __( "No groups matched the current filter.", 'sportszone' ); ?>

	<?php endif;

}
add_action( 'wp_ajax_widget_groups_list',        'groups_ajax_widget_groups_list' );
add_action( 'wp_ajax_nopriv_widget_groups_list', 'groups_ajax_widget_groups_list' );
