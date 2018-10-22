<?php
/**
 * SportsZone - Members Friends Requests
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<h2 class="screen-heading friendship-requests-screen"><?php esc_html_e( 'Friendship Requests', 'sportszone' ); ?></h2>

<?php sz_nouveau_member_hook( 'before', 'friend_requests_content' ); ?>

<?php if ( sz_has_members( 'type=alphabetical&include=' . sz_get_friendship_requests() ) ) : ?>

	<?php //sz_nouveau_pagination( 'top' ); ?>

	<ul id="friend-list" class="<?php sz_nouveau_loop_classes(); ?>" data-sz-list="friendship_requests">
		<?php
		while ( sz_members() ) :
			sz_the_member();
		?>

			<li id="friendship-<?php sz_friend_friendship_id(); ?>" <?php sz_member_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php sz_friend_friendship_id(); ?>" data-sz-item-component="members">
				<div class="item-avatar">
					<a href="<?php sz_member_link(); ?>"><?php sz_member_avatar( array( 'type' => 'full' ) ); ?></a>
				</div>
				

				<div class="item">
					<div class="item-title"><a href="<?php sz_member_link(); ?>"><?php sz_member_name(); ?></a></div>
					<!--div class="item-meta"><span class="activity"><?php sz_member_last_active(); ?></span></div-->

					<?php sz_nouveau_friend_hook( 'requests_item' ); ?>
					
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
							$user_friends = array_rand($user_friends, 3);
						}
	
						foreach($user_friends as $f) {
							
							echo '<a href="'.sz_core_get_user_domain($f).'" class="mini-avatar">'.sz_core_fetch_avatar ( array( "item_id" => $f, "type" => "full" ) ).'</a>';
						}
						?>
						<a href="<?php echo sz_member_permalink(); ?>friends/" class="mini-ellipsis">...</a>
					</div>
					<?php endif; ?>
				</div>
				

				<?php sz_nouveau_members_loop_buttons(); ?>
			</li>

		<?php endwhile; ?>
	</ul>

	<?php sz_nouveau_friend_hook( 'requests_content' ); ?>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'member-requests-none' ); ?>

<?php endif; ?>

<?php
sz_nouveau_member_hook( 'after', 'friend_requests_content' );
