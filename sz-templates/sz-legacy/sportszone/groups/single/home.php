<?php
/**
 * SportsZone - Groups Home
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>
<div id="sportszone">

	<?php if ( sz_has_groups() ) : while ( sz_groups() ) : sz_the_group(); ?>

	<?php

	/**
	 * Fires before the display of the group home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_before_group_home_content' ); ?>

	<div id="item-header" role="complementary">

		<?php
		/**
		 * If the cover image feature is enabled, use a specific header
		 */
		if ( sz_group_use_cover_image_header() ) :
			sz_get_template_part( 'groups/single/cover-image-header' );
		else :
			sz_get_template_part( 'groups/single/group-header' );
		endif;
		?>

	</div><!-- #item-header -->

	<div id="item-nav">
		<div class="item-list-tabs no-ajax" id="object-nav" aria-label="<?php esc_attr_e( 'Group primary navigation', 'sportszone' ); ?>" role="navigation">
			<ul>

				<?php sz_get_options_nav(); ?>

				<?php

				/**
				 * Fires after the display of group options navigation.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_group_options_nav' ); ?>

			</ul>
		</div>
	</div><!-- #item-nav -->

	<div id="item-body">

		<?php

		/**
		 * Fires before the display of the group home body.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_before_group_body' );

		/**
		 * Does this next bit look familiar? If not, go check out WordPress's
		 * /wp-includes/template-loader.php file.
		 *
		 * @todo A real template hierarchy? Gasp!
		 */

			// Looking at home location
			if ( sz_is_group_home() ) :

				if ( sz_group_is_visible() ) {

					// Load appropriate front template
					sz_groups_front_template_part();

				} else {

					/**
					 * Fires before the display of the group status message.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_before_group_status_message' ); ?>

					<div id="message" class="info">
						<p><?php sz_group_status_message(); ?></p>
					</div>

					<?php

					/**
					 * Fires after the display of the group status message.
					 *
					 * @since 1.1.0
					 */
					do_action( 'sz_after_group_status_message' );

				}

			// Not looking at home
			else :

				// Group Admin
				if     ( sz_is_group_admin_page() ) : sz_get_template_part( 'groups/single/admin'        );

				// Group Activity
				elseif ( sz_is_group_activity()   ) : sz_get_template_part( 'groups/single/activity'     );

				// Group Members
				elseif ( sz_is_group_members()    ) : sz_groups_members_template_part();

				// Group Invitations
				elseif ( sz_is_group_invites()    ) : sz_get_template_part( 'groups/single/send-invites' );

				// Membership request
				elseif ( sz_is_group_membership_request() ) : sz_get_template_part( 'groups/single/request-membership' );

				// Anything else (plugins mostly)
				else                                : sz_get_template_part( 'groups/single/plugins'      );

				endif;

			endif;

		/**
		 * Fires after the display of the group home body.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_after_group_body' ); ?>

	</div><!-- #item-body -->

	<?php

	/**
	 * Fires after the display of the group home content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'sz_after_group_home_content' ); ?>

	<?php endwhile; endif; ?>

</div><!-- #sportszone -->
