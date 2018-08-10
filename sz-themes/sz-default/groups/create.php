<?php

/**
 * SportsZone - Create Group
 *
 * @package SportsZone
 * @subpackage sz-default
 */

get_header( 'sportszone' ); ?>

	<div id="content">
		<div class="padder">
		
		<?php do_action( 'sz_before_create_group_content_template' ); ?>

		<form action="<?php sz_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">
			<h3><?php _e( 'Create a Group', 'sportszone' ); ?> &nbsp;<a class="button" href="<?php echo trailingslashit( sz_get_root_domain() . '/' . sz_get_groups_root_slug() ); ?>"><?php _e( 'Groups Directory', 'sportszone' ); ?></a></h3>

			<?php do_action( 'sz_before_create_group' ); ?>

			<div class="item-list-tabs no-ajax" id="group-create-tabs" role="navigation">
				<ul>

					<?php sz_group_creation_tabs(); ?>

				</ul>
			</div>

			<?php do_action( 'template_notices' ); ?>

			<div class="item-body" id="group-create-body">

				<?php /* Group creation step 1: Basic group details */ ?>
				<?php if ( sz_is_group_creation_step( 'group-details' ) ) : ?>

					<?php do_action( 'sz_before_group_details_creation_step' ); ?>

					<label for="group-name"><?php _e( 'Group Name (required)', 'sportszone' ); ?></label>
					<input type="text" name="group-name" id="group-name" aria-required="true" value="<?php sz_new_group_name(); ?>" />

					<label for="group-desc"><?php _e( 'Group Description (required)', 'sportszone' ); ?></label>
					<textarea name="group-desc" id="group-desc" aria-required="true"><?php sz_new_group_description(); ?></textarea>

					<?php
					do_action( 'sz_after_group_details_creation_step' );
					do_action( 'groups_custom_group_fields_editable' ); // @Deprecated

					wp_nonce_field( 'groups_create_save_group-details' ); ?>

				<?php endif; ?>

				<?php /* Group creation step 2: Group settings */ ?>
				<?php if ( sz_is_group_creation_step( 'group-settings' ) ) : ?>

					<?php do_action( 'sz_before_group_settings_creation_step' ); ?>

					<h4><?php _e( 'Privacy Options', 'sportszone' ); ?></h4>

					<div class="radio">
						<label><input type="radio" name="group-status" value="public"<?php if ( 'public' == sz_get_new_group_status() || !sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e( 'This is a public group', 'sportszone' ); ?></strong>
							<ul>
								<li><?php _e( 'Any site member can join this group.', 'sportszone' ); ?></li>
								<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'sportszone' ); ?></li>
								<li><?php _e( 'Group content and activity will be visible to any site member.', 'sportszone' ); ?></li>
							</ul>
						</label>

						<label><input type="radio" name="group-status" value="private"<?php if ( 'private' == sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e( 'This is a private group', 'sportszone' ); ?></strong>
							<ul>
								<li><?php _e( 'Only users who request membership and are accepted can join the group.', 'sportszone' ); ?></li>
								<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'sportszone' ); ?></li>
								<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'sportszone' ); ?></li>
							</ul>
						</label>

						<label><input type="radio" name="group-status" value="hidden"<?php if ( 'hidden' == sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e('This is a hidden group', 'sportszone'); ?></strong>
							<ul>
								<li><?php _e( 'Only users who are invited can join the group.', 'sportszone' ); ?></li>
								<li><?php _e( 'This group will not be listed in the groups directory or search results.', 'sportszone' ); ?></li>
								<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'sportszone' ); ?></li>
							</ul>
						</label>
					</div>

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

					<?php if ( sz_is_active( 'forums' ) ) : ?>

						<h4><?php _e( 'Group Forums', 'sportszone' ); ?></h4>

						<?php if ( sz_forums_is_installed_correctly() ) : ?>

							<p><?php _e( 'Should this group have a forum?', 'sportszone' ); ?></p>

							<div class="checkbox">
								<label><input type="checkbox" name="group-show-forum" id="group-show-forum" value="1"<?php checked( sz_get_new_group_enable_forum(), true, true ); ?> /> <?php _e( 'Enable discussion forum', 'sportszone' ); ?></label>
							</div>
						<?php elseif ( is_super_admin() ) : ?>

							<p><?php printf( __( '<strong>Attention Site Admin:</strong> Group forums require the <a href="%s">correct setup and configuration</a> of a bbPress installation.', 'sportszone' ), sz_core_do_network_admin() ? network_admin_url( 'settings.php?page=bb-forums-setup' ) :  admin_url( 'admin.php?page=bb-forums-setup' ) ); ?></p>

						<?php endif; ?>

					<?php endif; ?>

					<?php do_action( 'sz_after_group_settings_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-settings' ); ?>

				<?php endif; ?>

				<?php /* Group creation step 3: Avatar Uploads */ ?>
				<?php if ( sz_is_group_creation_step( 'group-avatar' ) ) : ?>

					<?php do_action( 'sz_before_group_avatar_creation_step' ); ?>

					<?php if ( 'upload-image' == sz_get_avatar_admin_step() ) : ?>

						<div class="left-menu">

							<?php sz_new_group_avatar(); ?>

						</div><!-- .left-menu -->

						<div class="main-column">
							<p><?php _e( "Upload an image to use as an avatar for this group. The image will be shown on the main group page, and in search results.", 'sportszone' ); ?></p>

							<p>
								<input type="file" name="file" id="file" />
								<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'sportszone' ); ?>" />
								<input type="hidden" name="action" id="action" value="sz_avatar_upload" />
							</p>

							<p><?php _e( 'To skip the avatar upload process, hit the "Next Step" button.', 'sportszone' ); ?></p>
						</div><!-- .main-column -->

					<?php endif; ?>

					<?php if ( 'crop-image' == sz_get_avatar_admin_step() ) : ?>

						<h3><?php _e( 'Crop Group Avatar', 'sportszone' ); ?></h3>

						<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php esc_attr_e( 'Avatar to crop', 'sportszone' ); ?>" />

						<div id="avatar-crop-pane">
							<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php esc_attr_e( 'Avatar preview', 'sportszone' ); ?>" />
						</div>

						<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'sportszone' ); ?>" />

						<input type="hidden" name="image_src" id="image_src" value="<?php sz_avatar_to_crop_src(); ?>" />
						<input type="hidden" name="upload" id="upload" />
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />

					<?php endif; ?>

					<?php do_action( 'sz_after_group_avatar_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-avatar' ); ?>

				<?php endif; ?>

				<?php /* Group creation step 4: Invite friends to group */ ?>
				<?php if ( sz_is_group_creation_step( 'group-invites' ) ) : ?>

					<?php do_action( 'sz_before_group_invites_creation_step' ); ?>

					<?php if ( sz_is_active( 'friends' ) && sz_get_total_friend_count( sz_loggedin_user_id() ) ) : ?>

						<div class="left-menu">

							<div id="invite-list">
								<ul>
									<?php sz_new_group_invite_friend_list(); ?>
								</ul>

								<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ); ?>
							</div>

						</div><!-- .left-menu -->

						<div class="main-column">

							<div id="message" class="info">
								<p><?php _e('Select people to invite from your friends list.', 'sportszone'); ?></p>
							</div>

							<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
							<ul id="friend-list" class="item-list" role="main">

							<?php if ( sz_group_has_invites() ) : ?>

								<?php while ( sz_group_invites() ) : sz_group_the_invite(); ?>

									<li id="<?php sz_group_invite_item_id(); ?>">

										<?php sz_group_invite_user_avatar(); ?>

										<h4><?php sz_group_invite_user_link(); ?></h4>
										<span class="activity"><?php sz_group_invite_user_last_active(); ?></span>

										<div class="action">
											<a class="remove" href="<?php sz_group_invite_user_remove_invite_url(); ?>" id="<?php sz_group_invite_item_id(); ?>"><?php _e( 'Remove Invite', 'sportszone' ); ?></a>
										</div>
									</li>

								<?php endwhile; ?>

								<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ); ?>

							<?php endif; ?>

							</ul>

						</div><!-- .main-column -->

					<?php else : ?>

						<div id="message" class="info">
							<p><?php _e( 'Once you have built up friend connections you will be able to invite others to your group.', 'sportszone' ); ?></p>
						</div>

					<?php endif; ?>

					<?php wp_nonce_field( 'groups_create_save_group-invites' ); ?>

					<?php do_action( 'sz_after_group_invites_creation_step' ); ?>

				<?php endif; ?>

				<?php do_action( 'groups_custom_create_steps' ); // Allow plugins to add custom group creation steps ?>

				<?php do_action( 'sz_before_group_creation_step_buttons' ); ?>

				<?php if ( 'crop-image' != sz_get_avatar_admin_step() ) : ?>

					<div class="submit" id="previous-next">

						<?php /* Previous Button */ ?>
						<?php if ( !sz_is_first_group_creation_step() ) : ?>

							<input type="button" value="<?php esc_attr_e( 'Back to Previous Step', 'sportszone' ); ?>" id="group-creation-previous" name="previous" onclick="location.href='<?php sz_group_creation_previous_link(); ?>'" />

						<?php endif; ?>

						<?php /* Next Button */ ?>
						<?php if ( !sz_is_last_group_creation_step() && !sz_is_first_group_creation_step() ) : ?>

							<input type="submit" value="<?php esc_attr_e( 'Next Step', 'sportszone' ); ?>" id="group-creation-next" name="save" />

						<?php endif;?>

						<?php /* Create Button */ ?>
						<?php if ( sz_is_first_group_creation_step() ) : ?>

							<input type="submit" value="<?php esc_attr_e( 'Create Group and Continue', 'sportszone' ); ?>" id="group-creation-create" name="save" />

						<?php endif; ?>

						<?php /* Finish Button */ ?>
						<?php if ( sz_is_last_group_creation_step() ) : ?>

							<input type="submit" value="<?php esc_attr_e( 'Finish', 'sportszone' ); ?>" id="group-creation-finish" name="save" />

						<?php endif; ?>
					</div>

				<?php endif;?>

				<?php do_action( 'sz_after_group_creation_step_buttons' ); ?>

				<?php /* Don't leave out this hidden field */ ?>
				<input type="hidden" name="group_id" id="group_id" value="<?php sz_new_group_id(); ?>" />

				<?php do_action( 'sz_directory_groups_content' ); ?>

			</div><!-- .item-body -->

			<?php do_action( 'sz_after_create_group' ); ?>

		</form>
		
		<?php do_action( 'sz_after_create_group_content_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'sportszone' ); ?>
<?php get_footer( 'sportszone' ); ?>
