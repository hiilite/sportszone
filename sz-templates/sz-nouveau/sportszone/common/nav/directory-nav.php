<?php
/**
 * BP Nouveau Component's directory nav template.
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<nav class="<?php sz_nouveau_directory_type_navs_class(); ?>" role="navigation" aria-label="<?php esc_attr_e( 'Directory menu', 'sportszone' ); ?>">
	<?php if ( sz_nouveau_has_nav( array( 'object' => 'directory' ) ) ) : ?>
		
		<?php $current_user = wp_get_current_user(); ?>
		<div id="item-nav-avatar">
			<div>
				<a href="/members/<?php echo $current_user->user_nicename; ?>/profile/">
        			<img src="<?php echo esc_url( get_avatar_url( $current_user->ID, array('50') ) ); ?>" />
        		</a>
			</div>
			<div>
        		<a href="/members/<?php echo $current_user->user_nicename; ?>/profile/">
        			<?php echo xprofile_get_field_data(1,sz_loggedin_user_id(),'string'); ?>
        		</a>
			</div>
		</div>

		<ul class="component-navigation <?php sz_nouveau_directory_list_class(); ?>">

			<?php
			while ( sz_nouveau_nav_items() ) :
				sz_nouveau_nav_item();
			?>

				<li id="<?php sz_nouveau_nav_id(); ?>" class="<?php sz_nouveau_nav_classes(); ?>" <?php sz_nouveau_nav_scope(); ?> data-sz-object="<?php sz_nouveau_directory_nav_object(); ?>">
					<a href="<?php sz_nouveau_nav_link(); ?>">
						<?php sz_nouveau_nav_link_text(); ?>

						<?php if ( sz_nouveau_nav_has_count() ) : ?>
							<span class="count badge badge-primary badge-pill"><?php sz_nouveau_nav_count(); ?></span>
						<?php endif; ?>
					</a>
				</li>

			<?php endwhile; ?>

		</ul><!-- .component-navigation -->

	<?php endif; ?>

</nav><!-- .sz-navs -->
