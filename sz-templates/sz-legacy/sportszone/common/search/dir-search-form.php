<?php
/**
 * Output the search form markup.
 *
 * @since 2.7.0
 * @version 3.0.0
 */
?>

<div id="<?php echo esc_attr( sz_current_component() ); ?>-dir-search" class="dir-search" role="search">
	<form action="" method="get" id="search-<?php echo esc_attr( sz_current_component() ); ?>-form">
		<label for="<?php sz_search_input_name(); ?>" class="sz-screen-reader-text"><?php sz_search_placeholder(); ?></label>
		<input type="text" name="<?php echo esc_attr( sz_core_get_component_search_query_arg() ); ?>" id="<?php sz_search_input_name(); ?>" placeholder="<?php sz_search_placeholder(); ?>" />

		<input type="submit" id="<?php echo esc_attr( sz_get_search_input_name() ); ?>_submit" name="<?php sz_search_input_name(); ?>_submit" value="<?php esc_attr_e( 'Search', 'sportszone' ); ?>" />
	</form>
</div><!-- #<?php echo esc_attr( sz_current_component() ); ?>-dir-search -->
