<?php
/**
 * BP Nouveau Default group's front template.
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<div class="group-front-page">

	<?php if ( ! is_active_sidebar( 'sidebar-sportszone-groups' ) || ! sz_nouveau_groups_do_group_boxes() ) : ?>
		<?php if ( ! is_customize_preview() && sz_current_user_can( 'sz_moderate' ) ) : ?>

			<div class="sz-feedback custom-homepage-info info no-icon">
				<strong><?php esc_html_e( 'Manage the Groups default front page', 'sportszone' ); ?></strong>

				<p>
				<?php
				printf(
					esc_html__( 'You can set your preferences for the %1$s or add %2$s to it.', 'sportszone' ),
					sz_nouveau_groups_get_customizer_option_link(),
					sz_nouveau_groups_get_customizer_widgets_link()
				);
				?>
				</p>

			</div>

		<?php endif; ?>
	<?php endif; ?>

	<?php if ( sz_nouveau_groups_front_page_description() ) : ?>
		<div class="group-description">

			<?php sz_group_description(); ?>

		</div><!-- .group-description -->
	<?php endif; ?>

	<?php if ( sz_nouveau_groups_do_group_boxes() ) : ?>
		<div class="sz-plugin-widgets">

			<?php sz_custom_group_boxes(); ?>

		</div><!-- .sz-plugin-widgets -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-sportszone-groups' ) ) : ?>
		<div id="group-front-widgets" class="sz-sidebar sz-widget-area" role="complementary">

			<?php dynamic_sidebar( 'sidebar-sportszone-groups' ); ?>

		</div><!-- .sz-sidebar.sz-widget-area -->
	<?php endif; ?>

</div>
