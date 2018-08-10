<?php
/**
 * SportsZone - Users Settings
 *
 * @version 3.0.0
 */

?>

<?php if ( sz_core_can_edit_settings() ) : ?>

	<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Settings menu', 'sportszone' ); ?>">
		<ul class="subnav">

			<?php sz_get_template_part( 'members/single/parts/item-subnav' ); ?>

		</ul>
	</nav>

<?php
endif;

switch ( sz_current_action() ) :
	case 'notifications':
		sz_get_template_part( 'members/single/settings/notifications' );
		break;
	case 'capabilities':
		sz_get_template_part( 'members/single/settings/capabilities' );
		break;
	case 'delete-account':
		sz_get_template_part( 'members/single/settings/delete-account' );
		break;
	case 'general':
		sz_get_template_part( 'members/single/settings/general' );
		break;
	case 'profile':
		sz_get_template_part( 'members/single/settings/profile' );
		break;
	case 'invites':
		sz_get_template_part( 'members/single/settings/group-invites' );
		break;
	default:
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
