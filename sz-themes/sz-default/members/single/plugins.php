<?php

/**
 * SportsZone - Users Plugins
 *
 * This is a fallback file that external plugins can use if the template they
 * need is not installed in the current theme. Use the actions in this template
 * to output everything your plugin needs.
 *
 * @package SportsZone
 * @subpackage sz-default
 */

?>

<?php get_header( 'sportszone' ); ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'sz_before_member_plugin_template' ); ?>

			<div id="item-header">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php sz_get_displayed_user_nav(); ?>

						<?php do_action( 'sz_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body" role="main">

				<?php do_action( 'sz_before_member_body' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php sz_get_options_nav(); ?>

						<?php do_action( 'sz_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->

				<h3><?php do_action( 'sz_template_title' ); ?></h3>

				<?php do_action( 'sz_template_content' ); ?>

				<?php do_action( 'sz_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'sz_after_member_plugin_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_sidebar( 'sportszone' ); ?>
<?php get_footer( 'sportszone' ); ?>
