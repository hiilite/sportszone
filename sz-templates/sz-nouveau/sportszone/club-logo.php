<?php
/**
 * Team Logo
 *
 * @author 		ThemeBoy
 * @package 	SportsPress/Templates
 * @version     1.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( get_option( 'sportszone_club_show_logo', 'yes' ) === 'no' ) return;

if ( ! isset( $id ) )
	$id = get_the_ID();

if ( has_post_thumbnail( $id ) ):
	?>
	<div class="sp-template sp-template-club-logo sp-template-logo sp-club-logo">
		<?php echo get_the_post_thumbnail( $id, 'sportszone-fit-icon' ); ?>
	</div>
	<?php
endif;