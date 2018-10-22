<?php
/**
 * SportsZone - Events Create
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_events_create_hook( 'before', 'page' ); ?>

	<h2 class="sz-subhead"><?php esc_html_e( 'Create A New Event', 'sportszone' ); ?></h2>

	<?php sz_nouveau_events_create_hook( 'before', 'content_template' ); ?>

	<?php if ( 'event-invites' !== sz_get_events_current_create_step() ) : ?>
		<form action="<?php sz_event_creation_form_action(); ?>" method="post" id="create-event-form" class="standard-form" enctype="multipart/form-data">
	<?php else : ?>
		<div id="create-event-form" class="standard-form">
	<?php endif; ?>

		<?php sz_nouveau_events_create_hook( 'before' ); ?>

		<?php sz_nouveau_template_notices(); ?>

		<div class="item-body" id="event-create-body">

			<nav class="<?php sz_nouveau_events_create_steps_classes(); ?>" id="event-create-tabs" role="navigation" aria-label="<?php esc_attr_e( 'Event creation menu', 'sportszone' ); ?>">
				<ol class="event-create-buttons button-tabs">

					<?php sz_event_creation_tabs(); ?>

				</ol>
			</nav>

			<?php sz_nouveau_event_creation_screen(); ?>

		</div><!-- .item-body -->

		<?php sz_nouveau_events_create_hook( 'after' ); ?>

	<?php if ( 'event-invites' !== sz_get_events_current_create_step() ) : ?>
		</form><!-- #create-event-form -->
	<?php else : ?>
		</div><!-- #create-event-form -->
	<?php endif; ?>

	<?php sz_nouveau_events_create_hook( 'after', 'content_template' ); ?>

<?php
sz_nouveau_events_create_hook( 'after', 'page' );
