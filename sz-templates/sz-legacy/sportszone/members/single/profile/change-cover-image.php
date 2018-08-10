<?php
/**
 * SportsZone - Members Profile Change Cover Image
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<h2><?php _e( 'Change Cover Image', 'sportszone' ); ?></h2>

<?php

/**
 * Fires before the display of profile cover image upload content.
 *
 * @since 2.4.0
 */
do_action( 'sz_before_profile_edit_cover_image' ); ?>

<p><?php _e( 'Your Cover Image will be used to customize the header of your profile.', 'sportszone' ); ?></p>

<?php sz_attachments_get_template_part( 'cover-images/index' ); ?>

<?php

/**
 * Fires after the display of profile cover image upload content.
 *
 * @since 2.4.0
 */
do_action( 'sz_after_profile_edit_cover_image' );
