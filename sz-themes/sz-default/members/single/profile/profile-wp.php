<?php do_action( 'sz_before_profile_loop_content' ); ?>

<?php $ud = get_userdata( sz_displayed_user_id() ); ?>

<?php do_action( 'sz_before_profile_field_content' ); ?>

	<div class="sz-widget wp-profile">
		<h4><?php sz_is_my_profile() ? _e( 'My Profile', 'sportszone' ) : printf( __( "%s's Profile", 'sportszone' ), sz_get_displayed_user_fullname() ); ?></h4>

		<table class="wp-profile-fields">

			<?php if ( $ud->display_name ) : ?>

				<tr id="wp_displayname">
					<td class="label"><?php _e( 'Name', 'sportszone' ); ?></td>
					<td class="data"><?php echo $ud->display_name; ?></td>
				</tr>

			<?php endif; ?>

			<?php if ( $ud->user_description ) : ?>

				<tr id="wp_desc">
					<td class="label"><?php _e( 'About Me', 'sportszone' ); ?></td>
					<td class="data"><?php echo $ud->user_description; ?></td>
				</tr>

			<?php endif; ?>

			<?php if ( $ud->user_url ) : ?>

				<tr id="wp_website">
					<td class="label"><?php _e( 'Website', 'sportszone' ); ?></td>
					<td class="data"><?php echo make_clickable( $ud->user_url ); ?></td>
				</tr>

			<?php endif; ?>

			<?php if ( $ud->jabber ) : ?>

				<tr id="wp_jabber">
					<td class="label"><?php _e( 'Jabber', 'sportszone' ); ?></td>
					<td class="data"><?php echo $ud->jabber; ?></td>
				</tr>

			<?php endif; ?>

			<?php if ( $ud->aim ) : ?>

				<tr id="wp_aim">
					<td class="label"><?php _e( 'AOL Messenger', 'sportszone' ); ?></td>
					<td class="data"><?php echo $ud->aim; ?></td>
				</tr>

			<?php endif; ?>

			<?php if ( $ud->yim ) : ?>

				<tr id="wp_yim">
					<td class="label"><?php _e( 'Yahoo Messenger', 'sportszone' ); ?></td>
					<td class="data"><?php echo $ud->yim; ?></td>
				</tr>

			<?php endif; ?>

		</table>
	</div>

<?php do_action( 'sz_after_profile_field_content' ); ?>

<?php do_action( 'sz_profile_field_buttons' ); ?>

<?php do_action( 'sz_after_profile_loop_content' ); ?>
