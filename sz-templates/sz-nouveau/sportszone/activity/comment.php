<?php
/**
 * SportsZone - Activity Stream Comment
 *
 * This template is used by sz_activity_comments() functions to show
 * each activity.
 *
 * @version 3.0.0
 */

	?>

<li id="acomment-<?php sz_activity_comment_id(); ?>" class="comment-item" data-sz-activity-comment-id="<?php sz_activity_comment_id(); ?>">
	<div class="acomment-avatar item-avatar">
		<a href="<?php sz_activity_comment_user_link(); ?>">
			<?php
			sz_activity_avatar(
				array(
					'type'    => 'thumb',
					'user_id' => sz_get_activity_comment_user_id(),
				)
			);
			?>
		</a>
	</div>

	<div class="acomment-meta">

		<?php sz_nouveau_activity_comment_action(); ?>

	</div>

	<div class="acomment-content"><?php sz_activity_comment_content(); ?></div>

	<?php sz_nouveau_activity_comment_buttons( array( 'container' => 'div' ) ); ?>

	<?php sz_nouveau_activity_recurse_comments( sz_activity_current_comment() ); ?>
</li>
