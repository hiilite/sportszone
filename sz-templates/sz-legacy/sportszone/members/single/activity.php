<?php
/**
 * SportsZone - Users Activity
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'sportszone' ); ?>" role="navigation">
	<ul>

		<?php sz_get_options_nav(); ?>

		<li id="activity-filter-select" class="last">
			<label for="activity-filter-by"><?php _e( 'Show:', 'sportszone' ); ?></label>
			<select id="activity-filter-by">
				<option value="-1"><?php _e( '&mdash; Everything &mdash;', 'sportszone' ); ?></option>

				<?php sz_activity_show_filters(); ?>

				<?php

				/**
				 * Fires inside the select input for member activity filter options.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_member_activity_filter_options' ); ?>

			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php

/**
 * Fires before the display of the member activity post form.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_member_activity_post_form' ); ?>

<?php
if ( is_user_logged_in() && sz_is_my_profile() && ( !sz_current_action() || sz_is_current_action( 'just-me' ) ) )
	sz_get_template_part( 'activity/post-form' );

/**
 * Fires after the display of the member activity post form.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_member_activity_post_form' );

/**
 * Fires before the display of the member activities list.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_member_activity_content' ); ?>

<div class="activity" aria-live="polite" aria-atomic="true" aria-relevant="all">

	<?php sz_get_template_part( 'activity/activity-loop' ) ?>

</div><!-- .activity -->

<?php

/**
 * Fires after the display of the member activities list.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_member_activity_content' );
