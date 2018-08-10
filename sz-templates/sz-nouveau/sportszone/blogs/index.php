<?php
/**
 * SportsZone - Blogs Directory
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

	<?php sz_nouveau_before_blogs_directory_content(); ?>

	<?php if ( ! sz_nouveau_is_object_nav_in_sidebar() ) : ?>

		<?php sz_get_template_part( 'common/nav/directory-nav' ); ?>

	<?php endif; ?>

	<div class="screen-content">

	<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>

		<div id="blogs-dir-list" class="blogs dir-list" data-sz-list="blogs">
			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'directory-blogs-loading' ); ?></div>
		</div><!-- #blogs-dir-list -->

		<?php sz_nouveau_after_blogs_directory_content(); ?>
	</div><!-- // .screen-content -->

