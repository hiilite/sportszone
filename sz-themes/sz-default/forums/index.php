<?php

/**
 * SportsZone - Forums Directory
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php get_header( 'sportszone' ); ?>

	<?php do_action( 'sz_before_directory_forums_page' ); ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'sz_before_directory_forums' ); ?>

			<form action="" method="post" id="forums-search-form" class="dir-form">

				<h3><?php _e( 'Forums Directory', 'sportszone' ); ?><?php if ( is_user_logged_in() ) : ?> &nbsp;<a class="button show-hide-new" href="#new-topic" id="new-topic-button"><?php _e( 'New Topic', 'sportszone' ); ?></a><?php endif; ?></h3>

				<?php do_action( 'sz_before_directory_forums_content' ); ?>

				<div id="forums-dir-search" class="dir-search" role="search">

					<?php sz_directory_forums_search_form(); ?>

				</div>
			</form>

			<?php do_action( 'sz_before_topics' ); ?>

			<form action="" method="post" id="forums-directory-form" class="dir-form">

				<div class="item-list-tabs" role="navigation">
					<ul>
						<li class="selected" id="forums-all"><a href="<?php echo trailingslashit( sz_get_root_domain() . '/' . sz_get_forums_root_slug() ); ?>"><?php printf( __( 'All Topics <span>%s</span>', 'sportszone' ), sz_get_forum_topic_count() ); ?></a></li>

						<?php if ( is_user_logged_in() && sz_get_forum_topic_count_for_user( sz_loggedin_user_id() ) ) : ?>

							<li id="forums-personal"><a href="<?php echo trailingslashit( sz_loggedin_user_domain() . sz_get_forums_slug() . '/topics' ); ?>"><?php printf( __( 'My Topics <span>%s</span>', 'sportszone' ), sz_get_forum_topic_count_for_user( sz_loggedin_user_id() ) ); ?></a></li>

						<?php endif; ?>

						<?php do_action( 'sz_forums_directory_group_types' ); ?>

					</ul>
				</div>

				<div class="item-list-tabs" id="subnav" role="navigation">
					<ul>

						<?php do_action( 'sz_forums_directory_group_sub_types' ); ?>

						<li id="forums-order-select" class="last filter">

							<label for="forums-order-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
							<select id="forums-order-by">
								<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
								<option value="popular"><?php _e( 'Most Posts', 'sportszone' ); ?></option>
								<option value="unreplied"><?php _e( 'Unreplied', 'sportszone' ); ?></option>

								<?php do_action( 'sz_forums_directory_order_options' ); ?>

							</select>
						</li>
					</ul>
				</div>

				<div id="forums-dir-list" class="forums dir-list" role="main">

					<?php locate_template( array( 'forums/forums-loop.php' ), true ); ?>

				</div>

				<?php do_action( 'sz_directory_forums_content' ); ?>

				<?php wp_nonce_field( 'directory_forums', '_wpnonce-forums-filter' ); ?>

			</form>

			<?php do_action( 'sz_after_directory_forums' ); ?>

			<?php do_action( 'sz_before_new_topic_form' ); ?>

			<div id="new-topic-post">

				<?php if ( is_user_logged_in() ) : ?>

					<?php if ( sz_is_active( 'groups' ) && sz_has_groups( 'user_id=' . sz_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100' ) ) : ?>

						<form action="" method="post" id="forum-topic-form" class="standard-form">

							<?php do_action( 'groups_forum_new_topic_before' ); ?>

							<a name="post-new"></a>
							<h5><?php _e( 'Create New Topic:', 'sportszone' ); ?></h5>

							<?php do_action( 'template_notices' ); ?>

							<label><?php _e( 'Title:', 'sportszone' ); ?></label>
							<input type="text" name="topic_title" id="topic_title" value="" maxlength="100" />

							<label><?php _e( 'Content:', 'sportszone' ); ?></label>
							<textarea name="topic_text" id="topic_text"></textarea>

							<label><?php _e( 'Tags (comma separated):', 'sportszone' ); ?></label>
							<input type="text" name="topic_tags" id="topic_tags" value="" />

							<label><?php _e( 'Post In Group Forum:', 'sportszone' ); ?></label>
							<select id="topic_group_id" name="topic_group_id">

								<option value=""><?php /* translators: no option picked in select box */ _e( '----', 'sportszone' ); ?></option>

								<?php while ( sz_groups() ) : sz_the_group(); ?>

									<?php if ( sz_group_is_forum_enabled() && ( sz_current_user_can( 'sz_moderate' ) || 'public' == sz_get_group_status() || sz_group_is_member() ) ) : ?>

										<option value="<?php sz_group_id(); ?>"><?php sz_group_name(); ?></option>

									<?php endif; ?>

								<?php endwhile; ?>

							</select><!-- #topic_group_id -->

							<?php do_action( 'groups_forum_new_topic_after' ); ?>

							<div class="submit">
								<input type="submit" name="submit_topic" id="submit" value="<?php esc_attr_e( 'Post Topic', 'sportszone' ); ?>" />
								<input type="button" name="submit_topic_cancel" id="submit_topic_cancel" value="<?php esc_attr_e( 'Cancel', 'sportszone' ); ?>" />
							</div>

							<?php wp_nonce_field( 'sz_forums_new_topic' ); ?>

						</form><!-- #forum-topic-form -->

					<?php elseif ( sz_is_active( 'groups' ) ) : ?>

						<div id="message" class="info">

							<p><?php printf( __( "You are not a member of any groups so you don't have any group forums you can post in. To start posting, first find a group that matches the topic subject you'd like to start. If this group does not exist, why not <a href='%s'>create a new group</a>? Once you have joined or created the group you can post your topic in that group's forum.", 'sportszone' ), site_url( sz_get_groups_root_slug() . '/create/' ) ); ?></p>

						</div>

					<?php endif; ?>

				<?php endif; ?>
			</div><!-- #new-topic-post -->

			<?php do_action( 'sz_after_new_topic_form' ); ?>

			<?php do_action( 'sz_after_directory_forums_content' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'sz_after_directory_forums_page' ); ?>

<?php get_sidebar( 'sportszone' ); ?>
<?php get_footer( 'sportszone' ); ?>
