/* jshint devel: true */
/* global SZ_Confirm */

jQuery( document ).ready( function() {
	jQuery( '#sportszone' ).on( 'click', 'a.confirm', function() {
		if ( confirm( SZ_Confirm.are_you_sure ) ) {
			return true;
		} else {
			return false;
		}
	} );
} );
