<?php
/**
 * SportsZone - Users Groups
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'sportszone' ); ?>" role="navigation">
	<ul>
		<?php if ( sz_is_my_profile() ) sz_get_options_nav(); ?>

		<?php if ( !sz_is_current_action( 'invites' ) ) : ?>

			<li id="groups-order-select" class="last filter">

				<label for="groups-order-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
				<select id="groups-order-by">
					<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
					<option value="popular"><?php _e( 'Most Members', 'sportszone' ); ?></option>
					<option value="newest"><?php _e( 'Newly Created', 'sportszone' ); ?></option>
					<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

					<?php

					/**
					 * Fires inside the members group order options select input.
					 *
					 * @since 1.2.0
					 */
					do_action( 'sz_member_group_order_options' ); ?>

				</select>
			</li>

		<?php endif; ?>

	</ul>
</div><!-- .item-list-tabs -->

<?php

switch ( sz_current_action() ) :

	// Home/My Groups
	case 'my-groups' :

		/**
		 * Fires before the display of member groups content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_before_member_groups_content' ); ?>

		<?php if ( is_user_logged_in() ) : ?>
			<h2 class="sz-screen-reader-text"><?php
				/* translators: accessibility text */
				_e( 'My groups', 'sportszone' );
			?></h2>
		<?php else : ?>
			<h2 class="sz-screen-reader-text"><?php
				/* translators: accessibility text */
				_e( 'Member\'s groups', 'sportszone' );
			?></h2>
		<?php endif; ?>

		<div class="groups mygroups">

			<?php sz_get_template_part( 'groups/groups-loop' ); ?>

		</div>

		<?php

		/**
		 * Fires after the display of member groups content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_after_member_groups_content' );
		break;

	// Group Invitations
	case 'invites' :
		sz_get_template_part( 'members/single/groups/invites' );
		break;

	// Any other
	default :
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
