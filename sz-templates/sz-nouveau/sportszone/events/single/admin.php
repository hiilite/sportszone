<?php
/**
 * SportsZone - Events Admin
 *
 * @since 3.0.0
 * @version 3.0.0
 */

sz_get_template_part( 'events/single/parts/admin-subnav' ); ?>

<form action="<?php sz_event_admin_form_action(); ?>" name="event-settings-form" id="event-settings-form" class="standard-form" method="post" enctype="multipart/form-data">

	<?php sz_nouveau_event_manage_screen(); ?>

</form><!-- #event-settings-form -->

