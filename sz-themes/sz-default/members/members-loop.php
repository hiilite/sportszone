<?php

/**
 * SportsZone - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - sz_dtheme_object_filter()
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php do_action( 'sz_before_members_loop' ); ?>

<?php if ( sz_has_members( sz_ajax_querystring( 'members' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">

			<?php sz_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php sz_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'sz_before_directory_members_list' ); ?>

	<ul id="members-list" class="item-list" role="main">

	<?php while ( sz_members() ) : sz_the_member(); ?>

		<li>
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

				<div class="item-meta"><span class="activity"><?php sz_member_last_active(); ?></span></div>

				<?php do_action( 'sz_directory_members_item' ); ?>

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

				<?php do_action( 'sz_directory_members_actions' ); ?>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'sz_after_directory_members_list' ); ?>

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

<?php do_action( 'sz_after_members_loop' ); ?>
