<?php
/**
 * SportsZone - Users Header
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<div id="item-header-avatar">
	<a href="<?php sz_displayed_user_link(); ?>">

		<?php sz_displayed_user_avatar( 'type=full' ); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<?php if ( sz_is_active( 'activity' ) && sz_activity_do_mentions() ) : ?>
		<h2 class="user-nicename">@<?php sz_displayed_user_mentionname(); ?></h2>
	<?php endif; ?>

	<?php sz_nouveau_member_hook( 'before', 'header_meta' ); ?>

	<?php if ( sz_nouveau_member_has_meta() ) : ?>
		<div class="item-meta">

			<?php sz_nouveau_member_meta(); ?>

		</div><!-- #item-meta -->
	<?php endif; ?>

	<?php sz_nouveau_member_header_buttons( array( 'container_classes' => array( 'member-header-actions' ) ) ); ?>
</div><!-- #item-header-content -->
