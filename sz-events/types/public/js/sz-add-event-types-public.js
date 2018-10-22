(function( $ ) {
	'use strict';
	var event_all_clicked = true;

	var object = 'events';// wbcom_agt_sz_filter_request
	$.cookie(
		'sz-' + object + '-extras', '', {
			path: '/'
		}
	);

	$( document ).on(
		'change', 'select.szgt-events-search-event-type', function(){
			//alert('trigger');
			if( !event_all_clicked ) {
				return;
			}
			sz_filter_request(
				object,
				$.cookie( 'sz-' + object + '-filter' ),
				$.cookie( 'sz-' + object + '-scope' ),
				'div.' + object,
				$( '#' + object + '_search' ).val(),// ( '#szgt-events-search-text' ).val(),
				1,
				'event_type=' + $( this ).val(),
				'',
				''
			);
		}
	);

	// Submit event search
	$( document ).on(
		'click', '#szgt-events-search-submit', function(){
			return;
			var object = 'events';// wbcom_agt_sz_filter_request.
			sz_filter_request(
				object,
				$.cookie( 'sz-' + object + '-filter' ),
				$.cookie( 'sz-' + object + '-scope' ),
				'div.' + object,
				$( '#szgt-events-search-text' ).val(),
				1,
				'event_type=' + $( '#szgt-events-search-event-type' ).val(),
				'',
				''
			);
			$.cookie(
				'sz-' + object + '-extras', '', {
					path: '/'
				}
			);

			return;

			var search_type    = $( this ).data( 'search_type' );
			var process_search = true;
			if ( search_type == 'both' ) {
				if ( $( '#szgt-events-search-text' ).val() == '' && $( '#szgt-events-search-event-type' ).val() == '' ) {
					process_search = false;
				} else {
					var data = {
						'action'		: 'szet_search_events',
						'search_type'	: search_type,
						'search_text'	: $( '#szgt-events-search-text' ).val(),
						'event_type'	: $( '#szgt-events-search-event-type' ).val()
					}
				}

			}

			if ( search_type == 'select' ) {
				process_search = true;
				var data = {
					'action'		: 'szet_search_events',
					'search_type'	: search_type,
					'event_type'	: $( '#szgt-events-search-event-type' ).val()
				}
			}

			if ( process_search == true ) {
				$.ajax(
					{
						dataType: "JSON",
						url: szet_front_js_object.ajaxurl,
						type: 'POST',
						data: data,
						success: function( response ) {
							//console.log( response );
							$( '#events-dir-list' ).html( response['data']['events_html'] );
							//console.log( response['data']['message'] );
						},
					}
				);
			}

		}
	);

	function wbcom_agt_sz_filter_request( object, filter, scope, target, search_terms, page, extras, caller, template ) {
		if ( 'activity' === object ) {
			return false;
		}
		if ( null === scope ) {
			scope = 'all';
		}
		/* Set the correct selected nav and filter */
		jq( '.item-list-tabs li' ).each(
			function() {
					jq( this ).removeClass( 'selected' );
			}
		);
		jq( '#' + object + '-' + scope + ', #object-nav li.current' ).addClass( 'selected' );
		jq( '.item-list-tabs li.selected' ).addClass( 'loading' );
		jq( '.item-list-tabs select option[value="' + filter + '"]' ).prop( 'selected', true );
		if ( 'friends' === object || 'event_members' === object ) {
			object = 'members';
		}
		if ( sz_ajax_request ) {
			sz_ajax_request.abort();
		}
		sz_ajax_request = $.post(
			ajaxurl, {
				action: object + '_filter',
					// 'cookie': sz_get_cookies(),
				'object': object,
				'filter': filter,
				'search_terms': search_terms,
				'scope': scope,
				'page': page,
				'extras': extras,
				'template': template,
				'blarg' :	1
			},
			function(response)
			{
				/* animate to top if called from bottom pagination */
				if ( caller === 'pag-bottom' && jq( '#subnav' ).length ) {
					var top = jq( '#subnav' ).parent();
					jq( 'html,body' ).animate(
						{scrollTop: top.offset().top}, 'slow', function() {
							jq( target ).fadeOut(
								100, function() {
									jq( this ).html( response );

									/* KLEO added */
									jq( this ).fadeIn(
										100, function(){
											jq( "body" ).trigger( 'gridloaded' );
										}
									);
								}
							);
						}
					);

				} else {
					jq( target ).fadeOut(
						100, function() {
							jq( this ).html( response );
							jq( this ).fadeIn(
								100, function(){
									/* KLEO added */
									jq( "body" ).trigger( 'gridloaded' );
								}
							);
						}
					);
				}

				jq( '.item-list-tabs li.selected' ).removeClass( 'loading' );

			}
		);
	}

})( jQuery );

jQuery(document).ready(function($){
	jQuery( '.szgt-type-tab' ).on('click', function(){
			event_all_clicked = false;
			jQuery('.szgt-events-search-event-type').val('').trigger('change');
			jQuery('#events_search').val('').trigger('submit');
	});

	jQuery( '.item-list-tabs #event-all' ).on('click', function(){
			event_all_clicked = true;
	});
});

jQuery(document).ready(function($){
	jQuery( '#events-types-select' ).on('change', function(){
			var type = jQuery(this).val();
			
			if(type != 'all') {
				window.location.href = "/events/type/"+type+"/";
			}
			else {
				window.location.href = "/events/";
			}
	});
});
