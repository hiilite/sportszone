<?php
/**
 * SportsZone Single Events item Navigation
 *
 * @since 3.0.0
 * @version 3.0.0
 */
global $post;
if(get_post_type( $post ) == 'sz_match'){
	$match_event = get_post_meta( get_the_id(  ), 'sz_event', true );
}

?>

<nav class="<?php sz_nouveau_single_item_nav_classes(); ?>" id="object-nav" role="navigation" aria-label="<?php esc_attr_e( 'Event menu', 'sportszone' ); ?>">
	<?php if ( sz_nouveau_has_nav( array( 'object' => 'events' ) ) ) : ?>
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

			<?php sz_nouveau_event_hook( '', 'options_nav' ); ?>

		</ul>

	<?php endif; ?>

</nav>
