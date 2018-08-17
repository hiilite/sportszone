<?php
/**
 * BP Nouveau Default event's front template.
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<div class="event-front-page">

	<?php if ( ! is_active_sidebar( 'sidebar-sportszone-events' ) || ! sz_nouveau_events_do_event_boxes() ) : ?>
		<?php if ( ! is_customize_preview() && sz_current_user_can( 'sz_moderate' ) ) : ?>

			<div class="sz-feedback custom-homepage-info info no-icon">
				<strong><?php esc_html_e( 'Manage the Events default front page', 'sportszone' ); ?></strong>

				<p>
				<?php
				printf(
					esc_html__( 'You can set your preferences for the %1$s or add %2$s to it.', 'sportszone' ),
					sz_nouveau_events_get_customizer_option_link(),
					sz_nouveau_events_get_customizer_widgets_link()
				);
				?>
				</p>

			</div>

		<?php endif; ?>
	<?php endif; ?>

	<?php if ( sz_nouveau_events_front_page_description() ) : ?>
		<div class="event-description">

			<?php sz_event_description(); ?>

		</div><!-- .event-description -->
	<?php endif; ?>

	<?php if ( sz_nouveau_events_do_event_boxes() ) : ?>
		<div class="sz-plugin-widgets">

			<?php sz_custom_event_boxes(); ?>

		</div><!-- .sz-plugin-widgets -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-sportszone-events' ) ) : ?>
		<div id="event-front-widgets" class="sz-sidebar sz-widget-area" role="complementary">

			<?php dynamic_sidebar( 'sidebar-sportszone-events' ); ?>

		</div><!-- .sz-sidebar.sz-widget-area -->
	<?php endif; ?>

</div>
