<?php
/**
 * BP Nouveau template notices template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>
<aside class="<?php sz_nouveau_template_message_classes(); ?>">
	<span class="sz-icon" aria-hidden="true"></span>
	<?php sz_nouveau_template_message(); ?>

	<?php if ( sz_nouveau_has_dismiss_button() ) : ?>

		<button type="button" class="sz-tooltip" data-sz-tooltip="<?php echo esc_attr_x( 'Close', 'button', 'sportszone' ); ?>" aria-label="<?php esc_attr_e( 'Close this notice', 'sportszone' ); ?>" data-sz-close="<?php sz_nouveau_dismiss_button_type(); ?>"><span class="dashicons dashicons-dismiss" aria-hidden="true"></span></button>

	<?php endif; ?>
</aside>
