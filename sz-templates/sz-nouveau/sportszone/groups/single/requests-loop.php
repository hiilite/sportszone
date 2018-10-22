<?php
/**
 * SportsZone - Groups Requests Loop
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<?php if ( sz_group_has_membership_requests( sz_ajax_querystring( 'membership_requests' ) ) ) : ?>

	<h2 class="sz-screen-title">
		<?php esc_html_e( 'Manage Membership Requests', 'sportszone' ); ?>
	</h2>

	<?php //sz_nouveau_pagination( 'top' ); ?>

	<ul id="request-list" class="item-list sz-list membership-requests-list">
		<?php
		while ( sz_group_membership_requests() ) :
			sz_group_the_membership_request();
		?>

			<li>
				<div class="item-avatar">
					<?php sz_group_request_user_avatar_thumb(); ?>
				</div>

				<div class="item">

					<div class="item-title">
						<h3><?php sz_group_request_user_link(); ?></h3>
					</div>

					<div class="item-meta">
						<span class="comments"><?php sz_group_request_comment(); ?></span>
						<span class="activity"><?php sz_group_request_time_since_requested(); ?></span>
						<?php sz_nouveau_group_hook( '', 'membership_requests_admin_item' ); ?>
					</div>

				</div>

				<?php sz_nouveau_groups_request_buttons(); ?>
			</li>

		<?php endwhile; ?>
	</ul>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'group-requests-none' ); ?>

<?php
endif;
