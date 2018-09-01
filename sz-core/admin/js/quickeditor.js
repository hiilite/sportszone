(function($) {

	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );

		// now we take care of our business

		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}

		if ( $post_id > 0 ) {
			// define the edit row
			var $edit_row = $( '#edit-' + $post_id );
			var $post_row = $( '#post-' + $post_id );

			// get the data
			var $number = $( '.column-sz_number', $post_row ).text();
			var $current_teams = String( $( '.column-sz_team', $post_row ).find( '.sz-player-teams' ).data( 'current-teams' ) ).split(',');
			var $past_teams = String( $( '.column-sz_team', $post_row ).find( '.sz-player-teams' ).data( 'past-teams' ) ).split(',');

			// populate the data
			$( ':input[name="sz_number"]', $edit_row ).val( $number );
			$( ':input[name="sz_current_team[]"]', $edit_row ).each(function() {
				$(this).prop("checked", ($.inArray($(this).val(), $current_teams ) != -1));
			});
			$( ':input[name="sz_past_team[]"]', $edit_row ).each(function() {
				$(this).prop("checked", ($.inArray($(this).val(), $past_teams ) != -1));
			});
		}
	};

	$( document ).on( 'click', '#bulk_edit', function() {
		// define the bulk edit row
		var $bulk_row = $( '#bulk-edit' );

		// get the selected post ids that are being edited
		var $post_ids = new Array();
		$bulk_row.find( '#bulk-titles' ).children().each( function() {
			$post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});

		// get the data
		var $current_teams = [];
		$bulk_row.find( 'input[name="sz_current_team[]"]:checked' ).each(function() {
			$current_teams.push( $(this).val() );
		});

		var $past_teams = [];
		$bulk_row.find( 'input[name="sz_past_team[]"]:checked' ).each(function() {
			$past_teams.push( $(this).val() );
		});

		// save the data
		$.ajax({
			url: ajaxurl, // this is a variable that WordPress has already defined for us
			type: 'POST',
			async: false,
			cache: false,
			data: {
				action: 'save_bulk_edit_sz_player',
				post_ids: $post_ids,
				current_teams: $current_teams,
				past_teams: $past_teams,
				nonce: $("#sz_player_edit_nonce").val()
			}
		});
	});

})(jQuery);