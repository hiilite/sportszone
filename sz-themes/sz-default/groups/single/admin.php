<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php sz_group_admin_tabs(); ?>
	</ul>
</div><!-- .item-list-tabs -->

<form action="<?php sz_group_admin_form_action(); ?>" name="group-settings-form" id="group-settings-form" class="standard-form" method="post" enctype="multipart/form-data" role="main">

<?php do_action( 'sz_before_group_admin_content' ); ?>

<?php /* Edit Group Details */ ?>
<?php if ( sz_is_group_admin_screen( 'edit-details' ) ) : ?>

	<?php do_action( 'sz_before_group_details_admin' ); ?>

	<label for="group-name"><?php _e( 'Group Name (required)', 'sportszone' ); ?></label>
	<input type="text" name="group-name" id="group-name" value="<?php sz_group_name(); ?>" aria-required="true" />

	<label for="group-desc"><?php _e( 'Group Description (required)', 'sportszone' ); ?></label>
	<textarea name="group-desc" id="group-desc" aria-required="true"><?php sz_group_description_editable(); ?></textarea>

	<?php do_action( 'groups_custom_group_fields_editable' ); ?>

	<p>
		<label for="group-notifiy-members">
			<input type="checkbox" name="group-notify-members" value="1" /> <?php _e( 'Notify group members of these changes via email', 'sportszone' ); ?>
		</label>
	</p>

	<?php do_action( 'sz_after_group_details_admin' ); ?>

	<p><input type="submit" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?>" id="save" name="save" /></p>
	<?php wp_nonce_field( 'groups_edit_group_details' ); ?>

<?php endif; ?>

<?php /* Manage Group Settings */ ?>
<?php if ( sz_is_group_admin_screen( 'group-settings' ) ) : ?>

	<?php do_action( 'sz_before_group_settings_admin' ); ?>

	<?php if ( sz_is_active( 'forums' ) ) : ?>

		<?php if ( sz_forums_is_installed_correctly() ) : ?>

			<div class="checkbox">
				<label><input type="checkbox" name="group-show-forum" id="group-show-forum" value="1"<?php sz_group_show_forum_setting(); ?> /> <?php _e( 'Enable discussion forum', 'sportszone' ); ?></label>
			</div>

			<hr />

		<?php endif; ?>

	<?php endif; ?>

	<h4><?php _e( 'Privacy Options', 'sportszone' ); ?></h4>

	<div class="radio">
		<label>
			<input type="radio" name="group-status" value="public"<?php sz_group_show_status_setting( 'public' ); ?> />
			<strong><?php _e( 'This is a public group', 'sportszone' ); ?></strong>
			<ul>
				<li><?php _e( 'Any site member can join this group.', 'sportszone' ); ?></li>
				<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'sportszone' ); ?></li>
				<li><?php _e( 'Group content and activity will be visible to any site member.', 'sportszone' ); ?></li>
			</ul>
		</label>

		<label>
			<input type="radio" name="group-status" value="private"<?php sz_group_show_status_setting( 'private' ); ?> />
			<strong><?php _e( 'This is a private group', 'sportszone' ); ?></strong>
			<ul>
				<li><?php _e( 'Only users who request membership and are accepted can join the group.', 'sportszone' ); ?></li>
				<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'sportszone' ); ?></li>
				<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'sportszone' ); ?></li>
			</ul>
		</label>

		<label>
			<input type="radio" name="group-status" value="hidden"<?php sz_group_show_status_setting( 'hidden' ); ?> />
			<strong><?php _e( 'This is a hidden group', 'sportszone' ); ?></strong>
			<ul>
				<li><?php _e( 'Only users who are invited can join the group.', 'sportszone' ); ?></li>
				<li><?php _e( 'This group will not be listed in the groups directory or search results.', 'sportszone' ); ?></li>
				<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'sportszone' ); ?></li>
			</ul>
		</label>
	</div>

	<hr />

	<h4><?php _e( 'Group Invitations', 'sportszone' ); ?></h4>

	<p><?php _e( 'Which members of this group are allowed to invite others?', 'sportszone' ); ?></p>

	<div class="radio">
		<label>
			<input type="radio" name="group-invite-status" value="members"<?php sz_group_show_invite_status_setting( 'members' ); ?> />
			<strong><?php _e( 'All group members', 'sportszone' ); ?></strong>
		</label>

		<label>
			<input type="radio" name="group-invite-status" value="mods"<?php sz_group_show_invite_status_setting( 'mods' ); ?> />
			<strong><?php _e( 'Group admins and mods only', 'sportszone' ); ?></strong>
		</label>

		<label>
			<input type="radio" name="group-invite-status" value="admins"<?php sz_group_show_invite_status_setting( 'admins' ); ?> />
			<strong><?php _e( 'Group admins only', 'sportszone' ); ?></strong>
		</label>
 	</div>

	<hr />

	<?php do_action( 'sz_after_group_settings_admin' ); ?>

	<p><input type="submit" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?>" id="save" name="save" /></p>
	<?php wp_nonce_field( 'groups_edit_group_settings' ); ?>

