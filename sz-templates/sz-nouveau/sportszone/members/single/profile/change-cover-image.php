<?php
/**
 * SportsZone - Members Profile Change Cover Image
 *
 * @since 3.0.0
 * @version 3.1.0
 */

?>
<!-- sportszone > sz-templates > sz-nouveau > sportszone > members > single > profile > change-cover-image -->
<h2 class="screen-heading change-cover-image-screen"><?php esc_html_e( 'Cover Image', 'sportszone' ); ?></h2>

<?php sz_nouveau_member_hook( 'before', 'edit_cover_image' ); ?>

<p class="info sz-feedback">
	<span class="sz-icon" aria-hidden="true"></span>
	<span class="sz-help-text"><?php esc_html_e( 'Your Cover Image will be used to customize the header of your profile.', 'sportszone' ); ?></span>
</p>
<?php
////////////////////////////
//	START NEW CROP SYSTEM
////////////////////////////



////////////////////////////
//	END NEW CROP SYSTEM
////////////////////////////

// Load the cover image UI
sz_attachments_get_template_part( 'cover-images/index' );

sz_nouveau_member_hook( 'after', 'edit_cover_image' );
