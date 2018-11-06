<?php
/**
 * SportsZone Activity templates
 *
 * @since 2.3.0
 * @version 3.0.0
 */
?>

	<?php sz_nouveau_before_activity_directory_content(); ?>

	<?php if ( is_user_logged_in() ) : ?>

		<?php sz_get_template_part( 'activity/post-form' ); ?>

	<?php endif; ?>

	<?php sz_nouveau_template_notices(); ?>

	<?php if ( ! sz_nouveau_is_object_nav_in_sidebar() ) : ?>

		<?php sz_get_template_part( 'common/nav/directory-nav' ); ?>

	<?php endif; ?>
	
	<?php sz_get_template_part( 'common/nav/directory-nav' ); ?>

	<div class="screen-content">

		<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>

		<?php sz_nouveau_activity_hook( 'before_directory', 'list' ); ?>

		<div id="activity-stream" class="activity" data-sz-list="activity">

				<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'directory-activity-loading' ); ?></div>

		</div><!-- .activity -->

		<?php sz_nouveau_after_activity_directory_content(); ?>

	</div><!-- // .screen-content -->