<?php endif; ?>

<?php /* Group Avatar Settings */ ?>
<?php if ( sz_is_group_admin_screen( 'group-avatar' ) ) : ?>

	<?php if ( 'upload-image' == sz_get_avatar_admin_step() ) : ?>

			<p><?php _e("Upload an image to use as an avatar for this group. The image will be shown on the main group page, and in search results.", 'sportszone'); ?></p>

			<p>
				<input type="file" name="file" id="file" />
				<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'sportszone' ); ?>" />
				<input type="hidden" name="action" id="action" value="sz_avatar_upload" />
			</p>

			<?php if ( sz_get_group_has_avatar() ) : ?>

				<p><?php _e( "If you'd like to remove the existing avatar but not upload a new one, please use the delete avatar button.", 'sportszone' ); ?></p>

				<?php sz_button( array( 'id' => 'delete_group_avatar', 'component' => 'groups', 'wrapper_id' => 'delete-group-avatar-button', 'link_class' => 'edit', 'link_href' => sz_get_group_avatar_delete_link(), 'link_title' => __( 'Delete Avatar', 'sportszone' ), 'link_text' => __( 'Delete Avatar', 'sportszone' ) ) ); ?>

			<?php endif; ?>

			<?php wp_nonce_field( 'sz_avatar_upload' ); ?>

	<?php endif; ?>

	<?php if ( 'crop-image' == sz_get_avatar_admin_step() ) : ?>

		<h3><?php _e( 'Crop Avatar', 'sportszone' ); ?></h3>

		<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php esc_attr_e( 'Avatar to crop', 'sportszone' ); ?>" />

		<div id="avatar-crop-pane">
			<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php esc_attr_e( 'Avatar preview', 'sportszone' ); ?>" />
		</div>

		<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'sportszone' ); ?>" />

		<input type="hidden" name="image_src" id="image_src" value="<?php sz_avatar_to_crop_src(); ?>" />
		<input type="hidden" id="x" name="x" />
		<input type="hidden" id="y" name="y" />
		<input type="hidden" id="w" name="w" />
		<input type="hidden" id="h" name="h" />

		<?php wp_nonce_field( 'sz_avatar_cropstore' ); ?>

	<?php endif; ?>

<?php endif; ?>

