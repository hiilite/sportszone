<?php
/**
 * SportsZone - Members Single Group Invites
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<h2 class="screen-heading group-invites-screen"><?php esc_html_e( 'Group Invites', 'sportszone' ); ?></h2>

<?php sz_nouveau_group_hook( 'before', 'invites_content' ); ?>

<?php if ( sz_has_groups( 'type=invites&user_id=' . sz_loggedin_user_id() ) ) : ?>

	<ul id="group-list" class="invites item-list sz-list" data-sz-list="groups_invites">

		<?php
		while ( sz_groups() ) :
			sz_the_group();
		?>

			<li class="item-entry invites-list" data-sz-item-id="<?php sz_group_id(); ?>" data-sz-item-component="groups">

				<div class="wrap">

				<?php if ( ! sz_disable_group_avatar_uploads() ) : ?>
					<div class="item-avatar">
						<a href="<?php sz_group_permalink(); ?>"><?php sz_group_avatar(); ?></a>
					</div>
				<?php endif; ?>

					<div class="item">
						<h2 class="list-title groups-title"><?php sz_group_link(); ?></h2>
						<p class="meta group-details">
							<span class="small">
							<?php
							printf(
								/* translators: %s = number of members */
								_n(
									'%s member',
									'%s members',
									sz_get_group_total_members( false ),
									'sportszone'
								),
								number_format_i18n( sz_get_group_total_members( false ) )
							);
							?>
							</span>
						</p>

						<p class="desc">
							<?php sz_group_description_excerpt(); ?>
						</p>

						<?php sz_nouveau_group_hook( '', 'invites_item' ); ?>

						<?php
						sz_nouveau_groups_invite_buttons(
							array(
								'container'      => 'ul',
								'button_element' => 'button',
							)
						);
						?>
					</div>

				</div>
			</li>

		<?php endwhile; ?>
	</ul>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'member-invites-none' ); ?>

<?php endif; ?>

<?php
sz_nouveau_group_hook( 'after', 'invites_content' );
