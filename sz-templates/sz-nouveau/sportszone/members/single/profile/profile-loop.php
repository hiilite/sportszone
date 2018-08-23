<?php
/**
 * SportsZone - Members Profile Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

?>

<h2 class="screen-heading view-profile-screen"><?php esc_html_e( 'View Profile', 'sportszone' ); ?></h2>

<?php sz_nouveau_xprofile_hook( 'before', 'loop_content' ); 
	
/*
 *	Profile Tabs Navigation
 *
 */
if ( sz_has_profile() ) :
	echo "<div class='sz-tabs'>
		<ul class='sz-tab-navigation nav nav-pills' role='tablist'>";
	$tab_index = 0;
	while ( sz_profile_groups() ) : 
		sz_the_profile_group();
		
		$tab_id = "sz-".sz_get_the_profile_group_slug()."-profile";
		$active = ($tab_index == 0)?'active':'';
		if ( sz_profile_group_has_fields() ) :
			echo '<li class="nav-item">
					<a href="#'.$tab_id.'" data-tab-index="'.$tab_index.'" class="nav-link '.$active.'" data-toggle="pill" role="tab" aria-controls="'.$tab_id.'" aria-selected="true">'.sz_get_the_profile_group_name().' Profile</a>
				</li>';
			$tab_index++;
		endif;
	endwhile;
	echo '</ul>';

/*
 *	Profile Content Tabs
 *
 */
?>
	<div class="tab-content"> 
	<?php
	
	
	$tab_index = 0;
	while ( sz_profile_groups() ) : 
		sz_the_profile_group();
		
		$tab_id = "sz-".sz_get_the_profile_group_slug()."-profile";
		if ( sz_profile_group_has_fields() ) :
		
			sz_nouveau_xprofile_hook( 'before', 'field_content' ); ?>

			<div id="<?php echo $tab_id; ?>" class="sz-widget tab-pane fade <?php echo ($tab_index == 0)?'show active ':''; ?>" role="tabpanel" aria-labelledby="<?php echo $tab_id; ?>">

				<h3 class="screen-heading profile-group-title">
					<?php sz_the_profile_group_name(); ?>
					
				</h3>

				<table class="profile-fields sz-tables-user">

					<?php
					while ( sz_profile_fields() ) :
						sz_the_profile_field();
					?>

						<?php if ( sz_field_has_data() ) : ?>

							<tr<?php sz_field_css_class(); ?>>

								<td class="label"><?php sz_the_profile_field_name(); ?></td>

								<td class="data"><?php sz_the_profile_field_value(); ?></td>

							</tr>

						<?php endif; ?>

						<?php sz_nouveau_xprofile_hook( '', 'field_item' ); ?>

					<?php endwhile; ?>

				</table>
				
				<?php 
				/*
				 * Display all groups user is a member of by type
				 *
				 */
				$teams = $unions = $clubs = $organizatons = $societies = $resources = $sponsors = array();
				$group_ids = groups_get_user_groups(sz_displayed_user_id());
				foreach($group_ids["groups"] as $group_id) { 
					//echo (groups_get_group(array( 'group_id' => $group_id )) -> name . (end($group_ids["groups"]) == $group_id ? '' : ', ' ) ); 
					
					$group_type = sz_groups_get_group_type($group_id);
					switch($group_type):
						case 'team':
							$teams[] = $group_id;
						break;
						case 'club':
							$clubs[] = $group_id;
						break;
						case 'union':
							$unions[] = $group_id;
						break;
						case 'organization':
							$organizatons[] = $group_id;
						break;
						case 'societies':
							$societies[] = $group_id;
						break;
						case 'resourses':
							$resources[] = $group_id;
						break;
						case 'sponsors':
							$sponsors[] = $group_id;
						break;
					endswitch;
				}
				
				if(count($teams) > 0): 
				?>
				<h2>Teams</h2>
				<div class="card-deck">
					<?php 
					foreach($teams as $group_id){
						$group = groups_get_group( array('group_id' => $group_id) );
						$avatar = sz_core_fetch_avatar(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
						$cover_image = sz_core_fetch_cover_image(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
						echo "<div class='card'>
							<div class='card-body'>$avatar
								<h5 class='card-title'>".sz_get_group_name($group)."</h5>
							</div>
						</div>";		
					}
					?>
				</div>
				<?php 
				endif;
				if(count($clubs) > 0): ?>
					<h2>Clubs</h2>
					<div class="card-deck">
						<?php 
						foreach($clubs as $group_id){
							$group = groups_get_group( array('group_id' => $group_id) );
							$avatar = sz_core_fetch_avatar(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
							$cover_image = sz_core_fetch_cover_image(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
							echo "<div class='card'>
								<div class='card-body'>$cover_image
									<h5 class='card-title'>".sz_get_group_name($group)."</h5>
								</div>
							</div>";		
						}
						?>
					</div>
				<?php 
				endif;
				if(count($unions) > 0): ?>
					<h2>Unions</h2>
					<div class="card-deck">
						<?php 
						foreach($unions as $group_id){
							$group = groups_get_group( array('group_id' => $group_id) );
							$avatar = sz_core_fetch_avatar(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
							$cover_image = sz_core_fetch_cover_image(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
							echo "<div class='card'>
								<div class='card-body'>$cover_image
									<h5 class='card-title'>".sz_get_group_name($group)."</h5>
								</div>
							</div>";		
						}
						?>
					</div>
				<?php 
				endif;
				if(count($organizatons) > 0): ?>
					<h2>Organizations</h2>
					<div class="card-deck">
						<?php 
						foreach($organizatons as $group_id){
							$group = groups_get_group( array('group_id' => $group_id) );
							$avatar = sz_core_fetch_avatar(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
							$cover_image = sz_core_fetch_cover_image(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
							echo "<div class='card'>
								<div class='card-body'>$cover_image
									<h5 class='card-title'>".sz_get_group_name($group)."</h5>
								</div>
							</div>";		
						}
						?>
					</div>
				<?php 
				endif;
				if(count($societies) > 0): ?>
					<h2>Societies</h2>
					<div class="card-deck">
						<?php 
						foreach($societies as $group_id){
							$group = groups_get_group( array('group_id' => $group_id) );
							$avatar = sz_core_fetch_avatar(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
							$cover_image = sz_core_fetch_cover_image(array( 'item_id' => $group_id, 'object'=>'group', 'class'=>'card-img-top'));
							echo "<div class='card'>
								<div class='card-body'>$cover_image
									<h5 class='card-title'>".sz_get_group_name($group)."</h5>
								</div>
							</div>";		
						}
						?>
					</div>
				<?php 
				endif;	
				?>
				<div class="input-group mb-3">
					<input type="url" value="<?php echo get_bloginfo('url').$_SERVER['REQUEST_URI']."#".$tab_id; ?>" id="<?php echo $tab_id."-link-input"; ?>" class="form-control">
					<div class="input-group-append">
						<button class="btn" onclick="copyLink('<?php echo $tab_id."-link-input"; ?>')">Copy Link</button>
					</div>
				</div>
				
			</div>

			<?php sz_nouveau_xprofile_hook( 'after', 'field_content' );
				
		endif;
		$tab_index++;
	endwhile;
	echo "</div>";
	sz_nouveau_xprofile_hook( '', 'field_buttons' );
	echo "</div>";
endif; 
	
sz_nouveau_xprofile_hook( 'after', 'loop_content' );
