<?php
/**
 * SportsZone - Users Blogs
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Sites menu', 'sportszone' ); ?>">
	<ul class="subnav">

		<?php sz_get_template_part( 'members/single/parts/item-subnav' ); ?>

	</ul>
</nav><!-- .sz-navs -->

<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>

<?php
switch ( sz_current_action() ) :

	// Home/My Blogs
	case 'my-sites':
		sz_nouveau_member_hook( 'before', 'blogs_content' );
		?>

		<div class="blogs myblogs" data-sz-list="blogs">

			<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'member-blogs-loading' ); ?></div>

		</div><!-- .blogs.myblogs -->

		<?php
		sz_nouveau_member_hook( 'after', 'blogs_content' );
		break;

	// Any other
	default:
		sz_get_template_part( 'members/single/plugins' );
		break;
endswitch;
