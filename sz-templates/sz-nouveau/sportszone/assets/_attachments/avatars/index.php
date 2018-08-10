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
 * @version 3.1.0
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
		<p><?php esc_html_e( "If you'd like to delete your current profile photo, use the delete profile photo button.", 'sportszone' ); ?></p>
		<button type="button" class="button edit" id="sz-delete-avatar"><?php esc_html_e( 'Delete My Profile Photo', 'sportszone' ); ?></button>
	<# } else if ( 'group' === data.object ) { #>
		<?php sz_nouveau_user_feedback( 'group-avatar-delete-info' ); ?>
		<button type="button" class="button edit" id="sz-delete-avatar"><?php esc_html_e( 'Delete Group Profile Photo', 'sportszone' ); ?></button>
	<# } else { #>
		<?php
			/**
			 * Fires inside the avatar delete frontend template markup if no other data.object condition is met.
			 *
			 * @since 3.0.0
			 */
			do_action( 'sz_attachments_avatar_delete_template' ); ?>
	<# } #>
</script>

<?php
	/**
	 * Fires after the avatar main frontend template markup.
	 *
	 * @since 3.0.0
	 */
	do_action( 'sz_attachments_avatar_main_template' ); ?>
