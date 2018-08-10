<?php
/**
 * SportsZone - Groups Create
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires at the top of the groups creation template file.
 *
 * @since 1.7.0
 */
do_action( 'sz_before_create_group_page' ); ?>

<div id="sportszone">

	<?php

	/**
	 * Fires before the display of group creation content.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_before_create_group_content_template' ); ?>

	<form action="<?php sz_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">

		<?php

		/**
		 * Fires before the display of group creation.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_before_create_group' ); ?>

		<div class="item-list-tabs no-ajax" id="group-create-tabs">
			<ul>

				<?php sz_group_creation_tabs(); ?>

			</ul>
		</div>

		<div id="template-notices" role="alert" aria-atomic="true">
			<?php

			/** This action is documented in sz-templates/sz-legacy/sportszone/activity/index.php */
			do_action( 'template_notices' ); ?>

		</div>

		<div class="item-body" id="group-create-body">

			<?php /* Group creation step 1: Basic group details */ ?>
			<?php if ( sz_is_group_creation_step( 'group-details' ) ) : ?>

				<h2 class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Group Details', 'sportszone' );
				?></h2>

				<?php

				/**
				 * Fires before the display of the group details creation step.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_before_group_details_creation_step' ); ?>

				<div>
					<label for="group-name"><?php _e( 'Group Name (required)', 'sportszone' ); ?></label>
					<input type="text" name="group-name" id="group-name" aria-required="true" value="<?php sz_new_group_name(); ?>" />
				</div>

				<div>
					<label for="group-desc"><?php _e( 'Group Description (required)', 'sportszone' ); ?></label>
					<textarea name="group-desc" id="group-desc" aria-required="true"><?php sz_new_group_description(); ?></textarea>
				</div>

				<?php

				/**
				 * Fires after the display of the group details creation step.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_after_group_details_creation_step' );
				do_action( 'groups_custom_group_fields_editable' ); // @Deprecated

				wp_nonce_field( 'groups_create_save_group-details' ); ?>

			<?php endif; ?>

			<?php /* Group creation step 2: Group settings */ ?>
			<?php if ( sz_is_group_creation_step( 'group-settings' ) ) : ?>

				<h2 class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Group Settings', 'sportszone' );
				?></h2>

				<?php

				/**
				 * Fires before the display of the group settings creation step.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_before_group_settings_creation_step' ); ?>

				<fieldset class="group-create-privacy">

					<legend><?php _e( 'Privacy Options', 'sportszone' ); ?></legend>

					<div class="radio">

						<label for="group-status-public"><input type="radio" name="group-status" id="group-status-public" value="public"<?php if ( 'public' == sz_get_new_group_status() || !sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="public-group-description" /> <?php _e( 'This is a public group', 'sportszone' ); ?></label>

						<ul id="public-group-description">
							<li><?php _e( 'Any site member can join this group.', 'sportszone' ); ?></li>
							<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'sportszone' ); ?></li>
							<li><?php _e( 'Group content and activity will be visible to any site member.', 'sportszone' ); ?></li>
						</ul>

						<label for="group-status-private"><input type="radio" name="group-status" id="group-status-private" value="private"<?php if ( 'private' == sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="private-group-description" /> <?php _e( 'This is a private group', 'sportszone' ); ?></label>

						<ul id="private-group-description">
							<li><?php _e( 'Only users who request membership and are accepted can join the group.', 'sportszone' ); ?></li>
							<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'sportszone' ); ?></li>
							<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'sportszone' ); ?></li>
						</ul>

						<label for="group-status-hidden"><input type="radio" name="group-status" id="group-status-hidden" value="hidden"<?php if ( 'hidden' == sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="hidden-group-description" /> <?php _e('This is a hidden group', 'sportszone' ); ?></label>

						<ul id="hidden-group-description">
							<li><?php _e( 'Only users who are invited can join the group.', 'sportszone' ); ?></li>
							<li><?php _e( 'This group will not be listed in the groups directory or search results.', 'sportszone' ); ?></li>
							<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'sportszone' ); ?></li>
						</ul>

					</div>

				</fieldset>

				<?php // Group type selection ?>
				<?php if ( $group_types = sz_groups_get_group_types( array( 'show_in_create_screen' => true ), 'objects' ) ): ?>

					<fieldset class="group-create-types">
						<legend><?php _e( 'Group Types', 'sportszone' ); ?></legend>

						<p><?php _e( 'Select the types this group should be a part of.', 'sportszone' ); ?></p>

						<?php foreach ( $group_types as $type ) : ?>
							<div class="checkbox">
								<label for="<?php printf( 'group-type-%s', $type->name ); ?>"><input type="checkbox" name="group-types[]" id="<?php printf( 'group-type-%s', $type->name ); ?>" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( true, ! empty( $type->create_screen_checked ) ); ?> /> <?php echo esc_html( $type->labels['name'] ); ?>
									<?php
										if ( ! empty( $type->description ) ) {
											/* translators: Group type description shown when creating a group. */
											printf( __( '&ndash; %s', 'sportszone' ), '<span class="sz-group-type-desc">' . esc_html( $type->description ) . '</span>' );
										}
									?>
								</label>
							</div>

						<?php endforeach; ?>

					</fieldset>

				<?php endif; ?>

				<fieldset class="group-create-invitations">

					<legend><?php _e( 'Group Invitations', 'sportszone' ); ?></legend>

					<p><?php _e( 'Which members of this group are allowed to invite others?', 'sportszone' ); ?></p>

					<div class="radio">

						<label for="group-invite-status-members"><input type="radio" name="group-invite-status" id="group-invite-status-members" value="members"<?php sz_group_show_invite_status_setting( 'members' ); ?> /> <?php _e( 'All group members', 'sportszone' ); ?></label>

						<label for="group-invite-status-mods"><input type="radio" name="group-invite-status" id="group-invite-status-mods" value="mods"<?php sz_group_show_invite_status_setting( 'mods' ); ?> /> <?php _e( 'Group admins and mods only', 'sportszone' ); ?></label>

						<label for="group-invite-status-admins"><input type="radio" name="group-invite-status" id="group-invite-status-admins" value="admins"<?php sz_group_show_invite_status_setting( 'admins' ); ?> /> <?php _e( 'Group admins only', 'sportszone' ); ?></label>

					</div>

				</fieldset>

				<?php

				/**
				 * Fires after the display of the group settings creation step.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_after_group_settings_creation_step' ); ?>

				<?php wp_nonce_field( 'groups_create_save_group-settings' ); ?>

			<?php endif; ?>

			<?php /* Group creation step 3: Avatar Uploads */ ?>
			<?php if ( sz_is_group_creation_step( 'group-avatar' ) ) : ?>

				<h2 class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Group Avatar', 'sportszone' );
				?></h2>

				<?php

				/**
				 * Fires before the display of the group avatar creation step.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_before_group_avatar_creation_step' ); ?>

				<?php if ( 'upload-image' == sz_get_avatar_admin_step() ) : ?>

					<div class="left-menu">

						<?php sz_new_group_avatar(); ?>

					</div><!-- .left-menu -->

					<div class="main-column">
						<p><?php _e( "Upload an image to use as a profile photo for this group. The image will be shown on the main group page, and in search results.", 'sportszone' ); ?></p>

						<p>
							<label for="file" class="sz-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Select an image', 'sportszone' );
							?></label>
							<input type="file" name="file" id="file" />
							<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'sportszone' ); ?>" />
							<input type="hidden" name="action" id="action" value="sz_avatar_upload" />
						</p>

						<p><?php _e( 'To skip the group profile photo upload process, hit the "Next Step" button.', 'sportszone' ); ?></p>
					</div><!-- .main-column -->

					<?php
					/**
					 * Load the Avatar UI templates
					 *
					 * @since 2.3.0
					 */
					sz_avatar_get_templates(); ?>

				<?php endif; ?>

				<?php if ( 'crop-image' == sz_get_avatar_admin_step() ) : ?>

					<h4><?php _e( 'Crop Group Profile Photo', 'sportszone' ); ?></h4>

					<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php esc_attr_e( 'Profile photo to crop', 'sportszone' ); ?>" />

					<div id="avatar-crop-pane">
						<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php esc_attr_e( 'Profile photo preview', 'sportszone' ); ?>" />
					</div>

					<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'sportszone' ); ?>" />

					<input type="hidden" name="image_src" id="image_src" value="<?php sz_avatar_to_crop_src(); ?>" />
					<input type="hidden" name="upload" id="upload" />
					<input type="hidden" id="x" name="x" />
					<input type="hidden" id="y" name="y" />
					<input type="hidden" id="w" name="w" />
					<input type="hidden" id="h" name="h" />

				<?php endif; ?>

				<?php

				/**
				 * Fires after the display of the group avatar creation step.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_after_group_avatar_creation_step' ); ?>

				<?php wp_nonce_field( 'groups_create_save_group-avatar' ); ?>

			<?php endif; ?>

			<?php /* Group creation step 4: Cover image */ ?>
			<?php if ( sz_is_group_creation_step( 'group-cover-image' ) ) : ?>

				<h2 class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Cover Image', 'sportszone' );
				?></h2>

				<?php

				/**
				 * Fires before the display of the group cover image creation step.
				 *
				 * @since 2.4.0
				 */
				do_action( 'sz_before_group_cover_image_creation_step' ); ?>

				<div id="header-cover-image"></div>

				<p><?php _e( 'The Cover Image will be used to customize the header of your group.', 'sportszone' ); ?></p>

				<?php sz_attachments_get_template_part( 'cover-images/index' ); ?>

				<?php

				/**
				 * Fires after the display of the group cover image creation step.
				 *
				 * @since 2.4.0
				 */
				do_action( 'sz_after_group_cover_image_creation_step' ); ?>

				<?php wp_nonce_field( 'groups_create_save_group-cover-image' ); ?>

			<?php endif; ?>

			<?php /* Group creation step 5: Invite friends to group */ ?>
			<?php if ( sz_is_group_creation_step( 'group-invites' ) ) : ?>

				<h2 class="sz-screen-reader-text"><?php
					/* translators: accessibility text */
					_e( 'Group Invites', 'sportszone' );
				?></h2>

				<?php

				/**
				 * Fires before the display of the group invites creation step.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_before_group_invites_creation_step' ); ?>

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
							<p><?php _e('Select people to invite from your friends list.', 'sportszone' ); ?></p>
						</div>

						<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
						<ul id="friend-list" class="item-list">

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

				<?php

				/**
				 * Fires after the display of the group invites creation step.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_after_group_invites_creation_step' ); ?>

			<?php endif; ?>

			<?php

			/**
			 * Fires inside the group admin template.
			 *
			 * Allows plugins to add custom group creation steps.
			 *
			 * @since 1.1.0
			 */
			do_action( 'groups_custom_create_steps' ); ?>

			<?php

			/**
			 * Fires before the display of the group creation step buttons.
			 *
			 * @since 1.1.0
			 */
			do_action( 'sz_before_group_creation_step_buttons' ); ?>

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

			<?php

			/**
			 * Fires after the display of the group creation step buttons.
			 *
			 * @since 1.1.0
			 */
			do_action( 'sz_after_group_creation_step_buttons' ); ?>

			<?php /* Don't leave out this hidden field */ ?>
			<input type="hidden" name="group_id" id="group_id" value="<?php sz_new_group_id(); ?>" />

			<?php

			/**
			 * Fires and displays the groups directory content.
			 *
			 * @since 1.1.0
			 */
			do_action( 'sz_directory_groups_content' ); ?>

		</div><!-- .item-body -->

		<?php

		/**
		 * Fires after the display of group creation.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_after_create_group' ); ?>

	</form>

	<?php

	/**
	 * Fires after the display of group creation content.
	 *
	 * @since 1.6.0
	 */
	do_action( 'sz_after_create_group_content_template' ); ?>

</div>

<?php

/**
 * Fires at the bottom of the groups creation template file.
 *
 * @since 1.7.0
 */
do_action( 'sz_after_create_group_page' );
