<?php

/**
 * SportsZone - Users Home
 *
 * @package SportsZone
 * @subpackage sz-default
 */

get_header( 'sportszone' ); ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'sz_before_member_home_content' ); ?>

			<div id="item-header" role="complementary">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php sz_get_displayed_user_nav(); ?>

						<?php do_action( 'sz_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">

				<?php do_action( 'sz_before_member_body' );

				if ( sz_is_user_activity() || !sz_current_component() ) :
					locate_template( array( 'members/single/activity.php'  ), true );

				 elseif ( sz_is_user_blogs() ) :
					locate_template( array( 'members/single/blogs.php'     ), true );

				elseif ( sz_is_user_friends() ) :
					locate_template( array( 'members/single/friends.php'   ), true );

				elseif ( sz_is_user_groups() ) :
					locate_template( array( 'members/single/groups.php'    ), true );
				
				elseif ( sz_is_user_events() ) :
					locate_template( array( 'members/single/events.php'    ), true );

				elseif ( sz_is_user_messages() ) :
					locate_template( array( 'members/single/messages.php'  ), true );

				elseif ( sz_is_user_profile() ) :
					locate_template( array( 'members/single/profile.php'   ), true );

				elseif ( sz_is_user_forums() ) :
					locate_template( array( 'members/single/forums.php'    ), true );

				elseif ( sz_is_user_settings() ) :
					locate_template( array( 'members/single/settings.php'  ), true );

				elseif ( sz_is_user_notifications() ) :
					locate_template( array( 'members/single/notifications.php' ), true );

				// If nothing sticks, load a generic template
				else :
					locate_template( array( 'members/single/plugins.php'   ), true );

				endif;

				do_action( 'sz_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'sz_after_member_home_content' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'sportszone' ); ?>
<?php get_footer( 'sportszone' ); ?>
