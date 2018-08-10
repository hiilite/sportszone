<?php

/**
 * SportsZone - Activity Post Form
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<form action="<?php sz_activity_post_form_action(); ?>" method="post" id="whats-new-form" name="whats-new-form" role="complementary">

	<?php do_action( 'sz_before_activity_post_form' ); ?>

	<div id="whats-new-avatar">
		<a href="<?php echo sz_loggedin_user_domain(); ?>">
			<?php sz_loggedin_user_avatar( 'width=' . sz_core_avatar_thumb_width() . '&height=' . sz_core_avatar_thumb_height() ); ?>
		</a>
	</div>

	<h5><?php if ( sz_is_group() )
			printf( __( "What's new in %s, %s?", 'sportszone' ), sz_get_group_name(), sz_get_user_firstname() );
		else
			printf( __( "What's new, %s?", 'sportszone' ), sz_get_user_firstname() );
	?></h5>

	<div id="whats-new-content">
		<div id="whats-new-textarea">
			<textarea name="whats-new" id="whats-new" class="sz-suggestions" cols="50" rows="10"><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_textarea( $_GET['r'] ); ?> <?php endif; ?></textarea>
		</div>

		<div id="whats-new-options">
			<div id="whats-new-submit">
				<input type="submit" name="aw-whats-new-submit" id="aw-whats-new-submit" value="<?php esc_attr_e( 'Post Update', 'sportszone' ); ?>" />
			</div>

			<?php if ( sz_is_active( 'groups' ) && !sz_is_my_profile() && !sz_is_group() ) : ?>

				<div id="whats-new-post-in-box">

					<?php _e( 'Post in', 'sportszone' ); ?>:

					<select id="whats-new-post-in" name="whats-new-post-in">
						<option selected="selected" value="0"><?php _e( 'My Profile', 'sportszone' ); ?></option>

						<?php if ( sz_has_groups( 'user_id=' . sz_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0' ) ) :
							while ( sz_groups() ) : sz_the_group(); ?>

								<option value="<?php sz_group_id(); ?>"><?php sz_group_name(); ?></option>

							<?php endwhile;
						endif; ?>

					</select>
				</div>
				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />

			<?php elseif ( sz_is_group_home() ) : ?>

				<input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
				<input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php sz_group_id(); ?>" />

			<?php endif; ?>

			<?php do_action( 'sz_activity_post_form_options' ); ?>

		</div><!-- #whats-new-options -->
	</div><!-- #whats-new-content -->

	<?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
	<?php do_action( 'sz_after_activity_post_form' ); ?>

</form><!-- #whats-new-form -->
