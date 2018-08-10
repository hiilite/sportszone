<?php

/**
 * SportsZone - Users Settings
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php if ( sz_core_can_edit_settings() ) : ?>
		
			<?php sz_get_options_nav(); ?>
		
		<?php endif; ?>
	</ul>
</div>

<?php

if ( sz_is_current_action( 'notifications' ) ) :
	 locate_template( array( 'members/single/settings/notifications.php' ), true );

elseif ( sz_is_current_action( 'delete-account' ) ) :
	 locate_template( array( 'members/single/settings/delete-account.php' ), true );

elseif ( sz_is_current_action( 'general' ) ) :
	locate_template( array( 'members/single/settings/general.php' ), true );

elseif ( sz_is_current_action( 'profile' ) ) :
	locate_template( array( 'members/single/settings/profile.php' ), true );

else :
	locate_template( array( 'members/single/plugins.php' ), true );

endif;
