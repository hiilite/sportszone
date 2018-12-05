<?php
/**
 * SportsZone - Members Loop
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>
<!-- sz-templates > sz-nouveau > sportszone > members > members-loop -->
<?php 
sz_nouveau_before_loop();

//echo '<h2>'.__('Members').'</h2>';

if ( sz_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php sz_current_member_type_message(); ?></p>
<?php endif; ?>

<?php if ( sz_has_members( sz_ajax_querystring( 'members' ) ) ) : ?>

	<?php //sz_nouveau_pagination( 'top' ); ?>

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
					</div>


				</div><!-- // .item -->
				
				
				<div class="sz_user_profiles">
					<ul>
					<?php
										
						$player_profile = array();
						$player_profile[] = xprofile_get_field_data('Player Bio',sz_get_member_user_id());
						$player_profile[] = xprofile_get_field_data('Playing Positions',sz_get_member_user_id());
						$player_profile[] = xprofile_get_field_data('Previous Clubs',sz_get_member_user_id());
						$player_profile[] = xprofile_get_field_data('Weight',sz_get_member_user_id());
						$player_profile[] = xprofile_get_field_data('Height',sz_get_member_user_id());
						$player_profile[] = xprofile_get_field_data('Nationality',sz_get_member_user_id());
						
						if(array_filter($player_profile)) {
							/* Player */
							echo '<li><a href="'.sz_get_member_permalink().'profile/#sz-player-profile">Player</a></li>';
						}
						
						$tm_profile = array();
						$tm_profile[] = xprofile_get_field_data(45,sz_get_member_user_id());
						$tm_profile[] = xprofile_get_field_data('Roles',sz_get_member_user_id());
						
						if(array_filter($tm_profile)) {
							/* TM */
							echo '<li><a href="'.sz_get_member_permalink().'profile/#sz-team-management">Coach</a></li>';
						}
						
						$ref_profile = array();
						$ref_profile[] = xprofile_get_field_data(47,sz_get_member_user_id());
						$ref_profile[] = xprofile_get_field_data('Level',sz_get_member_user_id());
						$ref_profile[] = xprofile_get_field_data('Certification',sz_get_member_user_id());
						
						if(array_filter($ref_profile)) {
							/* Ref */
							echo '<li><a href="'.sz_get_member_permalink().'profile/#sz-referee-profile">Referee</a></li>';
						}
						

					?>
					</ul>
				</div>
				
				<?php 
				$user_friends = friends_get_friend_user_ids(sz_get_member_user_id()); 

				if(!empty($user_friends)) :
				?>
				<div class="sz_user_friends">
					<?php 
					if(count($user_friends) > 3) {
						$friends = array_rand($user_friends, 3);
					}

					foreach($friends as $f) {
						
						echo '<a href="'.sz_core_get_user_domain($user_friends[$f]).'" class="mini-avatar">'.sz_core_fetch_avatar ( array( "item_id" => $user_friends[$f], "type" => "full" ) ).'</a>';
					}
					?>
					<a href="<?php echo sz_member_permalink(); ?>friends/" class="mini-ellipsis">...</a>
				</div>
				<?php endif; ?>

				<div>
					<?php
						sz_nouveau_members_loop_buttons(
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

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php
else :

	sz_nouveau_user_feedback( 'members-loop-none' );

endif;
?>

<?php sz_nouveau_after_loop(); ?>
