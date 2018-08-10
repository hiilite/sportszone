<?php
/**
 * SportsZone - Groups Cover Image Header.
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the display of a group's header.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_group_header' ); ?>

<div id="cover-image-container">
	<a id="header-cover-image" href="<?php echo esc_url( sz_get_group_permalink() ); ?>"></a>

	<div id="item-header-cover-image">
		<?php if ( ! sz_disable_group_avatar_uploads() ) : ?>
			<div id="item-header-avatar">
				<a href="<?php echo esc_url( sz_get_group_permalink() ); ?>">

					<?php sz_group_avatar(); ?>

				</a>
			</div><!-- #item-header-avatar -->
		<?php endif; ?>

		<div id="item-header-content">

			<div id="item-buttons"><?php

				/**
				 * Fires in the group header actions section.
				 *
				 * @since 1.2.6
				 */
				do_action( 'sz_group_header_actions' ); ?></div><!-- #item-buttons -->

			<?php

			/**
			 * Fires before the display of the group's header meta.
			 *
			 * @since 1.2.0
			 */
			do_action( 'sz_before_group_header_meta' ); ?>

			<div id="item-meta">

				<?php

				/**
				 * Fires after the group header actions section.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_group_header_meta' ); ?>

				<span class="highlight"><?php sz_group_type(); ?></span>
				<span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_group_last_active( 0, array( 'relative' => false ) ) ); ?>"><?php printf( __( 'active %s', 'sportszone' ), sz_get_group_last_active() ); ?></span>

				<?php sz_group_description(); ?>

				<?php sz_group_type_list(); ?>
			</div>
		</div><!-- #item-header-content -->

		<div id="item-actions">

			<?php if ( sz_group_is_visible() ) : ?>

				<h2><?php _e( 'Group Admins', 'sportszone' ); ?></h2>

				<?php sz_group_list_admins();

				/**
				 * Fires after the display of the group's administrators.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_after_group_menu_admins' );

				if ( sz_group_has_moderators() ) :

					/**
					 * Fires before the display of the group's moderators, if there are any.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_before_group_menu_mods' ); ?>

					<h2><?php _e( 'Group Mods' , 'sportszone' ); ?></h2>

					<?php sz_group_list_mods();

					/**
					 * Fires after the display of the group's moderators, if there are any.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_after_group_menu_mods' );

				endif;

			endif; ?>

		</div><!-- #item-actions -->

	</div><!-- #item-header-cover-image -->
</div><!-- #cover-image-container -->

<?php

/**
 * Fires after the display of a group's header.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_group_header' ); ?>

<div id="template-notices" role="alert" aria-atomic="true">
	<?php

	/** This action is documented in sz-templates/sz-legacy/sportszone/activity/index.php */
	do_action( 'template_notices' ); ?>

</div>
