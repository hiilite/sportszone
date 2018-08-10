<?php
/**
 * SportsZone - Users Notifications
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Notifications menu', 'sportszone' ); ?>">
	<ul class="subnav">

		<?php sz_get_template_part( 'members/single/parts/item-subnav' ); ?>

	</ul>
</nav>

<?php
switch ( sz_current_action() ) :

	case 'unread':
	case 'read':
	?>


	<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>


		<div id="notifications-user-list" class="notifications dir-list" data-sz-list="notifications">
			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'member-notifications-loading' ); ?></div>
		</div><!-- #groups-dir-list -->

		<?php
		break;

	// Any other actions.
	default:
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
