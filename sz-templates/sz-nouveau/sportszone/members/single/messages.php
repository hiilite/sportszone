<?php
/**
 * SportsZone - Users Messages
 *
 * @version 3.0.0
 */
?>

<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Messages menu', 'sportszone' ); ?>">
	<ul class="subnav">

		<?php sz_get_template_part( 'members/single/parts/item-subnav' ); ?>

	</ul>
</nav><!-- .sz-navs -->

<?php
if ( ! in_array( sz_current_action(), array( 'inbox', 'sentbox', 'starred', 'view', 'compose', 'notices' ), true ) ) :

	sz_get_template_part( 'members/single/plugins' );

else :

	sz_nouveau_messages_member_interface();

endif;
?>
