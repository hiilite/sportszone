<?php
/**
 * @version 3.0.0
 */

if ( sz_activity_embed_has_activity( sz_current_action() ) ) :
?>

	<?php
	while ( sz_activities() ) :
		sz_the_activity();
	?>

		<div class="sz-embed-excerpt"><?php sz_activity_embed_excerpt(); ?></div>

		<?php sz_activity_embed_media(); ?>

	<?php endwhile; ?>

<?php
endif;
