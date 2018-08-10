<?php
/**
 * BP Nouveau - Groups Directory
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

	<?php sz_nouveau_before_groups_directory_content(); ?>

	<?php sz_nouveau_template_notices(); ?>

		<?php sz_get_template_part( 'common/nav/directory-nav' ); ?>

	<div class="screen-content">

	<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>

		<div id="groups-dir-list" class="groups dir-list" data-sz-list="groups">
			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'directory-groups-loading' ); ?></div>
		</div><!-- #groups-dir-list -->

	<?php sz_nouveau_after_groups_directory_content(); ?>
	</div><!-- // .screen-content -->

