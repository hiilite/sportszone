<?php
/**
 * Bp add event type general setting file.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  SZ_Add_Event_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $sz_evt_types;
?>
<form method="POST" action="">
	<table class="form-table wcctp-admin-page-table">
		<tbody>
			<!-- EVENT TYPES PRE-SELECTED -->
			<tr>
				<th scope="row"><label for="event-types-selection"><?php esc_html_e( 'Pre-select Event Types', 'sz-add-event-types' ); ?></label></th>
				<td>
					<input type="checkbox" value="" name="szgt-event-types-pre-selected" id="szgt-event-types-pre-selected" <?php echo ( isset( $sz_evt_types->event_types_pre_selected ) && 'yes' === $sz_evt_types->event_types_pre_selected ) ? 'checked' : ''; ?> />
					<label for="szgt-event-types-pre-selected"><?php esc_html_e( 'Pre-select all the event types', 'sz-add-event-types' ); ?></label>
					<p class="description"><?php esc_html_e( 'This setting will pre-select all the event types that get listed during event creation.', 'sz-add-event-types' ); ?></p>
				</td>
			</tr>

			<!-- EVENT TYPE SEARCH -->
			<tr>
				<th scope="row"><label for="event-types-search"><?php esc_html_e( 'Enable Event Type Search', 'sz-add-event-types' ); ?></label></th>
				<td>
					<input type="checkbox" value="" name="szgt-event-types-search-enabled" id="szgt-event-types-search-enabled" <?php echo ( isset( $sz_evt_types->event_type_search_enabled ) && 'yes' === $sz_evt_types->event_type_search_enabled ) ? 'checked' : ''; ?> />
					<label for="szgt-event-types-search-enabled"><?php esc_html_e( 'Event type searching on front-end', 'sz-add-event-types' ); ?></label>
					<p class="description"><?php esc_html_e( 'This setting will enable the event type searching on the <strong>domain.com/events</strong> page.', 'sz-add-event-types' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<?php wp_nonce_field( 'szgt', 'szgt-general-settings-nonce' ); ?>
		<input type="submit" name="szet_submit_general_settings" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'sz-add-event-types' ); ?>">
	</p>
</form>
