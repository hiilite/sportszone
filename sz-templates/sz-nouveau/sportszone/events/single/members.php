<?php
/**
 * SportsZone - Events Members
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<!-- sz-templates > sz-nouveau > sportszone > events > single > members -->
<div class="subnav-filters filters clearfix no-subnav">

	<?php sz_nouveau_search_form(); ?>

	<?php sz_get_template_part( 'common/filters/events-screens-filters' ); ?>

</div>

<h2 class="sz-screen-title">
	<?php esc_html_e( 'Membership List', 'sportszone' ); ?>
</h2>


<div id="members-event-list" class="event_members dir-list" data-sz-list="event_members">

	<div id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'event-members-loading' ); ?></div>

</div><!-- .event_members.dir-list -->
