<?php
/**
 * SportsZone - Users Blogs
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<div class="item-list-tabs" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'sportszone' ); ?>" role="navigation">
	<ul>

		<?php sz_get_options_nav(); ?>

		<li id="blogs-order-select" class="last filter">

			<label for="blogs-order-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
			<select id="blogs-order-by">
				<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
				<option value="newest"><?php _e( 'Newest', 'sportszone' ); ?></option>
				<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

				<?php

				/**
				 * Fires inside the members blogs order options select input.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_member_blog_order_options' ); ?>

			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php
switch ( sz_current_action() ) :

	// Home/My Blogs
	case 'my-sites' :

		/**
		 * Fires before the display of member blogs content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_before_member_blogs_content' ); ?>

		<div class="blogs myblogs">

			<?php sz_get_template_part( 'blogs/blogs-loop' ) ?>

		</div><!-- .blogs.myblogs -->

		<?php

		/**
		 * Fires after the display of member blogs content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_after_member_blogs_content' );
		break;

	// Any other
	default :
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
