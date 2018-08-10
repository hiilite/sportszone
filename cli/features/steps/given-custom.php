<?php

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode,
    WP_CLI\Process;

$steps->Given( '/^a BP install$/',
	function ( $world ) {
		$world->install_wp();
		$dest_dir = $world->variables['RUN_DIR'] . '/wp-content/plugins/sportszone/';
		if ( ! is_dir( $dest_dir ) ) {
			mkdir( $dest_dir );
		}

		$sz_src_dir = getenv( 'SZ_SRC_DIR' );
		if ( ! is_dir( $sz_src_dir ) ) {
			throw new Exception( 'SportsZone not found in SZ_SRC_DIR' );
		}

		try {
			$world->copy_dir( $sz_src_dir, $dest_dir );
			$world->proc( 'wp plugin activate sportszone' )->run_check();

			$components = array( 'friends', 'groups', 'xprofile', 'activity', 'messages' );
			foreach ( $components as $component ) {
				$world->proc( "wp bp component activate $component" )->run_check();
			}
		} catch ( Exception $e ) {};
	}
);
