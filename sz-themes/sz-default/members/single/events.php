<?php

/**
 * SportsZone - Users Events
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php if ( sz_is_my_profile() ) sz_get_options_nav(); ?>

		<?php if ( !sz_is_current_action( 'invites' ) ) : ?>

			<li id="events-order-select" class="last filter">

				<label for="events-sort-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
				<select id="events-sort-by">
					<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
					<option value="popular"><?php _e( 'Most Members', 'sportszone' ); ?></option>
					<option value="newest"><?php _e( 'Newly Created', 'sportszone' ); ?></option>
					<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

					<?php do_action( 'sz_member_event_order_options' ); ?>

				</select>
			</li>

		<?php endif; ?>

	</ul>
</div><!-- .item-list-tabs -->

<?php

if ( sz_is_current_action( 'invites' ) ) :
	locate_template( array( 'members/single/events/invites.php' ), true );

else :
	do_action( 'sz_before_member_events_content' ); ?>

	<div class="events myevents">

		<?php locate_template( array( 'events/events-loop.php' ), true ); ?>

	</div>

	<?php do_action( 'sz_after_member_events_content' ); ?>

<?php endif; ?>
