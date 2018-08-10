<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'sz_before_blog_search' ); ?>

		<div class="page" id="blog-search" role="main">

			<h2 class="pagetitle"><?php _e( 'Site', 'sportszone' ); ?></h2>

			<?php if (have_posts()) : ?>

				<h3 class="pagetitle"><?php _e( 'Search Results', 'sportszone' ); ?></h3>

				<?php sz_dtheme_content_nav( 'nav-above' ); ?>

				<?php while (have_posts()) : the_post(); ?>

					<?php do_action( 'sz_before_blog_post' ); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<div class="author-box">
							<?php echo get_avatar( get_the_author_meta( 'email' ), '50' ); ?>
							<p><?php printf( _x( 'by %s', 'Post written by...', 'sportszone' ), sz_core_get_userlink( $post->post_author ) ); ?></p>
						</div>

						<div class="post-content">
							<h2 class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php esc_attr_e( 'Permanent Link to', 'sportszone' ); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

							<p class="date"><?php printf( __( '%1$s <span>in %2$s</span>', 'sportszone' ), get_the_date(), get_the_category_list( ', ' ) ); ?></p>

							<div class="entry">
								<?php the_content( __( 'Read the rest of this entry &rarr;', 'sportszone' ) ); ?>
							</div>

							<p class="postmetadata"><?php the_tags( '<span class="tags">' . __( 'Tags: ', 'sportszone' ), ', ', '</span>' ); ?> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'sportszone' ), __( '1 Comment &#187;', 'sportszone' ), __( '% Comments &#187;', 'sportszone' ) ); ?></span></p>
						</div>

					</div>

					<?php do_action( 'sz_after_blog_post' ); ?>

				<?php endwhile; ?>

				<?php sz_dtheme_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<h2 class="center"><?php _e( 'No posts found. Try a different search?', 'sportszone' ); ?></h2>
				<?php get_search_form(); ?>

			<?php endif; ?>

		</div>

		<?php do_action( 'sz_after_blog_search' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
