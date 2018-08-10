<?php
/**
 * SportsZone Cover Images main template.
 *
 * This template is used to inject the SportsZone Backbone views
 * dealing with cover images.
 *
 * It's also used to create the common Backbone views.
 *
 * @since 2.4.0
 * @version 3.1.0
 */

?>

<div class="sz-cover-image"></div>
<div class="sz-cover-image-status"></div>
<div class="sz-cover-image-manage"></div>

<?php sz_attachments_get_template_part( 'uploader' ); ?>

<script id="tmpl-sz-cover-image-delete" type="text/html">
	<# if ( 'user' === data.object ) { #>
		<p><?php esc_html_e( "If you'd like to delete your current cover image, use the delete Cover Image button.", 'sportszone' ); ?></p>
		<button type="button" class="button edit" id="sz-delete-cover-image">
			<?php
			echo esc_html_x( 'Delete My Cover Image', 'button', 'sportszone' );
			?>
		</button>
	<# } else if ( 'group' === data.object ) { #>
		<p><?php esc_html_e( "If you'd like to remove the existing group cover image but not upload a new one, please use the delete group cover image button.", 'sportszone' ); ?></p>
		<button type="button" class="button edit" id="sz-delete-cover-image">
			<?php
			echo esc_html_x( 'Delete Group Cover Image', 'button', 'sportszone' );
			?>
		</button>
	<# } else { #>
		<?php
			/**
			 * Fires inside the cover image delete frontend template markup if no other data.object condition is met.
			 *
			 * @since 3.0.0
			 */
			do_action( 'sz_attachments_cover_image_delete_template' ); ?>
	<# } #>
</script>

<?php
	/**
	 * Fires after the cover image main frontend template markup.
	 *
	 * @since 3.0.0
	 */
	do_action( 'sz_attachments_cover_image_main_template' ); ?>
