<?php
/**
 * Venue Map
 *
 * @author      ThemeBoy
 * @package     SportsPress/Templates
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! isset( $meta ) )
    return;

$address = sz_array_value( $meta, 'sz_address', null );
$address = urlencode( $address );
$latitude = sz_array_value( $meta, 'sz_latitude', null );
$longitude = sz_array_value( $meta, 'sz_longitude', null );
$zoom = get_option( 'sportszone_map_zoom', 15 );
$maptype = get_option( 'sportszone_map_type', 'roadmap' );
$maptype = strtolower( $maptype );

if ( '' === $address ) $address = '+';
if ( 'satellite' !== $maptype ) $maptype = 'roadmap';

if ( $latitude != null && $longitude != null ):
    ?>
    <iframe
      class="sp-google-map<?php if ( is_tax( 'sz_venue' ) ): ?> sp-venue-map<?php endif; ?>"
      width="600"
      height="320"
      frameborder="0" style="border:0"
      src="https://www.google.com/maps/embed/v1/search?key=AIzaSyAWyt_AG0k_Pgz4LuegtHwesA_OMRnSSAE&amp;q=<?php echo $address; ?>&amp;center=<?php echo $latitude; ?>,<?php echo $longitude; ?>&amp;zoom=<?php echo $zoom; ?>&amp;maptype=<?php echo $maptype; ?>" allowfullscreen>
    </iframe>
    <?php
endif;
