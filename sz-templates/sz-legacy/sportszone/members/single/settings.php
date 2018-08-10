<?php
/**
 * SportsZone - Users Settings
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'sportszone' ); ?>" role="navigation">
	<ul>
		<?php if ( sz_core_can_edit_settings() ) : ?>

			<?php sz_get_options_nav(); ?>

		<?php endif; ?>
	</ul>
</div>

<?php

switch ( sz_current_action() ) :
	case 'notifications'  :
		sz_get_template_part( 'members/single/settings/notifications'  );
		break;
	case 'capabilities'   :
		sz_get_template_part( 'members/single/settings/capabilities'   );
		break;
	case 'delete-account' :
		sz_get_template_part( 'members/single/settings/delete-account' );
		break;
	case 'general'        :
		sz_get_template_part( 'members/single/settings/general'        );
		break;
	case 'profile'        :
		sz_get_template_part( 'members/single/settings/profile'        );
		break;
	default:
		sz_get_template_part( 'members/single/plugins'                 );
		break;
endswitch;
