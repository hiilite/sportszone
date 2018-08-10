<?php

/**
 * SportsZone - Users Activity
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>

		<?php sz_get_options_nav(); ?>

		<li id="activity-filter-select" class="last">
			<label for="activity-filter-by"><?php _e( 'Show:', 'sportszone' ); ?></label>
			<select id="activity-filter-by">
				<option value="-1"><?php _e( '&mdash; Everything &mdash;', 'sportszone' ); ?></option>
				<option value="activity_update"><?php _e( 'Updates', 'sportszone' ); ?></option>

				<?php
				if ( !sz_is_current_action( 'groups' ) ) :
					if ( sz_is_active( 'blogs' ) ) : ?>

						<option value="new_blog_post"><?php _e( 'Posts', 'sportszone' ); ?></option>
						<option value="new_blog_comment"><?php _e( 'Comments', 'sportszone' ); ?></option>

					<?php
					endif;

					if ( sz_is_active( 'friends' ) ) : ?>

						<option value="friendship_accepted,friendship_created"><?php _e( 'Friendships', 'sportszone' ); ?></option>

					<?php endif;

				endif;

				if ( sz_is_active( 'forums' ) ) : ?>

					<option value="new_forum_topic"><?php _e( 'Forum Topics', 'sportszone' ); ?></option>
					<option value="new_forum_post"><?php _e( 'Forum Replies', 'sportszone' ); ?></option>

				<?php endif;

				if ( sz_is_active( 'groups' ) ) : ?>

					<option value="created_group"><?php _e( 'New Groups', 'sportszone' ); ?></option>
					<option value="joined_group"><?php _e( 'Group Memberships', 'sportszone' ); ?></option>

				<?php endif;

				do_action( 'sz_member_activity_filter_options' ); ?>

			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'sz_before_member_activity_post_form' ); ?>

<?php
if ( is_user_logged_in() && sz_is_my_profile() && ( !sz_current_action() || sz_is_current_action( 'just-me' ) ) )
	locate_template( array( 'activity/post-form.php'), true );

do_action( 'sz_after_member_activity_post_form' );
do_action( 'sz_before_member_activity_content' ); ?>

<div class="activity" role="main">

	<?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>

</div><!-- .activity -->

<?php do_action( 'sz_after_member_activity_content' ); ?>