<?php /* Manage Group Members */ ?>
<?php if ( sz_is_group_admin_screen( 'manage-members' ) ) : ?>

	<?php do_action( 'sz_before_group_manage_members_admin' ); ?>

	<div class="sz-widget">
		<h4><?php _e( 'Administrators', 'sportszone' ); ?></h4>

		<?php if ( sz_has_members( '&include='. sz_group_admin_ids() ) ) : ?>

		<ul id="admins-list" class="item-list single-line">

			<?php while ( sz_members() ) : sz_the_member(); ?>
			<li>
				<?php echo sz_core_fetch_avatar( array( 'item_id' => sz_get_member_user_id(), 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_get_member_name() ) ) ); ?>
				<h5>
					<a href="<?php sz_member_permalink(); ?>"> <?php sz_member_name(); ?></a>
					<?php if ( count( sz_group_admin_ids( false, 'array' ) ) > 1 ) : ?>
					<span class="small">
						<a class="button confirm admin-demote-to-member" href="<?php sz_group_member_demote_link( sz_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'sportszone' ); ?></a>
					</span>
					<?php endif; ?>
				</h5>
			</li>
			<?php endwhile; ?>

		</ul>

		<?php endif; ?>

	</div>

	<?php if ( sz_group_has_moderators() ) : ?>
		<div class="sz-widget">
			<h4><?php _e( 'Moderators', 'sportszone' ); ?></h4>

			<?php if ( sz_has_members( '&include=' . sz_group_mod_ids() ) ) : ?>
				<ul id="mods-list" class="item-list single-line">

					<?php while ( sz_members() ) : sz_the_member(); ?>
					<li>
						<?php echo sz_core_fetch_avatar( array( 'item_id' => sz_get_member_user_id(), 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => sprintf( __( 'Profile picture of %s', 'sportszone' ), sz_get_member_name() ) ) ); ?>
						<h5>
							<a href="<?php sz_member_permalink(); ?>"> <?php sz_member_name(); ?></a>
							<span class="small">
								<a href="<?php sz_group_member_promote_admin_link( array( 'user_id' => sz_get_member_user_id() ) ); ?>" class="button confirm mod-promote-to-admin" title="<?php esc_attr_e( 'Promote to Admin', 'sportszone' ); ?>"><?php _e( 'Promote to Admin', 'sportszone' ); ?></a>
								<a class="button confirm mod-demote-to-member" href="<?php sz_group_member_demote_link( sz_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'sportszone' ); ?></a>
							</span>
						</h5>
					</li>
					<?php endwhile; ?>

				</ul>

			<?php endif; ?>
		</div>
	<?php endif ?>


	<div class="sz-widget">
		<h4><?php _e("Members", "sportszone"); ?></h4>

		<?php if ( sz_group_has_members( 'per_page=15&exclude_banned=0' ) ) : ?>

			<?php if ( sz_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php sz_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php sz_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

			<ul id="members-list" class="item-list single-line">
				<?php while ( sz_group_members() ) : sz_group_the_member(); ?>

					<li class="<?php sz_group_member_css_class(); ?>">
						<?php sz_group_member_avatar_mini(); ?>

						<h5>
							<?php sz_group_member_link(); ?>

							<?php if ( sz_get_group_member_is_banned() ) _e( '(banned)', 'sportszone'); ?>

							<span class="small">

							<?php if ( sz_get_group_member_is_banned() ) : ?>

								<a href="<?php sz_group_member_unban_link(); ?>" class="button confirm member-unban" title="<?php esc_attr_e( 'Unban this member', 'sportszone' ); ?>"><?php _e( 'Remove Ban', 'sportszone' ); ?></a>

							<?php else : ?>

								<a href="<?php sz_group_member_ban_link(); ?>" class="button confirm member-ban" title="<?php esc_attr_e( 'Kick and ban this member', 'sportszone' ); ?>"><?php _e( 'Kick &amp; Ban', 'sportszone' ); ?></a>
								<a href="<?php sz_group_member_promote_mod_link(); ?>" class="button confirm member-promote-to-mod" title="<?php esc_attr_e( 'Promote to Mod', 'sportszone' ); ?>"><?php _e( 'Promote to Mod', 'sportszone' ); ?></a>
								<a href="<?php sz_group_member_promote_admin_link(); ?>" class="button confirm member-promote-to-admin" title="<?php esc_attr_e( 'Promote to Admin', 'sportszone' ); ?>"><?php _e( 'Promote to Admin', 'sportszone' ); ?></a>

							<?php endif; ?>

								<a href="<?php sz_group_member_remove_link(); ?>" class="button confirm" title="<?php esc_attr_e( 'Remove this member', 'sportszone' ); ?>"><?php _e( 'Remove from group', 'sportszone' ); ?></a>

								<?php do_action( 'sz_group_manage_members_admin_item' ); ?>

							</span>
						</h5>
					</li>

				<?php endwhile; ?>
			</ul>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'This group has no members.', 'sportszone' ); ?></p>
			</div>

		<?php endif; ?>

	</div>

	<?php do_action( 'sz_after_group_manage_members_admin' ); ?>

