<?php
/**
 * SportsZone - Groups Admin - Group Cover Image Settings
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<h2><?php _e( 'Cover Image', 'sportszone' ); ?></h2>

<?php

/**
 * Fires before the display of profile cover image upload content.
 *
 * @since 2.4.0
 */
do_action( 'sz_before_group_settings_cover_image' ); ?>

<p><?php _e( 'The Cover Image will be used to customize the header of your group.', 'sportszone' ); ?></p>

<?php sz_attachments_get_template_part( 'cover-images/index' ); ?>

<?php

/**
 * Fires after the display of group cover image upload content.
 *
 * @since 2.4.0
 */
do_action( 'sz_after_group_settings_cover_image' );
