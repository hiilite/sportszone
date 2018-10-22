<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div id="message" class="error sportszone-message">
	<p><?php _e( '<strong>Your theme does not declare SportsPress support</strong> &#8211; if you encounter layout issues please read our integration guide or choose a SportsPress theme :)', 'sportszone' ); ?></p>
	<p><?php _e( 'Have you tried the free Rookie theme yet?', 'sportszone' ); ?></p>
	<p class="submit">
		<a class="button-primary" href="<?php echo add_query_arg( array( 'theme' => 'rookie' ), network_admin_url( 'theme-install.php' ) ); ?>"><?php _e( 'Install Now', 'sportszone' ); ?></a>
		<a class="button-secondary" href="http://tboy.co/integration"><?php _e( 'Theme Integration Guide', 'sportszone' ); ?></a>
		<a class="button" href="<?php echo add_query_arg( 'hide_theme_support_notice', 'true' ); ?>"><?php _e( 'Hide this notice', 'sportszone' ); ?></a>
	</p>
</div>