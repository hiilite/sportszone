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

<?php endif; 
$group_id 	= sz_get_current_group_id();
if ( isset($group_id) ) :	
	$email 		= groups_get_groupmeta( $group_id, 'group_email' );
	$phone 		= groups_get_groupmeta( $group_id, 'group_phone' );
	$facebook	= groups_get_groupmeta( $group_id, 'group_facebook' );
	$twitter 	= groups_get_groupmeta( $group_id, 'group_twitter' );
	$website 	= groups_get_groupmeta( $group_id, 'group_website' );
	$colors 	= groups_get_groupmeta( $group_id, 'group_colors' );
	$group_country 	= groups_get_groupmeta( $group_id, 'group_country' );
	$group_province 	= groups_get_groupmeta( $group_id, 'group_province' );	
else:
	$email = $phone = $group_country = $facebook = $twitter = $website = $colors = $group_province = $group_country = '';
endif;
?>
<label for="group-name"><?php esc_html_e( 'Group Name (required)', 'sportszone' ); ?></label>
<input type="text" name="group-name" id="group-name" value="<?php sz_is_group_create() ? sz_new_group_name() : sz_group_name(); ?>" aria-required="true" />

<label for="group-desc"><?php esc_html_e( 'Group Description (required)', 'sportszone' ); ?></label>
<textarea name="group-desc" id="group-desc" aria-required="true"><?php sz_is_group_create() ? sz_new_group_description() : sz_group_description_editable(); ?></textarea>

<label for="group-email"><?php esc_html_e( 'Email Address', 'sportszone' ); ?></label>
<input type="email" name="group-email" id="group-email" value="<?php echo $email; ?>" aria-required="true" />

<label for="group-website"><?php esc_html_e( 'Website', 'sportszone' ); ?></label>
<input type="url" name="group-website" id="group-website" value="<?php echo $website; ?>" aria-required="true" />

<label for="group-facebook"><?php esc_html_e( 'Facebook Page (optional)', 'sportszone' ); ?></label>
<input type="url" name="group-facebook" id="group-facebook" value="<?php echo $facebook; ?>" aria-required="true" />

<label for="group-twitter"><?php esc_html_e( 'Twitter Page (optional)', 'sportszone' ); ?></label>
<input type="url" name="group-twitter" id="group-twitter" value="<?php echo $twitter; ?>" aria-required="true" />

<label for="group-country"><?php esc_html_e( 'Country', 'sportszone' ); ?></label>
<select name="group-country" id="group-country" class="crs-country" data-region-id="group-province" data-default-value="<?php echo $group_country; ?>" aria-required="true" /></select>

<label for="group-province"><?php esc_html_e( 'Province', 'sportszone' ); ?></label>
<select name="group-province" id="group-province" data-default-value="<?php echo $group_province; ?>" aria-required="true" /></select>

<?php if ( ! sz_is_group_create() ) : ?>
	<p class="sz-controls-wrap">
		<label for="group-notify-members" class="sz-label-text">
			<input type="checkbox" name="group-notify-members" id="group-notify-members" value="1" /> <?php esc_html_e( 'Notify group members of these changes via email', 'sportszone' ); ?>
		</label>
	</p>
<?php endif; ?>
