<?php
/**
 * SportsZone - Groups plugins
 *
 * @since 3.0.0
 * @version 3.0.0
 */

sz_nouveau_group_hook( 'before', 'plugin_template' );

sz_nouveau_plugin_hook( 'content' );

sz_nouveau_group_hook( 'after', 'plugin_template' );
