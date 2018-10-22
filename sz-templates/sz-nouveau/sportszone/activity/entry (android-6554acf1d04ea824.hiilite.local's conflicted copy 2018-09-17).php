<?php
/**
 * SportsZone - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @since 3.0.0
 * @version 3.0.0
 */

sz_nouveau_activity_hook( 'before', 'entry' ); ?>

<li class="<?php sz_activity_css_class(); ?>" id="activity-<?php sz_activity_id(); ?>" data-sz-activity-id="<?php sz_activity_id(); ?>" data-sz-timestamp="<?php sz_nouveau_activity_timestamp(); ?>">

	<div class="activity-avatar item-avatar">

		<a href="<?php sz_activity_user_link(); ?>">

			<?php sz_activity_avatar( array( 'type' => 'full' ) ); ?>

		</a>

	</div>
	
	<div class="activity-content">

		<div class="activity-header">

			<?php sz_activity_action(); ?> 

		</div>

		<?php if ( sz_nouveau_activity_has_content() ) : ?>

			<div class="activity-inner">

				<?php sz_nouveau_activity_content(); ?>

			</div>

		<?php endif; ?>

		<?php sz_nouveau_activity_entry_buttons(); ?>

	</div>

	<!--div class="activity-content">

		<div class="activity-header">

			<?php sz_activity_action(); ?> 

		</div>

		<?php /*if ( sz_nouveau_activity_has_content() ) : ?>

			<div class="activity-inner">

				<?php sz_nouveau_activity_content(); ?>

			</div>

		<?php endif;*/ ?>

		<?php //sz_nouveau_activity_entry_buttons(); ?>

	</div-->

	<?php sz_nouveau_activity_hook( 'before', 'entry_comments' ); ?>

	<?php if ( sz_activity_get_comment_count() || ( is_user_logged_in() && ( sz_activity_can_comment() || sz_is_single_activity() ) ) ) : ?>

		<div class="activity-comments">

			<?php sz_activity_comments(); ?>

			<?php sz_nouveau_activity_comment_form(); ?>

		</div>

	<?php endif; ?>

	<?php sz_nouveau_activity_hook( 'after', 'entry_comments' ); ?>

</li>

<?php
sz_nouveau_activity_hook( 'after', 'entry' );
