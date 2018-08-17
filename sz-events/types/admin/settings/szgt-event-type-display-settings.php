<?php
/**
 * Bp add event type display setting file.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  SZ_Add_Event_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed firectly.
}
global $sz_evt_types;
$event_types                = $sz_evt_types->event_types;
$szet_type_display_settings = get_site_option( 'szet_type_display_settings' );
?>
<form method="POST" action="">
	<table class="form-table szgt-admin-page-table">
		<tbody>
			<!-- EVENT TYPES DISPLAY SETTINGS -->
			<tr>
				<th scope="row"><label for="event-types-display"><?php esc_html_e( 'Display Event Type(s) as tab.', 'sz-add-event-types' ); ?></label></th>
				<td>
			<?php
			if ( is_array( $event_types ) && ! empty( $event_types ) ) {
				foreach ( $event_types as $key => $type ) {
				?>
					<p>
						<input type="checkbox" name="szet_event_type_display[]" value="<?php echo esc_attr( $type['slug'] ); ?>"
																									<?php
																									if ( is_array( $szet_type_display_settings ) && ! empty( $szet_type_display_settings ) ) {
																										if ( in_array( $type['slug'], $szet_type_display_settings, true ) ) {
																											echo 'checked="checked"'; }
																									}
?>
/>
						<label for="szgt-event-type-search-textbox"><?php echo esc_html( $type['name'] ); ?></label>
					</p>
			<?php
				}
			}
?>
					<p class="description"><?php esc_html_e( 'This setting will display event types as tab in frontend events directory .', 'sz-add-event-types' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<?php wp_nonce_field( 'szgt-type-display-settings', 'szet_submit_event_type_display_settings' ); ?>
		<input type="submit" name="szet_submit_event_type_display_settings" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'sz-add-event-types' ); ?>">
	</p>
</form>
