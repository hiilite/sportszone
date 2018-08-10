<?php
/**
 * SportsZone - Groups Home
 *
 * @since 3.0.0
 * @version 3.0.0
 */

if ( sz_has_groups() ) :
	while ( sz_groups() ) :
		sz_the_group();
	?>

		<?php sz_nouveau_group_hook( 'before', 'home_content' ); ?>

		<div id="item-header" role="complementary" data-sz-item-id="<?php sz_group_id(); ?>" data-sz-item-component="groups" class="groups-header single-headers">

			<?php sz_nouveau_group_header_template_part(); ?>

		</div><!-- #item-header -->
 
		<div class="sz-wrap">

				<?php sz_get_template_part( 'groups/single/parts/item-nav' ); ?>

			<div id="item-body" class="item-body">

				<?php sz_nouveau_group_template_part(); ?>

			</div><!-- #item-body -->

		</div><!-- // .sz-wrap -->

		<?php sz_nouveau_group_hook( 'after', 'home_content' ); ?>

	<?php endwhile; ?>

<?php
endif;
