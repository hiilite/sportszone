<?php
/**
 * SportsZone - Groups Send Invites
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the send invites content.
 *
 * @since 1.1.0
 */
do_action( 'sz_before_group_send_invites_content' ); ?>

<?php
/* Does the user have friends that could be invited to the group? */
if ( sz_get_new_group_invite_friend_list() ) : ?>

	<h2 class="sz-screen-reader-text"><?php _e( 'Send invites', 'sportszone' ); ?></h2>

	<?php /* 'send-invite-form' is important for AJAX support */ ?>
	<form action="<?php sz_group_send_invite_form_action(); ?>" method="post" id="send-invite-form" class="standard-form">

		<div class="invite" aria-live="polite" aria-atomic="false" aria-relevant="all">
			<?php sz_get_template_part( 'groups/single/invites-loop' ); ?>
		</div>

		<div class="submit">
			<input type="submit" name="submit" id="submit" value="<?php esc_attr_e( 'Send Invites', 'sportszone' ); ?>" />
		</div>

		<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ); ?>

		<?php /* This is important, don't forget it */ ?>
		<input type="hidden" name="group_id" id="group_id" value="<?php sz_group_id(); ?>" />

	</form><!-- #send-invite-form -->

<?php
/* No eligible friends? Maybe the user doesn't have any friends yet. */
elseif ( 0 == sz_get_total_friend_count( sz_loggedin_user_id() ) ) : ?>

	<div id="message" class="info">
		<p class="notice"><?php _e( 'Group invitations can only be extended to friends.', 'sportszone' ); ?></p>
		<p class="message-body"><?php _e( "Once you've made some friendships, you'll be able to invite those members to this group.", 'sportszone' ); ?></p>
	</div>

<?php
/* The user does have friends, but none are eligible to be invited to this group. */
else : ?>

	<div id="message" class="info">
		<p class="notice"><?php _e( 'All of your friends already belong to this group.', 'sportszone' ); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the send invites content.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_group_send_invites_content' );
