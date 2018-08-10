<?php
/**
 * SportsZone - Activity Post Form
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<form action="<?php sz_activity_post_form_action(); ?>" method="post" id="whats-new-form" name="whats-new-form">

	<?php

	/**
	 * Fires before the activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_activity_post_form' ); ?>

	<div id="whats-new-avatar">
		<a href="<?php echo sz_loggedin_user_domain(); ?>">
			<?php sz_loggedin_user_avatar( 'width=' . sz_core_avatar_thumb_width() . '&height=' . sz_core_avatar_thumb_height() ); ?>
		</a>
	</div>

	<p class="activity-greeting"><?php if ( sz_is_group() )
		printf( __( "What's new in %s, %s?", 'sportszone' ), sz_get_group_name(), sz_get_user_firstname( sz_get_loggedin_user_fullname() ) );
	else
		printf( __( "What's new, %s?", 'sportszone' ), sz_get_user_firstname( sz_get_loggedin_user_fullname() ) );
	?></p>

	<div id="whats-new-content">
		<div id="whats-new-textarea">
			<label for="whats-new" class="sz-screen-reader-text"><?php
				/* translators: accessibility text */
				_e( 'Post what\'s new', 'sportszone' );
			?></label>
			<textarea class="sz-suggestions" name="whats-new" id="whats-new" cols="50" rows="10"
				<?php if ( sz_is_group() ) : ?>data-suggestions-group-id="<?php echo esc_attr( (int) sz_get_current_group_id() ); ?>" <?php endif; ?>
			><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_textarea( $_GET['r'] ); ?> <?php endif; ?></textarea>
		</div>

		<div id="whats-new-options">
			<div id="whats-new-submit">
				<input type="submit" name="aw-whats-new-submit" id="aw-whats-new-submit" value="<?php esc_attr_e( 'Post Update', 'sportszone' ); ?>" />
			</div>

			<?php if ( sz_is_active( 'groups' ) && !sz_is_my_profile() && !sz_is_group() ) : ?>

				<div id="whats-new-post-in-box">

					<?php _e( 'Post in', 'sportszone' ); ?>:

					<label for="whats-new-post-in" class="sz-screen-reader-text"><?php
						/* translators: accessibility text */
						_e( 'Post in', 'sportszone' );
					?></label>
					<select id="whats-new-post-in" name="whats-new-post-in">
						<option selected="selected" value="0"><?php _e( 'My Profile', 'sportszone' ); ?></option>

						<?php if ( sz_has_groups( 'user_id=' . sz_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0&update_meta_cache=0' ) ) :
							while ( sz_groups() ) : sz_the_group(); ?>

								<option value="<?php sz_group_id(); ?>"><?php sz_group_name(); ?></option>

							<?php endwhile;
						endif; ?>

					</select>
				</div>
				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />

			<?php elseif ( sz_is_group_activity() ) : ?>

				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
				<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php sz_group_id(); ?>" />

			<?php endif; ?>

			<?php

			/**
			 * Fires at the end of the activity post form markup.
			 *
			 * @since 1.2.0
			 */
			do_action( 'sz_activity_post_form_options' ); ?>

		</div><!-- #whats-new-options -->
	</div><!-- #whats-new-content -->

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php

	/**
	 * Fires after the activity post form.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_activity_post_form' ); ?>

</form><!-- #whats-new-form -->
