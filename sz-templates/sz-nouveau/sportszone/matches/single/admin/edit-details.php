<?php
/**
 * BP Nouveau Event's edit details template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<?php if ( sz_is_event_create() ) : ?>

	<h3 class="sz-screen-title creation-step-name">
		<?php esc_html_e( 'Enter Event Name &amp; Description', 'sportszone' ); ?>
	</h3>

<?php else : ?>

	<h2 class="sz-screen-title">
		<?php esc_html_e( 'Edit Event Name &amp; Description', 'sportszone' ); ?>
	</h2>

<?php endif; 
$event_id 	= sz_get_current_event_id();


// Event type selection
$event_types = sz_events_get_event_types( array( 'show_in_create_screen' => true ), 'objects' );
if ( $event_types ) : ?>

	<fieldset class="event-create-types">
		<legend><?php esc_html_e( 'Event Type', 'sportszone' ); ?></legend>

		<p tabindex="0"><?php esc_html_e( 'Select the type of Event you are creating.', 'sportszone' ); ?></p>

		<?php foreach ( $event_types as $type ) : ?>
			<div class="radioevent">
				<label for="<?php printf( 'event-type-%s', $type->name ); ?>">
					<input type="radio" name="event-types[]" id="<?php printf( 'event-type-%s', $type->name ); ?>" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( sz_events_has_event_type( sz_get_current_event_id(), $type->name ) ); ?>/> <?php echo esc_html( $type->labels['name'] ); ?>
					<?php
					if ( ! empty( $type->description ) ) {
						printf( '&ndash; %s', '<span class="sz-event-type-desc">' . esc_html( $type->description ) . '</span>' );
					}
					?>
				</label>
			</div>

		<?php endforeach; ?>

	</fieldset>

<?php endif; 


if ( isset($event_id) ) :	
	$event_club = events_get_eventmeta( $event_id, 'event_club' );
else:
	$event_club = '';
endif;
?>
<label for="event-name"><?php esc_html_e( 'Event Name (required)', 'sportszone' ); ?></label>
<input type="text" name="event-name" id="event-name" value="<?php sz_is_event_create() ? sz_new_event_name() : sz_event_name(); ?>" aria-required="true" />

<label for="event-desc"><?php esc_html_e( 'Event Description (required)', 'sportszone' ); ?></label>
<textarea name="event-desc" id="event-desc" aria-required="true"><?php sz_is_event_create() ? sz_new_event_description() : sz_event_description_editable(); ?></textarea>

<?php

if ( ! sz_is_event_create() ) : ?>
	<p class="sz-controls-wrap">
		<label for="event-notify-members" class="sz-label-text">
			<input type="checkbox" name="event-notify-members" id="event-notify-members" value="1" /> <?php esc_html_e( 'Notify event members of these changes via email', 'sportszone' ); ?>
		</label>
	</p>
<?php endif; ?>
