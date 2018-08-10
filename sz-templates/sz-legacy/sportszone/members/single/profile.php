<?php
/**
 * SportsZone - Users Profile
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'sportszone' ); ?>" role="navigation">
	<ul>
		<?php sz_get_options_nav(); ?>
	</ul>
</div><!-- .item-list-tabs -->

<?php

/**
 * Fires before the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'sz_before_profile_content' ); ?>

<div class="profile">

<?php switch ( sz_current_action() ) :

	// Edit
	case 'edit'   :
		sz_get_template_part( 'members/single/profile/edit' );
		break;

	// Change Avatar
	case 'change-avatar' :
		sz_get_template_part( 'members/single/profile/change-avatar' );
		break;

	// Change Cover Image
	case 'change-cover-image' :
		sz_get_template_part( 'members/single/profile/change-cover-image' );
		break;

	// Compose
	case 'public' :

		// Display XProfile
		if ( sz_is_active( 'xprofile' ) )
			sz_get_template_part( 'members/single/profile/profile-loop' );

		// Display WordPress profile (fallback)
		else
			sz_get_template_part( 'members/single/profile/profile-wp' );

		break;

	// Any other
	default :
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch; ?>
</div><!-- .profile -->

<?php

/**
 * Fires after the display of member profile content.
 *
 * @since 1.1.0
 */
do_action( 'sz_after_profile_content' );
