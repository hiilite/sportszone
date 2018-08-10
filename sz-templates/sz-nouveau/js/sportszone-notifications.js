/* global bp, SZ_Nouveau */
/* @version 3.0.0 */
window.bp = window.bp || {};

( function( exports, $ ) {

	// Bail if not set
	if ( typeof SZ_Nouveau === 'undefined' ) {
		return;
	}

	bp.Nouveau = bp.Nouveau || {};

	/**
	 * [Activity description]
	 * @type {Object}
	 */
	bp.Nouveau.Notifications = {

		/**
		 * [start description]
		 * @return {[type]} [description]
		 */
		start: function() {
			this.setupGlobals();

			// Listen to events ("Add hooks!")
			this.addListeners();
		},

		/**
		 * [setupGlobals description]
		 * @return {[type]} [description]
		 */
		setupGlobals: function() {
			// Always reset sort to Newest notifications
			bp.Nouveau.setStorage( 'sz-notifications', 'extras', 'DESC' );
		},

		/**
		 * [addListeners description]
		 */
		addListeners: function() {
			// Change the Order actions visibility once the ajax request is done.
			$( '#sportszone [data-sz-list="notifications"]' ).on( 'sz_ajax_request', this.prepareDocument );

			// Trigger Notifications order request.
			$( '#sportszone [data-sz-list="notifications"]' ).on( 'click', '[data-sz-notifications-order]', bp.Nouveau, this.sortNotifications );

			// Enable the Apply Button once the bulk action is selected
			$( '#sportszone [data-sz-list="notifications"]' ).on( 'change', '#notification-select', this.enableBulkSubmit );

			// Select all displayed notifications
			$( '#sportszone [data-sz-list="notifications"]' ).on( 'click', '#select-all-notifications', this.selectAll );

			// Reset The filter before unload
			$( window ).on( 'unload', this.resetFilter );
		},

		/**
		 * [prepareDocument description]
		 * @return {[type]} [description]
		 */
		prepareDocument: function() {
			var store = bp.Nouveau.getStorage( 'sz-notifications' );

			if ( 'ASC' === store.extras ) {
				$( '[data-sz-notifications-order="DESC"]' ).show();
				$( '[data-sz-notifications-order="ASC"]' ).hide();
			} else {
				$( '[data-sz-notifications-order="ASC"]' ).show();
				$( '[data-sz-notifications-order="DESC"]' ).hide();
			}

			// Make sure a 'Bulk Action' is selected before submitting the form
			$( '#notification-bulk-manage' ).prop( 'disabled', 'disabled' );
		},

		/**
		 * [sortNotifications description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		sortNotifications: function( event ) {
			var store = event.data.getStorage( 'sz-notifications' ),
				scope = store.scope || null, filter = store.filter || null,
				sort = store.extra || null, search_terms = '';

			event.preventDefault();

			sort = $( event.currentTarget ).data( 'sz-notifications-order' );
			bp.Nouveau.setStorage( 'sz-notifications', 'extras', sort );

			if ( $( '#sportszone [data-sz-search="notifications"] input[type=search]' ).length ) {
				search_terms = $( '#sportszone [data-sz-search="notifications"] input[type=search]' ).val();
			}

			bp.Nouveau.objectRequest( {
				object              : 'notifications',
				scope               : scope,
				filter              : filter,
				search_terms        : search_terms,
				extras              : sort,
				page                : 1
			} );
		},

		/**
		 * [enableBulkSubmit description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		enableBulkSubmit: function( event ) {
			$( '#notification-bulk-manage' ).prop( 'disabled', $( event.currentTarget ).val().length <= 0 );
		},

		/**
		 * [selectAll description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		selectAll: function( event ) {
			$.each( $( '.notification-check' ), function( cb, checkbox ) {
				$( checkbox ).prop( 'checked', $( event.currentTarget ).prop( 'checked' ) );
			} );
		},

		/**
		 * [resetFilter description]
		 * @return {[type]} [description]
		 */
		resetFilter: function() {
			bp.Nouveau.setStorage( 'sz-notifications', 'filter', 0 );
		}
	};

	// Launch BP Nouveau Notifications
	bp.Nouveau.Notifications.start();

} )( bp, jQuery );
