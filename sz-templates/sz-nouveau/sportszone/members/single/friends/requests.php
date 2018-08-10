<?php
/**
 * SportsZone - Members Friends Requests
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<h2 class="screen-heading friendship-requests-screen"><?php esc_html_e( 'Friendship Requests', 'sportszone' ); ?></h2>

<?php sz_nouveau_member_hook( 'before', 'friend_requests_content' ); ?>

<?php if ( sz_has_members( 'type=alphabetical&include=' . sz_get_friendship_requests() ) ) : ?>

	<?php sz_nouveau_pagination( 'top' ); ?>

	<ul id="friend-list" class="<?php sz_nouveau_loop_classes(); ?>" data-sz-list="friendship_requests">
		<?php
		while ( sz_members() ) :
			sz_the_member();
		?>

			<li id="friendship-<?php sz_friend_friendship_id(); ?>" <?php sz_member_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php sz_friend_friendship_id(); ?>" data-sz-item-component="members">
				<div class="item-avatar">
					<a href="<?php sz_member_link(); ?>"><?php sz_member_avatar( array( 'type' => 'full' ) ); ?></a>
				</div>

				<div class="item">
					<div class="item-title"><a href="<?php sz_member_link(); ?>"><?php sz_member_name(); ?></a></div>
					<div class="item-meta"><span class="activity"><?php sz_member_last_active(); ?></span></div>

					<?php sz_nouveau_friend_hook( 'requests_item' ); ?>
				</div>

				<?php sz_nouveau_members_loop_buttons(); ?>
			</li>

		<?php endwhile; ?>
	</ul>

	<?php sz_nouveau_friend_hook( 'requests_content' ); ?>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'member-requests-none' ); ?>

<?php endif; ?>

<?php
sz_nouveau_member_hook( 'after', 'friend_requests_content' );
