(function ( $ ) {
	"use strict";

	var fetching_child_events = false;

	$( document ).ready( function() {
		// Enable the "show tree view" toggle only when loading the "all events" view.
		$( "#hgsz-enable-tree-view-container input" ).prop( "disabled", $( "#events-personal" ).hasClass( "selected" ) );

		/*
		 * Hide the "show tree view" toggle when switching away from the "all events" view.
		 * Using a very targeted MutationObserver seems like the best bet for now.
		 * Worst-case scenario if this code isn't supported is that user sees toggle that
		 * is ignored on the "my events" view, so not too bad.
		 */
		var $directory_nav_item = document.getElementById( "events-all" );
		if ( $directory_nav_item !== null && window.MutationObserver ) {
			var observer = new MutationObserver( function( mutations ) {
				mutations.forEach( function( mutation ) {
					if ( mutation.attributeName === "class" ) {
						$( "#hgsz-enable-tree-view-container input" ).prop( "disabled", $( "#events-personal" ).hasClass( "selected" ) );
					}
				} );
			} );
			observer.observe( $directory_nav_item,  {
				attributes: true,
				childList: false,
				characterData: false,
				subtree: false,
				attributeFilter: ['class']
			} );
		}

		/*
		 * Expand folders to show contents on click.
		 * Contents are fetched via an AJAX request.
		 */
		$( "#sportszone" ).on( "click", ".toggle-child-events", function( e ) {
			e.preventDefault();

			// Show or hide the child events div.
			toggle_results_pane( $( this ) );

			// Send for the results.
			fetch_child_events( $( this ) );
		} );

		// Refresh events list when the "use tree view" toggle is clicked.
		$( "#sportszone" ).on( "change", "#hgsz-enable-tree-view", function( e ) {
			send_filter_request( $( this ) );
		} );
	} );

	/*
	 * Refresh events list when the "use tree view" toggle is clicked.
	 */
	function send_filter_request( input ) {
		var checked      = input.prop( "checked" ) ? 1 : 0,
			filter       = $( "select#events-order-by" ).val(),
			search_terms = "";

		$.cookie( "sz-events-use-tree-view", checked, { path: "/" } );

		if ( $(".dir-search input").length ) {
			search_terms = $(".dir-search input").val();
		}

		sz_filter_request( "events", filter, "filter", "div.events", search_terms, 1, $.cookie( "sz-event-extras" ) );

		return false;
	}

	/**
	 * Toggle the child events pane and indicators.
	 */
	function toggle_results_pane( anchor ) {
		// Toggle the child events pane and open indicator.
		anchor.siblings( ".child-events" ).toggleClass( "open" );
		anchor.toggleClass( "open" );

		// Update the aria-expanded attribute on the related control.
		anchor.attr( "aria-expanded", anchor.siblings( ".child-events" ).hasClass( "open" ) );
	}

	/**
	 * Fetch the child events of a event,
	 * if the container isn't already populated.
	 */
	function fetch_child_events( anchor ) {
		var target = anchor.closest( ".child-events-container" ).find( ".child-events" ).first();

		// If the folder content has already been populated, do nothing.
		if ( $.trim( target.text() ).length ) {
			return;
		}

		// Do not continue if we are currently fetching a set of results.
		if ( fetching_child_events !== false ) {
			return;
		}
		fetching_child_events = true;

		// Show a loading indicator.
		target.addClass( "loading" );

		// Make the AJAX request and populate the list.
		$.ajax({
			url: ajaxurl,
			type: "GET",
			data: {
				parent_id: anchor.data( "event-id" ),
				action: "hgsz_get_child_events",
			},
			success: function( response ) {
				/*
				 * Upon success, flow the html into the target container.
				 * Also fire an event so other javascript can respond if needed, like
				 * jQuery( ".child-events" ).on( "childEventsContainerPopulated", function(){ console.log( "doing something" ); });
				 */
				$( target ).html( response ).trigger( "childEventsContainerPopulated" );
			}
		})
		.done( function( response ) {
			fetching_child_events = false;
			target.removeClass( "loading" );
		});

	}

}(jQuery));