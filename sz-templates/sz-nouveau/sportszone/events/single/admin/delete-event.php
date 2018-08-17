<?php
/**
 * BP Nouveau Event's delete event template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<h2 class="sz-screen-title warn">
	<?php esc_html_e( 'Delete this event', 'sportszone' ); ?>
</h2>

<?php sz_nouveau_user_feedback( 'event-delete-warning' ); ?>

<label for="delete-event-understand" class="sz-label-text warn">
	<input type="checkbox" name="delete-event-understand" id="delete-event-understand" value="1" onclick="if(this.checked) { document.getElementById( 'delete-event-button' ).disabled = ''; } else { document.getElementById( 'delete-event-button' ).disabled = 'disabled'; }" />
	<?php esc_html_e( 'I understand the consequences of deleting this event.', 'sportszone' ); ?>
</label>
