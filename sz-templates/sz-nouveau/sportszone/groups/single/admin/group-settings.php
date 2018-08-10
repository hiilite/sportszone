<?php
/**
 * BP Nouveau Group's edit settings template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<?php if ( sz_is_group_create() ) : ?>

	<h3 class="sz-screen-title creation-step-name">
		<?php esc_html_e( 'Select Group Settings', 'sportszone' ); ?>
	</h3>

<?php else : ?>

	<h2 class="sz-screen-title">
		<?php esc_html_e( 'Change Group Settings', 'sportszone' ); ?>
	</h2>

<?php endif; ?>

<div class="group-settings-selections">

	<fieldset class="radio group-status-type">
		<legend><?php esc_html_e( 'Privacy Options', 'sportszone' ); ?></legend>

		<label for="group-status-public">
			<input type="radio" name="group-status" id="group-status-public" value="public"<?php if ( 'public' === sz_get_new_group_status() || ! sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="public-group-description" /> <?php esc_html_e( 'This is a public group', 'sportszone' ); ?>
		</label>

		<ul id="public-group-description">
			<li><?php esc_html_e( 'Any site member can join this group.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'This group will be listed in the groups directory and in search results.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'Group content and activity will be visible to any site member.', 'sportszone' ); ?></li>
		</ul>

		<label for="group-status-private">
			<input type="radio" name="group-status" id="group-status-private" value="private"<?php if ( 'private' === sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="private-group-description" /> <?php esc_html_e( 'This is a private group', 'sportszone' ); ?>
		</label>

		<ul id="private-group-description">
			<li><?php esc_html_e( 'Only people who request membership and are accepted can join the group.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'This group will be listed in the groups directory and in search results.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'Group content and activity will only be visible to members of the group.', 'sportszone' ); ?></li>
		</ul>

		<label for="group-status-hidden">
			<input type="radio" name="group-status" id="group-status-hidden" value="hidden"<?php if ( 'hidden' === sz_get_new_group_status() ) { ?> checked="checked"<?php } ?> aria-describedby="hidden-group-description" /> <?php esc_html_e( 'This is a hidden group', 'sportszone' ); ?>
		</label>

		<ul id="hidden-group-description">
			<li><?php esc_html_e( 'Only people who are invited can join the group.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'This group will not be listed in the groups directory or search results.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'Group content and activity will only be visible to members of the group.', 'sportszone' ); ?></li>
		</ul>

	</fieldset>


	<fieldset class="radio group-invitations">
		<legend><?php esc_html_e( 'Group Invitations', 'sportszone' ); ?></legend>

		<p tabindex="0"><?php esc_html_e( 'Which members of this group are allowed to invite others?', 'sportszone' ); ?></p>

		<label for="group-invite-status-members">
			<input type="radio" name="group-invite-status" id="group-invite-status-members" value="members"<?php sz_group_show_invite_status_setting( 'members' ); ?> />
				<?php esc_html_e( 'All group members', 'sportszone' ); ?>
		</label>

		<label for="group-invite-status-mods">
			<input type="radio" name="group-invite-status" id="group-invite-status-mods" value="mods"<?php sz_group_show_invite_status_setting( 'mods' ); ?> />
				<?php esc_html_e( 'Group admins and mods only', 'sportszone' ); ?>
		</label>

		<label for="group-invite-status-admins">
			<input type="radio" name="group-invite-status" id="group-invite-status-admins" value="admins"<?php sz_group_show_invite_status_setting( 'admins' ); ?> />
				<?php esc_html_e( 'Group admins only', 'sportszone' ); ?>
		</label>

	</fieldset>

</div><!-- // .group-settings-selections -->
