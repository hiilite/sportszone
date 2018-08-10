<?php

/**
 * SportsZone - Users Header
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php do_action( 'sz_before_member_header' ); ?>

<div id="item-header-avatar">
	<a href="<?php sz_displayed_user_link(); ?>">

		<?php sz_displayed_user_avatar( 'type=full' ); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<h2>
		<a href="<?php sz_displayed_user_link(); ?>"><?php sz_displayed_user_fullname(); ?></a>
	</h2>

	<?php if ( sz_is_active( 'activity' ) && sz_activity_do_mentions() ) : ?>
		<span class="user-nicename">@<?php sz_displayed_user_mentionname(); ?></span>
	<?php endif; ?>

	<span class="activity"><?php sz_last_activity( sz_displayed_user_id() ); ?></span>

	<?php do_action( 'sz_before_member_header_meta' ); ?>

	<div id="item-meta">

		<?php if ( sz_is_active( 'activity' ) ) : ?>

			<div id="latest-update">

				<?php sz_activity_latest_update( sz_displayed_user_id() ); ?>

			</div>

		<?php endif; ?>

		<div id="item-buttons">

			<?php do_action( 'sz_member_header_actions' ); ?>

		</div><!-- #item-buttons -->

		<?php
		/***
		 * If you'd like to show specific profile fields here use:
		 * sz_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 */
		 do_action( 'sz_profile_header_meta' );

		 ?>

	</div><!-- #item-meta -->

</div><!-- #item-header-content -->

<?php do_action( 'sz_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>