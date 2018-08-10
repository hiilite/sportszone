<?php
/**
 * @version 3.0.0
 */
?>

		<div id="sz-embed-header">
			<div class="sz-embed-avatar">
				<a href="<?php sz_displayed_user_link(); ?>">
					<?php sz_displayed_user_avatar( 'type=thumb&width=36&height=36' ); ?>
				</a>
			</div>

			<p class="wp-embed-heading">
				<a href="<?php sz_displayed_user_link(); ?>">
					<?php sz_displayed_user_fullname(); ?>
				</a>
			</p>

			<?php if ( sz_is_active( 'activity' ) && sz_activity_do_mentions() ) : ?>
				<p class="sz-embed-mentionname">@<?php sz_displayed_user_mentionname(); ?></p>
			<?php endif; ?>
		</div>
