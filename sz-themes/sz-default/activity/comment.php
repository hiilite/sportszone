<?php

/**
 * SportsZone - Activity Stream Comment
 *
 * This template is used by sz_activity_comments() functions to show
 * each activity.
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php do_action( 'sz_before_activity_comment' ); ?>

<li id="acomment-<?php sz_activity_comment_id(); ?>">
	<div class="acomment-avatar">
		<a href="<?php sz_activity_comment_user_link(); ?>">
			<?php sz_activity_avatar( array(
				'type'    => 'thumb',
				'user_id' => sz_get_activity_comment_user_id()
			) ); ?>
		</a>
	</div>

	<div class="acomment-meta">
		<?php
		/* translators: 1: user profile link, 2: user name, 3: activity permalink, 4: activity timestamp */
		printf( __( '<a href="%1$s">%2$s</a> replied <a href="%3$s" class="activity-time-since"><span class="time-since">%4$s</span></a>', 'sportszone' ), sz_get_activity_comment_user_link(), sz_get_activity_comment_name(), sz_get_activity_comment_permalink(), sz_get_activity_comment_date_recorded() );
		?>
	</div>

	<div class="acomment-content"><?php sz_activity_comment_content(); ?></div>

	<div class="acomment-options">

		<?php if ( is_user_logged_in() && sz_activity_can_comment_reply( sz_activity_current_comment() ) ) : ?>

			<a href="#acomment-<?php sz_activity_comment_id(); ?>" class="acomment-reply sz-primary-action" id="acomment-reply-<?php sz_activity_id(); ?>-from-<?php sz_activity_comment_id(); ?>"><?php _e( 'Reply', 'sportszone' ); ?></a>

		<?php endif; ?>

		<?php if ( sz_activity_user_can_delete() ) : ?>

			<a href="<?php sz_activity_comment_delete_link(); ?>" class="delete acomment-delete confirm sz-secondary-action" rel="nofollow"><?php _e( 'Delete', 'sportszone' ); ?></a>

		<?php endif; ?>

		<?php do_action( 'sz_activity_comment_options' ); ?>

	</div>

	<?php sz_activity_recurse_comments( sz_activity_current_comment() ); ?>
</li>

<?php do_action( 'sz_after_activity_comment' ); ?>
