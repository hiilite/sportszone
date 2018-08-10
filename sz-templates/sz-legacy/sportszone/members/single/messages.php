<?php
/**
 * SportsZone - Users Messages
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'sportszone' ); ?>" role="navigation">
	<ul>

		<?php sz_get_options_nav(); ?>

	</ul>

	<?php if ( sz_is_messages_inbox() || sz_is_messages_sentbox() ) : ?>

		<div class="message-search"><?php sz_message_search_form(); ?></div>

	<?php endif; ?>

</div><!-- .item-list-tabs -->

<?php
switch ( sz_current_action() ) :

	// Inbox/Sentbox
	case 'inbox'   :
	case 'sentbox' :

		/**
		 * Fires before the member messages content for inbox and sentbox.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_before_member_messages_content' ); ?>

		<?php if ( sz_is_messages_inbox() ) : ?>
			<h2 class="sz-screen-reader-text"><?php
				/* translators: accessibility text */
				_e( 'Messages inbox', 'sportszone' );
			?></h2>
		<?php elseif ( sz_is_messages_sentbox() ) : ?>
			<h2 class="sz-screen-reader-text"><?php
				/* translators: accessibility text */
				_e( 'Sent Messages', 'sportszone' );
			?></h2>
		<?php endif; ?>

		<div class="messages">
			<?php sz_get_template_part( 'members/single/messages/messages-loop' ); ?>
		</div><!-- .messages -->

		<?php

		/**
		 * Fires after the member messages content for inbox and sentbox.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_after_member_messages_content' );
		break;

	// Single Message View
	case 'view' :
		sz_get_template_part( 'members/single/messages/single' );
		break;

	// Compose
	case 'compose' :
		sz_get_template_part( 'members/single/messages/compose' );
		break;

	// Sitewide Notices
	case 'notices' :

		/**
		 * Fires before the member messages content for notices.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_before_member_messages_content' ); ?>

		<h2 class="sz-screen-reader-text"><?php
			/* translators: accessibility text */
			_e( 'Sitewide Notices', 'sportszone' );
		?></h2>

		<div class="messages">
			<?php sz_get_template_part( 'members/single/messages/notices-loop' ); ?>
		</div><!-- .messages -->

		<?php

		/**
		 * Fires after the member messages content for inbox and sentbox.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_after_member_messages_content' );
		break;

	// Any other
	default :
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
