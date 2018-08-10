<?php
/**
 * SportsZone - Members Loop
 *
 * @since 3.0.0
 * @version 3.0.0
 */

sz_nouveau_before_loop(); ?>

<?php if ( sz_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php sz_current_member_type_message(); ?></p>
<?php endif; ?>

<?php if ( sz_has_members( sz_ajax_querystring( 'members' ) ) ) : ?>

	<?php sz_nouveau_pagination( 'top' ); ?>

	<ul id="members-list" class="<?php sz_nouveau_loop_classes(); ?>">

	<?php while ( sz_members() ) : sz_the_member(); ?>

		<li <?php sz_member_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php sz_member_user_id(); ?>" data-sz-item-component="members">
			<div class="list-wrap">

				<div class="item-avatar">
					<a href="<?php sz_member_permalink(); ?>"><?php sz_member_avatar( sz_nouveau_avatar_args() ); ?></a>
				</div>

				<div class="item">

					<div class="item-block">

						<h2 class="list-title member-name">
							<a href="<?php sz_member_permalink(); ?>"><?php sz_member_name(); ?></a>
						</h2>

						<?php if ( sz_nouveau_member_has_meta() ) : ?>
							<p class="item-meta last-activity">
								<?php sz_nouveau_member_meta(); ?>
							</p><!-- #item-meta -->
						<?php endif; ?>

						<?php
						sz_nouveau_members_loop_buttons(
							array(
								'container'      => 'ul',
								'button_element' => 'button',
							)
						);
?>

					</div>

					<?php if ( sz_get_member_latest_update() && ! sz_nouveau_loop_is_grid() ) : ?>
					<div class="user-update">
						<p class="update"> <?php sz_member_latest_update(); ?></p>
					</div>
						<?php endif; ?>

				</div><!-- // .item -->



			</div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php
else :

	sz_nouveau_user_feedback( 'members-loop-none' );

endif;
?>

<?php sz_nouveau_after_loop(); ?>
