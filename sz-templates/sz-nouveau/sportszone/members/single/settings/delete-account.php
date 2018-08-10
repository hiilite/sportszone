<?php
/**
 * SportsZone - Members Settings ( Delete Account )
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_member_hook( 'before', 'settings_template' ); ?>

<h2 class="screen-heading delete-account-screen warn">
	<?php esc_html_e( 'Delete Account', 'sportszone' ); ?>
</h2>

<?php sz_nouveau_user_feedback( 'member-delete-account' ); ?>

<form action="<?php echo esc_url( sz_displayed_user_domain() . sz_get_settings_slug() . '/delete-account' ); ?>" name="account-delete-form" id="#account-delete-form" class="standard-form" method="post">

	<label id="delete-account-understand" class="warn" for="delete-account-understand">
		<input class="disabled" type="checkbox" name="delete-account-understand" value="1" data-sz-disable-input="#delete-account-button" />
		<?php esc_html_e( 'I understand the consequences.', 'sportszone' ); ?>
	</label>

	<?php sz_nouveau_submit_button( 'member-delete-account' ); ?>

</form>

<?php
sz_nouveau_member_hook( 'after', 'settings_template' );
