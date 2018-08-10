/**
 * Loads for SportsZone Hello in wp-admin for query string `hello=sportszone`.
 *
 * @since 3.0.0
 */
(function() {
	/**
	 * Open the SportsZone Hello modal.
	 */
	var sz_hello_open_modal = function() {
		var backdrop = document.getElementById( 'sz-hello-backdrop' ),
			modal = document.getElementById( 'sz-hello-container' );

		document.body.classList.add( 'sz-disable-scroll' );

		// Show modal and overlay.
		backdrop.style.display = '';
		modal.style.display    = '';

		// Focus the "X" so sz_hello_handle_keyboard_events() works.
		var focus_target = modal.querySelectorAll( 'a[href], button' );
		focus_target     = Array.prototype.slice.call( focus_target );
		focus_target[0].focus();

		// Events.
		modal.addEventListener( 'keydown', sz_hello_handle_keyboard_events );
		backdrop.addEventListener( 'click', sz_hello_close_modal );
	};

	/**
	 * Close modal if "X" or background is touched.
	 *
	 * @param {Event} event - A click event.
	 */
	document.addEventListener( 'click', function( event ) {
		var backdrop = document.getElementById( 'sz-hello-backdrop' );
		if ( ! backdrop || ! document.getElementById( 'sz-hello-container' ) ) {
			return;
		}

		var backdrop_click    = backdrop.contains( event.target ),
			modal_close_click = event.target.classList.contains( 'close-modal' );

		if ( ! modal_close_click && ! backdrop_click ) {
			return;
		}

		sz_hello_close_modal();
	}, false );

	/**
	 * Close the Hello modal.
	 */
	var sz_hello_close_modal = function() {
		var backdrop = document.getElementById( 'sz-hello-backdrop' ),
			modal = document.getElementById( 'sz-hello-container' );

		document.body.classList.remove( 'sz-disable-scroll' );

		// Remove modal and overlay.
		modal.parentNode.removeChild( modal );
		backdrop.parentNode.removeChild( backdrop );
	};

	/**
	 * Restrict keyboard focus to elements within the SportsZone Hello modal.
	 *
	 * @param {Event} event - A keyboard focus event.
	 */
	var sz_hello_handle_keyboard_events = function( event ) {
		var modal = document.getElementById( 'sz-hello-container' ),
			focus_targets = Array.prototype.slice.call(
				modal.querySelectorAll( 'a[href], button' )
			),
			first_tab_stop = focus_targets[0],
			last_tab_stop  = focus_targets[ focus_targets.length - 1 ];

		// Check for TAB key press.
		if ( event.keyCode !== 9 ) {
			return;
		}

		// When SHIFT+TAB on first tab stop, go to last tab stop in modal.
		if ( event.shiftKey && document.activeElement === first_tab_stop ) {
			event.preventDefault();
			last_tab_stop.focus();

		// When TAB reaches last tab stop, go to first tab stop in modal.
		} else if ( document.activeElement === last_tab_stop ) {
			event.preventDefault();
			first_tab_stop.focus();
		}
	};

	/**
	 * Close modal if escape key is presssed.
	 *
	 * @param {Event} event - A keyboard focus event.
	 */
	document.addEventListener( 'keyup', function( event ) {
		if ( event.keyCode === 27 ) {
			if ( ! document.getElementById( 'sz-hello-backdrop' ) || ! document.getElementById( 'sz-hello-container' ) ) {
				return;
			}

			sz_hello_close_modal();
		}
	}, false );

	// Init modal after the screen's loaded.
	if ( document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading' ) {
		sz_hello_open_modal();
	} else {
		document.addEventListener( 'DOMContentLoaded', sz_hello_open_modal );
	}
}());
