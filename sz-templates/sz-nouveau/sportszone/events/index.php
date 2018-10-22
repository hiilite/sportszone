<?php
/**
 * BP Nouveau - Events Directory
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

	<?php sz_nouveau_before_events_directory_content(); ?>

	<?php sz_nouveau_template_notices(); ?>

		<?php sz_get_template_part( 'common/nav/directory-nav' ); ?>

	<div class="screen-content">

	<?php 
		sz_get_template_part( 'events/search-and-filters-bar' ); ?>

		<div id="events-dir-list" class="events dir-list" data-sz-list="events">
			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'directory-events-loading' ); ?></div>
		</div><!-- #events-dir-list -->

	<?php sz_nouveau_after_events_directory_content(); ?>
	</div><!-- // .screen-content -->

