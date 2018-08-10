<?php
/**
 * BP Nouveau Default user's front template.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<div class="member-front-page">

	<?php if ( ! is_customize_preview() && sz_current_user_can( 'sz_moderate' ) && ! is_active_sidebar( 'sidebar-sportszone-members' ) ) : ?>

		<div class="sz-feedback custom-homepage-info info">
			<strong><?php esc_html_e( 'Manage the members default front page', 'sportszone' ); ?></strong>
			<button type="button" class="sz-tooltip" data-sz-tooltip="<?php echo esc_attr_x( 'Close', 'button', 'sportszone' ); ?>" aria-label="<?php esc_attr_e( 'Close this notice', 'sportszone' ); ?>" data-sz-close="remove"><span class="dashicons dashicons-dismiss" aria-hidden="true"></span></button><br/>
			<?php
			printf(
				esc_html__( 'You can set the preferences of the %1$s or add %2$s to it.', 'sportszone' ),
				sz_nouveau_members_get_customizer_option_link(),
				sz_nouveau_members_get_customizer_widgets_link()
			);
			?>
		</div>

	<?php endif; ?>

	<?php if ( sz_nouveau_members_wp_bio_info() ) : ?>

		<div class="member-description">

			<?php if ( get_the_author_meta( 'description', sz_displayed_user_id() ) ) : ?>
				<blockquote class="member-bio">
					<?php sz_nouveau_member_description( sz_displayed_user_id() ); ?>
				</blockquote><!-- .member-bio -->
			<?php endif; ?>

			<?php
			if ( sz_is_my_profile() ) :

				sz_nouveau_member_description_edit_link();

			endif;
			?>

		</div><!-- .member-description -->

	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-sportszone-members' ) ) : ?>

		<div id="member-front-widgets" class="sz-sidebar sz-widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-sportszone-members' ); ?>
		</div><!-- .sz-sidebar.sz-widget-area -->

	<?php endif; ?>

</div>
