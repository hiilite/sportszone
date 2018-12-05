<?php
/**
 * BP Nouveau Default group's front template.
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<div class="group-front-page">
	<?php if ( ! is_active_sidebar( 'sidebar-sportszone-groups' ) || ! sz_nouveau_groups_do_group_boxes() ) : ?>
		<?php if ( ! is_customize_preview() && sz_current_user_can( 'sz_moderate' ) ) : ?>

			<div class="sz-feedback custom-homepage-info info no-icon">
				<strong><?php //esc_html_e( 'Manage the Groups default front page', 'sportszone' ); ?></strong>
				<?php echo '<h4>'.__('About').'</h4>'; ?>
				<p>
					<?php sz_group_description(); ?>
				<?php
				printf(
					//esc_html__( 'You can set your preferences for the %1$s or add %2$s to it.', 'sportszone' ),
					sz_nouveau_groups_get_customizer_option_link(),
					sz_nouveau_groups_get_customizer_widgets_link()
				);
				?>
				</p>
				
				<?php	
				$country = groups_get_groupmeta( sz_get_group_id(), 'sz_group_country');
				$province = groups_get_groupmeta( sz_get_group_id(), 'sz_group_province');
				
				$email = groups_get_groupmeta( sz_get_group_id(), 'sz_group_email');
				$website = groups_get_groupmeta( sz_get_group_id(), 'sz_group_website');
				
				$facebook = groups_get_groupmeta( sz_get_group_id(), 'sz_group_facebook');
				$twitter = groups_get_groupmeta( sz_get_group_id(), 'sz_group_twitter');
				//echo '<pre>'.print_r(groups_get_groupmeta( sz_get_group_id()),true).'</pre>';
				
				if((isset($province['province']) && $province['province'] != '') || (isset($country['country']) && $country['country'] != '')) {
					echo '<h4>'.__('Location').'</h4>';
					echo '<p>';
				}
				if(isset($province['province']) && $province['province'] != '') {
					echo $province['province'];
				}
				if(isset($country['country']) && $country['country'] != '') {
					echo ', '.$country['country'];
				}
				if((isset($province['province']) && $province['province'] != '') || (isset($country['country']) && $country['country'] != '')) {
					echo '</p>';
				}
				
				if((isset($email) && $email != '') || (isset($website) && $website != '')) {
					echo '<h4>'.__('Contact').'</h4>';
				}
				if(isset($email) && $email != '') {
					echo '<p><a href="mailto:'.$email.'">'.$email.'</a></p>';
				}
				if(isset($website) && $website != '') {
					echo '<p><a href="'.$website.'">'.$website.'</a></p>';
				}
				
				if((isset($email) && $email != '') || (isset($website) && $website != '')) {
					echo '<h4>'.__('Social').'</h4>';
				}
				if(isset($facebook) && $facebook != '') {
					echo '<a href="'.$facebook.'" class="group-social"><i class="fa fa-facebook-square"></i></a>';
				}
				if(isset($twitter) && $twitter != '') {
					echo '<a href="'.$twitter.'" class="group-social"><i class="fa fa-twitter-square"></i></a>';
				}
				
				
				?>

			</div>

		<?php endif; ?>
	<?php endif; ?>

	<?php if ( sz_nouveau_groups_front_page_description() ) : ?>
		<div class="group-description">

			<?php sz_group_description(); ?>

		</div><!-- .group-description -->
	<?php endif; ?>

	<?php if ( sz_nouveau_groups_do_group_boxes() ) : ?>
		<div class="sz-plugin-widgets">

			<?php sz_custom_group_boxes(); ?>

		</div><!-- .sz-plugin-widgets -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-sportszone-groups' ) ) : ?>
	
		<div id="group-front-widgets" class="sz-sidebar sz-widget-area" role="complementary">

			<?php dynamic_sidebar( 'sidebar-sportszone-groups' ); ?>

		</div><!-- .sz-sidebar.sz-widget-area -->
	<?php endif; ?>

</div>
