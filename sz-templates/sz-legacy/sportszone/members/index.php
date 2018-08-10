<?php
/**
 * SportsZone - Members
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires at the top of the members directory template file.
 *
 * @since 1.5.0
 */
do_action( 'sz_before_directory_members_page' ); ?>

<div id="sportszone">

	<?php

	/**
	 * Fires before the display of the members.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_directory_members' ); ?>

	<?php

	/**
	 * Fires before the display of the members content.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_directory_members_content' ); ?>

	<?php /* Backward compatibility for inline search form. Use template part instead. */ ?>
	<?php if ( has_filter( 'sz_directory_members_search_form' ) ) : ?>

		<div id="members-dir-search" class="dir-search" role="search">
			<?php sz_directory_members_search_form(); ?>
		</div><!-- #members-dir-search -->

	<?php else: ?>

		<?php sz_get_template_part( 'common/search/dir-search-form' ); ?>

	<?php endif; ?>

	<?php
	/**
	 * Fires before the display of the members list tabs.
	 *
	 * @since 1.8.0
	 */
	do_action( 'sz_before_directory_members_tabs' ); ?>

	<form action="" method="post" id="members-directory-form" class="dir-form">

		<div class="item-list-tabs" aria-label="<?php esc_attr_e( 'Members directory main navigation', 'sportszone' ); ?>" role="navigation">
			<ul>
				<li class="selected" id="members-all"><a href="<?php sz_members_directory_permalink(); ?>"><?php printf( __( 'All Members %s', 'sportszone' ), '<span>' . sz_get_total_member_count() . '</span>' ); ?></a></li>

				<?php if ( is_user_logged_in() && sz_is_active( 'friends' ) && sz_get_total_friend_count( sz_loggedin_user_id() ) ) : ?>
					<li id="members-personal"><a href="<?php echo esc_url( sz_loggedin_user_domain() . sz_get_friends_slug() . '/my-friends/' ); ?>"><?php printf( __( 'My Friends %s', 'sportszone' ), '<span>' . sz_get_total_friend_count( sz_loggedin_user_id() ) . '</span>' ); ?></a></li>
				<?php endif; ?>

				<?php

				/**
				 * Fires inside the members directory member types.
				 *
				 * @since 1.2.0
				 */
				do_action( 'sz_members_directory_member_types' ); ?>

			</ul>
		</div><!-- .item-list-tabs -->

		<div class="item-list-tabs" id="subnav" aria-label="<?php esc_attr_e( 'Members directory secondary navigation', 'sportszone' ); ?>" role="navigation">
			<ul>
				<?php

				/**
				 * Fires inside the members directory member sub-types.
				 *
				 * @since 1.5.0
				 */
				do_action( 'sz_members_directory_member_sub_types' ); ?>

				<li id="members-order-select" class="last filter">
					<label for="members-order-by"><?php _e( 'Order By:', 'sportszone' ); ?></label>
					<select id="members-order-by">
						<option value="active"><?php _e( 'Last Active', 'sportszone' ); ?></option>
						<option value="newest"><?php _e( 'Newest Registered', 'sportszone' ); ?></option>

						<?php if ( sz_is_active( 'xprofile' ) ) : ?>
							<option value="alphabetical"><?php _e( 'Alphabetical', 'sportszone' ); ?></option>
						<?php endif; ?>

						<?php

						/**
						 * Fires inside the members directory member order options.
						 *
						 * @since 1.2.0
						 */
						do_action( 'sz_members_directory_order_options' ); ?>
					</select>
				</li>
			</ul>
		</div>

		<h2 class="sz-screen-reader-text"><?php
			/* translators: accessibility text */
			_e( 'Members directory', 'sportszone' );
		?></h2>

		<div id="members-dir-list" class="members dir-list">
			<?php sz_get_template_part( 'members/members-loop' ); ?>
		</div><!-- #members-dir-list -->

		<?php

		/**
		 * Fires and displays the members content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_directory_members_content' ); ?>

		<?php wp_nonce_field( 'directory_members', '_wpnonce-member-filter' ); ?>

		<?php

		/**
		 * Fires after the display of the members content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'sz_after_directory_members_content' ); ?>

	</form><!-- #members-directory-form -->

	<?php

	/**
	 * Fires after the display of the members.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_directory_members' ); ?>

</div><!-- #sportszone -->

<?php

/**
 * Fires at the bottom of the members directory template file.
 *
 * @since 1.5.0
 */
do_action( 'sz_after_directory_members_page' );
