<?php
/**
 * SportsZone Members Directory
 *
 * @version 3.0.0
 */

?>
<!-- sz-templates > sz-nouveau > sportszone > members > index -->
	<?php sz_nouveau_before_members_directory_content(); ?>

	<?php if ( ! sz_nouveau_is_object_nav_in_sidebar() ) : ?>

		<?php sz_get_template_part( 'common/nav/directory-nav' ); ?>

	<?php endif; ?>

	<div class="screen-content">

	<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>

		<div id="members-dir-list" class="members dir-list" data-sz-list="members">
			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'directory-members-loading' ); ?></div>
		</div><!-- #members-dir-list -->

		<?php sz_nouveau_after_members_directory_content(); ?>
	</div><!-- // .screen-content -->
