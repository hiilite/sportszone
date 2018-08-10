<?php
/**
 * SportsZone Admin Component Functions.
 *
 * @package SportsZone
 * @subpackage CoreAdministration
 * @since 2.3.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Renders the Component Setup admin panel.
 *
 * @since 1.6.0
 *
 */
function sz_core_admin_components_settings() {
?>

	<div class="wrap">

		<h1><?php _e( 'SportsZone Settings', 'sportszone' ); ?> </h1>

		<h2 class="nav-tab-wrapper"><?php sz_core_admin_tabs( __( 'Components', 'sportszone' ) ); ?></h2>
		<form action="" method="post" id="sz-admin-component-form">

			<?php sz_core_admin_components_options(); ?>

			<p class="submit clear">
				<input class="button-primary" type="submit" name="sz-admin-component-submit" id="sz-admin-component-submit" value="<?php esc_attr_e( 'Save Settings', 'sportszone' ) ?>"/>
			</p>

			<?php wp_nonce_field( 'sz-admin-component-setup' ); ?>

		</form>
	</div>

<?php
}

/**
 * Creates reusable markup for component setup on the Components and Pages dashboard panel.
 *
 * @since 1.6.0
 *
 * @todo Use settings API
 */
function sz_core_admin_components_options() {

	// Declare local variables.
	$deactivated_components = array();

	/**
	 * Filters the array of available components.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed $value Active components.
	 */
	$active_components      = apply_filters( 'sz_active_components', sz_get_option( 'sz-active-components' ) );

	// The default components (if none are previously selected).
	$default_components = array(
		'xprofile' => array(
			'title'       => __( 'Extended Profiles', 'sportszone' ),
			'description' => __( 'Customize your community with fully editable profile fields that allow your users to describe themselves.', 'sportszone' )
		),
		'settings' => array(
			'title'       => __( 'Account Settings', 'sportszone' ),
			'description' => __( 'Allow your users to modify their account and notification settings directly from within their profiles.', 'sportszone' )
		),
		'notifications' => array(
			'title'       => __( 'Notifications', 'sportszone' ),
			'description' => __( 'Notify members of relevant activity with a toolbar bubble and/or via email, and allow them to customize their notification settings.', 'sportszone' )
		),
	);

	$optional_components = sz_core_admin_get_components( 'optional' );
	$required_components = sz_core_admin_get_components( 'required' );
	$retired_components  = sz_core_admin_get_components( 'retired'  );

	// Merge optional and required together.
	$all_components = $optional_components + $required_components;

	// If this is an upgrade from before SportsZone 1.5, we'll have to convert
	// deactivated components into activated ones.
	if ( empty( $active_components ) ) {
		$deactivated_components = sz_get_option( 'sz-deactivated-components' );
		if ( !empty( $deactivated_components ) ) {

			// Trim off namespace and filename.
			$trimmed = array();
			foreach ( array_keys( (array) $deactivated_components ) as $component ) {
				$trimmed[] = str_replace( '.php', '', str_replace( 'sz-', '', $component ) );
			}

			// Loop through the optional components to create an active component array.
			foreach ( array_keys( (array) $optional_components ) as $ocomponent ) {
				if ( !in_array( $ocomponent, $trimmed ) ) {
					$active_components[$ocomponent] = 1;
				}
			}
		}
	}

	// On new install, set active components to default.
	if ( empty( $active_components ) ) {
		$active_components = $default_components;
	}

	// Core component is always active.
	$active_components['core'] = $all_components['core'];
	$inactive_components       = array_diff( array_keys( $all_components ) , array_keys( $active_components ) );

	/** Display **************************************************************
	 */

	// Get the total count of all plugins.
	$all_count = count( $all_components );
	$page      = sz_core_do_network_admin()  ? 'settings.php' : 'options-general.php';
	$action    = !empty( $_GET['action'] ) ? $_GET['action'] : 'all';

	switch( $action ) {
		case 'all' :
			$current_components = $all_components;
			break;
		case 'active' :
			foreach ( array_keys( $active_components ) as $component ) {
				$current_components[$component] = $all_components[$component];
			}
			break;
		case 'inactive' :
			foreach ( $inactive_components as $component ) {
				$current_components[$component] = $all_components[$component];
			}
			break;
		case 'mustuse' :
			$current_components = $required_components;
			break;
		case 'retired' :
			$current_components = $retired_components;
			break;
	} ?>

	<h3 class="screen-reader-text"><?php
		/* translators: accessibility text */
		_e( 'Filter components list', 'sportszone' );
	?></h3>

	<ul class="subsubsub">
		<li><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'sz-components', 'action' => 'all'      ), sz_get_admin_url( $page ) ) ); ?>" <?php if ( $action === 'all'      ) : ?>class="current"<?php endif; ?>><?php printf( _nx( 'All <span class="count">(%s)</span>',      'All <span class="count">(%s)</span>',      $all_count,         'plugins', 'sportszone' ), number_format_i18n( $all_count                    ) ); ?></a> | </li>
		<li><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'sz-components', 'action' => 'active'   ), sz_get_admin_url( $page ) ) ); ?>" <?php if ( $action === 'active'   ) : ?>class="current"<?php endif; ?>><?php printf( _n(  'Active <span class="count">(%s)</span>',   'Active <span class="count">(%s)</span>',   count( $active_components   ), 'sportszone' ), number_format_i18n( count( $active_components   ) ) ); ?></a> | </li>
		<li><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'sz-components', 'action' => 'inactive' ), sz_get_admin_url( $page ) ) ); ?>" <?php if ( $action === 'inactive' ) : ?>class="current"<?php endif; ?>><?php printf( _n(  'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', count( $inactive_components ), 'sportszone' ), number_format_i18n( count( $inactive_components ) ) ); ?></a> | </li>
		<li><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'sz-components', 'action' => 'mustuse'  ), sz_get_admin_url( $page ) ) ); ?>" <?php if ( $action === 'mustuse'  ) : ?>class="current"<?php endif; ?>><?php printf( _n(  'Must-Use <span class="count">(%s)</span>', 'Must-Use <span class="count">(%s)</span>', count( $required_components ), 'sportszone' ), number_format_i18n( count( $required_components ) ) ); ?></a> | </li>
		<li><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'sz-components', 'action' => 'retired'  ), sz_get_admin_url( $page ) ) ); ?>" <?php if ( $action === 'retired'  ) : ?>class="current"<?php endif; ?>><?php printf( _n(  'Retired <span class="count">(%s)</span>',  'Retired <span class="count">(%s)</span>',  count( $retired_components ),  'sportszone' ), number_format_i18n( count( $retired_components  ) ) ); ?></a></li>
	</ul>

	<h3 class="screen-reader-text"><?php
		/* translators: accessibility text */
		_e( 'Components list', 'sportszone' );
	?></h3>

	<table class="wp-list-table widefat plugins">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox" <?php checked( empty( $inactive_components ) ); ?>>
					<label class="screen-reader-text" for="cb-select-all-1"><?php
					/* translators: accessibility text */
					_e( 'Enable or disable all optional components in bulk', 'sportszone' );
				?></label></td>
				<th scope="col" id="name" class="manage-column column-title column-primary"><?php _e( 'Component', 'sportszone' ); ?></th>
				<th scope="col" id="description" class="manage-column column-description"><?php _e( 'Description', 'sportszone' ); ?></th>
			</tr>
		</thead>

		<tbody id="the-list">

			<?php if ( !empty( $current_components ) ) : ?>

				<?php foreach ( $current_components as $name => $labels ) : ?>

					<?php if ( !in_array( $name, array( 'core', 'members' ) ) ) :
						$class = isset( $active_components[esc_attr( $name )] ) ? 'active' : 'inactive';
					else :
						$class = 'active';
					endif; ?>

					<tr id="<?php echo esc_attr( $name ); ?>" class="<?php echo esc_attr( $name ) . ' ' . esc_attr( $class ); ?>">
						<th scope="row" class="check-column">

							<?php if ( !in_array( $name, array( 'core', 'members' ) ) ) : ?>

								<input type="checkbox" id="<?php echo esc_attr( "sz_components[$name]" ); ?>" name="<?php echo esc_attr( "sz_components[$name]" ); ?>" value="1"<?php checked( isset( $active_components[esc_attr( $name )] ) ); ?> /><label for="<?php echo esc_attr( "sz_components[$name]" ); ?>" class="screen-reader-text"><?php
									/* translators: accessibility text */
									printf( __( 'Select %s', 'sportszone' ), esc_html( $labels['title'] ) ); ?></label>

							<?php endif; ?>

						</th>
						<td class="plugin-title column-primary">
							<label for="<?php echo esc_attr( "sz_components[$name]" ); ?>">
								<span aria-hidden="true"></span>
								<strong><?php echo esc_html( $labels['title'] ); ?></strong>
							</label>
						</td>

						<td class="column-description desc">
							<div class="plugin-description">
								<p><?php echo $labels['description']; ?></p>
							</div>

						</td>
					</tr>

				<?php endforeach ?>

			<?php else : ?>

				<tr class="no-items">
					<td class="colspanchange" colspan="3"><?php _e( 'No components found.', 'sportszone' ); ?></td>
				</tr>

			<?php endif; ?>

		</tbody>

		<tfoot>
			<tr>
				<td class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox" <?php checked( empty( $inactive_components ) ); ?>>
					<label class="screen-reader-text" for="cb-select-all-2"><?php
					/* translators: accessibility text */
					_e( 'Enable or disable all optional components in bulk', 'sportszone' );
				?></label></td>
				<th class="manage-column column-title column-primary"><?php _e( 'Component', 'sportszone' ); ?></th>
				<th class="manage-column column-description"><?php _e( 'Description', 'sportszone' ); ?></th>
			</tr>
		</tfoot>

	</table>

	<input type="hidden" name="sz_components[members]" value="1" />

	<?php
}

