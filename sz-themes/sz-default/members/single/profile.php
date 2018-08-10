<?php

/**
 * SportsZone - Users Profile
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php sz_get_options_nav(); ?>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'sz_before_profile_content' ); ?>

<div class="profile" role="main">

	<?php
		// Profile Edit
		if ( sz_is_current_action( 'edit' ) )
			locate_template( array( 'members/single/profile/edit.php' ), true );

		// Change Avatar
		elseif ( sz_is_current_action( 'change-avatar' ) )
			locate_template( array( 'members/single/profile/change-avatar.php' ), true );

		// Display XProfile
		elseif ( sz_is_active( 'xprofile' ) )
			locate_template( array( 'members/single/profile/profile-loop.php' ), true );

		// Display WordPress profile (fallback)
		else
			locate_template( array( 'members/single/profile/profile-wp.php' ), true );
	?>

</div><!-- .profile -->

<?php do_action( 'sz_after_profile_content' ); ?>