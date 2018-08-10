<?php
/**
 * SportsZone - Users Profile
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Profile menu', 'sportszone' ); ?>">
	<ul class="subnav">

		<?php sz_get_template_part( 'members/single/parts/item-subnav' ); ?>

	</ul>
</nav><!-- .item-list-tabs -->

<?php sz_nouveau_member_hook( 'before', 'profile_content' ); ?>

<div class="profile <?php echo sz_current_action(); ?>">

<?php
switch ( sz_current_action() ) :

	// Edit
	case 'edit':
		sz_get_template_part( 'members/single/profile/edit' );
		break;

	// Change Avatar
	case 'change-avatar':
		sz_get_template_part( 'members/single/profile/change-avatar' );
		break;

	// Change Cover Image
	case 'change-cover-image':
		sz_get_template_part( 'members/single/profile/change-cover-image' );
		break;

	// Compose
	case 'public':
		// Display XProfile
		if ( sz_is_active( 'xprofile' ) ) {
			sz_get_template_part( 'members/single/profile/profile-loop' );

		// Display WordPress profile (fallback)
		} else {
			sz_get_template_part( 'members/single/profile/profile-wp' );
		}

		break;

	// Any other
	default:
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
?>
</div><!-- .profile -->

<?php
sz_nouveau_member_hook( 'after', 'profile_content' );
