<?php
/**
 * BP Object search form
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>
<!--sportszone > sz-templates > sz-nouveau > sportszone > common > search > search-form-->
<div class="<?php sz_nouveau_search_container_class(); ?> sz-search" data-sz-search="<?php sz_nouveau_search_object_data_attr() ;?>">
	<form action="" method="get" class="sz-dir-search-form input-group" id="<?php sz_nouveau_search_selector_id( 'search-form' ); ?>" role="search">

		<label for="<?php sz_nouveau_search_selector_id( 'search' ); ?>" class="sz-screen-reader-text"><?php sz_nouveau_search_default_text( '', false ); ?></label>

		<input class="form-control" id="<?php sz_nouveau_search_selector_id( 'search' ); ?>" name="<?php sz_nouveau_search_selector_name(); ?>" type="search"  placeholder="<?php sz_nouveau_search_default_text(); ?>" />

		<button type="submit" id="<?php sz_nouveau_search_selector_id( 'search-submit' ); ?>" class="nouveau-search-submit" name="<?php sz_nouveau_search_selector_name( 'search_submit' ); ?>">
			<i class="fa fa-search"></i>
			<span id="button-text" class="sz-screen-reader-text"><?php echo esc_html_x( 'Search', 'button', 'sportszone' ); ?></span>
		</button>

	</form>
</div>
