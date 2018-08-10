<?php
/**
 * SportsZone - Members Single Group Invites
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the display of member group invites content.
 *
 * @since 1.1.0
 */
do_action( 'sz_before_group_invites_content' ); ?>

<?php if ( sz_has_groups( 'type=invites&user_id=' . sz_loggedin_user_id() ) ) : ?>

	<h2 class="sz-screen-reader-text"><?php
		/* translators: accessibility text */
		_e( 'Group invitations', 'sportszone' );
	?></h2>

	<ul id="group-list" class="invites item-list">

		<?php while ( sz_groups() ) : sz_the_group(); ?>

			<li>
				<?php if ( ! sz_disable_group_avatar_uploads() ) : ?>
					<div class="item-avatar">
						<a href="<?php sz_group_permalink(); ?>"><?php sz_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
					</div>
				<?php endif; ?>

				<h4><?php sz_group_link(); ?><span class="small"> - <?php printf( _nx( '%d member', '%d members', sz_get_group_total_members( false ),'Group member count', 'sportszone' ), sz_get_group_total_members( false )  ); ?></span></h4>

				<p class="desc">
					<?php sz_group_description_excerpt(); ?>
				</p>

				<?php

				/**
				 * Fires inside the display of a member group invite item.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_group_invites_item' ); ?>

				<div class="action">
					<a class="button accept" href="<?php sz_group_accept_invite_link(); ?>"><?php _e( 'Accept', 'sportszone' ); ?></a> &nbsp;
					<a class="button reject confirm" href="<?php sz_group_reject_invite_link(); ?>"><?php _e( 'Reject', 'sportszone' ); ?></a>

					<?php

					/**
					 * Fires inside the member group item action markup.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_group_invites_item_action' ); ?>

				</div>
			</li>

		<?php endwhile; ?>
	</ul>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'You have no outstanding group invites.', 'sportszone' ); ?></p>
	</div>

<?php endif;?>

<?php

/**
 * Fires after the display of member group invites content.
 *
 * @since 1.1.0
 */
do_action( 'sz_after_group_invites_content' );
