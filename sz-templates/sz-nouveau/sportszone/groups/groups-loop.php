<?php
/**
 * SportsZone - Groups Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_before_loop(); 
echo '<!--sz-templates/sz-nouveau/sportszone/groups/groups-loop-->';

echo '<h2>'.__('Groups').'</h2>';

if ( sz_get_current_group_directory_type() ) : ?>
	<p class="current-group-type"><?php sz_current_group_directory_type_message(); ?></p>
<?php endif; ?>

<?php if ( sz_has_groups( sz_ajax_querystring( 'groups' ) ) ) : ?>

	<?php //sz_nouveau_pagination( 'top' ); ?>

	<ul id="groups-list" class="<?php sz_nouveau_loop_classes(); ?>">

	<?php
	while ( sz_groups() ) :
		sz_the_group();
	?>

		<li <?php sz_group_class( array( 'item-entry' ) ); ?> data-sz-item-id="<?php sz_group_id(); ?>" data-sz-item-component="groups">
			<div class="list-wrap">

				<?php if ( ! sz_disable_group_avatar_uploads() ) : ?>
					<div class="item-avatar">
						<a href="<?php sz_group_permalink(); ?>"><?php sz_group_avatar( sz_nouveau_avatar_args() ); ?></a>
					</div>
				<?php endif; ?>

				<div class="item">
					
					<?php
					
					?>
					
					<div class="item-block">

						<h4 class="list-title groups-title"><?php sz_group_link(); ?></h4>
						
						<p class="item-meta group-details">
							<?php	
							$country = groups_get_groupmeta( sz_get_group_id(), 'sz_group_country');
							$province = groups_get_groupmeta( sz_get_group_id(), 'sz_group_province');
							if(isset($province['province']) && $province['province'] != '') {
								echo $province['province'];
							}
							if(isset($country['country']) && $country['country'] != '') {
								echo ', '.$country['country'];
							}
							?>	
						</p>

					</div>
					
					
					
					
					<?php 
					$group_members = SZ_Groups_Member::get_all_for_group(sz_get_group_id());
	
					if(!empty($group_members['members'])) :
					?>
					<div class="sz_user_friends">
						<?php 
						if(count($group_members['members']) > 3) {
							$g_members = array_rand($group_members['members'], 3);
						}
						else {
							$g_members = $group_members['members'];
						}

						foreach($g_members as $key => $val) {
							echo '<a href="'.sz_core_get_user_domain($group_members['members'][$key]->user_id).'" class="mini-avatar">'.sz_core_fetch_avatar ( array( "item_id" => $group_members['members'][$key]->user_id, "type" => "full" ) ).'</a>';
						}
						?>
						<a href="<?php echo sz_group_permalink(); ?>members/" class="mini-ellipsis">...</a>
					</div>
					<?php endif; ?>
					
					<?php sz_nouveau_groups_loop_item(); ?>
					<?php sz_nouveau_groups_loop_buttons(); ?>
					
					
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
		</li>

	<?php endwhile; ?>

	</ul>

	<?php sz_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php sz_nouveau_user_feedback( 'groups-loop-none' ); ?>

<?php endif; ?>

<?php
sz_nouveau_after_loop();
