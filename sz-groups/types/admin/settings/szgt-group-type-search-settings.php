<?php
/**
 * Bp add group type search setting file.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package  SZ_Add_Group_Types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed firectly.
}
global $sz_grp_types;
?>
<form method="POST" action="">
	<table class="form-table szgt-admin-page-table">
		<tbody>
			<!-- GROUP TYPES SEARCH SETTINGS -->
			<tr>
				<th scope="row"><label for="group-types-search-filter"><?php esc_html_e( 'Search Template Content', 'sz-add-group-types' ); ?></label></th>
				<td>
					<p>
						<input type="radio" value="textbox" name="szgt-group-type-search-template" id="szgt-group-type-search-textbox" <?php echo ( isset( $sz_grp_types->group_type_search_template ) && 'textbox' === $sz_grp_types->group_type_search_template ) ? 'checked' : ''; ?> required/>
						<label for="szgt-group-type-search-textbox"><?php esc_html_e( 'Textbox', 'sz-add-group-types' ); ?><span class="description">&nbsp;&nbsp;[<?php esc_html_e( 'SportsZone Group Search Textbox.', 'sz-add-group-types' ); ?>]</span></label>
					</p>
					<p>
						<input type="radio" value="select" name="szgt-group-type-search-template" id="szgt-group-type-search-select" <?php echo ( isset( $sz_grp_types->group_type_search_template ) && 'select' === $sz_grp_types->group_type_search_template ) ? 'checked' : ''; ?> />
						<label for="szgt-group-type-search-select"><?php esc_html_e( 'Group Type Selectbox', 'sz-add-group-types' ); ?></label>
					</p>
					<p>
						<input type="radio" value="both" name="szgt-group-type-search-template" id="szgt-group-type-search-both" <?php echo ( isset( $sz_grp_types->group_type_search_template ) && 'both' === $sz_grp_types->group_type_search_template ) ? 'checked' : ''; ?> />
						<label for="szgt-group-type-search-both"><?php esc_html_e( 'Both', 'sz-add-group-types' ); ?></label>
					</p>
					<p class="description"><?php esc_html_e( 'This setting will change the group search template in frontend.', 'sz-add-group-types' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<?php wp_nonce_field( 'szgt-search-settings', 'szgt-group-type-search-settings-nonce' ); ?>
		<input type="submit" name="szgt_submit_group_type_search_settings" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'sz-add-group-types' ); ?>">
	</p>
</form>
