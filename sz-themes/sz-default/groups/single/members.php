<?php if ( sz_group_has_members( 'exclude_admins_mods=0' ) ) : ?>

	<?php do_action( 'sz_before_group_members_content' ); ?>

	<div class="item-list-tabs" id="subnav" role="navigation">
		<ul>

			<?php do_action( 'sz_members_directory_member_sub_types' ); ?>

		</ul>
	</div>

	<div id="pag-top" class="pagination no-ajax">

		<div class="pag-count" id="member-count-top">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-top">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'sz_before_group_members_list' ); ?>

	<ul id="member-list" class="item-list" role="main">

		<?php while ( sz_group_members() ) : sz_group_the_member(); ?>

			<li>
				<a href="<?php sz_group_member_domain(); ?>">

					<?php sz_group_member_avatar_thumb(); ?>

				</a>

				<h5><?php sz_group_member_link(); ?></h5>
				<span class="activity"><?php sz_group_member_joined_since(); ?></span>

				<?php do_action( 'sz_group_members_list_item' ); ?>

				<?php if ( sz_is_active( 'friends' ) ) : ?>

					<div class="action">

						<?php sz_add_friend_button( sz_get_group_member_id(), sz_get_group_member_is_friend() ); ?>

						<?php do_action( 'sz_group_members_list_item_action' ); ?>

					</div>

				<?php endif; ?>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php do_action( 'sz_after_group_members_list' ); ?>

	<div id="pag-bottom" class="pagination no-ajax">

		<div class="pag-count" id="member-count-bottom">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-pag-bottom">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'sz_after_group_members_content' ); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This group has no members.', 'sportszone' ); ?></p>
	</div>

<?php endif; ?>
