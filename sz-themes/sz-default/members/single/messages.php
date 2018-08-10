<?php

/**
 * SportsZone - Users Messages
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>

		<?php sz_get_options_nav(); ?>

	</ul>
	
	<?php if ( sz_is_messages_inbox() || sz_is_messages_sentbox() ) : ?>

		<div class="message-search"><?php sz_message_search_form(); ?></div>

	<?php endif; ?>

</div><!-- .item-list-tabs -->

<?php

	if ( sz_is_current_action( 'compose' ) ) :
		locate_template( array( 'members/single/messages/compose.php' ), true );

	elseif ( sz_is_current_action( 'view' ) ) :
		locate_template( array( 'members/single/messages/single.php' ), true );

	else :
		do_action( 'sz_before_member_messages_content' ); ?>

	<div class="messages" role="main">

		<?php
			if ( sz_is_current_action( 'notices' ) )
				locate_template( array( 'members/single/messages/notices-loop.php' ), true );
			else
				locate_template( array( 'members/single/messages/messages-loop.php' ), true );
		?>

	</div><!-- .messages -->

	<?php do_action( 'sz_after_member_messages_content' ); ?>

<?php endif; ?>
