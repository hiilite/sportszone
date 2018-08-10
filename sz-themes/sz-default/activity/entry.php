<?php

/**
 * SportsZone - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php do_action( 'sz_before_activity_entry' ); ?>

<li class="<?php sz_activity_css_class(); ?>" id="activity-<?php sz_activity_id(); ?>">
	<div class="activity-avatar">
		<a href="<?php sz_activity_user_link(); ?>">

			<?php sz_activity_avatar(); ?>

		</a>
	</div>

	<div class="activity-content">

		<div class="activity-header">

			<?php sz_activity_action(); ?>

		</div>

		<?php if ( 'activity_comment' == sz_get_activity_type() ) : ?>

			<div class="activity-inreplyto">
				<strong><?php _e( 'In reply to: ', 'sportszone' ); ?></strong><?php sz_activity_parent_content(); ?> <a href="<?php sz_activity_thread_permalink(); ?>" class="view" title="<?php esc_attr_e( 'View Thread / Permalink', 'sportszone' ); ?>"><?php _e( 'View', 'sportszone' ); ?></a>
			</div>

		<?php endif; ?>

		<?php if ( sz_activity_has_content() ) : ?>

			<div class="activity-inner">

				<?php sz_activity_content_body(); ?>

			</div>

		<?php endif; ?>

		<?php do_action( 'sz_activity_entry_content' ); ?>

		<?php if ( is_user_logged_in() ) : ?>

			<div class="activity-meta">

				<?php if ( sz_activity_can_comment() ) : ?>

					<a href="<?php sz_activity_comment_link(); ?>" class="button acomment-reply sz-primary-action" id="acomment-comment-<?php sz_activity_id(); ?>"><?php printf( __( 'Comment <span>%s</span>', 'sportszone' ), sz_activity_get_comment_count() ); ?></a>

				<?php endif; ?>

				<?php if ( sz_activity_can_favorite() ) : ?>

					<?php if ( !sz_get_activity_is_favorite() ) : ?>

						<a href="<?php sz_activity_favorite_link(); ?>" class="button fav sz-secondary-action" title="<?php esc_attr_e( 'Mark as Favorite', 'sportszone' ); ?>"><?php _e( 'Favorite', 'sportszone' ); ?></a>

					<?php else : ?>

						<a href="<?php sz_activity_unfavorite_link(); ?>" class="button unfav sz-secondary-action" title="<?php esc_attr_e( 'Remove Favorite', 'sportszone' ); ?>"><?php _e( 'Remove Favorite', 'sportszone' ); ?></a>

					<?php endif; ?>

				<?php endif; ?>

				<?php if ( sz_activity_user_can_delete() ) sz_activity_delete_link(); ?>

				<?php do_action( 'sz_activity_entry_meta' ); ?>

			</div>

		<?php endif; ?>

	</div>

	<?php do_action( 'sz_before_activity_entry_comments' ); ?>

	<?php if ( ( is_user_logged_in() && sz_activity_can_comment() ) || sz_is_single_activity() ) : ?>

		<div class="activity-comments">

			<?php sz_activity_comments(); ?>

			<?php if ( is_user_logged_in() ) : ?>

				<form action="<?php sz_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php sz_activity_id(); ?>" class="ac-form"<?php sz_activity_comment_form_nojs_display(); ?>>
					<div class="ac-reply-avatar"><?php sz_loggedin_user_avatar( 'width=' . SZ_AVATAR_THUMB_WIDTH . '&height=' . SZ_AVATAR_THUMB_HEIGHT ); ?></div>
					<div class="ac-reply-content">
						<div class="ac-textarea">
							<textarea id="ac-input-<?php sz_activity_id(); ?>" class="ac-input sz-suggestions" name="ac_input_<?php sz_activity_id(); ?>"></textarea>
						</div>
						<input type="submit" name="ac_form_submit" value="<?php esc_attr_e( 'Post', 'sportszone' ); ?>" /> &nbsp; <?php _e( 'or press esc to cancel.', 'sportszone' ); ?>
						<input type="hidden" name="comment_form_id" value="<?php sz_activity_id(); ?>" />
					</div>

					<?php do_action( 'sz_activity_entry_comments' ); ?>

					<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>

				</form>

			<?php endif; ?>

		</div>

	<?php endif; ?>

	<?php do_action( 'sz_after_activity_entry_comments' ); ?>

</li>

<?php do_action( 'sz_after_activity_entry' ); ?>
