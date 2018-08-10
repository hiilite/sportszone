<?php
/**
 * BP Nouveau Groups screens filters
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>
<div id="comp-filters" class="component-filters clearfix">
		<div id="<?php sz_nouveau_filter_container_id(); ?>" class="last filter">
			<label for="<?php sz_nouveau_filter_id(); ?>" class="sz-screen-reader-text">
				<span ><?php sz_nouveau_filter_label(); ?></span>
			</label>
			<div class="select-wrap">
				<select id="<?php sz_nouveau_filter_id(); ?>" data-sz-filter="<?php sz_nouveau_filter_component(); ?>">

					<?php sz_nouveau_filter_options(); ?>

				</select>
				<span class="select-arrow" aria-hidden="true"></span>
			</div>
		</div>
</div>
