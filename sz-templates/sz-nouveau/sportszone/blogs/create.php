<?php
/**
 * SportsZone - Blogs Create
 *
 * @since 3.0.0
 * @version 3.0.0
 */

sz_nouveau_blogs_create_hook( 'before', 'content_template' ); ?>

<?php sz_nouveau_template_notices(); ?>

<?php sz_nouveau_blogs_create_hook( 'before', 'content' ); ?>

<?php if ( sz_blog_signup_enabled() ) : ?>

	<?php sz_show_blog_signup_form(); ?>

<?php
else :

	sz_nouveau_user_feedback( 'blogs-no-signup' );

endif;
?>

<?php
sz_nouveau_blogs_create_hook( 'after', 'content' );

sz_nouveau_blogs_create_hook( 'after', 'content_template' );
