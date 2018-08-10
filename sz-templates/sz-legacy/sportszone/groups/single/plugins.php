<?php
/**
 * SportsZone - Groups plugins
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the display of content for plugins using the SZ_Group_Extension.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_group_plugin_template' ); ?>

<?php

/**
 * Fires and displays content for plugins using the SZ_Group_Extension.
 *
 * @since 1.0.0
 */
do_action( 'sz_template_content' ); ?>

<?php

/**
 * Fires after the display of content for plugins using the SZ_Group_Extension.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_group_plugin_template' );
