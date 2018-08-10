<?php
/**
 * SportsZone - Home
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>
<div id="sportszone">

	<div id="template-notices" role="alert" aria-atomic="true">
		<?php

		/** This action is documented in sz-templates/sz-legacy/sportszone/activity/index.php */
		do_action( 'template_notices' ); ?>

	</div>

	<div class="activity no-ajax">
		<?php if ( sz_has_activities( 'display_comments=threaded&show_hidden=true&include=' . sz_current_action() ) ) : ?>

			<ul id="activity-stream" class="activity-list item-list">
			<?php while ( sz_activities() ) : sz_the_activity(); ?>

				<?php sz_get_template_part( 'activity/entry' ); ?>

			<?php endwhile; ?>
			</ul>

		<?php endif; ?>
	</div>
</div>
