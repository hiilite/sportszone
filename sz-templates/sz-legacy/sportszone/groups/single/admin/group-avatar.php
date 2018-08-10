<?php
/**
 * SportsZone - Groups Admin - Group Avatar
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<h2 class="sz-screen-reader-text"><?php _e( 'Group Avatar', 'sportszone' ); ?></h2>

<?php if ( 'upload-image' == sz_get_avatar_admin_step() ) : ?>

	<p><?php _e("Upload an image to use as a profile photo for this group. The image will be shown on the main group page, and in search results.", 'sportszone' ); ?></p>

	<p>
		<label for="file" class="sz-screen-reader-text"><?php
			/* translators: accessibility text */
			_e( 'Select an image', 'sportszone' );
		?></label>
		<input type="file" name="file" id="file" />
		<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'sportszone' ); ?>" />
		<input type="hidden" name="action" id="action" value="sz_avatar_upload" />
	</p>

	<?php if ( sz_get_group_has_avatar() ) : ?>

		<p><?php _e( "If you'd like to remove the existing group profile photo but not upload a new one, please use the delete group profile photo button.", 'sportszone' ); ?></p>

		<?php sz_button( array( 'id' => 'delete_group_avatar', 'component' => 'groups', 'wrapper_id' => 'delete-group-avatar-button', 'link_class' => 'edit', 'link_href' => sz_get_group_avatar_delete_link(), 'link_text' => __( 'Delete Group Profile Photo', 'sportszone' ) ) ); ?>

	<?php endif; ?>

	<?php
	/**
	 * Load the Avatar UI templates
	 *
	 * @since  2.3.0
	 */
	sz_avatar_get_templates(); ?>

	<?php wp_nonce_field( 'sz_avatar_upload' ); ?>

<?php endif; ?>

<?php if ( 'crop-image' == sz_get_avatar_admin_step() ) : ?>

	<h4><?php _e( 'Crop Profile Photo', 'sportszone' ); ?></h4>

	<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php esc_attr_e( 'Profile photo to crop', 'sportszone' ); ?>" />

	<div id="avatar-crop-pane">
		<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php esc_attr_e( 'Profile photo preview', 'sportszone' ); ?>" />
	</div>

	<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'sportszone' ); ?>" />

	<input type="hidden" name="image_src" id="image_src" value="<?php sz_avatar_to_crop_src(); ?>" />
	<input type="hidden" id="x" name="x" />
	<input type="hidden" id="y" name="y" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />

	<?php wp_nonce_field( 'sz_avatar_cropstore' ); ?>

<?php endif;
