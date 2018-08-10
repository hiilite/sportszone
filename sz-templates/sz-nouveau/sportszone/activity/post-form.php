<?php
/**
 * SportsZone - Activity Post Form
 *
 * @version 3.1.0
 */

?>

<?php
/*
 * Template tag to prepare the activity post form checks capability and enqueue needed scripts.
 */
sz_nouveau_before_activity_post_form();
?>

<h2 class="sz-screen-reader-text"><?php echo esc_html_x( 'Post Update', 'heading', 'sportszone' ); ?></h2>

<div id="sz-nouveau-activity-form" class="activity-update-form"></div>

<?php
/*
 * Template tag to load the Javascript templates of the Post form UI.
 */
sz_nouveau_after_activity_post_form();
