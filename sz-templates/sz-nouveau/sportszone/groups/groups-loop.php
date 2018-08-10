<?php
/**
 * SportsZone - Groups Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_before_loop(); ?>

<?php if ( sz_get_current_group_directory_type() ) : ?>
	<p class="current-group-type"><?php sz_current_group_directory_type_message(); ?></p>
<?php endif; ?>

<?php if ( sz_has_groups( sz_ajax_querystring( 'groups' ) ) ) : ?>

	<?php sz_nouveau_pagination( 'top' ); ?>

	<ul id="groups-list" class="<?php sz_nouveau_loop_classes(); ?>">

	<?php
	while ( sz_groups() ) :
		sz_the_group();
	?>

		<li <?php sz_group_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php sz_group_id(); ?>" data-sz-item-component="groups">
			<div class="list-wrap">

				<?php if ( ! sz_disable_group_avatar_uploads() ) : ?>
					<div class="item-avatar">
						<a href="<?php sz_group_permalink(); ?>"><?php sz_group_avatar( sz_nouveau_avatar_args() ); ?></a>
					</div>
				<?php endif; ?>

				<div class="item">

					<div class="item-block">

						<h2 class="list-title groups-title"><?php sz_group_link(); ?></h2>

						<?php if ( sz_nouveau_group_has_meta() ) : ?>

							<p class="item-meta group-details"><?php sz_nouveau_group_meta(); ?></p>

						<?php endif; ?>

						<p class="last-activity item-meta">
							<?php
							printf(
								/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
								__( 'active %s', 'sportszone' ),
								sz_get_group_last_active()
							);
							?>
						</p>

					</div>

					<div class="group-desc"><p><?php sz_nouveau_group_description_excerpt(); ?></p></div>

					<?php sz_nouveau_groups_loop_item(); ?>

					<?php sz_nouveau_groups_loop_buttons(); ?>

				</div>


			</div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'groups-loop-none' ); ?>

<?php endif; ?>

<?php
sz_nouveau_after_loop();