/**
 * Handle saving the Component settings.
 *
 * @since 1.6.0
 *
 * @todo Use settings API when it supports saving network settings
 */
function sz_core_admin_components_settings_handler() {

	// Bail if not saving settings.
	if ( ! isset( $_POST['sz-admin-component-submit'] ) )
		return;

	// Bail if nonce fails.
	if ( ! check_admin_referer( 'sz-admin-component-setup' ) )
		return;

	// Settings form submitted, now save the settings. First, set active components.
	if ( isset( $_POST['sz_components'] ) ) {

		// Load up SportsZone.
		$sz = sportszone();

		// Save settings and upgrade schema.
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		require_once( $sz->plugin_dir . '/sz-core/admin/sz-core-admin-schema.php' );

		$submitted = stripslashes_deep( $_POST['sz_components'] );
		$sz->active_components = sz_core_admin_get_active_components_from_submitted_settings( $submitted );

		sz_core_install( $sz->active_components );
		sz_core_add_page_mappings( $sz->active_components );
		sz_update_option( 'sz-active-components', $sz->active_components );
	}

	// Where are we redirecting to?
	$base_url = sz_get_admin_url( add_query_arg( array( 'page' => 'sz-components', 'updated' => 'true' ), 'admin.php' ) );

	// Redirect.
	wp_redirect( $base_url );
	die();
}
add_action( 'sz_admin_init', 'sz_core_admin_components_settings_handler' );

