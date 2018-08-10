<?php
/**
 * BP Nouveau Activity Comment form template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */

if ( ! sz_nouveau_current_user_can( 'comment_activity' ) || ! sz_activity_can_comment() ) {
	return;
} ?>

<form action="<?php sz_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php sz_activity_id(); ?>" class="ac-form"<?php sz_activity_comment_form_nojs_display(); ?>>

	<div class="ac-reply-avatar"><?php sz_loggedin_user_avatar( array( 'type' => 'thumb' ) ); ?></div>
	<div class="ac-reply-content">
		<div class="ac-textarea">
			<label for="ac-input-<?php sz_activity_id(); ?>" class="sz-screen-reader-text">
				<?php echo esc_html( _x( 'Comment', 'heading', 'sportszone' ) ); ?>
			</label>
			<textarea id="ac-input-<?php sz_activity_id(); ?>" class="ac-input sz-suggestions" name="ac_input_<?php sz_activity_id(); ?>"></textarea>
		</div>
		<input type="hidden" name="comment_form_id" value="<?php sz_activity_id(); ?>" />

		<?php
		sz_nouveau_submit_button( 'activity-new-comment' );
		printf(
			'&nbsp; <button type="button" class="ac-reply-cancel">%s</button>',
			esc_html( _x( 'Cancel', 'button', 'sportszone' ) )
		);
		?>
	</div>

</form>
