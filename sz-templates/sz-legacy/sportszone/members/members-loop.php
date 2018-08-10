<?php
/**
 * SportsZone - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - sz_legacy_theme_object_filter()
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

/**
 * Fires before the display of the members loop.
 *
 * @since 1.2.0
 */
do_action( 'sz_before_members_loop' ); ?>

<?php if ( sz_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php sz_current_member_type_message() ?></p>
<?php endif; ?>

<?php if ( sz_has_members( sz_ajax_querystring( 'members' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires before the display of the members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_before_directory_members_list' ); ?>

	<ul id="members-list" class="item-list" aria-live="assertive" aria-relevant="all">

	<?php while ( sz_members() ) : sz_the_member(); ?>

		<li <?php sz_member_class(); ?>>
			<div class="item-avatar">
				<a href="<?php sz_member_permalink(); ?>"><?php sz_member_avatar(); ?></a>
			</div>

			<div class="item">
				<div class="item-title">
					<a href="<?php sz_member_permalink(); ?>"><?php sz_member_name(); ?></a>

					<?php if ( sz_get_member_latest_update() ) : ?>

						<span class="update"> <?php sz_member_latest_update(); ?></span>

					<?php endif; ?>

				</div>

				<div class="item-meta"><span class="activity" data-livestamp="<?php sz_core_iso8601_date( sz_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php sz_member_last_active(); ?></span></div>

				<?php

				/**
				 * Fires inside the display of a directory member item.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_directory_members_item' ); ?>

				<?php
				 /***
				  * If you want to show specific profile fields here you can,
				  * but it'll add an extra query for each member in the loop
				  * (only one regardless of the number of fields you show):
				  *
				  * sz_member_profile_data( 'field=the field name' );
				  */
				?>
			</div>

			<div class="action">

				<?php

				/**
				 * Fires inside the members action HTML markup to display actions.
				 *
				 * @since 1.1.0
				 */
				do_action( 'sz_directory_members_actions' ); ?>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the display of the members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'sz_after_directory_members_list' ); ?>

	<?php sz_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'sportszone' ); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the display of the members loop.
 *
 * @since 1.2.0
 */
do_action( 'sz_after_members_loop' );
