<?php
/**
 * SportsZone - Groups Header item-actions.
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>
<div id="item-actions" class="group-item-actions">

	<?php if ( sz_current_user_can( 'groups_access_group' ) ) : ?>

		<h2 class="sz-screen-reader-text"><?php esc_html_e( 'Group Leadership', 'sportszone' ); ?></h2>

		<dl class="moderators-lists">
			<dt class="moderators-title"><?php esc_html_e( 'Group Administrators', 'sportszone' ); ?></dt>
			<dd class="user-list admins"><?php sz_group_list_admins(); ?>
				<?php sz_nouveau_group_hook( 'after', 'menu_admins' ); ?>
			</dd>
		</dl>

		<?php
		if ( sz_group_has_moderators() ) :
			  sz_nouveau_group_hook( 'before', 'menu_mods' );
		?>

			<dl class="moderators-lists">
				<dt class="moderators-title"><?php esc_html_e( 'Group Mods', 'sportszone' ); ?></dt>
				<dd class="user-list moderators">
					<?php
					sz_group_list_mods();
					sz_nouveau_group_hook( 'after', 'menu_mods' );
					?>
				</dd>
			</dl>

		<?php endif; ?>

	<?php endif; ?>

</div><!-- .item-actions -->
