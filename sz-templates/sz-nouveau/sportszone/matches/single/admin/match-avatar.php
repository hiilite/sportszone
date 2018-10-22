<?php
/**
 * BP Nouveau Event's avatar template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<?php if ( sz_is_event_create() ) : ?>

	<h3 class="sz-screen-title creation-step-name">
		<?php esc_html_e( 'Upload Event Avatar', 'sportszone' ); ?>
	</h3>

<?php else : ?>

	<h2 class="sz-screen-title">
		<?php esc_html_e( 'Change Event Avatar', 'sportszone' ); ?>
	</h2>

<?php endif; ?>

<?php if ( ! sz_is_event_create() ) : ?>
	<?php if ( ! sz_get_event_has_avatar() ) : ?>
		<p class="sz-help-text"><?php esc_html_e( 'Add an image to use as a profile photo for this event. The image will be shown on the main event page, and in search results.', 'sportszone' ); ?></p>
	<?php else : ?>
		<p class="sz-help-text"><?php esc_html_e( 'Edit or update your avatar image for this event.', 'sportszone' ); ?></p>
	<?php endif; ?>
<?php endif; ?>


<?php if ( 'upload-image' === sz_get_avatar_admin_step() ) : ?>
	<?php if ( sz_is_event_create() ) : ?>


		<div class="left-menu">

			<?php sz_new_event_avatar(); ?>

		</div><!-- .left-menu -->

		<div class="main-column">
	<?php endif; ?>

			<p class="sz-help-text"><?php esc_html_e( 'Upload an image to use as a profile photo for this event. The image will be shown on the main event page, and in search results.', 'sportszone' ); ?></p>

			<p>
				<label for="file" class="sz-screen-reader-text"><?php esc_html_e( 'Select an image', 'sportszone' ); ?></label>
				<input type="file" name="file" id="file" />
				<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'sportszone' ); ?>" />
				<input type="hidden" name="action" id="action" value="sz_avatar_upload" />
			</p>

	<?php if ( sz_is_event_create() ) : ?>
			<p class="sz-help-text"><?php esc_html_e( 'To skip the event profile photo upload process, hit the "Next Step" button.', 'sportszone' ); ?></p>
		</div><!-- .main-column -->

	<?php elseif ( sz_get_event_has_avatar() ) : ?>

		<p><?php esc_html_e( "If you'd like to remove the existing event profile photo but not upload a new one, please use the delete event profile photo button.", 'sportszone' ); ?></p>

		<?php
		sz_button(
			array(
				'id'         => 'delete_event_avatar',
				'component'  => 'events',
				'wrapper_id' => 'delete-event-avatar-button',
				'link_class' => 'edit',
				'link_href'  => sz_get_event_avatar_delete_link(),
				'link_title' => __( 'Delete Event Profile Photo', 'sportszone' ),
				'link_text'  => __( 'Delete Event Profile Photo', 'sportszone' ),
			)
		);
		?>

	<?php
	endif;

	/**
	 * Load the Avatar UI templates
	 *
	 * @since 2.3.0
	 */
	sz_avatar_get_templates();

	if ( ! sz_is_event_create() ) {
		wp_nonce_field( 'sz_avatar_upload' );
	}
	?>

<?php
endif;

if ( 'crop-image' === sz_get_avatar_admin_step() ) :
?>

	<h2><?php esc_html_e( 'Crop Event Profile Photo', 'sportszone' ); ?></h2>

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

	<?php
	if ( ! sz_is_event_create() ) {
		wp_nonce_field( 'sz_avatar_cropstore' );
	}
	?>

<?php
endif;
