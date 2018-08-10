<?php
/**
 * SportsZone - Members Single Profile WP
 *
 * @since 3.0.0
 * @version 3.1.0
 */

sz_nouveau_wp_profile_hooks( 'before' ); ?>

<div class="sz-widget wp-profile">

	<h2 class="screen-heading wp-profile-screen">
		<?php
		if ( sz_is_my_profile() ) {
			esc_html_e( 'My Profile', 'sportszone' );
		} else {
			printf(
				/* Translators: a member's profile, e.g. "Paul's profile". */
				__( "%s's Profile", 'sportszone' ),
				sz_get_displayed_user_fullname()
			);
		}
		?>
	</h2>

	<?php if ( sz_nouveau_has_wp_profile_fields() ) : ?>

		<table class="wp-profile-fields">

			<?php
			while ( sz_nouveau_wp_profile_fields() ) :
				sz_nouveau_wp_profile_field();
			?>

				<tr id="<?php sz_nouveau_wp_profile_field_id(); ?>">
					<td class="label"><?php sz_nouveau_wp_profile_field_label(); ?></td>
					<td class="data"><?php sz_nouveau_wp_profile_field_data(); ?></td>
				</tr>

			<?php endwhile; ?>

		</table>

	<?php else : ?>

		<?php sz_nouveau_user_feedback( 'member-wp-profile-none' ); ?>

	<?php endif; ?>

</div>

<?php
sz_nouveau_wp_profile_hooks( 'after' );

