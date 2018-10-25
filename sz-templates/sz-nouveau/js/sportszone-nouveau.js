/* global wp, bp, SZ_Nouveau, JSON */
/* jshint devel: true */
/* jshint browser: true */
/* @version 3.0.0 */
window.wp = window.wp || {};
window.bp = window.bp || {};

function copyLink(link){
	var copyText = document.getElementById(link);
	copyText.select();
	document.execCommand("copy");
	
}

( function( exports, $ ) {

	// Bail if not set
	if ( typeof SZ_Nouveau === 'undefined' ) {
		return;
	}
	
	/**
	 * Activate jQuery-Tabs
	 */
	//$( ".sz-tabs" ).tabs();
	var hash = window.location.hash;
	  hash && $('.sz-tabs ul.nav a[href="' + hash + '"]').tab('show');
	
	  $('.nav-tabs a').click(function (e) {
	    $(this).tab('show');
	    var scrollmem = $('body').scrollTop();
	    window.location.hash = this.hash;
	    $('html,body').scrollTop(scrollmem);
	  });
	
	
	  
	/**
	 * [Nouveau description]
	 * @type {Object}
	 */
	bp.Nouveau = {
		/**
		 * [start description]
		 * @return {[type]} [description]
		 */
		start: function() {

			// Setup globals
			this.setupGlobals();

			// Adjust Document/Forms properties
			this.prepareDocument();

			// Init the SportsZone objects
			this.initObjects();

			// Set SportsZone HeartBeat
			this.setHeartBeat();

			// Listen to events ("Add hooks!")
			this.addListeners();
		},

		/**
		 * [setupGlobals description]
		 * @return {[type]} [description]
		 */
		setupGlobals: function() {
			this.ajax_request           = null;

			// Object Globals
			this.objects                = $.map( SZ_Nouveau.objects, function( value ) { return value; } );
			this.objectNavParent        = SZ_Nouveau.object_nav_parent;

			// HeartBeat Global
			this.heartbeat              = wp.heartbeat || {};

			// An object containing each query var
			this.querystring            = this.getLinkParams();
		},

		/**
		 * [prepareDocument description]
		 * @return {[type]} [description]
		 */
		prepareDocument: function() {

			// Remove the no-js class and add the js one
			if ( $( 'body' ).hasClass( 'no-js' ) ) {
				$('body').removeClass( 'no-js' ).addClass( 'js' );
			}

			// Log Warnings into the console instead of the screen
			if ( SZ_Nouveau.warnings && 'undefined' !== typeof console && console.warn ) {
				$.each( SZ_Nouveau.warnings, function( w, warning ) {
					console.warn( warning );
				} );
			}

			// Remove the directory title if there's a widget containing it
			if ( $( '.sportszone_object_nav .widget-title' ).length ) {
				var text = $( '.sportszone_object_nav .widget-title' ).html();

				$( 'body' ).find( '*:contains("' + text + '")' ).each( function( e, element ) {
					if ( ! $( element ).hasClass( 'widget-title' ) && text === $( element ).html() && ! $( element ).is( 'a' ) ) {
						$( element ).remove();
					}
				} );
			}
		},

		/** Helpers *******************************************************************/

		/**
		 * [getStorage description]
		 * @param  {[type]} type     [description]
		 * @param  {[type]} property [description]
		 * @return {[type]}          [description]
		 */
		getStorage: function( type, property ) {
			var store = sessionStorage.getItem( type );

			if ( store ) {
				store = JSON.parse( store );
			} else {
				store = {};
			}

			if ( undefined !== property ) {
				return store[property] || false;
			}

			return store;
		},

		/**
		 * [setStorage description]
		 * @param {[type]} type     [description]
		 * @param {[type]} property [description]
		 * @param {[type]} value    [description]
		 */
		setStorage: function( type, property, value ) {
			var store = this.getStorage( type );

			if ( undefined === value && undefined !== store[ property ] ) {
				delete store[ property ];
			} else {
				// Set property
				store[ property ] = value;
			}

			sessionStorage.setItem( type, JSON.stringify( store ) );

			return sessionStorage.getItem( type ) !== null;
		},

		/**
		 * [getLinkParams description]
		 * @param  {[type]} url   [description]
		 * @param  {[type]} param [description]
		 * @return {[type]}       [description]
		 */
		getLinkParams: function( url, param ) {
			var qs;
			if ( url ) {
				qs = ( -1 !== url.indexOf( '?' ) ) ? '?' + url.split( '?' )[1] : '';
			} else {
				qs = document.location.search;
			}

			if ( ! qs ) {
				return null;
			}

			var params = qs.replace( /(^\?)/, '' ).split( '&' ).map( function( n ) {
				return n = n.split( '=' ), this[n[0]] = n[1], this;
			}.bind( {} ) )[0];

			if ( param ) {
				return params[param];
			}

			return params;
		},

		/**
		 * [ajax description]
		 * @param  {[type]} post_data [description]
		 * @param  {[type]} object    [description]
		 * @return {[type]}           [description]
		 */
		ajax: function( post_data, object ) {
			if ( this.ajax_request ) {
				this.ajax_request.abort();
			}

			// Extend posted data with stored data and object nonce
			var postData = $.extend( {}, bp.Nouveau.getStorage( 'sz-' + object ), { nonce: SZ_Nouveau.nonces[object] }, post_data );

			if ( undefined !== SZ_Nouveau.customizer_settings ) {
				postData.customized = SZ_Nouveau.customizer_settings;
			}
			console.log(SZ_Nouveau.ajaxurl, postData);
			
			this.ajax_request = $.post( SZ_Nouveau.ajaxurl, postData, 'json' );
			
			return this.ajax_request;
		},

		inject: function( selector, content, method ) {
			if ( ! $( selector ).length || ! content ) {
				return;
			}

			/**
			 * How the content should be injected in the selector
			 *
			 * possible methods are
			 * - reset: the selector will be reset with the content
			 * - append:  the content will be added after selector's content
			 * - prepend: the content will be added before selector's content
			 */
			method = method || 'reset';

			if ( 'append' === method ) {
				$( selector ).append( content );
			} else if ( 'prepend' === method ) {
				$( selector ).prepend( content );
			} else {
				$( selector ).html( content );
			}

			if ( 'undefined' !== typeof sz_mentions || 'undefined' !== typeof bp.mentions ) {
				$( '.sz-suggestions' ).sz_mentions( bp.mentions.users );
			}
		},

		/**
		 * [objectRequest description]
		 * @param  {[type]} data [description]
		 * @return {[type]}      [description]
		 */
		objectRequest: function( data ) {
			var postdata = {}, self = this;

			data = $.extend( {
				object       : '',
				scope        : null,
				filter       : null,
				target       : '#sportszone [data-sz-list]',
				search_terms : '',
				/*loc_country  : '',
				loc_province : '',
				loc_city 	 : '',*/
				page         : 1,
				extras       : null,
				caller       : null,
				template     : null,
				method       : 'reset'
			}, data );

			// Do not request if we don't have the object or the target to inject results into
			if ( ! data.object || ! data.target ) {
				return;
			}

			// Set session's data
			if ( null !== data.scope ) {
				this.setStorage( 'sz-' + data.object, 'scope', data.scope );
			}

			if ( null !== data.filter ) {
				this.setStorage( 'sz-' + data.object, 'filter', data.filter );
			}

			if ( null !== data.extras ) {
				this.setStorage( 'sz-' + data.object, 'extras', data.extras );
			}

			/* Set the correct selected nav and filter */ 
			$( this.objectNavParent + ' [data-sz-object]' ).each( function() {
				$( this ).removeClass( 'selected loading' );
			} );

			$( this.objectNavParent + ' [data-sz-scope="' + data.scope + '"], #object-nav li.current' ).addClass( 'selected loading' );
			$( '#sportszone [data-sz-filter="' + data.object + '"] option[value="' + data.filter + '"]' ).prop( 'selected', true );

			if ( 'friends' === data.object || 'group_members' === data.object ) {
				data.template = data.object;
				data.object   = 'members';
			} else if ( 'group_requests' === data.object ) {
				data.object = 'groups';
				data.template = 'group_requests';
			} else if ( 'friends' === data.object || 'event_members' === data.object ) {
				data.template = data.object;
				data.object   = 'members';
			} else if ( 'event_requests' === data.object ) {
				data.object = 'events';
				data.template = 'event_requests';
			} else if ( 'notifications' === data.object ) {
				data.object = 'members';
				data.template = 'member_notifications';
			}

			postdata = $.extend( {
				action: data.object + '_filter'
			}, data );
			console.log(postdata, data.object);
			return this.ajax( postdata, data.object ).done( function( response ) {
				if ( false === response.success ) {
					return;
				}

				$( self.objectNavParent + ' [data-sz-scope="' + data.scope + '"]' ).removeClass( 'loading' );

				if ( 'reset' !== data.method ) {
					self.inject( data.target, response.data.contents, data.method );

					$( data.target ).trigger( 'sz_ajax_' + data.method, $.extend( data, { response: response.data } ) );
				} else {
					/* animate to top if called from bottom pagination */
					if ( data.caller === 'pag-bottom' && $( '#subnav' ).length ) {
						var top = $('#subnav').parent();
						$( 'html,body' ).animate( { scrollTop: top.offset().top }, 'slow', function() {
							$( data.target ).fadeOut( 100, function() {
								self.inject( this, response.data.contents, data.method );
								$( this ).fadeIn( 100 );

								// Inform other scripts the list of objects has been refreshed.
								$( data.target ).trigger( 'sz_ajax_request', $.extend( data, { response: response.data } ) );
							} );
						} );

					} else {
						$( data.target ).fadeOut( 100, function() {
							self.inject( this, response.data.contents, data.method );
							$( this ).fadeIn( 100 );

							// Inform other scripts the list of objects has been refreshed.
							$( data.target ).trigger( 'sz_ajax_request', $.extend( data, { response: response.data } ) );
						} );
					}
				}
			} );
		},

		/**
		 * [initObjects description]
		 * @return {[type]} [description]
		 */
		initObjects: function() {
			var self = this, objectData = {}, queryData = {}, scope = 'all', search_terms = '', extras = null, filter = null;
			var loc_country = '', loc_province = '', loc_city = '';
			$.each( this.objects, function( o, object ) {
				objectData = self.getStorage( 'sz-' + object );

				if ( undefined !== objectData.scope ) {
					scope = objectData.scope;
				}

				// Notifications always need to start with Newest ones
				if ( undefined !== objectData.extras && 'notifications' !== object ) {
					extras = objectData.extras;
				}

				if (  $( '#sportszone [data-sz-filter="' + object + '"]' ).length ) {
					if ( '-1' !== $( '#sportszone [data-sz-filter="' + object + '"]' ).val() && '0' !== $( '#sportszone [data-sz-filter="' + object + '"]' ).val() ) {
						filter = $( '#sportszone [data-sz-filter="' + object + '"]' ).val();
					} else if ( undefined !== objectData.filter ) {
						filter = objectData.filter,
						$( '#sportszone [data-sz-filter="' + object + '"] option[value="' + filter + '"]' ).prop( 'selected', true );
					}
				}

				if ( $( this.objectNavParent + ' [data-sz-object="' + object + '"]' ).length ) {
					$( this.objectNavParent + ' [data-sz-object="' + object + '"]' ).each( function() {
						$( this ).removeClass( 'selected' );
					} );

					$( this.objectNavParent + ' [data-sz-scope="' + object + '"], #object-nav li.current' ).addClass( 'selected' );
				}

				// Check the querystring to eventually include the search terms
				if ( null !== self.querystring ) {
					if ( undefined !== self.querystring[ object + '_search'] ) {
						search_terms = self.querystring[ object + '_search'];
					} else if ( undefined !== self.querystring.s ) {
						search_terms = self.querystring.s;
					}
					
					/*
					if ( undefined !== self.querystring[ 'dir_' + object + '_loc_province'] ) {
						loc_province = self.querystring[ 'dir_' + object + '_loc_province'];
					} else if ( undefined !== self.querystring.s ) {
						loc_province = self.querystring.s;
					}

					if ( search_terms ) {
						$( '#sportszone [data-sz-search="' + object + '"] input[type=search]' ).val( search_terms );
					}

					// TODO: Change to handle dropdown options
					if ( loc_country) {
						$( '#sportszone [data-sz-search="' + object + '"] input#dir-events-loc_country' ).val( loc_country );
					}
					if ( loc_province ) {
						$( '#sportszone [data-sz-search="' + object + '"] input#dir-events-loc_province' ).val( loc_province );
					}
					if ( loc_city ) {
						$( '#sportszone [data-sz-search="' + object + '"] input#dir-events-loc_city' ).val( loc_city );
					}*/
				}

				
				if ( $( '#sportszone [data-sz-list="' + object + '"]' ).length ) {
					queryData =  {
						object       : object,
						scope        : scope,
						filter       : filter,
						search_terms : search_terms,
						extras       : extras,
						/*loc_country  : loc_country,
						loc_province : loc_province,
						loc_city 	 : loc_city,*/
					};

					// Populate the object list
					self.objectRequest( queryData );
				}
			} );
		},

		/**
		 * [setHeartBeat description]
		 */
		setHeartBeat: function() {
			if ( typeof SZ_Nouveau.pulse === 'undefined' || ! this.heartbeat ) {
				return;
			}

			this.heartbeat.interval( Number( SZ_Nouveau.pulse ) );

			// Extend "send" with SportsZone namespace
			$.fn.extend( {
				'heartbeat-send': function() {
					return this.bind( 'heartbeat-send.sportszone' );
				}
			} );

			// Extend "tick" with SportsZone namespace
			$.fn.extend( {
				'heartbeat-tick': function() {
					return this.bind( 'heartbeat-tick.sportszone' );
				}
			} );
		},

		/** Event Listeners ***********************************************************/

		/**
		 * [addListeners description]
		 */
		 // TODO: Add search by region
		addListeners: function() {
			// Disabled inputs
			$( '[data-sz-disable-input]' ).on( 'change', this.toggleDisabledInput );

			// HeartBeat Send and Receive
			$( document ).on( 'heartbeat-send.sportszone', this.heartbeatSend );
			$( document ).on( 'heartbeat-tick.sportszone', this.heartbeatTick );

			// Refreshing
			$( this.objectNavParent + ' .sz-navs' ).on( 'click', 'a', this, this.scopeQuery );

			// Filtering
			$( '#sportszone [data-sz-filter]' ).on( 'change', this, this.filterQuery );

			// Searching
			$( '#sportszone [data-sz-search]' ).on( 'submit', 'form', this, this.searchQuery );
			$( '#sportszone [data-sz-search] form' ).on( 'search', 'input[type=search]', this.resetSearch );

			// Buttons
			$( '#sportszone [data-sz-list], #sportszone #item-header' ).on( 'click', '[data-sz-btn-action]', this, this.buttonAction );

			// Close notice
			$( '#sportszone [data-sz-close]' ).on( 'click', this, this.closeNotice );

			// Pagination
			$( '#sportszone [data-sz-list]' ).on( 'click', '[data-sz-pagination] a', this, this.paginateAction );
			
			// Custom Radio Buttons
			$( document ).ready( this.customRadio );
			
			// Custom Check Boxes
			$( document ).ready( this.customCheck );
			
			// Custom Select Boxes
			$( document ).ready( this.customSelect );
			
			// Set internal nav menues to display block at correct screen size
			$(window).resize(this.showNavs);
			
			// Toggle internal nav menues
			$('.internal-nav-mobile' ).on( 'click', '', this, this.toggleNavs );
		},

		/** Event Callbacks ***********************************************************/

		/**
		 * [enableDisabledInput description]
		 * @param  {[type]} event [description]
		 * @param  {[type]} data  [description]
		 * @return {[type]}       [description]
		 */
		toggleDisabledInput: function() {

			// Fetch the data attr value (id)
			// This a pro tem approach due to current conditions see
			// https://github.com/sportszone/next-template-packs/issues/180.
			var disabledControl = $(this).attr('data-sz-disable-input');

			if ( $( disabledControl ).prop( 'disabled', true ) && !$(this).hasClass('enabled') ) {
				$(this).addClass('enabled').removeClass('disabled');
				$( disabledControl ).removeProp( 'disabled' );

			} else if( $( disabledControl ).prop( 'disabled', false ) && $(this).hasClass('enabled') ) {
				$(this).removeClass('enabled').addClass('disabled');
				// Set using attr not .prop else DOM renders as 'disable=""' CSS needs 'disable="disable"'.
				$( disabledControl ).attr( 'disabled', 'disabled' );
			}
		},

		/**
		 * [heartbeatSend description]
		 * @param  {[type]} event [description]
		 * @param  {[type]} data  [description]
		 * @return {[type]}       [description]
		 */
		heartbeatSend: function( event, data ) {
			// Add an heartbeat send event to possibly any SportsZone pages
			$( '#sportszone' ).trigger( 'sz_heartbeat_send', data );
		},

		/**
		 * [heartbeatTick description]
		 * @param  {[type]} event [description]
		 * @param  {[type]} data  [description]
		 * @return {[type]}       [description]
		 */
		heartbeatTick: function( event, data ) {
			// Add an heartbeat send event to possibly any SportsZone pages
			$( '#sportszone' ).trigger( 'sz_heartbeat_tick', data );
		},

		/**
		 * [queryScope description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		scopeQuery: function( event ) {
			var self = event.data, target = $( event.currentTarget ).parent(),
				scope = 'all', object, filter = null, search_terms = '';

			if ( target.hasClass( 'no-ajax' ) || $( event.currentTarget ).hasClass( 'no-ajax' ) || ! target.attr( 'data-sz-scope' ) ) {
				return event;
			}

			scope  = target.data( 'sz-scope' );
			object = target.data( 'sz-object' );

			if ( ! scope || ! object ) {
				return event;
			}

			// Stop event propagation
			event.preventDefault();

			filter = $( '#sportszone' ).find( '[data-sz-filter="' + object + '"]' ).first().val();

			if ( $( '#sportszone [data-sz-search="' + object + '"] input[type=search]' ).length ) {
				search_terms = $( '#sportszone [data-sz-search="' + object + '"] input[type=search]' ).val();
			}

			// Remove the New count on dynamic tabs
			if ( target.hasClass( 'dynamic' ) ) {
				target.find( 'a span' ).html('');
			}

			self.objectRequest( {
				object       : object,
				scope        : scope,
				filter       : filter,
				search_terms : search_terms,
				/*loc_country  : '',
				loc_province : '',
				loc_city 	 : '',*/
				page         : 1
			} );
		},

		/**
		 * [filterQuery description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		filterQuery: function( event ) {
			var self = event.data, object = $( event.target ).data( 'sz-filter' ),
				scope = 'all', filter = $( event.target ).val(),
				search_terms = '', loc_country = '', loc_province = '', loc_city = '', template = null;

			if ( ! object ) {
				return event;
			}

			if ( $( self.objectNavParent + ' [data-sz-object].selected' ).length ) {
				scope = $( self.objectNavParent + ' [data-sz-object].selected' ).data( 'sz-scope' );
			}

			if ( $( '#sportszone [data-sz-search="' + object + '"] input[type=search]' ).length ) {
				search_terms = $( '#sportszone [data-sz-search="' + object + '"] input[type=search]' ).val();
			}
			/*
			if ( $( '#sportszone [data-sz-search="' + object + '"] input[name=dir_events_loc_country]' ).length ) {
				loc_country = $( '#sportszone [data-sz-search="' + object + '"] input[name=dir_events_loc_country]' ).val();
			}
			if ( $( '#sportszone [data-sz-search="' + object + '"] input[name=dir_events_loc_province]' ).length ) {
				loc_province = $( '#sportszone [data-sz-search="' + object + '"] input[name=dir_events_loc_province]' ).val();
			}
			if ( $( '#sportszone [data-sz-search="' + object + '"] input[name=dir_events_loc_city]' ).length ) {
				loc_city = $( '#sportszone [data-sz-search="' + object + '"] input[name=dir_events_loc_city]' ).val();
			}
*/
			if ( 'friends' === object ) {
				object = 'members';
			}

			self.objectRequest( {
				object       : object,
				scope        : scope,
				filter       : filter,
				search_terms : search_terms,
				loc_country  : loc_country,
				loc_province : loc_province,
				loc_city 	 : loc_city,
				page         : 1,
				template     : template
			} );
		},

		/**
		 * [searchQuery description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		searchQuery: function( event ) {
			var self = event.data, object, scope = 'all', filter = null, template = null, search_terms = '', loc_country = '', loc_province = '', loc_city = '';

			if ( $( event.delegateTarget ).hasClass( 'no-ajax' ) || undefined === $( event.delegateTarget ).data( 'sz-search' ) ) {
				return event;
			}

			// Stop event propagation
			event.preventDefault();

			object       = $( event.delegateTarget ).data( 'sz-search' );
			filter       = $( '#sportszone' ).find( '[data-sz-filter="' + object + '"]' ).first().val();
			search_terms = $( event.delegateTarget ).find( 'input[type=search]' ).first().val();
			loc_country  = $( event.delegateTarget ).find( 'input[name=dir_events_loc_country]' ).first().val();
			loc_province = $( event.delegateTarget ).find( 'input[name=dir_events_loc_province]' ).first().val();
			loc_city 	 = $( event.delegateTarget ).find( 'input[name=dir_events_loc_city]' ).first().val();

			if ( $( self.objectNavParent + ' [data-sz-object]' ).length ) {
				scope = $( self.objectNavParent + ' [data-sz-object="' + object + '"].selected' ).data( 'sz-scope' );
			}

			self.objectRequest( {
				object       : object,
				scope        : scope,
				filter       : filter,
				search_terms : search_terms,
				loc_country  : loc_country,
				loc_province : loc_province,
				loc_city 	 : loc_city,
				page         : 1,
				template     : template
			} );
		},

		/**
		 * [showSearchSubmit description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		showSearchSubmit: function( event ) {
			$( event.delegateTarget ).find( '[type=submit]' ).addClass( 'sz-show' );
			if( $('[type=submit]').hasClass( 'sz-hide' ) ) {
				$( '[type=submit]' ).removeClass( 'sz-hide' );
			}
		},

		/**
		 * [resetSearch description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		resetSearch: function( event ) {
			if ( ! $( event.target ).val() ) {
				$( event.delegateTarget ).submit();
			} else {
				$( event.delegateTarget ).find( '[type=submit]' ).show();
			}
		},

		/**
		 * [buttonAction description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		buttonAction: function( event ) {
			var self = event.data, target = $( event.currentTarget ), action = target.data( 'sz-btn-action' ), nonceUrl = target.data( 'sz-nonce' ),
				item = target.closest( '[data-sz-item-id]' ), item_id = item.data( 'sz-item-id' ), item_inner = target.closest('.list-wrap'),
				object = item.data( 'sz-item-component' ), nonce = '';

			// Simply let the event fire if we don't have needed values
			if ( ! action || ! item_id || ! object ) {
				return event;
			}

			// Stop event propagation
			event.preventDefault();

			if ( ( undefined !== SZ_Nouveau[ action + '_confirm'] && false === window.confirm( SZ_Nouveau[ action + '_confirm'] ) ) || target.hasClass( 'pending' ) ) {
				return false;
			}

			// Find the required wpnonce string.
			// if  button element set we'll have our nonce set on a data attr
			// Check the value & if exists split the string to obtain the nonce string
			// if no value, i.e false, null then the href attr is used.
			if ( nonceUrl ) {
				nonce = nonceUrl.split('?_wpnonce=');
				nonce = nonce[1];
			} else {
				nonce = self.getLinkParams( target.prop( 'href' ), '_wpnonce' );
			}

			// Unfortunately unlike groups
			// Friends actions does not match the wpnonce
			var friends_actions_map = {
				is_friend         : 'remove_friend',
				not_friends       : 'add_friend',
				pending           : 'withdraw_friendship',
				accept_friendship : 'accept_friendship',
				reject_friendship : 'reject_friendship'
			};

			if ( 'members' === object && undefined !== friends_actions_map[ action ] ) {
				action = friends_actions_map[ action ];
				object = 'friends';
			}

			// Add a pending class to prevent queries while we're processing the action
			target.addClass( 'pending loading' );

			self.ajax( {
				action   : object + '_' + action,
				item_id  : item_id,
				_wpnonce : nonce
			}, object ).done( function( response ) {
				if ( false === response.success ) {
					item_inner.prepend( response.data.feedback );
					target.removeClass( 'pending loading' );
					item.find( '.sz-feedback' ).fadeOut( 6000 );
				} else {
					// Specific cases for groups
					if ( 'groups' === object ) {

						// Group's header button
						if ( undefined !== response.data.is_group && response.data.is_group ) {
							return window.location.reload();
						}
					} else if ( 'events' === object ) {

						// Events's header button
						if ( undefined !== response.data.is_event && response.data.is_event ) {
							return window.location.reload();
						}
					}

					// User's groups invitations screen & User's friend screens
					if ( undefined !== response.data.is_user && response.data.is_user ) {
						target.parent().html( response.data.feedback );
						item.fadeOut( 1500 );
						return;
					}

					// Update count
					if ( $( self.objectNavParent + ' [data-sz-scope="personal"]' ).length ) {
						var personal_count = Number( $( self.objectNavParent + ' [data-sz-scope="personal"] span' ).html() ) || 0;

						if ( -1 !== $.inArray( action, ['leave_group', 'remove_friend'] ) ) {
							personal_count -= 1;
						} else if ( -1 !== $.inArray( action, ['join_group'] ) ) {
							personal_count += 1;
						}

						if ( personal_count < 0 ) {
							personal_count = 0;
						}

						$( self.objectNavParent + ' [data-sz-scope="personal"] span' ).html( personal_count );
					}

					target.parent().replaceWith( response.data.contents );
				}
			} );
		},

		/**
		 * [closeNotice description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		closeNotice: function( event ) {
			var closeBtn = $( event.currentTarget );

			event.preventDefault();

			// Make sure cookies are removed
			if ( 'clear' === closeBtn.data( 'sz-close' ) ) {
				if ( undefined !== $.cookie( 'sz-message' ) ) {
					$.removeCookie( 'sz-message' );
				}

				if ( undefined !== $.cookie( 'sz-message-type' ) ) {
					$.removeCookie( 'sz-message-type' );
				}
			}

			// @todo other cases...
			// Dismissing site-wide notices.
			if ( closeBtn.closest( '.sz-feedback' ).hasClass( 'sz-sitewide-notice' ) ) {
				bp.Nouveau.ajax( {
					action : 'messages_dismiss_sitewide_notice'
				}, 'messages' );
			}

			// Remove the notice
			closeBtn.closest( '.sz-feedback' ).remove();
		},

		paginateAction: function( event ) {
			var self  = event.data, navLink = $( event.currentTarget ), pagArg,
			    scope = null, object, objectData, filter = null, search_terms = null, extras = null;

			pagArg = navLink.closest( '[data-sz-pagination]' ).data( 'sz-pagination' ) || null;

			if ( null === pagArg ) {
				return event;
			}

			event.preventDefault();

			object = $( event.delegateTarget ).data( 'sz-list' ) || null;

			// Set the scope & filter
			if ( null !== object ) {
				objectData = self.getStorage( 'sz-' + object );

				if ( undefined !== objectData.scope ) {
					scope = objectData.scope;
				}

				if ( undefined !== objectData.filter ) {
					filter = objectData.filter;
				}

				if ( undefined !== objectData.extras ) {
					extras = objectData.extras;
				}
			}

			// Set the search terms
			if ( $( '#sportszone [data-sz-search="' + object + '"] input[type=search]' ).length ) {
				search_terms = $( '#sportszone [data-sz-search="' + object + '"] input[type=search]' ).val();
			}

			var queryData = {
				object       : object,
				scope        : scope,
				filter       : filter,
				search_terms : search_terms,
				/*loc_country  : '',
				loc_province : '',
				loc_city 	 : '',*/
				extras       : extras,
				page         : self.getLinkParams( navLink.prop( 'href' ), pagArg ) || 1
			};

			// Request the page
			self.objectRequest( queryData );
		},
		
		// Custom Radio Buttons
		customRadio: function() {
			$('input[type=radio]').wrap('<span class="radio-container"></span>');
			$('input[type=radio]').after('<span class="custom-radio"></span>');
		},
		
		// Custom Check Boxes
		customCheck: function() {
			$('input[type=checkbox]').wrap('<span class="check-container"></span>');
			$('input[type=checkbox]').after('<span class="custom-check"></span>');
		},
		
		// Custom Select Boxes
		customSelect: function() {
			$('select').wrap('<span class="select-container"></span>');
			$('#settings-form .select-container').append('<span class="select-arrow"></span>');
		},
		
		// Toggle internal nav menues
		showNavs: function() {
			if($(window).width() > 748) {
				$('.single-screen-navs ul').css('display', 'block');	
			} else {
				$('.single-screen-navs ul').css('display', 'none');
			}
		},
		
		// Toggle internal nav menues
		toggleNavs: function() {
			$('.single-screen-navs ul').slideToggle();
		}
	};

	// Launch BP Nouveau
	bp.Nouveau.start();

} )( bp, jQuery );
