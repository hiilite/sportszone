<?php
/**
 * SportsZone - Users Activity
 *
 * @since 3.0.0
 * @version 3.0.0
 */

?>

<nav class="<?php sz_nouveau_single_item_subnav_classes(); ?>" id="subnav" role="navigation" aria-label="<?php esc_attr_e( 'Activity menu', 'sportszone' ); ?>">
	<ul class="subnav">

		<?php sz_get_template_part( 'members/single/parts/item-subnav' ); ?>

	</ul>
</nav><!-- .item-list-tabs#subnav -->

<h2 class="sz-screen-title<?php echo ( sz_displayed_user_has_front_template() ) ? ' sz-screen-reader-text' : ''; ?>">
	<?php esc_html_e( 'Member Activities', 'sportszone' ); ?>
</h2>

<?php sz_nouveau_activity_member_post_form(); ?>

<?php sz_get_template_part( 'common/search-and-filters-bar' ); ?>

<?php sz_nouveau_member_hook( 'before', 'activity_content' ); ?>

<div id="activity-stream" class="activity single-user" data-sz-list="activity">

	<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'member-activity-loading' ); ?></div>

	<ul  class="<?php sz_nouveau_loop_classes(); ?>" >

	</ul>

</div><!-- .activity -->

<?php
sz_nouveau_member_hook( 'after', 'activity_content' );
