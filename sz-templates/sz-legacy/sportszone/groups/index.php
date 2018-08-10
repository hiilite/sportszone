<?php
/**
 * SportsZone - Groups
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires at the top of the groups directory template file.
 *
 * @since 1.5.0
 */
do_action( 'sz_before_directory_groups_page' ); ?>

<div id="sportszone">

	<?php

	/**
	 * Fires before the display of the groups.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_directory_groups' ); ?>

	<?php

	/**
	 * Fires before the display of the groups content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_directory_groups_content' ); ?>

	<?php /* Backward compatibility for inline search form. Use template part instead. */ ?>
	<?php if ( has_filter( 'sz_directory_groups_search_form' ) ) : ?>

		<div id="group-dir-search" class="dir-search" role="search">
			<?php sz_directory_groups_search_form(); ?>
		</div><!-- #group-dir-search -->

	<?php else: ?>

		<?php sz_get_template_part( 'common/search/dir-search-form' ); ?>

	<?php endif; ?>

	<form action="" method="post" id="groups-directory-form" class="dir-form">

		<div id="template-notices" role="alert" aria-atomic="true">
			<?php

			/** This action is documented in sz-templates/sz-legacy/sportszone/activity/index.php */
			do_action( 'template_notices' ); ?>

		</div>

		<div class="item-list-tabs" aria-label="<?php esc_attr_e( 'Groups directory main navigation', 'sportszone' ); ?>">
			<ul>
				<li class="selected" id="groups-all"><a href="<?php sz_groups_directory_permalink(); ?>"><?php printf( __( 'All Groups %s', 'sportszone' ), '<span>' . sz_get_total_group_count() . '</span>' ); ?></a></li>

				<?php if ( is_user_logged_in() && sz_get_total_group_count_for_user( sz_loggedin_user_id() ) ) : ?>
					<li id="groups-personal"><a href="<?php echo sz_loggedin_user_domain() . sz_get_groups_slug() . '/my-groups/'; ?>"><?php printf( __( 'My Groups %s', 'sportszone' ), '<span>' . sz_get_total_group_count_for_user( sz_loggedin_user_id() ) . '</span>' ); ?></a></li>
				<?php endif; ?>

				<?php

				/**
				 * Fires inside the groups directory group filter input.
				 *
				 * @since 1.5.0
				 */
				do_action( 'sz_groups_directory_group_filter' ); ?>

			</ul>
		</div><!-- .item-list-tabs -->

		<div class="item-list-tabs" id="subnav" aria-label="<?php esc_attr_e( 'Groups directory secondary navigation', 'sportszone' ); ?>" role="navigation">
			<ul>
				<?php

				/**
				 * Fires inside the groups directory group types.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_groups_directory_group_types' ); ?>

				<li id="groups-order-select" class="last filter">

					<label for="groups-order-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>

					<select id="groups-order-by">
						<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
						<option value="popular"><?php _e( 'Most Members', 'sportszone' ); ?></option>
						<option value="newest"><?php _e( 'Newly Created', 'sportszone' ); ?></option>
						<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

						<?php

						/**
						 * Fires inside the groups directory group order options.
						 *
						 * @since 1.2.0
						 */
						do_action( 'sz_groups_directory_order_options' ); ?>
					</select>
				</li>
			</ul>
		</div>

		<h2 class="sz-screen-reader-text"><?php
			/* translators: accessibility text */
			_e( 'Groups directory', 'sportszone' );
		?></h2>

		<div id="groups-dir-list" class="groups dir-list">
			<?php sz_get_template_part( 'groups/groups-loop' ); ?>
		</div><!-- #groups-dir-list -->

		<?php

		/**
		 * Fires and displays the group content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_directory_groups_content' ); ?>

		<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

		<?php

		/**
		 * Fires after the display of the groups content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_after_directory_groups_content' ); ?>

	</form><!-- #groups-directory-form -->

	<?php

	/**
	 * Fires after the display of the groups.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_directory_groups' ); ?>

</div><!-- #sportszone -->

<?php

/**
 * Fires at the bottom of the groups directory template file.
 *
 * @since 1.5.0
 */
do_action( 'sz_after_directory_groups_page' );
