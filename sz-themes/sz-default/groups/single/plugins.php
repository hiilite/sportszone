<?php get_header( 'sportszone' ); ?>

	<div id="content">
		<div class="padder">
			<?php if ( sz_has_groups() ) : while ( sz_groups() ) : sz_the_group(); ?>

			<?php do_action( 'sz_before_group_plugin_template' ); ?>

			<div id="item-header">
				<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>
						<?php sz_get_options_nav(); ?>

						<?php do_action( 'sz_group_plugin_options_nav' ); ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body">

				<?php do_action( 'sz_before_group_body' ); ?>

				<?php do_action( 'sz_template_content' ); ?>

				<?php do_action( 'sz_after_group_body' ); ?>
			</div><!-- #item-body -->

			<?php do_action( 'sz_after_group_plugin_template' ); ?>

			<?php endwhile; endif; ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar( 'sportszone' ); ?>

<?php get_footer( 'sportszone' ); ?>