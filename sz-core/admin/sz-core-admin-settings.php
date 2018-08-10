<?php
/**
 * SportsZone Admin Settings.
 *
 * @package SportsZone
 * @subpackage CoreAdministration
 * @since 2.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main settings section description for the settings page.
 *
 * @since 1.6.0
 */
function sz_admin_setting_callback_main_section() { }

/**
 * Admin bar for logged out users setting field.
 *
 * @since 1.6.0
 *
 */
function sz_admin_setting_callback_admin_bar() {
?>

	<input id="hide-loggedout-adminbar" name="hide-loggedout-adminbar" type="checkbox" value="1" <?php checked( !sz_hide_loggedout_adminbar( false ) ); ?> />
	<label for="hide-loggedout-adminbar"><?php _e( 'Show the Toolbar for logged out users', 'sportszone' ); ?></label>

<?php
}

/**
 * Allow members to delete their accounts setting field.
 *
 * @since 1.6.0
 *
 */
function sz_admin_setting_callback_account_deletion() {
?>

	<input id="sz-disable-account-deletion" name="sz-disable-account-deletion" type="checkbox" value="1" <?php checked( !sz_disable_account_deletion( false ) ); ?> />
	<label for="sz-disable-account-deletion"><?php _e( 'Allow registered members to delete their own accounts', 'sportszone' ); ?></label>

<?php
}

/**
 * Form element to change the active template pack.
 */
function sz_admin_setting_callback_theme_package_id() {
	$options = '';

	/*
	 * Note: This should never be empty. /sz-templates/ is the
	 * canonical backup if no other packages exist. If there's an error here,
	 * something else is wrong.
	 *
	 * See SportsZone::register_theme_packages()
	 */
	foreach ( (array) sportszone()->theme_compat->packages as $id => $theme ) {
		$options .= sprintf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $id ),
			selected( $theme->id, sz_get_theme_package_id(), false ),
			esc_html( $theme->name )
		);
	}

	if ( $options ) : ?>
		<select name="_sz_theme_package_id" id="_sz_theme_package_id" aria-describedby="_sz_theme_package_description"><?php echo $options; ?></select>
		<p id="_sz_theme_package_description" class="description"><?php esc_html_e( 'The selected Template Pack will serve all SportsZone templates.', 'sportszone' ); ?></p>

	<?php else : ?>
		<p><?php esc_html_e( 'No template packages available.', 'sportszone' ); ?></p>

	<?php endif;
}

/** Activity *******************************************************************/

/**
 * Groups settings section description for the settings page.
 *
 * @since 1.6.0
 */
function sz_admin_setting_callback_activity_section() { }

/**
 * Allow Akismet setting field.
 *
 * @since 1.6.0
 *
 */
function sz_admin_setting_callback_activity_akismet() {
?>

	<input id="_sz_enable_akismet" name="_sz_enable_akismet" type="checkbox" value="1" <?php checked( sz_is_akismet_active( true ) ); ?> />
	<label for="_sz_enable_akismet"><?php _e( 'Allow Akismet to scan for activity stream spam', 'sportszone' ); ?></label>

<?php
}

/**
 * Allow activity comments on posts and comments.
 *
 * @since 1.6.0
 */
function sz_admin_setting_callback_blogforum_comments() {
?>

	<input id="sz-disable-blogforum-comments" name="sz-disable-blogforum-comments" type="checkbox" value="1" <?php checked( !sz_disable_blogforum_comments( false ) ); ?> />
	<label for="sz-disable-blogforum-comments"><?php _e( 'Allow activity stream commenting on posts and comments', 'sportszone' ); ?></label>

<?php
}

/**
 * Allow Heartbeat to refresh activity stream.
 *
 * @since 2.0.0
 */
function sz_admin_setting_callback_heartbeat() {
?>

	<input id="_sz_enable_heartbeat_refresh" name="_sz_enable_heartbeat_refresh" type="checkbox" value="1" <?php checked( sz_is_activity_heartbeat_active( true ) ); ?> />
	<label for="_sz_enable_heartbeat_refresh"><?php _e( 'Automatically check for new items while viewing the activity stream', 'sportszone' ); ?></label>

<?php
}

