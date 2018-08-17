<?php
/**
 * Bp add event type search setting file.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  SZ_Add_Event_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed firectly.
}
global $sz_evt_types;
?>
<form method="POST" action="">
	<table class="form-table szgt-admin-page-table">
		<tbody>
			<!-- EVENT TYPES SEARCH SETTINGS -->
			<tr>
				<th scope="row"><label for="event-types-search-filter"><?php esc_html_e( 'Search Template Content', 'sz-add-event-types' ); ?></label></th>
				<td>
					<p>
						<input type="radio" value="textbox" name="szgt-event-type-search-template" id="szgt-event-type-search-textbox" <?php echo ( isset( $sz_evt_types->event_type_search_template ) && 'textbox' === $sz_evt_types->event_type_search_template ) ? 'checked' : ''; ?> required/>
						<label for="szgt-event-type-search-textbox"><?php esc_html_e( 'Textbox', 'sz-add-event-types' ); ?><span class="description">&nbsp;&nbsp;[<?php esc_html_e( 'SportsZone Event Search Textbox.', 'sz-add-event-types' ); ?>]</span></label>
					</p>
					<p>
						<input type="radio" value="select" name="szgt-event-type-search-template" id="szgt-event-type-search-select" <?php echo ( isset( $sz_evt_types->event_type_search_template ) && 'select' === $sz_evt_types->event_type_search_template ) ? 'checked' : ''; ?> />
						<label for="szgt-event-type-search-select"><?php esc_html_e( 'Event Type Selectbox', 'sz-add-event-types' ); ?></label>
					</p>
					<p>
						<input type="radio" value="both" name="szgt-event-type-search-template" id="szgt-event-type-search-both" <?php echo ( isset( $sz_evt_types->event_type_search_template ) && 'both' === $sz_evt_types->event_type_search_template ) ? 'checked' : ''; ?> />
						<label for="szgt-event-type-search-both"><?php esc_html_e( 'Both', 'sz-add-event-types' ); ?></label>
					</p>
					<p class="description"><?php esc_html_e( 'This setting will change the event search template in frontend.', 'sz-add-event-types' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<?php wp_nonce_field( 'szgt-search-settings', 'szgt-event-type-search-settings-nonce' ); ?>
		<input type="submit" name="szet_submit_event_type_search_settings" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'sz-add-event-types' ); ?>">
	</p>
</form>
