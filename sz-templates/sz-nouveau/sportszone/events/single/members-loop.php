<?php
/**
 * Event Members Loop template
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>
<!-- sz-templates > sz-nouveau > sportszone > events > single > members-loop -->
<?php if ( sz_event_has_members( sz_ajax_querystring( 'event_members' ) ) ) : ?>

	<?php sz_nouveau_event_hook( 'before', 'members_content' ); ?>

	<?php //sz_nouveau_pagination( 'top' ); ?>

	<?php sz_nouveau_event_hook( 'before', 'members_list' ); ?>

	<ul id="members-list" class="<?php sz_nouveau_loop_classes(); ?>">

		<?php
		while ( sz_event_members() ) :
			sz_event_the_member();
		?>

			<li <?php sz_member_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php echo esc_attr( sz_get_event_member_id() ); ?>" data-sz-item-component="members">

				<div class="list-wrap">

					<div class="item-avatar">
						<a href="<?php sz_event_member_domain(); ?>">
							<?php sz_event_member_avatar(); ?>
						</a>
					</div>

					<div class="item">

						<div class="item-block">
							<h3 class="list-title member-name"><?php sz_event_member_link(); ?></h3>

							<p class="joined item-meta">
								<?php sz_event_member_joined_since(); ?>
							</p>

							<?php sz_nouveau_event_hook( '', 'members_list_item' ); ?>

							<?php sz_nouveau_members_loop_buttons(); ?>
						</div>

					</div>

				</div><!-- // .list-wrap -->

			</li>

		<?php endwhile; ?>

	</ul>

	<?php sz_nouveau_event_hook( 'after', 'members_list' ); ?>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

	<?php sz_nouveau_event_hook( 'after', 'members_content' ); ?>

<?php else :

		sz_nouveau_user_feedback( 'event-members-none' );
endif;
