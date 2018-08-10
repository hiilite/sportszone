<?php
/**
 * SportsZone Single Members item Sub Navigation
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<?php if ( sz_nouveau_has_nav( array( 'type' => 'secondary' ) ) ) : ?>

	<?php
	while ( sz_nouveau_nav_items() ) :
		sz_nouveau_nav_item();
	?>

		<li id="<?php sz_nouveau_nav_id(); ?>" class="<?php sz_nouveau_nav_classes(); ?>" <?php sz_nouveau_nav_scope(); ?>>
			<a href="<?php sz_nouveau_nav_link(); ?>" id="<?php sz_nouveau_nav_link_id(); ?>">
				<?php sz_nouveau_nav_link_text(); ?>

				<?php if ( sz_nouveau_nav_has_count() ) : ?>
					<span class="count"><?php sz_nouveau_nav_count(); ?></span>
				<?php endif; ?>
			</a>
		</li>

	<?php endwhile; ?>

<?php endif; ?>
