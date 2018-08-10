<?php
/**
 * SportsZone - Users Groups
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Groups menu', 'sportszone' ); ?>">
	<ul class="subnav">

		<?php if ( sz_is_my_profile() ) : ?>

			<?php sz_get_template_part( 'members/single/parts/item-subnav' ); ?>

		<?php endif; ?>

	</ul>
</nav><!-- .sz-navs -->

<?php if ( ! sz_is_current_action( 'invites' ) ) : ?>


	<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>

<?php endif; ?>

<?php

switch ( sz_current_action() ) :

	// Home/My Groups
	case 'my-groups':
		sz_nouveau_member_hook( 'before', 'groups_content' );
		?>

		<div class="groups mygroups" data-sz-list="groups">

			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'member-groups-loading' ); ?></div>

		</div>

		<?php
		sz_nouveau_member_hook( 'after', 'groups_content' );
		break;

	// Group Invitations
	case 'invites':
		sz_get_template_part( 'members/single/groups/invites' );
		break;

	// Any other
	default:
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
