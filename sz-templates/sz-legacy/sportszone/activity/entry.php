<?php
/**
 * SportsZone - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_activity_entry' ); ?>

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

		<?php if ( sz_activity_has_content() ) : ?>

			<div class="activity-inner">

				<?php sz_activity_content_body(); ?>

			</div>

		<?php endif; ?>

		<?php

		/**
		 * Fires after the display of an activity entry content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_activity_entry_content' ); ?>

		<div class="activity-meta">

			<?php if ( sz_get_activity_type() == 'activity_comment' ) : ?>

				<a href="<?php sz_activity_thread_permalink(); ?>" class="button view sz-secondary-action"><?php _e( 'View Conversation', 'sportszone' ); ?></a>

			<?php endif; ?>

			<?php if ( is_user_logged_in() ) : ?>

				<?php if ( sz_activity_can_comment() ) : ?>

					<a href="<?php sz_activity_comment_link(); ?>" class="button acomment-reply sz-primary-action" id="acomment-comment-<?php sz_activity_id(); ?>"><?php printf( __( 'Comment %s', 'sportszone' ), '<span>' . sz_activity_get_comment_count() . '</span>' ); ?></a>

				<?php endif; ?>

				<?php if ( sz_activity_can_favorite() ) : ?>

					<?php if ( !sz_get_activity_is_favorite() ) : ?>

						<a href="<?php sz_activity_favorite_link(); ?>" class="button fav sz-secondary-action"><?php _e( 'Favorite', 'sportszone' ); ?></a>

					<?php else : ?>

						<a href="<?php sz_activity_unfavorite_link(); ?>" class="button unfav sz-secondary-action"><?php _e( 'Remove Favorite', 'sportszone' ); ?></a>

					<?php endif; ?>

				<?php endif; ?>

				<?php if ( sz_activity_user_can_delete() ) sz_activity_delete_link(); ?>

				<?php

				/**
				 * Fires at the end of the activity entry meta data area.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_activity_entry_meta' ); ?>

			<?php endif; ?>

		</div>

	</div>

	<?php

	/**
	 * Fires before the display of the activity entry comments.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_activity_entry_comments' ); ?>

	<?php if ( ( sz_activity_get_comment_count() || sz_activity_can_comment() ) || sz_is_single_activity() ) : ?>

		<div class="activity-comments">

			<?php sz_activity_comments(); ?>

			<?php if ( is_user_logged_in() && sz_activity_can_comment() ) : ?>

				<form action="<?php sz_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php sz_activity_id(); ?>" class="ac-form"<?php sz_activity_comment_form_nojs_display(); ?>>
					<div class="ac-reply-avatar"><?php sz_loggedin_user_avatar( 'width=' . SZ_AVATAR_THUMB_WIDTH . '&height=' . SZ_AVATAR_THUMB_HEIGHT ); ?></div>
					<div class="ac-reply-content">
						<div class="ac-textarea">
							<label for="ac-input-<?php sz_activity_id(); ?>" class="sz-screen-reader-text"><?php
								/* translators: accessibility text */
								_e( 'Comment', 'sportszone' );
							?></label>
							<textarea id="ac-input-<?php sz_activity_id(); ?>" class="ac-input sz-suggestions" name="ac_input_<?php sz_activity_id(); ?>"></textarea>
						</div>
						<input type="submit" name="ac_form_submit" value="<?php esc_attr_e( 'Post', 'sportszone' ); ?>" /> &nbsp; <a href="#" class="ac-reply-cancel"><?php _e( 'Cancel', 'sportszone' ); ?></a>
						<input type="hidden" name="comment_form_id" value="<?php sz_activity_id(); ?>" />
					</div>

					<?php

					/**
					 * Fires after the activity entry comment form.
					 *
					 * @since 1.5.0
					 */
					do_action( 'sz_activity_entry_comments' ); ?>

					<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>

				</form>

			<?php endif; ?>

		</div>

	<?php endif; ?>

	<?php

	/**
	 * Fires after the display of the activity entry comments.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_activity_entry_comments' ); ?>

</li>

<?php

/**
 * Fires after the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_activity_entry' );
