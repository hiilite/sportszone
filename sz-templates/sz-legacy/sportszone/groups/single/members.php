<?php
/**
 * SportsZone - Groups Members
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<?php if ( sz_group_has_members( sz_ajax_querystring( 'group_members' ) ) ) : ?>

	<?php

	/**
	 * Fires before the display of the group members content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_group_members_content' ); ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-count-top">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-top">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires before the display of the group members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_group_members_list' ); ?>

	<ul id="member-list" class="item-list">

		<?php while ( sz_group_members() ) : sz_group_the_member(); ?>

			<li>
				<a href="<?php sz_group_member_domain(); ?>">

					<?php sz_group_member_avatar_thumb(); ?>

				</a>

				<h5><?php sz_group_member_link(); ?></h5>
				<span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_group_member_joined_since( array( 'relative' => false ) ) ); ?>"><?php sz_group_member_joined_since(); ?></span>

				<?php

				/**
				 * Fires inside the listing of an individual group member listing item.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_group_members_list_item' ); ?>

				<?php if ( sz_is_active( 'friends' ) ) : ?>

					<div class="action">

						<?php sz_add_friend_button( sz_get_group_member_id(), sz_get_group_member_is_friend() ); ?>

						<?php

						/**
						 * Fires inside the action section of an individual group member listing item.
						 *
						 * @since 1.1.0
						 */
						do_action( 'sz_group_members_list_item_action' ); ?>

					</div>

				<?php endif; ?>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the display of the group members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_group_members_list' ); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-count-bottom">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-bottom">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires after the display of the group members content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_group_members_content' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'No members were found.', 'sportszone' ); ?></p>
	</div>

<?php endif;
