<?php
/**
 * SportsZone Avatars crop template.
 *
 * This template is used to create the crop Backbone views.
 *
 * @since 2.3.0
 * @version 3.1.0
 */

?>
<!-- sportszone > sz-templates > sz-nouveau > sportszone > assets > _attachments > cover-images > crop -->
<script id="tmpl-sz-cover-image-item" type="text/html">
	<div id="cover-image-to-crop">
		<img src="{{data.url}}"/>
	</div>
	<div class="cover-image-crop-management">
		<div id="cover-image-crop-pane" class="cover-image" style="width:{{data.full_w}}px; height:{{data.full_h}}px">
			<img src="{{data.url}}" id="cover-image-crop-preview"/>
		</div>
		<div id="cover-image-crop-actions">
			<button type="button" class="button cover-image-crop-submit"><?php echo esc_html_x( 'Crop Image', 'button', 'sportszone' ); ?></button>
		</div>
	</div>
</script>
