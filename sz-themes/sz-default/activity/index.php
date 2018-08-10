<?php

/**
 * Template Name: SportsZone - Activity Directory
 *
 * @package SportsZone
 * @subpackage Theme
 */

get_header( 'sportszone' ); ?>

	<?php do_action( 'sz_before_directory_activity_page' ); ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'sz_before_directory_activity' ); ?>

			<?php if ( !is_user_logged_in() ) : ?>

				<h3><?php _e( 'Site Activity', 'sportszone' ); ?></h3>

			<?php endif; ?>

			<?php do_action( 'sz_before_directory_activity_content' ); ?>

			<?php if ( is_user_logged_in() ) : ?>

				<?php locate_template( array( 'activity/post-form.php'), true ); ?>

			<?php endif; ?>

			<?php do_action( 'template_notices' ); ?>

			<div class="item-list-tabs activity-type-tabs" role="navigation">
				<ul>
					<?php do_action( 'sz_before_activity_type_tab_all' ); ?>

					<li class="selected" id="activity-all"><a href="<?php sz_activity_directory_permalink(); ?>" title="<?php esc_attr_e( 'The public activity for everyone on this site.', 'sportszone' ); ?>"><?php printf( __( 'All Members <span>%s</span>', 'sportszone' ), sz_get_total_member_count() ); ?></a></li>

					<?php if ( is_user_logged_in() ) : ?>

						<?php do_action( 'sz_before_activity_type_tab_friends' ); ?>

						<?php if ( sz_is_active( 'friends' ) ) : ?>

							<?php if ( sz_get_total_friend_count( sz_loggedin_user_id() ) ) : ?>

								<li id="activity-friends"><a href="<?php echo sz_loggedin_user_domain() . sz_get_activity_slug() . '/' . sz_get_friends_slug() . '/'; ?>" title="<?php esc_attr_e( 'The activity of my friends only.', 'sportszone' ); ?>"><?php printf( __( 'My Friends <span>%s</span>', 'sportszone' ), sz_get_total_friend_count( sz_loggedin_user_id() ) ); ?></a></li>

							<?php endif; ?>

						<?php endif; ?>

						<?php do_action( 'sz_before_activity_type_tab_groups' ); ?>

						<?php if ( sz_is_active( 'groups' ) ) : ?>

							<?php if ( sz_get_total_group_count_for_user( sz_loggedin_user_id() ) ) : ?>

								<li id="activity-groups"><a href="<?php echo sz_loggedin_user_domain() . sz_get_activity_slug() . '/' . sz_get_groups_slug() . '/'; ?>" title="<?php esc_attr_e( 'The activity of groups I am a member of.', 'sportszone' ); ?>"><?php printf( __( 'My Groups <span>%s</span>', 'sportszone' ), sz_get_total_group_count_for_user( sz_loggedin_user_id() ) ); ?></a></li>

							<?php endif; ?>

						<?php endif; ?>

						<?php do_action( 'sz_before_activity_type_tab_favorites' ); ?>

						<?php if ( sz_get_total_favorite_count_for_user( sz_loggedin_user_id() ) ) : ?>

							<li id="activity-favorites"><a href="<?php echo sz_loggedin_user_domain() . sz_get_activity_slug() . '/favorites/'; ?>" title="<?php esc_attr_e( "The activity I've marked as a favorite.", 'sportszone' ); ?>"><?php printf( __( 'My Favorites <span>%s</span>', 'sportszone' ), sz_get_total_favorite_count_for_user( sz_loggedin_user_id() ) ); ?></a></li>

						<?php endif; ?>

						<?php if ( sz_activity_do_mentions() ) : ?>

							<?php do_action( 'sz_before_activity_type_tab_mentions' ); ?>

							<li id="activity-mentions"><a href="<?php echo sz_loggedin_user_domain() . sz_get_activity_slug() . '/mentions/'; ?>" title="<?php esc_attr_e( 'Activity that I have been mentioned in.', 'sportszone' ); ?>"><?php _e( 'Mentions', 'sportszone' ); ?><?php if ( sz_get_total_mention_count_for_user( sz_loggedin_user_id() ) ) : ?> <strong><span><?php printf( _nx( '%s new', '%s new', sz_get_total_mention_count_for_user( sz_loggedin_user_id() ), 'Number of new activity mentions', 'sportszone' ), sz_get_total_mention_count_for_user( sz_loggedin_user_id() ) ); ?></span></strong><?php endif; ?></a></li>

						<?php endif; ?>

					<?php endif; ?>

					<?php do_action( 'sz_activity_type_tabs' ); ?>
				</ul>
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
				<ul>
					<li class="feed"><a href="<?php sz_sitewide_activity_feed_link(); ?>" title="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>"><?php _e( 'RSS', 'sportszone' ); ?></a></li>

					<?php do_action( 'sz_activity_syndication_options' ); ?>

					<li id="activity-filter-select" class="last">
						<label for="activity-filter-by"><?php _e( 'Show:', 'sportszone' ); ?></label>
						<select id="activity-filter-by">
							<option value="-1"><?php _e( '&mdash; Everything &mdash;', 'sportszone' ); ?></option>
							<option value="activity_update"><?php _e( 'Updates', 'sportszone' ); ?></option>

							<?php if ( sz_is_active( 'blogs' ) ) : ?>

								<option value="new_blog_post"><?php _e( 'Posts', 'sportszone' ); ?></option>
								<option value="new_blog_comment"><?php _e( 'Comments', 'sportszone' ); ?></option>

							<?php endif; ?>

							<?php if ( sz_is_active( 'forums' ) ) : ?>

								<option value="new_forum_topic"><?php _e( 'Forum Topics', 'sportszone' ); ?></option>
								<option value="new_forum_post"><?php _e( 'Forum Replies', 'sportszone' ); ?></option>

							<?php endif; ?>

							<?php if ( sz_is_active( 'groups' ) ) : ?>

								<option value="created_group"><?php _e( 'New Groups', 'sportszone' ); ?></option>
								<option value="joined_group"><?php _e( 'Group Memberships', 'sportszone' ); ?></option>

							<?php endif; ?>

							<?php if ( sz_is_active( 'friends' ) ) : ?>

								<option value="friendship_accepted,friendship_created"><?php _e( 'Friendships', 'sportszone' ); ?></option>

							<?php endif; ?>

							<option value="new_member"><?php _e( 'New Members', 'sportszone' ); ?></option>

							<?php do_action( 'sz_activity_filter_options' ); ?>

						</select>
					</li>
				</ul>
			</div><!-- .item-list-tabs -->

			<?php do_action( 'sz_before_directory_activity_list' ); ?>

			<div class="activity" role="main">

				<?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>

			</div><!-- .activity -->

			<?php do_action( 'sz_after_directory_activity_list' ); ?>

			<?php do_action( 'sz_directory_activity_content' ); ?>

			<?php do_action( 'sz_after_directory_activity_content' ); ?>

			<?php do_action( 'sz_after_directory_activity' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'sz_after_directory_activity_page' ); ?>

<?php get_sidebar( 'sportszone' ); ?>
<?php get_footer( 'sportszone' ); ?>
