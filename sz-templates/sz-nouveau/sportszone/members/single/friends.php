<?php
/**
 * SportsZone - Users Friends
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Friends menu', 'sportszone' ); ?>">
	<ul class="subnav">
		<?php if ( sz_is_my_profile() ) : ?>

			<?php sz_get_template_part( 'members/single/parts/item-subnav' ); ?>

		<?php endif; ?>
	</ul>
</nav><!-- .sz-navs -->

<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>

<?php
switch ( sz_current_action() ) :

	// Home/My Friends
	case 'my-friends':
		sz_nouveau_member_hook( 'before', 'friends_content' );
		?>

		<div class="members friends" data-sz-list="members">

			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'member-friends-loading' ); ?></div>

		</div><!-- .members.friends -->

		<?php
		sz_nouveau_member_hook( 'after', 'friends_content' );
		break;

	case 'requests':
		sz_get_template_part( 'members/single/friends/requests' );
		break;

	// Any other
	default:
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
