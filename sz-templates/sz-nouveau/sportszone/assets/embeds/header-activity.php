<?php
/**
 * @version 3.0.0
 */
?>
<div id="sz-embed-header">
	<div class="sz-embed-avatar">
		<a href="<?php sz_displayed_user_link(); ?>">
			<?php sz_displayed_user_avatar( 'type=thumb&width=45&height=45' ); ?>
		</a>
	</div>

	<?php if ( sz_activity_embed_has_activity( sz_current_action() ) ) : ?>

		<?php
		while ( sz_activities() ) :
			sz_the_activity();
		?>
			<p class="sz-embed-activity-action">
				<?php sz_activity_action( array( 'no_timestamp' => true ) ); ?>
			</p>
		<?php endwhile; ?>

	<?php endif; ?>

	<p class="sz-embed-header-meta">
		<?php if ( sz_is_active( 'activity' ) && sz_activity_do_mentions() ) : ?>
			<span class="sz-embed-mentionname">@<?php sz_displayed_user_mentionname(); ?> &middot; </span>
		<?php endif; ?>

		<span class="sz-embed-timestamp"><a href="<?php sz_activity_thread_permalink(); ?>"><?php echo date_i18n( get_option( 'time_format' ) . ' - ' . get_option( 'date_format' ), strtotime( sz_get_activity_date_recorded() ) ); ?></a></span>
	</p>
</div>
