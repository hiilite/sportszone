<?php
/*
Template Name: Links
*/
?>

<?php get_header(); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'sz_before_blog_links' ); ?>

		<div class="page" id="blog-latest" role="main">

			<h2 class="pagetitle"><?php _e( 'Links', 'sportszone' ); ?></h2>

			<ul id="links-list">
				<?php wp_list_bookmarks(); ?>
			</ul>

		</div>

		<?php do_action( 'sz_after_blog_links' ); ?>

		</div>
	</div>

<?php get_footer(); ?>
