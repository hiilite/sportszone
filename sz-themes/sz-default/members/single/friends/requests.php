<?php do_action( 'sz_before_member_friend_requests_content' ); ?>

<?php if ( sz_has_members( 'type=alphabetical&include=' . sz_get_friendship_requests() ) ) : ?>

	<div id="pag-top" class="pagination no-ajax">

		<div class="pag-count" id="member-dir-count-top">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

	<ul id="friend-list" class="item-list" role="main">
		<?php while ( sz_members() ) : sz_the_member(); ?>

			<li id="friendship-<?php sz_friend_friendship_id(); ?>">
				<div class="item-avatar">
					<a href="<?php sz_member_link(); ?>"><?php sz_member_avatar(); ?></a>
				</div>

				<div class="item">
					<div class="item-title"><a href="<?php sz_member_link(); ?>"><?php sz_member_name(); ?></a></div>
					<div class="item-meta"><span class="activity"><?php sz_member_last_active(); ?></span></div>
				</div>

				<?php do_action( 'sz_friend_requests_item' ); ?>

				<div class="action">
					<a class="button accept" href="<?php sz_friend_accept_request_link(); ?>"><?php _e( 'Accept', 'sportszone' ); ?></a> &nbsp;
					<a class="button reject" href="<?php sz_friend_reject_request_link(); ?>"><?php _e( 'Reject', 'sportszone' ); ?></a>

					<?php do_action( 'sz_friend_requests_item_action' ); ?>
				</div>
			</li>

		<?php endwhile; ?>
	</ul>

	<?php do_action( 'sz_friend_requests_content' ); ?>

	<div id="pag-bottom" class="pagination no-ajax">

		<div class="pag-count" id="member-dir-count-bottom">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'You have no pending friendship requests.', 'sportszone' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'sz_after_member_friend_requests_content' ); ?>
