<?php
/**
 * SportsZone - Users Plugins Template
 *
 * 3rd-party plugins should use this template to easily add template
 * support to their plugins for the members component.
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

		/**
		 * Fires at the start of the member plugin template.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_before_member_plugin_template' ); ?>

		<?php if ( ! sz_is_current_component_core() ) : ?>

		<div class="item-list-tabs no-ajax" id="subnav" aria-label="<?php esc_attr_e( 'Member secondary navigation', 'sportszone' ); ?>" role="navigation">
			<ul>
				<?php sz_get_options_nav(); ?>

				<?php

				/**
				 * Fires inside the member plugin template nav <ul> tag.
				 *
				 * @since 1.2.2
				 */
				do_action( 'sz_member_plugin_options_nav' ); ?>
			</ul>
		</div><!-- .item-list-tabs -->

		<?php endif; ?>

		<?php if ( has_action( 'sz_template_title' ) ) : ?>
			<h3><?php

			/**
			 * Fires inside the member plugin template <h3> tag.
			 *
			 * @since 1.0.0
			 */
			do_action( 'sz_template_title' ); ?></h3>

		<?php endif; ?>

		<?php

		/**
		 * Fires and displays the member plugin template content.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sz_template_content' ); ?>

		<?php

		/**
		 * Fires at the end of the member plugin template.
		 *
		 * @since 1.2.0
		 */
		do_action( 'sz_after_member_plugin_template' );
