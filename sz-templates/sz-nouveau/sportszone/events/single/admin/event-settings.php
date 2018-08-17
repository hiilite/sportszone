<?php
/**
 * BP Nouveau Event's edit settings template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<?php if ( sz_is_event_create() ) : ?>

	<h3 class="sz-screen-title creation-step-name">
		<?php esc_html_e( 'Select Event Settings', 'sportszone' ); ?>
	</h3>

<?php else : ?>

	<h2 class="sz-screen-title">
		<?php esc_html_e( 'Change Event Settings', 'sportszone' ); ?>
	</h2>

<?php endif; ?>

<div class="event-settings-selections">

	<fieldset class="radio event-status-type">
		<legend><?php esc_html_e( 'Privacy Options', 'sportszone' ); ?></legend>

		<label for="event-status-public">
			<input type="radio" name="event-status" id="event-status-public" value="public"<?php if ( 'public' === sz_get_new_event_status() || ! sz_get_new_event_status() ) { ?> checked="checked"<?php } ?> aria-describedby="public-event-description" /> <?php esc_html_e( 'This is a public event', 'sportszone' ); ?>
		</label>

		<ul id="public-event-description">
			<li><?php esc_html_e( 'Any site member can join this event.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'This event will be listed in the events directory and in search results.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'Event content and activity will be visible to any site member.', 'sportszone' ); ?></li>
		</ul>

		<label for="event-status-private">
			<input type="radio" name="event-status" id="event-status-private" value="private"<?php if ( 'private' === sz_get_new_event_status() ) { ?> checked="checked"<?php } ?> aria-describedby="private-event-description" /> <?php esc_html_e( 'This is a private event', 'sportszone' ); ?>
		</label>

		<ul id="private-event-description">
			<li><?php esc_html_e( 'Only people who request membership and are accepted can join the event.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'This event will be listed in the events directory and in search results.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'Event content and activity will only be visible to members of the event.', 'sportszone' ); ?></li>
		</ul>

		<label for="event-status-hidden">
			<input type="radio" name="event-status" id="event-status-hidden" value="hidden"<?php if ( 'hidden' === sz_get_new_event_status() ) { ?> checked="checked"<?php } ?> aria-describedby="hidden-event-description" /> <?php esc_html_e( 'This is a hidden event', 'sportszone' ); ?>
		</label>

		<ul id="hidden-event-description">
			<li><?php esc_html_e( 'Only people who are invited can join the event.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'This event will not be listed in the events directory or search results.', 'sportszone' ); ?></li>
			<li><?php esc_html_e( 'Event content and activity will only be visible to members of the event.', 'sportszone' ); ?></li>
		</ul>

	</fieldset>


	<fieldset class="radio event-invitations">
		<legend><?php esc_html_e( 'Event Invitations', 'sportszone' ); ?></legend>

		<p tabindex="0"><?php esc_html_e( 'Which members of this event are allowed to invite others?', 'sportszone' ); ?></p>

		<label for="event-invite-status-members">
			<input type="radio" name="event-invite-status" id="event-invite-status-members" value="members"<?php sz_event_show_invite_status_setting( 'members' ); ?> />
				<?php esc_html_e( 'All event members', 'sportszone' ); ?>
		</label>

		<label for="event-invite-status-mods">
			<input type="radio" name="event-invite-status" id="event-invite-status-mods" value="mods"<?php sz_event_show_invite_status_setting( 'mods' ); ?> />
				<?php esc_html_e( 'Event admins and mods only', 'sportszone' ); ?>
		</label>

		<label for="event-invite-status-admins">
			<input type="radio" name="event-invite-status" id="event-invite-status-admins" value="admins"<?php sz_event_show_invite_status_setting( 'admins' ); ?> />
				<?php esc_html_e( 'Event admins only', 'sportszone' ); ?>
		</label>

	</fieldset>

</div><!-- // .event-settings-selections -->
