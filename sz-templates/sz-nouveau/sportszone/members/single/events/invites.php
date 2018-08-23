<?php
/**
 * SportsZone - Members Single Event Invites
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<h2 class="screen-heading event-invites-screen"><?php esc_html_e( 'Event Invites', 'sportszone' ); ?></h2>

<?php sz_nouveau_event_hook( 'before', 'invites_content' ); ?>

<?php if ( sz_has_events( 'type=invites&user_id=' . sz_loggedin_user_id() ) ) : ?>

	<ul id="event-list" class="invites item-list sz-list" data-sz-list="events_invites">

		<?php
		while ( sz_events() ) :
			sz_the_event();
		?>

			<li class="item-entry invites-list" data-sz-item-id="<?php sz_event_id(); ?>" data-sz-item-component="events">

				<div class="wrap">

				<?php if ( ! sz_disable_event_avatar_uploads() ) : ?>
					<div class="item-avatar">
						<a href="<?php sz_event_permalink(); ?>"><?php sz_event_avatar(); ?></a>
					</div>
				<?php endif; ?>

					<div class="item">
						<h2 class="list-title events-title"><?php sz_event_link(); ?></h2>
						<p class="meta event-details">
							<span class="small">
							<?php
							printf(
								/* translators: %s = number of members */
								_n(
									'%s member',
									'%s members',
									sz_get_event_total_members( false ),
									'sportszone'
								),
								number_format_i18n( sz_get_event_total_members( false ) )
							);
							?>
							</span>
						</p>

						<p class="desc">
							<?php sz_event_description_excerpt(); ?>
						</p>

						<?php sz_nouveau_event_hook( '', 'invites_item' ); ?>

						<?php
						sz_nouveau_events_invite_buttons(
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
sz_nouveau_event_hook( 'after', 'invites_content' );
