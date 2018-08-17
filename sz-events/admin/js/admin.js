/* global SZ_Event_Admin, event_id, isRtl */

(function($) {
	function add_member_to_list( e, ui ) {
		$('#sz-events-new-members-list').append('<li data-login="' + ui.item.value + '"><a href="#" class="sz-events-remove-new-member">x</a> ' + ui.item.label + '</li>');
	}

	var id = 'undefined' !== typeof event_id ? '&event_id=' + event_id : '';
	$(document).ready( function() {
		window.warn_on_leave = false;

		/* Initialize autocomplete */
		$( '.sz-suggest-user' ).autocomplete({
			source:    ajaxurl + '?action=sz_event_admin_member_autocomplete' + id,
			delay:     500,
			minLength: 2,
			position:  ( 'undefined' !== typeof isRtl && isRtl ) ? { my: 'right top', at: 'right bottom', offset: '0, -1' } : { offset: '0, -1' },
			open:      function() { $(this).addClass('open'); },
			close:     function() { $(this).removeClass('open'); $(this).val(''); },
			select:    function( event, ui ) { add_member_to_list( event, ui ); }
		});

		/* Replace noscript placeholder */
		$( '#sz-events-new-members' ).prop( 'placeholder', SZ_Event_Admin.add_member_placeholder );

		/* Remove a member on 'x' click */
		$( '#sz_event_add_members' ).on( 'click', '.sz-events-remove-new-member', function( e ) {
			e.preventDefault();
			$( e.target.parentNode ).remove();
		} );

		/* Warn before leaving unsaved changes */
		$(document).on( 'change', 'input#sz-events-name, input#sz-events-description, select.sz-events-role, #sz-events-settings-section-status input[type="radio"]', function() {
			window.warn_on_leave = true;
		});

		$( 'input#save' ).on( 'click', function() {
			/* Check for users to add */
			var users_to_add = [];

			$( '#sz-events-new-members-list li' ).each( function() {
				users_to_add.push( $(this).data('login' ) );
			} );

			/* There are users to add, include a comma separated list of users login in the main field */
			if ( users_to_add.length ) {
				$( '#sz-events-new-members' ).val( '' ).val( users_to_add.join( ', ' ) );
			}

			window.warn_on_leave = false;
		});

		window.onbeforeunload = function() {
			if ( window.warn_on_leave ) {
				return SZ_Event_Admin.warn_on_leave;
			}
		};
	});
})(jQuery);
