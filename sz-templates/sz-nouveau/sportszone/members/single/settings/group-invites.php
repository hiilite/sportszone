<?php
/**
 * SportsZone - Members Settings ( Group Invites )
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<h2 class="screen-heading group-invites-screen">
	<?php _e( 'Group Invites', 'sportszone' ); ?>
</h2>

<?php
if ( 1 === sz_nouveau_groups_get_group_invites_setting() ) {
	 sz_nouveau_user_feedback( 'member-group-invites-friends-only' );
} else {
	 sz_nouveau_user_feedback( 'member-group-invites-all' );
}
?>


<form action="<?php echo esc_url( sz_displayed_user_domain() . sz_get_settings_slug() . '/invites/' ); ?>" name="account-group-invites-form" id="account-group-invites-form" class="standard-form" method="post">

	<label for="account-group-invites-preferences">
		<input type="checkbox" name="account-group-invites-preferences" id="account-group-invites-preferences" value="1" <?php checked( 1, sz_nouveau_groups_get_group_invites_setting() ); ?>/>
			<?php esc_html_e( 'I want to restrict Group invites to my friends only.', 'sportszone' ); ?>
	</label>

	<?php sz_nouveau_submit_button( 'member-group-invites' ); ?>

</form>
