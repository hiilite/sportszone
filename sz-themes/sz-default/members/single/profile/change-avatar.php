<h4><?php _e( 'Change Avatar', 'sportszone' ); ?></h4>

<?php do_action( 'sz_before_profile_avatar_upload_content' ); ?>

<?php if ( !(int)sz_get_option( 'sz-disable-avatar-uploads' ) ) : ?>

	<p><?php _e( 'Your avatar will be used on your profile and throughout the site. If there is a <a href="http://gravatar.com">Gravatar</a> associated with your account email we will use that, or you can upload an image from your computer.', 'sportszone'); ?></p>

	<form action="" method="post" id="avatar-upload-form" class="standard-form" enctype="multipart/form-data">

		<?php if ( 'upload-image' == sz_get_avatar_admin_step() ) : ?>

			<?php wp_nonce_field( 'sz_avatar_upload' ); ?>
			<p><?php _e( 'Click below to select a JPG, GIF or PNG format photo from your computer and then click \'Upload Image\' to proceed.', 'sportszone' ); ?></p>

			<p id="avatar-upload">
				<input type="file" name="file" id="file" />
				<input type="submit" name="upload" id="upload" value="<?php esc_attr_e( 'Upload Image', 'sportszone' ); ?>" />
				<input type="hidden" name="action" id="action" value="sz_avatar_upload" />
			</p>

			<?php if ( sz_get_user_has_avatar() ) : ?>
				<p><?php _e( "If you'd like to delete your current avatar but not upload a new one, please use the delete avatar button.", 'sportszone' ); ?></p>
				<p><a class="button edit" href="<?php sz_avatar_delete_link(); ?>" title="<?php esc_attr_e( 'Delete Avatar', 'sportszone' ); ?>"><?php _e( 'Delete My Avatar', 'sportszone' ); ?></a></p>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( 'crop-image' == sz_get_avatar_admin_step() ) : ?>

			<h5><?php _e( 'Crop Your New Avatar', 'sportszone' ); ?></h5>

			<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php esc_attr_e( 'Avatar to crop', 'sportszone' ); ?>" />

			<div id="avatar-crop-pane">
				<img src="<?php sz_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php esc_attr_e( 'Avatar preview', 'sportszone' ); ?>" />
			</div>

			<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php esc_attr_e( 'Crop Image', 'sportszone' ); ?>" />

			<input type="hidden" name="image_src" id="image_src" value="<?php sz_avatar_to_crop_src(); ?>" />
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />

			<?php wp_nonce_field( 'sz_avatar_cropstore' ); ?>

		<?php endif; ?>

	</form>

<?php else : ?>

	<p><?php _e( 'Your avatar will be used on your profile and throughout the site. To change your avatar, please create an account with <a href="http://gravatar.com">Gravatar</a> using the same email address as you used to register with this site.', 'sportszone' ); ?></p>

<?php endif; ?>

<?php do_action( 'sz_after_profile_avatar_upload_content' ); ?>
