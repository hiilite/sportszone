<?php
/**
 * SportsZone - Members Single Group Invites
 *
 * @since 3.0.0
 * @version 3.1.0
 */
?>

<!--h2 class="screen-heading group-invites-screen"><?php esc_html_e( 'Group Invites', 'sportszone' ); ?></h2-->

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
						<div class="group-invite-info">
							<h4 class="list-title groups-title"><?php sz_group_link(); ?></h4>
	
	
							<p class="item-meta group-details">
								<?php	
								$country = groups_get_groupmeta( sz_get_group_id(), 'sz_group_country');
								$province = groups_get_groupmeta( sz_get_group_id(), 'sz_group_province');
								if(isset($country['country'])) {
									echo $country['country'];
								}
								if(isset($province['province'])) {
									echo ', '.$province['province'];
								}
								?>	
							</p>
	
	
							
							
							
							<?php
							$args = array( 
							    'group_id' => sz_get_group_id(),
							    'exclude_admins_mods' => false
							);
							$group_members_result = groups_get_group_members( $args );
							$group_members = array();
							
							foreach(  $group_members_result['members'] as $member ) {
								$group_members[] = $member->ID;
							}	
							
			
							if(!empty($group_members)) :
							?>
							<div class="sz_user_friends">
								<?php 
								if(count($group_members) > 3) {
									$g_members = array_rand($group_members, 3);
								}
		
								foreach($g_members as $g) {
									echo '<a href="'.sz_core_get_user_domain($group_members[$g]).'" class="mini-avatar">'.sz_core_fetch_avatar ( array( "item_id" => $group_members[$g], "type" => "full" ) ).'</a>';
								}
								?>
								<a href="<?php echo sz_group_permalink(); ?>members/" class="mini-ellipsis">...</a>
							</div>
							<?php endif; ?>
						</div>
						
						
						
						
						<div class="group-invite-btns">
							<?php sz_nouveau_group_hook( '', 'invites_item' ); ?>
	
							<?php
							sz_nouveau_groups_invite_buttons(
								array(
									'container'      => 'ul',
									'button_element' => 'button',
								)
							);
							?>
							
							<?php 
							if(sz_groups_get_group_type(sz_get_group_id(),true) != '') {
							?>
								<div class="group-type-name">
									<?php echo sz_groups_get_group_type(sz_get_group_id(),true); ?>
								</div>
							<?php
							}
							?>
					</div>
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
