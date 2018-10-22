<?php
/**
 * SportsZone - Events Header item-actions.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>
<div id="item-actions" class="event-item-actions">

	<?php if ( sz_current_user_can( 'events_access_event' ) ) : ?>

		<h2 class="sz-screen-reader-text"><?php esc_html_e( 'Event Leadership', 'sportszone' ); ?></h2>

		<dl class="moderators-lists">
			<dt class="moderators-title"><?php esc_html_e( 'Event Administrators', 'sportszone' ); ?></dt>
			<dd class="user-list admins"><?php sz_event_list_admins(); ?>
				<?php sz_nouveau_event_hook( 'after', 'menu_admins' ); ?>
			</dd>
		</dl>

		<?php
		if ( sz_event_has_moderators() ) :
			  sz_nouveau_event_hook( 'before', 'menu_mods' );
		?>

			<dl class="moderators-lists">
				<dt class="moderators-title"><?php esc_html_e( 'Event Mods', 'sportszone' ); ?></dt>
				<dd class="user-list moderators">
					<?php
					sz_event_list_mods();
					sz_nouveau_event_hook( 'after', 'menu_mods' );
					?>
				</dd>
			</dl>

		<?php endif; ?>

	<?php endif; ?>

</div><!-- .item-actions -->
