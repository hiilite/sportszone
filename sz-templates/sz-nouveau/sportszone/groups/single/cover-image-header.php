<?php
/**
 * SportsZone - Groups Cover Image Header.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<div id="cover-image-container">
	<div id="header-cover-image">
		<?php sz_group_cover_image(); ?>
	</div>

	<div id="item-header-cover-image">
		<?php if ( ! sz_disable_group_avatar_uploads() ) : ?>
			<div id="item-header-avatar">
				<a href="<?php echo esc_url( sz_get_group_permalink() ); ?>" title="<?php echo esc_attr( sz_get_group_name() ); ?>">

					<?php sz_group_avatar(); ?>

				</a>
			</div><!-- #item-header-avatar -->
		<?php endif; ?>

		<div id="item-header-content">

			<h2 class="highlight"><?php echo esc_attr( sz_get_group_name() ); ?></h2>
			
			<div class="item-meta">
		        <span lass="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>">
				<?php printf( __( 'active %s', 'sportszone' ), sz_get_group_last_active() );?></span>		 
			</div>
			
			<div id="item-header-type"><?php echo sz_groups_get_group_type(sz_get_group_id(),true); ?></div>

			
			<?php //sz_nouveau_group_hook( 'before', 'header_meta' ); ?>

			<?php /*if ( sz_nouveau_group_has_meta_extra() ) : ?>
				<div class="item-meta">

					<?php echo sz_nouveau_group_meta()->extra; ?>

				</div><!-- .item-meta -->
			<?php endif;*/ ?>

			<?php //sz_nouveau_group_header_buttons(); ?>

		</div><!-- #item-header-content -->

		<?php //sz_get_template_part( 'groups/single/parts/header-item-actions' ); ?>

	</div><!-- #item-header-cover-image -->


</div><!-- #cover-image-container -->

<?php /*if ( ! sz_nouveau_groups_front_page_description() ) : ?>
	<?php if ( ! empty( sz_nouveau_group_meta()->description ) ) : ?>
		<div class="desc-wrap">
			<div class="group-description">
			<?php echo esc_html( sz_nouveau_group_meta()->description ); ?>
		</div><!-- //.group_description -->
	</div>
	<?php endif; ?>
<?php endif;*/ ?>
