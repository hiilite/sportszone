<?php
/**
 * SportsZone Admin Slug Functions.
 *
 * @package SportsZone
 * @subpackage CoreAdministration
 * @since 2.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Renders the page mapping admin panel.
 *
 * @since 1.6.0
 * @todo Use settings API
 */
function sz_core_admin_slugs_settings() {
?>

	<div class="wrap">

		<h1><?php _e( 'SportsZone Settings', 'sportszone' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php sz_core_admin_tabs( __( 'Pages', 'sportszone' ) ); ?></h2>
		<form action="" method="post" id="sz-admin-page-form">

			<?php sz_core_admin_slugs_options(); ?>

			<p class="submit clear">
				<input class="button-primary" type="submit" name="sz-admin-pages-submit" id="sz-admin-pages-submit" value="<?php esc_attr_e( 'Save Settings', 'sportszone' ) ?>"/>
			</p>

			<?php wp_nonce_field( 'sz-admin-pages-setup' ); ?>

		</form>
	</div>

<?php
}

/**
 * Generate a list of directory pages, for use when building Components panel markup.
 *
 * @since 2.4.1
 *
 * @return array
 */
function sz_core_admin_get_directory_pages() {
	$sz = sportszone();
	$directory_pages = array();

	// Loop through loaded components and collect directories.
	if ( is_array( $sz->loaded_components ) ) {
		foreach( $sz->loaded_components as $component_slug => $component_id ) {

			// Only components that need directories should be listed here.
			if ( isset( $sz->{$component_id} ) && !empty( $sz->{$component_id}->has_directory ) ) {

				// The component->name property was introduced in BP 1.5, so we must provide a fallback.
				$directory_pages[$component_id] = !empty( $sz->{$component_id}->name ) ? $sz->{$component_id}->name : ucwords( $component_id );
			}
		}
	}

	/** Directory Display *****************************************************/

	/**
	 * Filters the loaded components needing directory page association to a WordPress page.
	 *
	 * @since 1.5.0
	 *
	 * @param array $directory_pages Array of available components to set associations for.
	 */
	return apply_filters( 'sz_directory_pages', $directory_pages );
}

/**
 * Generate a list of static pages, for use when building Components panel markup.
 *
 * By default, this list contains 'register' and 'activate'.
 *
 * @since 2.4.1
 *
 * @return array
 */
function sz_core_admin_get_static_pages() {
	$static_pages = array(
		'register' => __( 'Register', 'sportszone' ),
		'activate' => __( 'Activate', 'sportszone' ),
	);

	/**
	 * Filters the default static pages for SportsZone setup.
	 *
	 * @since 1.6.0
	 *
	 * @param array $static_pages Array of static default static pages.
	 */
	return apply_filters( 'sz_static_pages', $static_pages );
}

/**
 * Creates reusable markup for page setup on the Components and Pages dashboard panel.
 *
 * @package SportsZone
 * @since 1.6.0
 * @todo Use settings API
 */
function sz_core_admin_slugs_options() {

	// Get the existing WP pages
	$existing_pages = sz_core_get_directory_page_ids();

	// Set up an array of components (along with component names) that have directory pages.
	$directory_pages = sz_core_admin_get_directory_pages();

	if ( !empty( $directory_pages ) ) : ?>

		<h3><?php _e( 'Directories', 'sportszone' ); ?></h3>

		<p><?php _e( 'Associate a WordPress Page with each SportsZone component directory.', 'sportszone' ); ?></p>

		<table class="form-table">
			<tbody>

				<?php foreach ( $directory_pages as $name => $label ) : ?>

					<tr valign="top">
						<th scope="row">
							<label for="sz_pages[<?php echo esc_attr( $name ) ?>]"><?php echo esc_html( $label ) ?></label>
						</th>

						<td>

							<?php if ( ! sz_is_root_blog() ) switch_to_blog( sz_get_root_blog_id() ); ?>

							<?php echo wp_dropdown_pages( array(
								'name'             => 'sz_pages[' . esc_attr( $name ) . ']',
								'echo'             => false,
								'show_option_none' => __( '- None -', 'sportszone' ),
								'selected'         => !empty( $existing_pages[$name] ) ? $existing_pages[$name] : false
							) ); ?>

							<?php if ( !empty( $existing_pages[$name] ) ) : ?>

								<a href="<?php echo get_permalink( $existing_pages[$name] ); ?>" class="button-secondary" target="_bp"><?php _e( 'View', 'sportszone' ); ?></a>

							<?php endif; ?>

							<?php if ( ! sz_is_root_blog() ) restore_current_blog(); ?>

						</td>
					</tr>


				<?php endforeach ?>

				<?php

				/**
				 * Fires after the display of default directories.
				 *
				 * Allows plugins to add their own directory associations.
				 *
				 * @since 1.5.0
				 */
				do_action( 'sz_active_external_directories' ); ?>

			</tbody>
		</table>

	<?php

	endif;

	/** Static Display ********************************************************/

	$static_pages = sz_core_admin_get_static_pages();

	if ( !empty( $static_pages ) ) : ?>

		<h3><?php _e( 'Registration', 'sportszone' ); ?></h3>

		<?php if ( sz_get_signup_allowed() ) : ?>
			<p><?php _e( 'Associate WordPress Pages with the following SportsZone Registration pages.', 'sportszone' ); ?></p>
		<?php else : ?>
			<?php if ( is_multisite() ) : ?>
				<p><?php printf( __( 'Registration is currently disabled.  Before associating a page is allowed, please enable registration by selecting either the "User accounts may be registered" or "Both sites and user accounts can be registered" option on <a href="%s">this page</a>.', 'sportszone' ), network_admin_url( 'settings.php' ) ); ?></p>
			<?php else : ?>
				<p><?php printf( __( 'Registration is currently disabled.  Before associating a page is allowed, please enable registration by clicking on the "Anyone can register" checkbox on <a href="%s">this page</a>.', 'sportszone' ), admin_url( 'options-general.php' ) ); ?></p>
			<?php endif; ?>
		<?php endif; ?>

		<table class="form-table">
			<tbody>

				<?php if ( sz_get_signup_allowed() ) : foreach ( $static_pages as $name => $label ) : ?>

					<tr valign="top">
						<th scope="row">
							<label for="sz_pages[<?php echo esc_attr( $name ) ?>]"><?php echo esc_html( $label ) ?></label>
						</th>

						<td>

							<?php if ( ! sz_is_root_blog() ) switch_to_blog( sz_get_root_blog_id() ); ?>

							<?php echo wp_dropdown_pages( array(
								'name'             => 'sz_pages[' . esc_attr( $name ) . ']',
								'echo'             => false,
								'show_option_none' => __( '- None -', 'sportszone' ),
								'selected'         => !empty( $existing_pages[$name] ) ? $existing_pages[$name] : false
							) ) ?>

							<?php if ( !empty( $existing_pages[$name] ) ) : ?>

								<a href="<?php echo get_permalink( $existing_pages[$name] ); ?>" class="button-secondary" target="_bp"><?php _e( 'View', 'sportszone' ); ?></a>

							<?php endif; ?>

							<?php if ( ! sz_is_root_blog() ) restore_current_blog(); ?>

						</td>
					</tr>

				<?php endforeach; endif; ?>

				<?php

				/**
				 * Fires after the display of default static pages for SportsZone setup.
				 *
				 * @since 1.5.0
				 */
				do_action( 'sz_active_external_pages' ); ?>

			</tbody>
		</table>

		<?php
	endif;
}

/**
 * Handle saving of the SportsZone slugs.
 *
 * @since 1.6.0
 * @todo Use settings API
 */
function sz_core_admin_slugs_setup_handler() {

	if ( isset( $_POST['sz-admin-pages-submit'] ) ) {
		if ( !check_admin_referer( 'sz-admin-pages-setup' ) )
			return false;

		// Then, update the directory pages.
		if ( isset( $_POST['sz_pages'] ) ) {
			$valid_pages = array_merge( sz_core_admin_get_directory_pages(), sz_core_admin_get_static_pages() );

			$new_directory_pages = array();
			foreach ( (array) $_POST['sz_pages'] as $key => $value ) {
				if ( isset( $valid_pages[ $key ] ) ) {
					$new_directory_pages[ $key ] = (int) $value;
				}
			}
			sz_core_update_directory_page_ids( $new_directory_pages );
		}

		$base_url = sz_get_admin_url( add_query_arg( array( 'page' => 'sz-page-settings', 'updated' => 'true' ), 'admin.php' ) );

		wp_redirect( $base_url );
	}
}
add_action( 'sz_admin_init', 'sz_core_admin_slugs_setup_handler' );