/**
 * Calculates the components that should be active after save, based on submitted settings.
 *
 * The way that active components must be set after saving your settings must
 * be calculated differently depending on which of the Components subtabs you
 * are coming from:
 * - When coming from All or Active, the submitted checkboxes accurately
 *   reflect the desired active components, so we simply pass them through
 * - When coming from Inactive, components can only be activated - already
 *   active components will not be passed in the $_POST global. Thus, we must
 *   parse the newly activated components with the already active components
 *   saved in the $sz global
 * - When activating a Retired component, the situation is similar to Inactive.
 * - When deactivating a Retired component, no value is passed in the $_POST
 *   global (because the component settings are checkboxes). So, in order to
 *   determine whether a retired component is being deactivated, we retrieve a
 *   list of retired components, and check each one to ensure that its checkbox
 *   is not present, before merging the submitted components with the active
 *   ones.
 *
 * @since 1.7.0
 *
 * @param array $submitted This is the array of component settings coming from the POST
 *                         global. You should stripslashes_deep() before passing to this function.
 * @return array The calculated list of component settings
 */
function sz_core_admin_get_active_components_from_submitted_settings( $submitted ) {
	$current_action = 'all';

	if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'active', 'inactive', 'retired' ) ) ) {
		$current_action = $_GET['action'];
	}

	$current_components = sportszone()->active_components;

	switch ( $current_action ) {
		case 'retired' :
			$retired_components = sz_core_admin_get_components( 'retired' );
			foreach ( array_keys( $retired_components ) as $retired_component ) {
				if ( ! isset( $submitted[ $retired_component ] ) ) {
					unset( $current_components[ $retired_component ] );
				}
			} // Fall through.


		case 'inactive' :
			$components = array_merge( $submitted, $current_components );
			break;

		case 'all' :
		case 'active' :
		default :
			$components = $submitted;
			break;
	}

	return $components;
}

/**
 * Return a list of component information.
 *
 * We use this information both to build the markup for the admin screens, as
 * well as to do some processing on settings data submitted from those screens.
 *
 * @since 1.7.0
 *
 * @param string $type Optional; component type to fetch. Default value is 'all', or 'optional', 'retired', 'required'.
 * @return array Requested components' data.
 */
function sz_core_admin_get_components( $type = 'all' ) {
	$components = sz_core_get_components( $type );

	/**
	 * Filters the list of component information.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $components Array of component information.
	 * @param string $type       Type of component list requested.
	 *                           Possible values include 'all', 'optional',
	 *                           'retired', 'required'.
	 */
	return apply_filters( 'sz_core_admin_get_components', $components, $type );
}
