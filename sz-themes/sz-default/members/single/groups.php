<?php

/**
 * SportsZone - Users Groups
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php if ( sz_is_my_profile() ) sz_get_options_nav(); ?>

		<?php if ( !sz_is_current_action( 'invites' ) ) : ?>

			<li id="groups-order-select" class="last filter">

				<label for="groups-sort-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
				<select id="groups-sort-by">
					<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
					<option value="popular"><?php _e( 'Most Members', 'sportszone' ); ?></option>
					<option value="newest"><?php _e( 'Newly Created', 'sportszone' ); ?></option>
					<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

					<?php do_action( 'sz_member_group_order_options' ); ?>

				</select>
			</li>

		<?php endif; ?>

	</ul>
</div><!-- .item-list-tabs -->

<?php

if ( sz_is_current_action( 'invites' ) ) :
	locate_template( array( 'members/single/groups/invites.php' ), true );

else :
	do_action( 'sz_before_member_groups_content' ); ?>

	<div class="groups mygroups">

		<?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>

	</div>

	<?php do_action( 'sz_after_member_groups_content' ); ?>

<?php endif; ?>
