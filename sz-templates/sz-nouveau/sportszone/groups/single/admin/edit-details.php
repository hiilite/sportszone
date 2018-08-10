<?php
/**
 * BP Nouveau Group's edit details template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<?php if ( sz_is_group_create() ) : ?>

	<h3 class="sz-screen-title creation-step-name">
		<?php esc_html_e( 'Enter Group Name &amp; Description', 'sportszone' ); ?>
	</h3>

<?php else : ?>

	<h2 class="sz-screen-title">
		<?php esc_html_e( 'Edit Group Name &amp; Description', 'sportszone' ); ?>
	</h2>

<?php endif; ?>
<?php
// Group type selection
$group_types = sz_groups_get_group_types( array( 'show_in_create_screen' => true ), 'objects' );
if ( $group_types ) : ?>

	<fieldset class="group-create-types">
		<legend><?php esc_html_e( 'Group Type', 'sportszone' ); ?></legend>

		<p tabindex="0"><?php esc_html_e( 'Select the type of Group you are creating.', 'sportszone' ); ?></p>

		<?php foreach ( $group_types as $type ) : ?>
			<div class="radiogroup">
				<label for="<?php printf( 'group-type-%s', $type->name ); ?>">
					<input type="radio" name="group-types[]" id="<?php printf( 'group-type-%s', $type->name ); ?>" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( sz_groups_has_group_type( sz_get_current_group_id(), $type->name ) ); ?>/> <?php echo esc_html( $type->labels['name'] ); ?>
					<?php
					if ( ! empty( $type->description ) ) {
						printf( '&ndash; %s', '<span class="sz-group-type-desc">' . esc_html( $type->description ) . '</span>' );
					}
					?>
				</label>
			</div>

		<?php endforeach; ?>

	</fieldset>

<?php endif; ?>


<label for="group-name"><?php esc_html_e( 'Group Name (required)', 'sportszone' ); ?></label>
<input type="text" name="group-name" id="group-name" value="<?php sz_is_group_create() ? sz_new_group_name() : sz_group_name(); ?>" aria-required="true" />

<label for="group-desc"><?php esc_html_e( 'Group Description (required)', 'sportszone' ); ?></label>
<textarea name="group-desc" id="group-desc" aria-required="true"><?php sz_is_group_create() ? sz_new_group_description() : sz_group_description_editable(); ?></textarea>

<?php if ( ! sz_is_group_create() ) : ?>
	<p class="sz-controls-wrap">
		<label for="group-notify-members" class="sz-label-text">
			<input type="checkbox" name="group-notify-members" id="group-notify-members" value="1" /> <?php esc_html_e( 'Notify group members of these changes via email', 'sportszone' ); ?>
		</label>
	</p>
<?php endif; ?>
