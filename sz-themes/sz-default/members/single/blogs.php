<?php

/**
 * SportsZone - Users Blogs
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<div class="item-list-tabs" id="subnav" role="navigation">
	<ul>

		<?php sz_get_options_nav(); ?>

		<li id="blogs-order-select" class="last filter">

			<label for="blogs-all"><?php _e( 'Order By:', 'sportszone' ); ?></label>
			<select id="blogs-all">
				<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
				<option value="newest"><?php _e( 'Newest', 'sportszone' ); ?></option>
				<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

				<?php do_action( 'sz_member_blog_order_options' ); ?>

			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'sz_before_member_blogs_content' ); ?>

<div class="blogs myblogs" role="main">

	<?php locate_template( array( 'blogs/blogs-loop.php' ), true ); ?>

</div><!-- .blogs.myblogs -->

<?php do_action( 'sz_after_member_blogs_content' ); ?>
