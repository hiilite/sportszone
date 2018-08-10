<?php
/**
 * SportsZone - Users Header
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<?php

/**
 * Fires before the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_member_header' ); ?>

<div id="item-header-avatar">
	<a href="<?php sz_displayed_user_link(); ?>">

		<?php sz_displayed_user_avatar( 'type=full' ); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<?php if ( sz_is_active( 'activity' ) && sz_activity_do_mentions() ) : ?>
		<h2 class="user-nicename">@<?php sz_displayed_user_mentionname(); ?></h2>
	<?php endif; ?>

	<span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_user_last_activity( sz_displayed_user_id() ) ); ?>"><?php sz_last_activity( sz_displayed_user_id() ); ?></span>

	<?php

	/**
	 * Fires before the display of the member's header meta.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_member_header_meta' ); ?>

	<div id="item-meta">

		<?php if ( sz_is_active( 'activity' ) ) : ?>

			<div id="latest-update">

				<?php sz_activity_latest_update( sz_displayed_user_id() ); ?>

			</div>

		<?php endif; ?>

		<div id="item-buttons">

			<?php

			/**
			 * Fires in the member header actions section.
			 *
			 * @since 1.2.6
			 */
			do_action( 'sz_member_header_actions' ); ?>

		</div><!-- #item-buttons -->

		<?php

		 /**
		  * Fires after the group header actions section.
		  *
		  * If you'd like to show specific profile fields here use:
		  * sz_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		  *
		  * @since 1.2.0
		  */
		 do_action( 'sz_profile_header_meta' );

		 ?>

	</div><!-- #item-meta -->

</div><!-- #item-header-content -->

<?php

/**
 * Fires after the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_member_header' ); ?>

<div id="template-notices" role="alert" aria-atomic="true">
	<?php

	/** This action is documented in sz-templates/sz-legacy/sportszone/activity/index.php */
	do_action( 'template_notices' ); ?>

</div>