/**
 * Sanitization for sz-disable-blogforum-comments setting.
 *
 * In the UI, a checkbox asks whether you'd like to *enable* post/comment activity comments. For
 * legacy reasons, the option that we store is 1 if these comments are *disabled*. So we use this
 * function to flip the boolean before saving the intval.
 *
 * @since 1.6.0
 *
 * @param bool $value Whether or not to sanitize.
 * @return bool
 */
function sz_admin_sanitize_callback_blogforum_comments( $value = false ) {
	return $value ? 0 : 1;
}

/** XProfile ******************************************************************/

/**
 * Profile settings section description for the settings page.
 *
 * @since 1.6.0
 */
function sz_admin_setting_callback_xprofile_section() { }

/**
 * Enable BP->WP profile syncing field.
 *
 * @since 1.6.0
 *
 */
function sz_admin_setting_callback_profile_sync() {
?>

	<input id="sz-disable-profile-sync" name="sz-disable-profile-sync" type="checkbox" value="1" <?php checked( !sz_disable_profile_sync( false ) ); ?> />
	<label for="sz-disable-profile-sync"><?php _e( 'Enable SportsZone to WordPress profile syncing', 'sportszone' ); ?></label>

<?php
}

/**
 * Allow members to upload avatars field.
 *
 * @since 1.6.0
 *
 */
function sz_admin_setting_callback_avatar_uploads() {
?>

	<input id="sz-disable-avatar-uploads" name="sz-disable-avatar-uploads" type="checkbox" value="1" <?php checked( !sz_disable_avatar_uploads( false ) ); ?> />
	<label for="sz-disable-avatar-uploads"><?php _e( 'Allow registered members to upload avatars', 'sportszone' ); ?></label>

<?php
}

/**
 * Allow members to upload cover images field.
 *
 * @since 2.4.0
 */
function sz_admin_setting_callback_cover_image_uploads() {
?>
	<input id="sz-disable-cover-image-uploads" name="sz-disable-cover-image-uploads" type="checkbox" value="1" <?php checked( ! sz_disable_cover_image_uploads() ); ?> />
	<label for="sz-disable-cover-image-uploads"><?php _e( 'Allow registered members to upload cover images', 'sportszone' ); ?></label>
<?php
}

/** Groups Section ************************************************************/

/**
 * Groups settings section description for the settings page.
 *
 * @since 1.6.0
 */
function sz_admin_setting_callback_groups_section() { }

/**
 * Allow all users to create groups field.
 *
 * @since 1.6.0
 *
 */
function sz_admin_setting_callback_group_creation() {
?>

	<input id="sz_restrict_group_creation" name="sz_restrict_group_creation" type="checkbox" aria-describedby="sz_group_creation_description" value="1" <?php checked( !sz_restrict_group_creation( false ) ); ?> />
	<label for="sz_restrict_group_creation"><?php _e( 'Enable group creation for all users', 'sportszone' ); ?></label>
	<p class="description" id="sz_group_creation_description"><?php _e( 'Administrators can always create groups, regardless of this setting.', 'sportszone' ); ?></p>

<?php
}

/**
 * 'Enable group avatars' field markup.
 *
 * @since 2.3.0
 */
function sz_admin_setting_callback_group_avatar_uploads() {
?>
	<input id="sz-disable-group-avatar-uploads" name="sz-disable-group-avatar-uploads" type="checkbox" value="1" <?php checked( ! sz_disable_group_avatar_uploads() ); ?> />
	<label for="sz-disable-group-avatar-uploads"><?php _e( 'Allow customizable avatars for groups', 'sportszone' ); ?></label>
<?php
}

/**
 * 'Enable group cover images' field markup.
 *
 * @since 2.4.0
 */
function sz_admin_setting_callback_group_cover_image_uploads() {
?>
	<input id="sz-disable-group-cover-image-uploads" name="sz-disable-group-cover-image-uploads" type="checkbox" value="1" <?php checked( ! sz_disable_group_cover_image_uploads() ); ?> />
	<label for="sz-disable-group-cover-image-uploads"><?php _e( 'Allow customizable cover images for groups', 'sportszone' ); ?></label>
<?php
}

/** Settings Page *************************************************************/

