<?php
/**
 * SportsZone Activity templates
 *
 * @since 2.3.0
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the activity directory listing.
 *
 * @since 1.5.0
 */
do_action( 'sz_before_directory_activity' ); ?>

<div id="sportszone">

	<?php

	/**
	 * Fires before the activity directory display content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_directory_activity_content' ); ?>

	<?php if ( is_user_logged_in() ) : ?>

		<?php sz_get_template_part( 'activity/post-form' ); ?>

	<?php endif; ?>

	<div id="template-notices" role="alert" aria-atomic="true">
		<?php

		/**
		 * Fires towards the top of template pages for notice display.
		 *
		 * @since 1.0.0
		 */
		do_action( 'template_notices' ); ?>

	</div>

	<div class="item-list-tabs activity-type-tabs" aria-label="<?php esc_attr_e( 'Sitewide activities navigation', 'sportszone' ); ?>" role="navigation">
		<ul>
			<?php

			/**
			 * Fires before the listing of activity type tabs.
			 *
			 * @since 1.2.0
			 */
			do_action( 'sz_before_activity_type_tab_all' ); ?>

			<li class="selected" id="activity-all"><a href="<?php sz_activity_directory_permalink(); ?>"><?php printf( __( 'All Members %s', 'sportszone' ), '<span>' . sz_get_total_member_count() . '</span>' ); ?></a></li>

			<?php if ( is_user_logged_in() ) : ?>

				<?php

				/**
				 * Fires before the listing of friends activity type tab.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_before_activity_type_tab_friends' ); ?>

				<?php if ( sz_is_active( 'friends' ) ) : ?>

					<?php if ( sz_get_total_friend_count( sz_loggedin_user_id() ) ) : ?>

						<li id="activity-friends"><a href="<?php echo sz_loggedin_user_domain() . sz_get_activity_slug() . '/' . sz_get_friends_slug() . '/'; ?>"><?php printf( __( 'My Friends %s', 'sportszone' ), '<span>' . sz_get_total_friend_count( sz_loggedin_user_id() ) . '</span>' ); ?></a></li>

					<?php endif; ?>

				<?php endif; ?>

				<?php

				/**
				 * Fires before the listing of groups activity type tab.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_before_activity_type_tab_groups' ); ?>

				<?php if ( sz_is_active( 'groups' ) ) : ?>

					<?php if ( sz_get_total_group_count_for_user( sz_loggedin_user_id() ) ) : ?>

						<?php
						printf(
							'<li id="activity-groups"><a href="%1$s">%2$s</a></li>',
							esc_url( sz_loggedin_user_domain() . sz_get_activity_slug() . '/' . sz_get_groups_slug() . '/' ),
							sprintf(
								/* translators: %s: total joined groups count for the current user */
								__( 'My Groups %s', 'sportszone' ),
								'<span>' . sz_get_total_group_count_for_user( sz_loggedin_user_id() ) . '</span>'
							)
						);
						?>

					<?php endif; ?>

				<?php endif; ?>

				<?php

				/**
				 * Fires before the listing of favorites activity type tab.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_before_activity_type_tab_favorites' ); ?>

				<?php if ( sz_get_total_favorite_count_for_user( sz_loggedin_user_id() ) ) : ?>

					<li id="activity-favorites"><a href="<?php echo sz_loggedin_user_domain() . sz_get_activity_slug() . '/favorites/'; ?>"><?php printf( __( 'My Favorites %s', 'sportszone' ), '<span>' . sz_get_total_favorite_count_for_user( sz_loggedin_user_id() ) . '</span>' ); ?></a></li>

				<?php endif; ?>

				<?php if ( sz_activity_do_mentions() ) : ?>

					<?php

					/**
					 * Fires before the listing of mentions activity type tab.
					 *
					 * @since 1.2.0
					 */
					do_action( 'sz_before_activity_type_tab_mentions' ); ?>

					<li id="activity-mentions"><a href="<?php echo sz_loggedin_user_domain() . sz_get_activity_slug() . '/mentions/'; ?>"><?php _e( 'Mentions', 'sportszone' ); ?><?php if ( sz_get_total_mention_count_for_user( sz_loggedin_user_id() ) ) : ?> <strong><span><?php printf( _nx( '%s new', '%s new', sz_get_total_mention_count_for_user( sz_loggedin_user_id() ), 'Number of new activity mentions', 'sportszone' ), sz_get_total_mention_count_for_user( sz_loggedin_user_id() ) ); ?></span></strong><?php endif; ?></a></li>

				<?php endif; ?>

			<?php endif; ?>

			<?php

			/**
			 * Fires after the listing of activity type tabs.
			 *
			 * @since 1.2.0
			 */
			do_action( 'sz_activity_type_tabs' ); ?>
		</ul>
	</div><!-- .item-list-tabs -->

	<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Activity secondary navigation', 'sportszone' ); ?>" role="navigation">
		<ul>
			<li class="feed"><a href="<?php sz_sitewide_activity_feed_link(); ?>" class="sz-tooltip" data-sz-tooltip="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>" aria-label="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>"><?php _e( 'RSS', 'sportszone' ); ?></a></li>

			<?php

			/**
			 * Fires before the display of the activity syndication options.
			 *
			 * @since 1.2.0
			 */
			do_action( 'sz_activity_syndication_options' ); ?>

			<li id="activity-filter-select" class="last">
				<label for="activity-filter-by"><?php _e( 'Show:', 'sportszone' ); ?></label>
				<select id="activity-filter-by">
					<option value="-1"><?php _e( '&mdash; Everything &mdash;', 'sportszone' ); ?></option>

					<?php sz_activity_show_filters(); ?>

					<?php

					/**
					 * Fires inside the select input for activity filter by options.
					 *
					 * @since 1.2.0
					 */
					do_action( 'sz_activity_filter_options' ); ?>

				</select>
			</li>
		</ul>
	</div><!-- .item-list-tabs -->

	<?php

	/**
	 * Fires before the display of the activity list.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_before_directory_activity_list' ); ?>

	<div class="activity" aria-live="polite" aria-atomic="true" aria-relevant="all">

		<?php sz_get_template_part( 'activity/activity-loop' ); ?>

	</div><!-- .activity -->

	<?php

	/**
	 * Fires after the display of the activity list.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_after_directory_activity_list' ); ?>

	<?php

	/**
	 * Fires inside and displays the activity directory display content.
	 */
	do_action( 'sz_directory_activity_content' ); ?>

	<?php

	/**
	 * Fires after the activity directory display content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_directory_activity_content' ); ?>

	<?php

	/**
	 * Fires after the activity directory listing.
	 *
	 * @since 1.5.0
	 */
	do_action( 'sz_after_directory_activity' ); ?>

</div>
