<?php get_header( 'sportszone' ); ?>

	<div id="content">
		<div class="padder">

			<?php if ( sz_has_groups() ) : while ( sz_groups() ) : sz_the_group(); ?>

			<?php do_action( 'sz_before_group_home_content' ); ?>

			<div id="item-header" role="complementary">

				<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php sz_get_options_nav(); ?>

						<?php do_action( 'sz_group_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">

				<?php do_action( 'sz_before_group_body' );

				/**
				 * Does this next bit look familiar? If not, go check out WordPress's
				 * /wp-includes/template-loader.php file.
				 *
				 * @todo A real template hierarchy? Gasp!
				 */

				// Group is visible
				if ( sz_group_is_visible() ) :

					// Looking at home location
					if ( sz_is_group_home() ) :

						// Use custom front if one exists
						$custom_front = locate_template( array( 'groups/single/front.php' ) );
						if     ( ! empty( $custom_front   ) ) : load_template( $custom_front, true );

						// Default to activity
						elseif ( sz_is_active( 'activity' ) ) : locate_template( array( 'groups/single/activity.php' ), true );

						// Otherwise show members
						elseif ( sz_is_active( 'members'  ) ) : locate_template( array( 'groups/single/members.php'  ), true );

						endif;

					// Not looking at home
					else :

						// Group Admin
						if     ( sz_is_group_admin_page() ) : locate_template( array( 'groups/single/admin.php'        ), true );

						// Group Activity
						elseif ( sz_is_group_activity()   ) : locate_template( array( 'groups/single/activity.php'     ), true );

						// Group Members
						elseif ( sz_is_group_members()    ) : locate_template( array( 'groups/single/members.php'      ), true );

						// Group Invitations
						elseif ( sz_is_group_invites()    ) : locate_template( array( 'groups/single/send-invites.php' ), true );

						// Old group forums
						elseif ( sz_is_group_forum()      ) : locate_template( array( 'groups/single/forum.php'        ), true );

						// Membership request
						elseif ( sz_is_group_membership_request() ) : locate_template( array( 'groups/single/request-membership.php' ), true );

						// Anything else (plugins mostly)
						else                                : locate_template( array( 'groups/single/plugins.php'      ), true );

						endif;
					endif;

				// Group is not visible
				elseif ( ! sz_group_is_visible() ) :
					// Membership request
					if ( sz_is_group_membership_request() ) :
						locate_template( array( 'groups/single/request-membership.php' ), true );

					// The group is not visible, show the status message
					else :

						do_action( 'sz_before_group_status_message' ); ?>

						<div id="message" class="info">
							<p><?php sz_group_status_message(); ?></p>
						</div>

						<?php do_action( 'sz_after_group_status_message' );

					endif;
				endif;

				do_action( 'sz_after_group_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'sz_after_group_home_content' ); ?>

			<?php endwhile; endif; ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'sportszone' ); ?>
<?php get_footer( 'sportszone' ); ?>
