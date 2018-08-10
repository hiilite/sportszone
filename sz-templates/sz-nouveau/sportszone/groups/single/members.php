<?php
/**
 * SportsZone - Groups Members
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>


<div class="subnav-filters filters clearfix no-subnav">

	<?php sz_nouveau_search_form(); ?>

	<?php sz_get_template_part( 'common/filters/groups-screens-filters' ); ?>

</div>

<h2 class="sz-screen-title">
	<?php esc_html_e( 'Membership List', 'sportszone' ); ?>
</h2>


<div id="members-group-list" class="group_members dir-list" data-sz-list="group_members">

	<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'group-members-loading' ); ?></div>

</div><!-- .group_members.dir-list -->
