<?php
/**
 * SportsZone - Groups Activity
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>
<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Group secondary navigation', 'sportszone' ); ?>" role="navigation">
	<ul>
		<li class="feed"><a href="<?php sz_group_activity_feed_link(); ?>" class="sz-tooltip" data-sz-tooltip="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>" aria-label="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>"><?php _e( 'RSS', 'sportszone' ); ?></a></li>

		<?php

		/**
		 * Fires inside the syndication options list, after the RSS option.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_group_activity_syndication_options' ); ?>

		<li id="activity-filter-select" class="last">
			<label for="activity-filter-by"><?php _e( 'Show:', 'sportszone' ); ?></label>
			<select id="activity-filter-by">
				<option value="-1"><?php _e( '&mdash; Everything &mdash;', 'sportszone' ); ?></option>

				<?php sz_activity_show_filters( 'group' ); ?>

				<?php

				/**
				 * Fires inside the select input for group activity filter options.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_group_activity_filter_options' ); ?>
			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php

/**
 * Fires before the display of the group activity post form.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_group_activity_post_form' ); ?>

<?php if ( is_user_logged_in() && sz_group_is_member() ) : ?>

	<?php sz_get_template_part( 'activity/post-form' ); ?>

<?php endif; ?>

<?php

/**
 * Fires after the display of the group activity post form.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_group_activity_post_form' ); ?>
<?php

/**
 * Fires before the display of the group activities list.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_group_activity_content' ); ?>

<div class="activity single-group" aria-live="polite" aria-atomic="true" aria-relevant="all">

	<?php sz_get_template_part( 'activity/activity-loop' ); ?>

</div><!-- .activity.single-group -->

<?php

/**
 * Fires after the display of the group activities list.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_group_activity_content' );
