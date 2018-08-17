<?php
/**
 * BP Nouveau Event's manage members template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<h2 class="sz-screen-title <?php if ( sz_is_event_create() ) { echo esc_attr( 'creation-step-name' ); } ?>">
	<?php esc_html_e( 'Manage Event Members', 'sportszone' ); ?>
</h2>

	<p class="sz-help-text"><?php esc_html_e( 'Manage your event members; promote to moderators, admins or demote or ban.', 'sportszone' ); ?></p>

	<dl class="events-manage-members-list">

	<dt class="admin-section section-title"><?php esc_html_e( 'Administrators', 'sportszone' ); ?></dt>

	<?php if ( sz_has_members( '&include=' . sz_event_admin_ids() ) ) : ?>
		<dd class="admin-listing">
			<ul id="admins-list" class="item-list single-line">

				<?php while ( sz_members() ) : sz_the_member(); ?>
				<li class="member-entry clearfix">

					<?php echo sz_core_fetch_avatar( array( 'item_id' => sz_get_member_user_id(), 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => '' ) ); ?>
					<p class="list-title member-name">
						<a href="<?php sz_member_permalink(); ?>"> <?php sz_member_name(); ?></a>
					</p>

					<?php if ( count( sz_event_admin_ids( false, 'array' ) ) > 1 ) : ?>

						<p class="action text-links-list">
							<a class="button confirm admin-demote-to-member" href="<?php sz_event_member_demote_link( sz_get_member_user_id() ); ?>"><?php esc_html_e( 'Demote to Member', 'sportszone' ); ?></a>
						</p>

					<?php endif; ?>

				</li>
				<?php endwhile; ?>

			</ul>
		</dd>
	<?php endif; ?>

	<?php if ( sz_event_has_moderators() ) : ?>

		<dt class="moderator-section section-title"><?php esc_html_e( 'Moderators', 'sportszone' ); ?></dt>

		<dd class="moderator-listing">
		<?php if ( sz_has_members( '&include=' . sz_event_mod_ids() ) ) : ?>
			<ul id="mods-list" class="item-list single-line">

				<?php while ( sz_members() ) : sz_the_member(); ?>
				<li class="members-entry clearfix">

					<?php echo sz_core_fetch_avatar( array( 'item_id' => sz_get_member_user_id(), 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => '' ) ); ?>
					<p class="list-title member-name">
						<a href="<?php sz_member_permalink(); ?>"> <?php sz_member_name(); ?></a>
					</p>

					<div class="members-manage-buttons action text-links-list">
						<a href="<?php sz_event_member_promote_admin_link( array( 'user_id' => sz_get_member_user_id() ) ); ?>" class="button confirm mod-promote-to-admin"><?php esc_html_e( 'Promote to Admin', 'sportszone' ); ?></a>
						<a class="button confirm mod-demote-to-member" href="<?php sz_event_member_demote_link( sz_get_member_user_id() ); ?>"><?php esc_html_e( 'Demote to Member', 'sportszone' ); ?></a>
					</div>

				</li>

				<?php endwhile; ?>

			</ul>

		<?php endif; ?>
	</dd>
	<?php endif ?>


	<dt class="gen-members-section section-title"><?php esc_html_e( 'Members', 'sportszone' ); ?></dt>

	<dd class="general-members-listing">
		<?php if ( sz_event_has_members( 'per_page=15&exclude_banned=0' ) ) : ?>

			<?php if ( sz_event_member_needs_pagination() ) : ?>

				<?php sz_nouveau_pagination( 'top' ) ; ?>

			<?php endif; ?>

			<ul id="members-list" class="item-list single-line">
				<?php while ( sz_event_members() ) : sz_event_the_member(); ?>

					<li class="<?php sz_event_member_css_class(); ?> members-entry clearfix">
						<?php sz_event_member_avatar_mini(); ?>

						<p class="list-title member-name">
							<?php sz_event_member_link(); ?>
							<span class="banned warn">
								<?php if ( sz_get_event_member_is_banned() ) : ?>
									<?php
									/* translators: indicates a user is banned from a event, e.g. "Mike (banned)". */
									esc_html_e( '(banned)', 'sportszone' );
									?>
								<?php endif; ?>
							</span>
						</p>

						<?php sz_nouveau_events_manage_members_buttons( array( 'container' => 'div', 'container_classes' => array( 'members-manage-buttons', 'text-links-list' ), 'parent_element' => '  ' ) ) ; ?>

					</li>

				<?php endwhile; ?>
			</ul>
	</dd>

</dl>

	<?php else:

		sz_nouveau_user_feedback( 'event-manage-members-none' );

	endif; ?>

