<?php
/**
 * SportsZone - Groups Admin
 *
 * @since 3.0.0
 * @version 3.0.0
 */

sz_get_template_part( 'groups/single/parts/admin-subnav' ); ?>

<form action="<?php sz_group_admin_form_action(); ?>" name="group-settings-form" id="group-settings-form" class="standard-form" method="post" enctype="multipart/form-data">

	<?php sz_nouveau_group_manage_screen(); ?>

</form><!-- #group-settings-form -->