<?php endif; ?>

<?php /* Manage Membership Requests */ ?>
<?php if ( sz_is_group_admin_screen( 'membership-requests' ) ) : ?>

	<?php do_action( 'sz_before_group_membership_requests_admin' ); ?>

	<?php if ( sz_group_has_membership_requests() ) : ?>

		<ul id="request-list" class="item-list">
			<?php while ( sz_group_membership_requests() ) : sz_group_the_membership_request(); ?>

				<li>
					<?php sz_group_request_user_avatar_thumb(); ?>
					<h4><?php sz_group_request_user_link(); ?> <span class="comments"><?php sz_group_request_comment(); ?></span></h4>
					<span class="activity"><?php sz_group_request_time_since_requested(); ?></span>

					<?php do_action( 'sz_group_membership_requests_admin_item' ); ?>

					<div class="action">

						<?php sz_button( array( 'id' => 'group_membership_accept', 'component' => 'groups', 'wrapper_class' => 'accept', 'link_href' => sz_get_group_request_accept_link(), 'link_title' => __( 'Accept', 'sportszone' ), 'link_text' => __( 'Accept', 'sportszone' ) ) ); ?>

						<?php sz_button( array( 'id' => 'group_membership_reject', 'component' => 'groups', 'wrapper_class' => 'reject', 'link_href' => sz_get_group_request_reject_link(), 'link_title' => __( 'Reject', 'sportszone' ), 'link_text' => __( 'Reject', 'sportszone' ) ) ); ?>

						<?php do_action( 'sz_group_membership_requests_admin_item_action' ); ?>

					</div>
				</li>

			<?php endwhile; ?>
		</ul>

	<?php else: ?>

		<div id="message" class="info">
			<p><?php _e( 'There are no pending membership requests.', 'sportszone' ); ?></p>
		</div>

	<?php endif; ?>

	<?php do_action( 'sz_after_group_membership_requests_admin' ); ?>

<?php endif; ?>

<?php do_action( 'groups_custom_edit_steps' ) // Allow plugins to add custom group edit screens ?>

<?php /* Delete Group Option */ ?>
<?php if ( sz_is_group_admin_screen( 'delete-group' ) ) : ?>

	<?php do_action( 'sz_before_group_delete_admin' ); ?>

	<div id="message" class="info">
		<p><?php _e( 'WARNING: Deleting this group will completely remove ALL content associated with it. There is no way back, please be careful with this option.', 'sportszone' ); ?></p>
	</div>

	<label><input type="checkbox" name="delete-group-understand" id="delete-group-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-group-button').disabled = ''; } else { document.getElementById('delete-group-button').disabled = 'disabled'; }" /> <?php _e( 'I understand the consequences of deleting this group.', 'sportszone' ); ?></label>

	<?php do_action( 'sz_after_group_delete_admin' ); ?>

	<div class="submit">
		<input type="submit" disabled="disabled" value="<?php esc_attr_e( 'Delete Group', 'sportszone' ); ?>" id="delete-group-button" name="delete-group-button" />
	</div>

	<?php wp_nonce_field( 'groups_delete_group' ); ?>

<?php endif; ?>

<?php /* This is important, don't forget it */ ?>
	<input type="hidden" name="group-id" id="group-id" value="<?php sz_group_id(); ?>" />

<?php do_action( 'sz_after_group_admin_content' ); ?>

</form><!-- #group-settings-form -->

