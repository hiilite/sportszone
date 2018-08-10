<?php
/**
 * SportsZone - Users Cover Image Header
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<div id="cover-image-container">
	<div id="header-cover-image"></div>

	<div id="item-header-cover-image">
		<div id="item-header-avatar">
			<a href="<?php sz_displayed_user_link(); ?>">

				<?php sz_displayed_user_avatar( 'type=full' ); ?>

			</a>
		</div><!-- #item-header-avatar -->

		<div id="item-header-content">

			<?php if ( sz_is_active( 'activity' ) && sz_activity_do_mentions() ) : ?>
				<h2 class="user-nicename">@<?php sz_displayed_user_mentionname(); ?></h2>
			<?php endif; ?>

			<?php
			sz_nouveau_member_header_buttons(
				array(
					'container'         => 'ul',
					'button_element'    => 'button',
					'container_classes' => array( 'member-header-actions' ),
				)
			);
?>

			<?php sz_nouveau_member_hook( 'before', 'header_meta' ); ?>

			<?php if ( sz_nouveau_member_has_meta() ) : ?>
				<div class="item-meta">

					<?php sz_nouveau_member_meta(); ?>

				</div><!-- #item-meta -->
			<?php endif; ?>

		</div><!-- #item-header-content -->

	</div><!-- #item-header-cover-image -->
</div><!-- #cover-image-container -->
