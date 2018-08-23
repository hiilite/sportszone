<?php
/**
 * SportsZone - Members Settings ( Event Invites )
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<h2 class="screen-heading event-invites-screen">
	<?php _e( 'Event Invites', 'sportszone' ); ?>
</h2>

<?php
if ( 1 === sz_nouveau_events_get_event_invites_setting() ) {
	 sz_nouveau_user_feedback( 'member-event-invites-friends-only' );
} else {
	 sz_nouveau_user_feedback( 'member-event-invites-all' );
}
?>


<form action="<?php echo esc_url( sz_displayed_user_domain() . sz_get_settings_slug() . '/invites/' ); ?>" name="account-event-invites-form" id="account-event-invites-form" class="standard-form" method="post">

	<label for="account-event-invites-preferences">
		<input type="checkbox" name="account-event-invites-preferences" id="account-event-invites-preferences" value="1" <?php checked( 1, sz_nouveau_events_get_event_invites_setting() ); ?>/>
			<?php esc_html_e( 'I want to restrict Event invites to my friends only.', 'sportszone' ); ?>
	</label>

	<?php sz_nouveau_submit_button( 'member-event-invites' ); ?>

</form>
