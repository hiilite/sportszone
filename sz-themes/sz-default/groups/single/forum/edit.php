<?php do_action( 'sz_before_group_forum_edit_form' ); ?>

<?php if ( sz_has_forum_topic_posts() ) : ?>

	<form action="<?php sz_forum_topic_action(); ?>" method="post" id="forum-topic-form" class="standard-form">

		<div class="item-list-tabs" id="subnav" role="navigation">
			<ul>
				<li>
					<a href="#post-topic-reply"><?php _e( 'Reply', 'sportszone' ); ?></a>
				</li>

				<?php if ( sz_forums_has_directory() ) : ?>

					<li>
						<a href="<?php sz_forums_directory_permalink(); ?>"><?php _e( 'Forum Directory', 'sportszone'); ?></a>
					</li>

				<?php endif; ?>

			</ul>
		</div>

		<div id="topic-meta">
			<h3><?php _e( 'Edit:', 'sportszone' ); ?> <?php sz_the_topic_title(); ?> (<?php sz_the_topic_total_post_count(); ?>)</h3>

			<?php if ( sz_group_is_admin() || sz_group_is_mod() || sz_get_the_topic_is_mine() ) : ?>

				<div class="last admin-links">

					<?php sz_the_topic_admin_links(); ?>

				</div>

			<?php endif; ?>

			<?php do_action( 'sz_group_forum_topic_meta' ); ?>

		</div>

		<?php if ( sz_is_edit_topic() ) : ?>

			<div id="edit-topic">

				<?php do_action( 'sz_group_before_edit_forum_topic' ); ?>

				<label for="topic_title"><?php _e( 'Title:', 'sportszone' ); ?></label>
				<input type="text" name="topic_title" id="topic_title" value="<?php sz_the_topic_title(); ?>" maxlength="100" />

				<label for="topic_text"><?php _e( 'Content:', 'sportszone' ); ?></label>
				<textarea name="topic_text" id="topic_text"><?php sz_the_topic_text(); ?></textarea>

				<label><?php _e( 'Tags (comma separated):', 'sportszone' ); ?></label>
				<input type="text" name="topic_tags" id="topic_tags" value="<?php sz_forum_topic_tag_list(); ?>" />

				<?php do_action( 'sz_group_after_edit_forum_topic' ); ?>

				<p class="submit"><input type="submit" name="save_changes" id="save_changes" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?>" /></p>

				<?php wp_nonce_field( 'sz_forums_edit_topic' ); ?>

			</div>

		<?php else : ?>

			<div id="edit-post">

				<?php do_action( 'sz_group_before_edit_forum_post' ); ?>

				<textarea name="post_text" id="post_text"><?php sz_the_topic_post_edit_text(); ?></textarea>

				<?php do_action( 'sz_group_after_edit_forum_post' ); ?>

				<p class="submit"><input type="submit" name="save_changes" id="save_changes" value="<?php esc_attr_e( 'Save Changes', 'sportszone' ); ?>" /></p>

				<?php wp_nonce_field( 'sz_forums_edit_post' ); ?>

			</div>

		<?php endif; ?>

	</form><!-- #forum-topic-form -->

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This topic does not exist.', 'sportszone' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'sz_after_group_forum_edit_form' ); ?>
