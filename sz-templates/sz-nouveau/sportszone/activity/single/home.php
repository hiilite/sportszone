<?php
/**
 * SportsZone - Home
 *
 * @version 3.0.0
 */

?>

	<?php sz_nouveau_template_notices(); ?>

	<div class="activity" data-sz-single="<?php echo esc_attr( sz_current_action() ); ?>">

		<ul id="activity-stream" class="activity-list item-list sz-list" data-sz-list="activity">

			<li id="sz-ajax-loader"><?php sz_nouveau_user_feedback( 'single-activity-loading' ); ?></li>

		</ul>

	</div>
