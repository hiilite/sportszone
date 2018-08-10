<?php

/**
 * SportsZone - Users Friends
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php if ( sz_is_my_profile() ) sz_get_options_nav(); ?>

		<?php if ( !sz_is_current_action( 'requests' ) ) : ?>

			<li id="members-order-select" class="last filter">

				<label for="members-friends"><?php _e( 'Order By:', 'sportszone' ); ?></label>
				<select id="members-friends">
					<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
					<option value="newest"><?php _e( 'Newest Registered', 'sportszone' ); ?></option>
					<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

					<?php do_action( 'sz_member_friends_order_options' ); ?>

				</select>
			</li>

		<?php endif; ?>

	</ul>
</div>

<?php

if ( sz_is_current_action( 'requests' ) ) :
	 locate_template( array( 'members/single/friends/requests.php' ), true );

else :
	do_action( 'sz_before_member_friends_content' ); ?>

	<div class="members friends">

		<?php locate_template( array( 'members/members-loop.php' ), true ); ?>

	</div><!-- .members.friends -->

	<?php do_action( 'sz_after_member_friends_content' ); ?>

<?php endif; ?>
