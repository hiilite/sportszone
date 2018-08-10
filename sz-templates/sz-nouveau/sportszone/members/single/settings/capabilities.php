<?php
/**
 * SportsZone - Members Settings ( Capabilities )
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_member_hook( 'before', 'settings_template' ); ?>

<h2 class="screen-heading member-capabilities-screen">
	<?php esc_html_e( 'Members Capabilities', 'sportszone' ); ?>
</h2>

<form action="<?php echo esc_url( sz_displayed_user_domain() . sz_get_settings_slug() . '/capabilities/' ); ?>" name="account-capabilities-form" id="account-capabilities-form" class="standard-form" method="post">

	<label for="user-spammer">
		<input type="checkbox" name="user-spammer" id="user-spammer" value="1" <?php checked( sz_is_user_spammer( sz_displayed_user_id() ) ); ?> />
			<?php esc_html_e( 'This member is a spammer.', 'sportszone' ); ?>
	</label>

	<?php sz_nouveau_submit_button( 'member-capabilities' ); ?>

</form>

<?php
sz_nouveau_member_hook( 'after', 'settings_template' );
