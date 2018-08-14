<?php
/**
 * Bp add group type general setting file.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  SZ_Add_Group_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $sz_grp_types;
?>
<form method="POST" action="">
	<table class="form-table wcctp-admin-page-table">
		<tbody>
			<!-- GROUP TYPES PRE-SELECTED -->
			<tr>
				<th scope="row"><label for="group-types-selection"><?php esc_html_e( 'Pre-select Group Types', 'sz-add-group-types' ); ?></label></th>
				<td>
					<input type="checkbox" value="" name="szgt-group-types-pre-selected" id="szgt-group-types-pre-selected" <?php echo ( isset( $sz_grp_types->group_types_pre_selected ) && 'yes' === $sz_grp_types->group_types_pre_selected ) ? 'checked' : ''; ?> />
					<label for="szgt-group-types-pre-selected"><?php esc_html_e( 'Pre-select all the group types', 'sz-add-group-types' ); ?></label>
					<p class="description"><?php esc_html_e( 'This setting will pre-select all the group types that get listed during group creation.', 'sz-add-group-types' ); ?></p>
				</td>
			</tr>

			<!-- GROUP TYPE SEARCH -->
			<tr>
				<th scope="row"><label for="group-types-search"><?php esc_html_e( 'Enable Group Type Search', 'sz-add-group-types' ); ?></label></th>
				<td>
					<input type="checkbox" value="" name="szgt-group-types-search-enabled" id="szgt-group-types-search-enabled" <?php echo ( isset( $sz_grp_types->group_type_search_enabled ) && 'yes' === $sz_grp_types->group_type_search_enabled ) ? 'checked' : ''; ?> />
					<label for="szgt-group-types-search-enabled"><?php esc_html_e( 'Group type searching on front-end', 'sz-add-group-types' ); ?></label>
					<p class="description"><?php esc_html_e( 'This setting will enable the group type searching on the <strong>domain.com/groups</strong> page.', 'sz-add-group-types' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<?php wp_nonce_field( 'szgt', 'szgt-general-settings-nonce' ); ?>
		<input type="submit" name="szgt_submit_general_settings" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'sz-add-group-types' ); ?>">
	</p>
</form>
