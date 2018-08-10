<?php

/**
 * SportsZone - Users Notifications
 *
 * @package SportsZone
 * @subpackage sz-legacy
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php sz_get_options_nav(); ?>

		<li id="members-order-select" class="last filter">
			<?php sz_notifications_sort_order_form(); ?>
		</li>
	</ul>
</div>

<?php
switch ( sz_current_action() ) :

	// Unread
	case 'unread' :
		sz_get_template_part( 'members/single/notifications/unread' );
		break;

	// Read
	case 'read' :
		sz_get_template_part( 'members/single/notifications/read' );
		break;

	// Any other
	default :
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