/**
 * The main settings page
 *
 * @since 1.6.0
 *
 */
function sz_core_admin_settings() {

	// We're saving our own options, until the WP Settings API is updated to work with Multisite.
	$form_action = add_query_arg( 'page', 'sz-settings', sz_get_admin_url( 'admin.php' ) );

	?>

	<div class="wrap">

		<h1><?php _e( 'SportsZone Settings', 'sportszone' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php sz_core_admin_tabs( __( 'Options', 'sportszone' ) ); ?></h2>

		<form action="<?php echo esc_url( $form_action ) ?>" method="post">

			<?php settings_fields( 'sportszone' ); ?>

			<?php do_settings_sections( 'sportszone' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'sportszone' ); ?>" />
			</p>
		</form>
	</div>

<?php
}

/**
 * Save our settings.
 *
 * @since 1.6.0
 */
function sz_core_admin_settings_save() {
	global $wp_settings_fields;

	if ( isset( $_GET['page'] ) && 'sz-settings' == $_GET['page'] && !empty( $_POST['submit'] ) ) {
		check_admin_referer( 'sportszone-options' );

		// Because many settings are saved with checkboxes, and thus will have no values
		// in the $_POST array when unchecked, we loop through the registered settings.
		if ( isset( $wp_settings_fields['sportszone'] ) ) {
			foreach( (array) $wp_settings_fields['sportszone'] as $section => $settings ) {
				foreach( $settings as $setting_name => $setting ) {
					$value = isset( $_POST[$setting_name] ) ? $_POST[$setting_name] : '';

					sz_update_option( $setting_name, $value );
				}
			}
		}

		// Some legacy options are not registered with the Settings API, or are reversed in the UI.
		$legacy_options = array(
			'sz-disable-account-deletion',
			'sz-disable-avatar-uploads',
			'sz-disable-cover-image-uploads',
			'sz-disable-group-avatar-uploads',
			'sz-disable-group-cover-image-uploads',
			'sz_disable_blogforum_comments',
			'sz-disable-profile-sync',
			'sz_restrict_group_creation',
			'hide-loggedout-adminbar',
		);

		foreach( $legacy_options as $legacy_option ) {
			// Note: Each of these options is represented by its opposite in the UI
			// Ie, the Profile Syncing option reads "Enable Sync", so when it's checked,
			// the corresponding option should be unset.
			$value = isset( $_POST[$legacy_option] ) ? '' : 1;
			sz_update_option( $legacy_option, $value );
		}

		sz_core_redirect( add_query_arg( array( 'page' => 'sz-settings', 'updated' => 'true' ), sz_get_admin_url( 'admin.php' ) ) );
	}
}
add_action( 'sz_admin_init', 'sz_core_admin_settings_save', 100 );

/**
 * Output settings API option.
 *
 * @since 1.6.0
 *
 * @param string $option  Form option to echo.
 * @param string $default Form option default.
 * @param bool   $slug    Form option slug.
 */
function sz_form_option( $option, $default = '' , $slug = false ) {
	echo sz_get_form_option( $option, $default, $slug );
}
	/**
	 * Return settings API option
	 *
	 * @since 1.6.0
	 *
	 *
	 * @param string $option  Form option to return.
	 * @param string $default Form option default.
	 * @param bool   $slug    Form option slug.
	 * @return string
	 */
	function sz_get_form_option( $option, $default = '', $slug = false ) {

		// Get the option and sanitize it.
		$value = sz_get_option( $option, $default );

		// Slug?
		if ( true === $slug ) {

			/**
			 * Filters the slug value in the form field.
			 *
			 * @since 1.6.0
			 *
			 * @param string $value Value being returned for the requested option.
			 */
			$value = esc_attr( apply_filters( 'editable_slug', $value ) );
		} else { // Not a slug.
			$value = esc_attr( $value );
		}

		// Fallback to default.
		if ( empty( $value ) )
			$value = $default;

		/**
		 * Filters the settings API option.
		 *
		 * @since 1.6.0
		 *
		 * @param string $value  Value being returned for the requested option.
		 * @param string $option Option whose value is being requested.
		 */
		return apply_filters( 'sz_get_form_option', $value, $option );
	}
