<?php

/**
 * SportsZone - Members Directory
 *
 * @package SportsZone
 * @subpackage sz-default
 */

get_header( 'sportszone' ); ?>

	<?php do_action( 'sz_before_directory_members_page' ); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'sz_before_directory_members' ); ?>

		<form action="" method="post" id="members-directory-form" class="dir-form">

			<h3><?php _e( 'Members Directory', 'sportszone' ); ?></h3>

			<?php do_action( 'sz_before_directory_members_content' ); ?>

			<div id="members-dir-search" class="dir-search" role="search">

				<?php sz_directory_members_search_form(); ?>

			</div><!-- #members-dir-search -->

			<?php do_action( 'sz_before_directory_members_tabs' ); ?>

			<div class="item-list-tabs" role="navigation">
				<ul>
					<li class="selected" id="members-all"><a href="<?php echo trailingslashit( sz_get_root_domain() . '/' . sz_get_members_root_slug() ); ?>"><?php printf( __( 'All Members <span>%s</span>', 'sportszone' ), sz_get_total_member_count() ); ?></a></li>

					<?php if ( is_user_logged_in() && sz_is_active( 'friends' ) && sz_get_total_friend_count( sz_loggedin_user_id() ) ) : ?>

						<li id="members-personal"><a href="<?php echo sz_loggedin_user_domain() . sz_get_friends_slug() . '/my-friends/' ?>"><?php printf( __( 'My Friends <span>%s</span>', 'sportszone' ), sz_get_total_friend_count( sz_loggedin_user_id() ) ); ?></a></li>

					<?php endif; ?>

					<?php do_action( 'sz_members_directory_member_types' ); ?>

				</ul>
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs" id="subnav" role="navigation">
				<ul>

					<?php do_action( 'sz_members_directory_member_sub_types' ); ?>

					<li id="members-order-select" class="last filter">

						<label for="members-order-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
						<select id="members-order-by">
							<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
							<option value="newest"><?php _e( 'Newest Registered', 'sportszone' ); ?></option>

							<?php if ( sz_is_active( 'xprofile' ) ) : ?>

								<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>

							<?php endif; ?>

							<?php do_action( 'sz_members_directory_order_options' ); ?>

						</select>
					</li>
				</ul>
			</div>

			<div id="members-dir-list" class="members dir-list">

				<?php locate_template( array( 'members/members-loop.php' ), true ); ?>

			</div><!-- #members-dir-list -->

			<?php do_action( 'sz_directory_members_content' ); ?>

			<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

			<?php do_action( 'sz_after_directory_members_content' ); ?>

		</form><!-- #members-directory-form -->

		<?php do_action( 'sz_after_directory_members' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'sz_after_directory_members_page' ); ?>

<?php get_sidebar( 'sportszone' ); ?>
<?php get_footer( 'sportszone' ); ?>
