<?php
/**
 * SportsZone - Members Home
 *
 * @since   1.0.0
 * @version 3.0.0
 */
?>

	<?php sz_nouveau_member_hook( 'before', 'home_content' ); ?>

	<div id="item-header" role="complementary" data-sz-item-id="<?php echo esc_attr( sz_displayed_user_id() ); ?>" data-sz-item-component="members" class="users-header single-headers">

		<?php sz_nouveau_member_header_template_part(); ?>

	</div><!-- #item-header -->

	<div class="sz-wrap">
		
			<?php sz_get_template_part( 'members/single/parts/item-nav' ); ?>

		<div id="item-body" class="item-body">

			<?php sz_nouveau_member_template_part(); ?>

		</div><!-- #item-body -->
	</div><!-- // .sz-wrap -->

	<?php sz_nouveau_member_hook( 'after', 'home_content' ); ?>
