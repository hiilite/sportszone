<?php
/**
 * BP Nouveau Group's cover image template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<?php if ( sz_is_group_create() ) : ?>

	<h2 class="sz-screen-title creation-step-name">
		<?php esc_html_e( 'Upload Cover Image', 'sportszone' ); ?>
	</h2>

	<div id="header-cover-image"></div>

<?php else : ?>

	<h2 class="sz-screen-title">
		<?php esc_html_e( 'Change Cover Image', 'sportszone' ); ?>
	</h2>

<?php endif; ?>

<?php if ( ! sz_is_group_create() ) : ?>
	<?php if ( ! sz_get_group_has_cover_image() ) : ?>
		<p class="sz-help-text"><?php esc_html_e( 'The Cover Image will be used to customize the header of your group.', 'sportszone' ); ?></p>
	<?php else : ?>
		<p class="sz-help-text"><?php esc_html_e( 'Edit or update your cover image for this group.', 'sportszone' ); ?></p>
	<?php endif; ?>
<?php endif; ?>

<?php if ( 'upload-image' === sz_get_cover_image_admin_step() ) : ?>
	<?php if ( sz_is_group_create() ) : ?>


		<div class="left-menu">

			<?php sz_new_group_cover_image(); ?>

		</div><!-- .left-menu -->

		<div class="main-column">
	<?php endif; ?>

			<p class="sz-help-text"><?php esc_html_e( 'Upload an image to use as a cover photo for this group. The image will be shown on the main group page, and in search results.', 'sportszone' ); ?></p>

			<p>
				<label for="file" class="sz-screen-reader-text"><?php esc_html_e( 'Select an image', 'sportszone' ); ?></label>
				<input type="file" name="file" id="file" />
				<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'sportszone' ); ?>" />
				<input type="hidden" name="action" id="action" value="sz_cover_image_upload" />
			</p>

	<?php if ( sz_is_group_create() ) : ?>
			<p class="sz-help-text"><?php esc_html_e( 'To skip the group cover photo upload process, hit the "Next Step" button.', 'sportszone' ); ?></p>
		</div><!-- .main-column -->

	<?php elseif ( sz_get_group_has_cover_image() ) : ?>

		<p><?php esc_html_e( "If you'd like to remove the existing group cover photo but not upload a new one, please use the delete group cover photo button.", 'sportszone' ); ?></p>

		<?php
		sz_button(
			array(
				'id'         => 'delete_group_cover_image',
				'component'  => 'groups',
				'wrapper_id' => 'delete-group-cover-image-button',
				'link_class' => 'edit',
				'link_href'  => sz_get_group_cover_image_delete_link(),
				'link_title' => __( 'Delete Group Cover Photo', 'sportszone' ),
				'link_text'  => __( 'Delete Group Cover Photo', 'sportszone' ),
			)
		);
		?>

	<?php
	endif;

	/**
	 * Load the Cover Image UI templates
	 *
	 * @since 2.3.0
	 */
	sz_cover_image_get_templates();

	if ( ! sz_is_group_create() ) {
		wp_nonce_field( 'sz_cover_image_upload' );
	}
	?>

<?php
endif;

if ( 'crop-image' === sz_get_cover_image_admin_step() ) :
?>

	<h2><?php esc_html_e( 'Crop Group Cover Photo', 'sportszone' ); ?></h2>

	<img src="<?php sz_cover_image_to_crop(); ?>" id="cover-image-to-crop" class="cover_image" alt="<?php esc_attr_e( 'Cover photo to crop', 'sportszone' ); ?>" />

	<div id="cover-image-crop-pane">
		<img src="<?php sz_cover_image_to_crop(); ?>" id="cover-image-crop-preview" class="cover_image" alt="<?php esc_attr_e( 'Cover photo preview', 'sportszone' ); ?>" />
	</div>

	<input type="submit" name="cover-image-crop-submit" id="cover-image-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'sportszone' ); ?>" />

	<input type="hidden" name="image_src" id="image_src" value="<?php sz_cover_image_to_crop_src(); ?>" />
	<input type="hidden" id="x" name="x" />
	<input type="hidden" id="y" name="y" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />

	<?php
	if ( ! sz_is_group_create() ) {
		wp_nonce_field( 'sz_cover_image_cropstore' );
	}
	?>

<?php
endif;

//sz_attachments_get_template_part( 'cover-images/index' );
