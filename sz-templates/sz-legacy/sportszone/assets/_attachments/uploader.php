<?php
/**
 * SportsZone Uploader templates.
 *
 * This template is used to create the SportsZone Uploader Backbone views.
 *
 * @since 2.3.0
 *
 * @package SportsZone
 * @subpackage sz-attachments
 * @version 3.0.0
 */

?>
<script type="text/html" id="tmpl-upload-window">
	<?php if ( ! _device_can_upload() ) : ?>
		<h3 class="upload-instructions"><?php esc_html_e( 'The web browser on your device cannot be used to upload files.', 'sportszone' ); ?></h3>
	<?php elseif ( is_multisite() && ! is_upload_space_available() ) : ?>
		<h3 class="upload-instructions"><?php esc_html_e( 'Upload Limit Exceeded', 'sportszone' ); ?></h3>
	<?php else : ?>
		<div id="{{data.container}}">
			<div id="{{data.drop_element}}">
				<div class="drag-drop-inside">
					<p class="drag-drop-info"><?php esc_html_e( 'Drop your file here', 'sportszone' ); ?></p>
					<p><?php _ex( 'or', 'Uploader: Drop your file here - or - Select your File', 'sportszone' ); ?></p>
					<p class="drag-drop-buttons"><label for="{{data.browse_button}}" class="<?php echo is_admin() ? 'screen-reader-text' : 'sz-screen-reader-text' ;?>"><?php
						/* translators: accessibility text */
						esc_html_e( 'Select your File', 'sportszone' );
					?></label><input id="{{data.browse_button}}" type="button" value="<?php esc_attr_e( 'Select your File', 'sportszone' ); ?>" class="button" /></p>
				</div>
			</div>
		</div>
	<?php endif; ?>
</script>

<script type="text/html" id="tmpl-progress-window">
	<div id="{{data.id}}">
		<div class="sz-progress">
			<div class="sz-bar"></div>
		</div>
		<div class="filename">{{data.filename}}</div>
	</div>
</script>
