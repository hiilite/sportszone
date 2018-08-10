<?php
/**
 * SportsZone - Groups Requests Loop
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<?php if ( sz_group_has_membership_requests( sz_ajax_querystring( 'membership_requests' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="group-mem-requests-count-top">

			<?php sz_group_requests_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-mem-requests-pag-top">

			<?php sz_group_requests_pagination_links(); ?>

		</div>

	</div>

	<ul id="request-list" class="item-list">
		<?php while ( sz_group_membership_requests() ) : sz_group_the_membership_request(); ?>

			<li class="item-list group-request-list">

				<div class="item-avatar"><?php sz_group_request_user_avatar_thumb(); ?></div>

				<div class="item">

					<div class="item-title"><?php sz_group_request_user_link(); ?> </div>

					<span class="activity"><?php sz_group_request_time_since_requested(); ?></span>

					<p class="comments"><?php sz_group_request_comment(); ?></p>

					<?php

					/**
					 * Fires inside the groups membership request list loop.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_group_membership_requests_admin_item' ); ?>

				</div>

				<div class="action">

					<?php sz_button( array( 'id' => 'group_membership_accept', 'component' => 'groups', 'wrapper_class' => 'accept', 'link_href' => sz_get_group_request_accept_link(), 'link_text' => __( 'Accept', 'sportszone' ) ) ); ?>

					<?php sz_button( array( 'id' => 'group_membership_reject', 'component' => 'groups', 'wrapper_class' => 'reject', 'link_href' => sz_get_group_request_reject_link(), 'link_text' => __( 'Reject', 'sportszone' ) ) ); ?>

					<?php

					/**
					 * Fires inside the list of membership request actions.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_group_membership_requests_admin_item_action' ); ?>

				</div>
			</li>

		<?php endwhile; ?>
	</ul>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="group-mem-requests-count-bottom">

			<?php sz_group_requests_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="group-mem-requests-pag-bottom">

			<?php sz_group_requests_pagination_links(); ?>

		</div>

	</div>

	<?php else: ?>

		<div id="message" class="info">
			<p><?php _e( 'There are no pending membership requests.', 'sportszone' ); ?></p>
		</div>

	<?php endif;
