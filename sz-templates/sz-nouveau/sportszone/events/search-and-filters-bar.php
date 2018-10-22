<?php
/**
 * BP Nouveau Search & filters bar
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>
<!--sportszone > sz-templates > sz-nouveau > sportszone > events > search-and-filder-bar-->
<div class="subnav-filters filters no-ajax" id="subnav-filters">

	<?php if ( 'friends' !== sz_current_component() ) : ?>
	<div class="subnav-search clearfix">

		<?php if ( 'activity' === sz_current_component() ) : ?>
			<div class="feed"><a href="<?php sz_sitewide_activity_feed_link(); ?>" class="sz-tooltip" data-sz-tooltip="<?php esc_attr_e( 'RSS Feed', 'sportszone' ); ?>"><span class="sz-screen-reader-text"><?php esc_html_e( 'RSS', 'sportszone' ); ?></span></a></div>
		<?php endif; ?>

		<?php sz_nouveau_search_form('events'); ?>

	</div>
	<?php endif; ?>

		<?php if ( sz_is_user() && ! sz_is_current_action( 'requests' ) ) : ?>
			<?php sz_get_template_part( 'common/filters/user-screens-filters' ); ?>
		<?php elseif ( 'groups' === sz_current_component() ) : ?>
			<?php sz_get_template_part( 'common/filters/groups-screens-filters' ); ?>
		<?php else : ?>
			<?php sz_get_template_part( 'common/filters/directory-filters' ); ?>
		<?php endif; ?>

</div><!-- search & filters -->
