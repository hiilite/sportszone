<?php
/**
 * BP Nouveau Activity Widget template.
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<?php if ( sz_has_activities( sz_nouveau_activity_widget_query() ) ) : ?>

	<div class="activity-list item-list">

		<?php
		while ( sz_activities() ) :
			sz_the_activity();
		?>

			<?php if ( sz_activity_has_content() ) : ?>

				<blockquote>

					<?php sz_activity_content_body(); ?>

					<footer>

						<cite>
							<a href="<?php sz_activity_user_link(); ?>" class="sz-tooltip" data-sz-tooltip="<?php echo esc_attr( sz_activity_member_display_name() ); ?>">
								<?php
								sz_activity_avatar(
									array(
										'type'   => 'thumb',
										'width'  => '40',
										'height' => '40',
									)
								);
								?>
							</a>
						</cite>

						<?php echo sz_insert_activity_meta(); ?>

					</footer>

				</blockquote>

			<?php else : ?>

				<p><?php sz_activity_action(); ?></p>

			<?php endif; ?>

		<?php endwhile; ?>

	</div>

<?php else : ?>

	<div class="widget-error">
		<?php sz_nouveau_user_feedback( 'activity-loop-none' ); ?>
	</div>

<?php endif; ?>
