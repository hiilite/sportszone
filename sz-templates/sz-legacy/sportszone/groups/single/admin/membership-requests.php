<?php
/**
 * SportsZone - Groups Admin - Membership Requests
 *
 * @package SportsZone
 * @subpackage sz-legacy
 * @version 3.0.0
 */

?>

<h2 class="sz-screen-reader-text"><?php _e( 'Manage Membership Requests', 'sportszone' ); ?></h2>

<?php

/**
 * Fires before the display of group membership requests admin.
 *
 * @since 1.1.0
 */
do_action( 'sz_before_group_membership_requests_admin' ); ?>

	<div class="requests">

		<?php sz_get_template_part( 'groups/single/requests-loop' ); ?>

	</div>

<?php

/**
 * Fires after the display of group membership requests admin.
 *
 * @since 1.1.0
 */
do_action( 'sz_after_group_membership_requests_admin' );
