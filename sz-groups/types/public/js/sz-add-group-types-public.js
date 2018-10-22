(function( $ ) {
	'use strict';
	var group_all_clicked = true;

	var object = 'groups';// wbcom_agt_sz_filter_request
	$.cookie(
		'sz-' + object + '-extras', '', {
			path: '/'
		}
	);

	$( document ).on(
		'change', 'select.szgt-groups-search-group-type', function(){
			//alert('trigger');
			if( !group_all_clicked ) {
				return;
			}
			sz_filter_request(
				object,
				jq.cookie( 'sz-' + object + '-filter' ),
				jq.cookie( 'sz-' + object + '-scope' ),
				'div.' + object,
				$( '#' + object + '_search' ).val(),// ( '#szgt-groups-search-text' ).val(),
				1,
				'group_type=' + $( this ).val(),
				'',
				''
			);
		}
	);

	// Submit group search
	$( document ).on(
		'click', '#szgt-groups-search-submit', function(){
			return;
			var object = 'groups';// wbcom_agt_sz_filter_request.
			sz_filter_request(
				object,
				jq.cookie( 'sz-' + object + '-filter' ),
				jq.cookie( 'sz-' + object + '-scope' ),
				'div.' + object,
				$( '#szgt-groups-search-text' ).val(),
				1,
				'group_type=' + $( '#szgt-groups-search-group-type' ).val(),
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
				if ( $( '#szgt-groups-search-text' ).val() == '' && $( '#szgt-groups-search-group-type' ).val() == '' ) {
					process_search = false;
				} else {
					var data = {
						'action'		: 'szgt_search_groups',
						'search_type'	: search_type,
						'search_text'	: $( '#szgt-groups-search-text' ).val(),
						'group_type'	: $( '#szgt-groups-search-group-type' ).val()
					}
				}

			}

			if ( search_type == 'select' ) {
				process_search = true;
				var data = {
					'action'		: 'szgt_search_groups',
					'search_type'	: search_type,
					'group_type'	: $( '#szgt-groups-search-group-type' ).val()
				}
			}

			if ( process_search == true ) {
				$.ajax(
					{
						dataType: "JSON",
						url: szgt_front_js_object.ajaxurl,
						type: 'POST',
						data: data,
						success: function( response ) {
							//console.log( response );
							$( '#groups-dir-list' ).html( response['data']['groups_html'] );
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
		$( '.item-list-tabs li' ).each(
			function() {
					jq( this ).removeClass( 'selected' );
			}
		);
		$( '#' + object + '-' + scope + ', #object-nav li.current' ).addClass( 'selected' );
		$( '.item-list-tabs li.selected' ).addClass( 'loading' );
		$( '.item-list-tabs select option[value="' + filter + '"]' ).prop( 'selected', true );
		if ( 'friends' === object || 'group_members' === object ) {
			object = 'members';
		}
		if ( sz_ajax_request ) {
			sz_ajax_request.abort();
		}
		sz_ajax_request = jq.post(
			ajaxurl, {
				action: object + '_filter',
					// 'cookie': sz_get_cookies(),
				'object': object,
				'filter': filter,
				'search_terms': search_terms,
				'scope': scope,
				'page': page,
				'extras': extras,
				'template': template
			},
			function(response)
			{
				/* animate to top if called from bottom pagination */
				if ( caller === 'pag-bottom' && jq( '#subnav' ).length ) {
					var top = $( '#subnav' ).parent();
					$( 'html,body' ).animate(
						{scrollTop: top.offset().top}, 'slow', function() {
							$( target ).fadeOut(
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
					$( target ).fadeOut(
						100, function() {
							$( this ).html( response );
							$( this ).fadeIn(
								100, function(){
									/* KLEO added */
									jq( "body" ).trigger( 'gridloaded' );
								}
							);
						}
					);
				}

				$( '.item-list-tabs li.selected' ).removeClass( 'loading' );

			}
		);
	}

})( jQuery );

jQuery(document).ready(function($){
	jQuery( '.szgt-type-tab' ).on('click', function(){
			group_all_clicked = false;
			jQuery('.szgt-groups-search-group-type').val('').trigger('change');
			jQuery('#groups_search').val('').trigger('submit');
	});

	jQuery( '.item-list-tabs #group-all' ).on('click', function(){
			group_all_clicked = true;
	});
});

jQuery(document).ready(function($){
	jQuery( '#groups-types-select' ).on('change', function(){
			var type = jQuery(this).val();
			
			if(type != 'all') {
				window.location.href = "/groups/type/"+type+"/";
			}
			else {
				window.location.href = "/groups/";
			}
	});
});
