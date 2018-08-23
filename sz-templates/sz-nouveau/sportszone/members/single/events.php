<?php
/**
 * SportsZone - Users Events
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Events menu', 'sportszone' ); ?>">
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

	// Home/My Events
	case 'my-events':
		sz_nouveau_member_hook( 'before', 'events_content' );
		?>

		<div class="events myevents" data-sz-list="events">

			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'member-events-loading' ); ?></div>

		</div>

		<?php
		sz_nouveau_member_hook( 'after', 'events_content' );
		break;

	// Event Invitations
	case 'invites':
		sz_get_template_part( 'members/single/events/invites' );
		break;

	// Any other
	default:
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
