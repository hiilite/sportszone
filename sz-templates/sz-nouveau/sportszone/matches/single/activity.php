<?php
/**
 * SportsZone - Events Activity
 *
 * @since 3.0.0
 * @version 3.1.0
 */

?>

<h2 class="sz-screen-title<?php echo ( ! sz_is_event_home() ) ? ' sz-screen-reader-text' : ''; ?>">
	<?php esc_html_e( 'Event Activities', 'sportszone' ); ?>
</h2>

<?php sz_nouveau_events_activity_post_form(); ?>

<div class="subnav-filters filters clearfix">

	<ul>

		<li class="feed"><a href="<?php sz_event_activity_feed_link(); ?>" class="sz-tooltip no-ajax" data-sz-tooltip="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>"><span class="sz-screen-reader-text"><?php esc_html_e( 'RSS', 'sportszone' ); ?></span></a></li>

		<li class="event-act-search"><?php sz_nouveau_search_form(); ?></li>

	</ul>

		<?php sz_get_template_part( 'common/filters/events-screens-filters' ); ?>
</div><!-- // .subnav-filters -->

<?php sz_nouveau_event_hook( 'before', 'activity_content' ); ?>

<div id="activity-stream" class="activity single-event" data-sz-list="activity">

		<li id="sz-activity-ajax-loader"><?php sz_nouveau_user_feedback( 'event-activity-loading' ); ?></li>

</div><!-- .activity -->

<?php
sz_nouveau_event_hook( 'after', 'activity_content' );
