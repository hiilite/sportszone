<?php

do_action( 'sz_before_group_header' );

?>

<div id="item-actions">

	<?php if ( sz_group_is_visible() ) : ?>

		<h3><?php _e( 'Group Admins', 'sportszone' ); ?></h3>

		<?php sz_group_list_admins();

		do_action( 'sz_after_group_menu_admins' );

		if ( sz_group_has_moderators() ) :
			do_action( 'sz_before_group_menu_mods' ); ?>

			<h3><?php _e( 'Group Mods' , 'sportszone' ); ?></h3>

			<?php sz_group_list_mods();

			do_action( 'sz_after_group_menu_mods' );

		endif;

	endif; ?>

</div><!-- #item-actions -->

<div id="item-header-avatar">
	<a href="<?php sz_group_permalink(); ?>" title="<?php sz_group_name(); ?>">

		<?php sz_group_avatar(); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">
	<h2><a href="<?php sz_group_permalink(); ?>" title="<?php sz_group_name(); ?>"><?php sz_group_name(); ?></a></h2>
	<span class="highlight"><?php sz_group_type(); ?></span> <span class="activity"><?php printf( __( 'active %s', 'sportszone' ), sz_get_group_last_active() ); ?></span>

	<?php do_action( 'sz_before_group_header_meta' ); ?>

	<div id="item-meta">

		<?php sz_group_description(); ?>

		<div id="item-buttons">

			<?php do_action( 'sz_group_header_actions' ); ?>

		</div><!-- #item-buttons -->

		<?php do_action( 'sz_group_header_meta' ); ?>

	</div>
</div><!-- #item-header-content -->

<?php
do_action( 'sz_after_group_header' );
do_action( 'template_notices' );
?>