<?php
/**
 * SportsZone Avatars main template.
 *
 * This template is used to inject the SportsZone Backbone views
 * dealing with avatars.
 *
 * It's also used to create the common Backbone views.
 *
 * @since 2.3.0
 *
 * @package SportsZone
 * @subpackage sz-attachments
 * @version 3.0.0
 */

/**
 * This action is for internal use, please do not use it
 */
do_action( 'sz_attachments_avatar_check_template' );
?>
<div class="sz-avatar-nav"></div>
<div class="sz-avatar"></div>
<div class="sz-avatar-status"></div>

<script type="text/html" id="tmpl-sz-avatar-nav">
	<a href="{{data.href}}" class="sz-avatar-nav-item" data-nav="{{data.id}}">{{data.name}}</a>
</script>

<?php sz_attachments_get_template_part( 'uploader' ); ?>

<?php sz_attachments_get_template_part( 'avatars/crop' ); ?>

<?php sz_attachments_get_template_part( 'avatars/camera' ); ?>

<script id="tmpl-sz-avatar-delete" type="text/html">
	<# if ( 'user' === data.object ) { #>
		<p><?php _e( "If you'd like to delete your current profile photo but not upload a new one, please use the delete profile photo button.", 'sportszone' ); ?></p>
		<p><a class="button edit" id="sz-delete-avatar" href="#"><?php esc_html_e( 'Delete My Profile Photo', 'sportszone' ); ?></a></p>
	<# } else if ( 'group' === data.object ) { #>
		<p><?php _e( "If you'd like to remove the existing group profile photo but not upload a new one, please use the delete group profile photo button.", 'sportszone' ); ?></p>
		<p><a class="button edit" id="sz-delete-avatar" href="#"><?php esc_html_e( 'Delete Group Profile Photo', 'sportszone' ); ?></a></p>
	<# } else { #>
		<?php do_action( 'sz_attachments_avatar_delete_template' ); ?>
	<# } #>
</script>

<?php do_action( 'sz_attachments_avatar_main_template' );
