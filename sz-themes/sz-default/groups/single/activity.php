<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<li class="feed"><a href="<?php sz_group_activity_feed_link(); ?>" title="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>"><?php _e( 'RSS', 'sportszone' ); ?></a></li>

		<?php do_action( 'sz_group_activity_syndication_options' ); ?>

		<li id="activity-filter-select" class="last">
			<label for="activity-filter-by"><?php _e( 'Show:', 'sportszone' ); ?></label> 
			<select id="activity-filter-by">
				<option value="-1"><?php _e( '&mdash; Everything &mdash;', 'sportszone' ); ?></option>
				<option value="activity_update"><?php _e( 'Updates', 'sportszone' ); ?></option>

				<?php if ( sz_is_active( 'forums' ) ) : ?>
					<option value="new_forum_topic"><?php _e( 'Forum Topics', 'sportszone' ); ?></option>
					<option value="new_forum_post"><?php _e( 'Forum Replies', 'sportszone' ); ?></option>
				<?php endif; ?>

				<option value="joined_group"><?php _e( 'Group Memberships', 'sportszone' ); ?></option>

				<?php do_action( 'sz_group_activity_filter_options' ); ?>
			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'sz_before_group_activity_post_form' ); ?>

<?php if ( is_user_logged_in() && sz_group_is_member() ) : ?>
	<?php locate_template( array( 'activity/post-form.php'), true ); ?>
<?php endif; ?>

<?php do_action( 'sz_after_group_activity_post_form' ); ?>
<?php do_action( 'sz_before_group_activity_content' ); ?>

<div class="activity single-group" role="main">
	<?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>
</div><!-- .activity.single-group -->

<?php do_action( 'sz_after_group_activity_content' ); ?>
