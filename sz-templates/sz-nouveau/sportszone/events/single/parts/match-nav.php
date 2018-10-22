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
	<?php if ( isset($match_event) ) : ?>
		<ul>



		</ul>

	<?php endif; ?>

</nav>
