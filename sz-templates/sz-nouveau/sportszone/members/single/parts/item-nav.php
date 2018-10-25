<?php
/**
 * SportsZone Single Members item Navigation
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>
<nav class="<?php sz_nouveau_single_item_nav_classes(); ?>" id="object-nav" role="navigation" aria-label="<?php esc_attr_e( 'Member menu', 'sportszone' ); ?>">
	
	<div class="internal-nav-mobile">
		<i class="fa fa-bars"></i>
	</div>

	<?php if ( sz_nouveau_has_nav( array( 'type' => 'primary' ) ) ) : ?>

		<ul>

			<?php
			while ( sz_nouveau_nav_items() ) :
				sz_nouveau_nav_item();
			?>

				<li id="<?php sz_nouveau_nav_id(); ?>" class="<?php sz_nouveau_nav_classes(); ?>">
					<a href="<?php sz_nouveau_nav_link(); ?>" id="<?php sz_nouveau_nav_link_id(); ?>">
						<?php sz_nouveau_nav_link_text(); ?>

						<?php if ( sz_nouveau_nav_has_count() ) : ?>
							<span class="count badge badge-primary badge-pill"><?php sz_nouveau_nav_count(); ?></span>
					<?php endif; ?>
					</a>
				</li>

			<?php endwhile; ?>

			<?php sz_nouveau_member_hook( '', 'options_nav' ); ?>

		</ul>

	<?php endif; ?>

</nav>
