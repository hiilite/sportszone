<?php
/**
 * SportsZone - Members Home
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<div id="sportszone">

	<?php

	/**
	 * Fires before the display of member home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_member_home_content' ); ?>

	<div id="item-header" role="complementary">

		<?php
		/**
		 * If the cover image feature is enabled, use a specific header
		 */
		if ( sz_displayed_user_use_cover_image_header() ) :
			sz_get_template_part( 'members/single/cover-image-header' );
		else :
			sz_get_template_part( 'members/single/member-header' );
		endif;
		?>

	</div><!-- #item-header -->

	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" aria-label="<?php esc_attr_e( 'Member primary navigation', 'sportszone' ); ?>" role="navigation">
			<ul>

				<?php sz_get_displayed_user_nav(); ?>

				<?php

				/**
				 * Fires after the display of member options navigation.
				 *
				 * @since 1.2.4
				 */
				do_action( 'sz_member_options_nav' ); ?>

			</ul>
		</div>
	</div><!-- #item-nav -->

	<div id="item-body">

		<?php

		/**
		 * Fires before the display of member body content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_before_member_body' );

		if ( sz_is_user_front() ) :
			sz_displayed_user_front_template_part();

		elseif ( sz_is_user_activity() ) :
			sz_get_template_part( 'members/single/activity' );

		elseif ( sz_is_user_blogs() ) :
			sz_get_template_part( 'members/single/blogs'    );

		elseif ( sz_is_user_friends() ) :
			sz_get_template_part( 'members/single/friends'  );

		elseif ( sz_is_user_groups() ) :
			sz_get_template_part( 'members/single/groups'   );

		elseif ( sz_is_user_messages() ) :
			sz_get_template_part( 'members/single/messages' );

		elseif ( sz_is_user_profile() ) :
			sz_get_template_part( 'members/single/profile'  );

		elseif ( sz_is_user_notifications() ) :
			sz_get_template_part( 'members/single/notifications' );

		elseif ( sz_is_user_settings() ) :
			sz_get_template_part( 'members/single/settings' );

		// If nothing sticks, load a generic template
		else :
			sz_get_template_part( 'members/single/plugins'  );

		endif;

		/**
		 * Fires after the display of member body content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_after_member_body' ); ?>

	</div><!-- #item-body -->

	<?php

	/**
	 * Fires after the display of member home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_member_home_content' ); ?>

</div><!-- #sportszone -->
