/* global bp, SZ_Uploader, _, Backbone */

window.bp = window.bp || {};

( function( exports, $ ) {

	// Bail if not set
	if ( typeof SZ_Uploader === 'undefined' ) {
		return;
	}

	bp.Models      = bp.Models || {};
	bp.Collections = bp.Collections || {};
	bp.Views       = bp.Views || {};

	bp.CoverImage = {
		start: function() {
			var self = this;
			
			/**
			 * Remove the sz-legacy UI
			 *
			 * bp.Avatar successfully loaded, we can now
			 * safely remove the Legacy UI.
			 */
			this.removeLegacyUI();
			
			// Init some vars
			this.views   = new Backbone.Collection(); 
			this.jcropapi = {};
			this.warning = null;
			
			// Set up nav
			this.setupNav();
			
			// Avatars are uploaded files
			this.coverImages = bp.Uploader.filesUploaded;

			// The Cover Image Attachment object.
			this.Attachment = new Backbone.Model();

			// Set up views
			//this.uploaderView();

			// Wait till the queue is reset
			bp.Uploader.filesQueue.on( 'reset', this.cropView, this );
			
			// Inform about the needed dimensions
			//this.displayWarning( SZ_Uploader.strings.cover_image_warnings.dimensions );

			// Set up the delete view if needed
			//if ( true === SZ_Uploader.settings.defaults.multipart_params.sz_params.has_cover_image ) {
			//	this.deleteView();
			//}
			
			/**
			 * In Administration screens we're using Thickbox
			 * We need to make sure to reset the views if it's closed or opened
			 */
			$( 'body.wp-admin' ).on( 'tb_unload', '#TB_window', function() {
				self.resetViews();
			} );

			$( 'body.wp-admin' ).on( 'click', '.sz-xprofile-cover-image-user-edit', function() {
				self.resetViews();
			} );
		},
		
		removeLegacyUI: function() {
			// User
			if ( $( '#cover-image-upload-form' ).length ) {
				$( '#cover-image-upload' ).remove();
				$( '#cover-image-upload-form p' ).remove();

			// Group Manage
			} else if ( $( '#group-settings-form' ).length ) {
				$( '#group-settings-form p' ).each( function( i ) {
					if ( 0 !== i ) {
						$( this ).remove();
					}
				} );

				if ( $( '#delete-group-cover-image-button' ).length ) {
					$( '#delete-group-cover-image-button' ).remove();
				}

			// Group Create
			} else if ( $( '#group-create-body' ).length ) {
				$( '.main-column p #file' ).remove();
				$( '.main-column p #upload' ).remove();

			// Admin Extended Profile
			} else if ( $( '#sz_xprofile_user_admin_cover_image a.sz-xprofile-cover-image-user-admin' ).length ) {
				$( '#sz_xprofile_user_admin_cover_image a.sz-xprofile-cover-image-user-admin' ).remove();
			}
		},
		
		setView: function( view ) {
			// Clear views
			if ( ! _.isUndefined( this.views.models ) ) {
				_.each( this.views.models, function( model ) {
					model.get( 'view' ).remove();
				}, this );
			}

			// Reset Views
			this.views.reset();

			// Reset Avatars (file uploaded)
			if ( ! _.isUndefined( this.coverImages ) ) {
				this.coverImages.reset();
			}

			// Reset the Jcrop API
			if ( ! _.isEmpty( this.jcropapi ) ) {
				this.jcropapi.destroy();
				this.jcropapi = {};
			}

			// Load the required view
			switch ( view ) {
				case 'upload':
					this.uploaderView();
					break;

				case 'delete':
					this.deleteView();
					break;
			}
		},
		resetViews: function() {
			// Reset to the uploader view
			this.nav.trigger( 'sz-cover-image-view:changed', 'upload' );

			// Reset to the uploader nav
			_.each( this.navItems.models, function( model ) {
				if ( model.id === 'upload' ) {
					model.set( { active: 1 } );
				} else {
					model.set( { active: 0 } );
				}
			} );
		},
		
		setupNav: function() {
			var self = this,
			    initView, activeView;

			this.navItems = new Backbone.Collection();

			_.each( SZ_Uploader.settings.nav, function( item, index ) {
				if ( ! _.isObject( item ) ) {
					return;
				}

				// Reset active View
				activeView = 0;

				if ( 0 === index ) {
					initView = item.id;
					activeView = 1;
				}

				self.navItems.add( {
					id     : item.id,
					name   : item.caption,
					href   : '#',
					active : activeView,
					hide   : _.isUndefined( item.hide ) ? 0 : item.hide
				} );
			} );

			this.nav = new bp.Views.Nav( { collection: this.navItems } );
			this.nav.inject( '.sz-cover-image-nav' );

			// Activate the initial view (uploader)
			this.setView( initView );

			// Listen to nav changes (it's like a do_action!)
			this.nav.on( 'sz-cover-image-view:changed', _.bind( this.setView, this ) );
		},


		uploaderView: function() {
			// Listen to the Queued uploads
			bp.Uploader.filesQueue.on( 'add', this.uploadProgress, this );

			// Create the SportsZone Uploader
			var uploader = new bp.Views.Uploader();

			// Add it to views
			this.views.add( { id: 'upload', view: uploader } );

			// Display it
			uploader.inject( '.sz-cover-image' );
		},

		uploadProgress: function() {
			// Create the Uploader status view
			
			
			var coverImageStatus = new bp.Views.uploaderStatus( { collection: bp.Uploader.filesQueue } );
			if ( ! _.isUndefined( this.views.get( 'status' ) ) ) {
				this.views.set( { id: 'status', view: coverImageStatus } );
			} else {
				this.views.add( { id: 'status', view: coverImageStatus } );
			}
			console.log( 'uploadProgress' );
			// Display it
			coverImageStatus.inject( '.sz-cover-image-status' );
		},
		
		cropView: function() {
			console.log( 'cropView' );
			var status;

			// Bail there was an error during the Upload
			if ( _.isEmpty( this.coverImages.models ) ) {
				return;
			}

			// Make sure to remove the uploads status
			if ( ! _.isUndefined( this.views.get( 'status' ) ) ) {
				status = this.views.get( 'status' );
				status.get( 'view' ).remove();
				this.views.remove( { id: 'status', view: status } );
			}

			// Create the Avatars view
			var cover_image = new bp.Views.CoverImages( { collection: this.coverImages } );
			this.views.add( { id: 'crop', view: cover_image } );
			
			cover_image.inject( '.sz-cover-image' );
		},
		
		setCoverImage: function( cover_image ) {
			console.log( 'setCoverImage', cover_image );
			var self = this,
				crop;
			// Remove the crop view
			if ( ! _.isUndefined( this.views.get( 'crop' ) ) ) {
				// Remove the JCrop API
				if ( ! _.isEmpty( this.jcropapi ) ) {
					this.jcropapi.destroy();
					this.jcropapi = {};
				}
				crop = this.views.get( 'crop' );
				crop.get( 'view' ).remove();
				this.views.remove( { id: 'crop', view: crop } );
			}

			// Set the cover_image !
			bp.ajax.post( 'sz_cover_image_set', {
				json:          true,
				original_file: cover_image.get( 'url' ),
				crop_w:        cover_image.get( 'w' ),
				crop_h:        cover_image.get( 'h' ),
				crop_x:        cover_image.get( 'x' ),
				crop_y:        cover_image.get( 'y' ),
				item_id:       cover_image.get( 'item_id' ),
				object:        cover_image.get( 'object' ),
				type:          _.isUndefined( cover_image.get( 'type' ) ) ? 'crop' : cover_image.get( 'type' ),
				nonce:         cover_image.get( 'nonces' ).set
			} ).done( function( response ) {
				console.log('sz_cover_image_set.done', response);
				var coverImageStatus = new bp.Views.CoverImageStatus( {
					value : SZ_Uploader.strings.feedback_messages[ response.feedback_code ],
					type : 'success'
				} );

				self.views.add( {
					id   : 'status',
					view : coverImageStatus
				} );

				coverImageStatus.inject( '.sz-cover-image-status' );
				
				
				// Update each cover_images of the page
				$( '.' + cover_image.get( 'object' ) + '-' + response.item_id + '-cover-image' ).each( function() {
					$(this).prop( 'src', response.cover_image );
				} );

				// Inject the Delete nav
				bp.CoverImage.navItems.get( 'delete' ).set( { hide: 0 } );

				/**
				 * Set the Attachment object
				 *
				 * You can run extra actions once the cover_image is set using:
				 * bp.Avatar.Attachment.on( 'change:url', function( data ) { your code } );
				 *
				 * In this case data.attributes will include the url to the newly
				 * uploaded cover_image, the object and the item_id concerned.
				 */
				self.Attachment.set( _.extend(
					_.pick( cover_image.attributes, ['object', 'item_id'] ),
					{ url: response.cover_image, action: 'uploaded' }
				) );

			} ).fail( function( response ) {
				console.log( 'sz_cover_image_set.fail', response );
				var feedback = SZ_Uploader.strings.default_error;
				if ( ! _.isUndefined( response ) ) {
					feedback = SZ_Uploader.strings.feedback_messages[ response.feedback_code ];
				}

				var coverImageStatus = new bp.Views.CoverImageStatus( {
					value : feedback,
					type : 'error'
				} );

				self.views.add( {
					id   : 'status',
					view : coverImageStatus
				} );

				coverImageStatus.inject( '.sz-cover-image-status' );
			} );
		},

		deleteView: function() {
			// Create the delete model
			var delete_model = new Backbone.Model( _.pick( SZ_Uploader.settings.defaults.multipart_params.sz_params,
				'object', 
				'item_id', 
				'nonces'
			) );

			// Create the delete view
			var deleteView = new bp.Views.DeleteCoverImage( { model: delete_model } );

			// Add it to views
			this.views.add( { id: 'delete', view: deleteView } );

			// Display it
			deleteView.inject( '.sz-cover-image' );
		},

		deleteCoverImage: function( model ) {
			var self = this,
				deleteView;

			// Remove the delete view
			if ( ! _.isUndefined( this.views.get( 'delete' ) ) ) {
				deleteView = this.views.get( 'delete' );
				deleteView.get( 'view' ).remove();
				this.views.remove( { id: 'delete', view: deleteView } );
			}

			// Remove the cover image !
			bp.ajax.post( 'sz_cover_image_delete', {
				json:          true,
				item_id:       model.get( 'item_id' ),
				object:        model.get( 'object' ),
				nonce:         model.get( 'nonces' ).remove
			} ).done( function( response ) {
				var coverImageStatus = new bp.Views.CoverImageStatus( {
					value : SZ_Uploader.strings.feedback_messages[ response.feedback_code ],
					type : 'success'
				} );

				self.views.add( {
					id   : 'status',
					view : coverImageStatus
				} );

				coverImageStatus.inject( '.sz-cover-image-status' );

				// Update each avatars of the page
				$( '.' + model.get( 'object' ) + '-' + response.item_id + '-cover-image').each( function() {
					$( this ).prop( 'src', response.cover_image );
				} );

				// Remove the Delete nav
				bp.CoverImage.navItems.get( 'delete' ).set( { active: 0, hide: 1 } );

				// Reset the has_cover_image sz_param
				SZ_Uploader.settings.defaults.multipart_params.sz_params.has_cover_image = false;

				/**
				 * Reset the Attachment object
				 *
				 * You can run extra actions once the cover image is set using:
				 * bp.CoverImage.Attachment.on( 'change:url', function( data ) { your code } );
				 *
				 * In this case data.attributes will include the default url for the
				 * cover image (most of the time: ''), the object and the item_id concerned.
				 */
				self.Attachment.set( _.extend(
					_.pick( model.attributes, ['object', 'item_id'] ),
					{ url: response.reset_url, action: 'deleted' }
				) );

			} ).fail( function( response ) {
				console.log( response );
				
				var feedback = SZ_Uploader.strings.default_error;
				if ( ! _.isUndefined( response ) ) {
					feedback = SZ_Uploader.strings.feedback_messages[ response.feedback_code ];
				}

				var coverImageStatus = new bp.Views.CoverImageStatus( {
					value : feedback,
					type : 'error'
				} );

				self.views.add( {
					id   : 'status',
					view : coverImageStatus
				} );

				coverImageStatus.inject( '.sz-cover-image-status' );

			} );
		},

		removeWarning: function() {
			// console.log( 'removeWarning' );
			if ( ! _.isNull( this.warning ) ) {
				this.warning.remove();
			}
		},

		displayWarning: function( message ) {
			// console.log( 'displayWarning', message );
			this.removeWarning();

			this.warning = new bp.Views.uploaderWarning( {
				value: message
			} );

			this.warning.inject( '.sz-cover-image-status' );
		}
	};
	
	// Main Nav view
	bp.Views.Nav = bp.View.extend( {
		tagName:    'ul',
		className:  'cover-image-nav-items',

		events: {
			'click .sz-cover-image-nav-item' : 'toggleView'
		},

		initialize: function() {
			var hasCoverImage = _.findWhere( this.collection.models, { id: 'delete' } );
			// Display a message to inform about the delete tab
			if ( 1 !== hasCoverImage.get( 'hide' ) ) {
				bp.CoverImage.displayWarning( SZ_Uploader.strings.has_cover_image_warning );
			}

			_.each( this.collection.models, this.addNavItem, this );
			this.collection.on( 'change:hide', this.showHideNavItem, this );
		},

		addNavItem: function( item ) {
			/**
			 * The delete nav is not added if no cover_image
			 * is set for the object
			 */
			if ( 1 === item.get( 'hide' ) ) {
				return;
			}

			this.views.add( new bp.Views.NavItem( { model: item } ) );
		},

		showHideNavItem: function( item ) {
			var isRendered = null;

			/**
			 * Loop in views to show/hide the nav item
			 * SportsZone is only using this for the delete nav
			 */
			_.each( this.views._views[''], function( view ) {
				if ( 1 === view.model.get( 'hide' ) ) {
					view.remove();
				}

				// Check to see if the nav is not already rendered
				if ( item.get( 'id' ) === view.model.get( 'id' ) ) {
					isRendered = true;
				}
			} );

			// Add the Delete nav if not rendered
			if ( ! _.isBoolean( isRendered ) ) {
				this.addNavItem( item );
			}
		},

		toggleView: function( event ) {
			event.preventDefault();

			// First make sure to remove all warnings
			bp.CoverImage.removeWarning();

			var active = $( event.target ).data( 'nav' );

			_.each( this.collection.models, function( model ) {
				if ( model.id === active ) {
					model.set( { active: 1 } );
					this.trigger( 'sz-cover-image-view:changed', model.id );
				} else {
					model.set( { active: 0 } );
				}
			}, this );
		}
	} );
	
	// Nav item view
	bp.Views.NavItem = bp.View.extend( {
		tagName:    'li',
		className:  'cover-image-nav-item',
		template: bp.template( 'sz-cover-image-nav' ),

		initialize: function() {
			if ( 1 === this.model.get( 'active' ) ) {
				this.el.className += ' current';
			}
			this.el.id += 'sz-cover-image-' + this.model.get( 'id' );

			this.model.on( 'change:active', this.setCurrentNav, this );
		},

		setCurrentNav: function( model ) {
			if ( 1 === model.get( 'active' ) ) {
				this.$el.addClass( 'current' );
			} else {
				this.$el.removeClass( 'current' );
			}
		}
	} );
	
	// Cover Images view
	bp.Views.CoverImages = bp.View.extend( {
		className: 'items',

		initialize: function() {
			_.each( this.collection.models, this.addItemView, this );
		},

		addItemView: function( item ) {
			// console.log( 'addItemView', item );
			// Defaults to 150
			var full_d = { full_h: 315, full_w: 1300 };

			// Make sure to take in account sz_core_cover_image_full_height or sz_core_cover_image_full_width php filters
			if ( ! _.isUndefined( SZ_Uploader.settings.crop.full_h ) && ! _.isUndefined( SZ_Uploader.settings.crop.full_w ) ) {
				full_d.full_h = SZ_Uploader.settings.crop.full_h;
				full_d.full_w = SZ_Uploader.settings.crop.full_w;
			}

			// Set the cover_image model
			item.set( _.extend( _.pick( SZ_Uploader.settings.defaults.multipart_params.sz_params,
				'object',
				'item_id',
				'nonces'
			), full_d ) );

			// Add the view
			this.views.add( new bp.Views.CoverImage( { model: item } ) );
		}
	} );

	// Cover Image view
	bp.Views.CoverImage = bp.View.extend( {
		className: 'item',
		template: bp.template( 'sz-cover-image-item' ),

		events: {
			'click .cover-image-crop-submit': 'cropCoverImage'
		},
		
		initialize: function() {
			console.log( 'bp.Views.CoverImage', this.options );
			_.defaults( this.options, {
				full_h:  SZ_Uploader.settings.crop.full_h,
				full_w:  SZ_Uploader.settings.crop.full_w,
				aspectRatio : 4.126984126984127
			} );
			
			// Display a warning if the image is smaller than minimum advised
			if ( false !== this.model.get( 'feedback' ) ) {
				bp.CoverImage.displayWarning( this.model.get( 'feedback' ) );
			}

			this.on( 'ready', this.initCropper );
		},

		initCropper: function() {
			// console.log( 'initCropper' );
			var self = this,
				tocrop = this.$el.find( '#cover-image-to-crop img' ),
				availableWidth = this.$el.width(),
				selection = {}, crop_top, crop_bottom, crop_left, crop_right, nh, nw;
				

			if ( ! _.isUndefined( this.options.full_h ) && ! _.isUndefined( this.options.full_w ) ) {
				this.options.aspectRatio = this.options.full_w / this.options.full_h;
			}

			selection.w = $('#cover-image-to-crop').width();//(this.model.get( 'width' ) );
			selection.h = selection.w * 0.2423; //(this.model.get( 'height' ) );



			if ( selection.h <= selection.w ) {
				crop_top    = 0; //Math.round( selection.h / 4 );
				nh = nw     = Math.round( selection.h / 2 );
				crop_bottom = nh + crop_top;
				crop_left   = 0; //( selection.w - nw ) / 2;
				crop_right  = nw + crop_left;
			} else {
				crop_left   = 0;//Math.round( selection.w / 4 );
				nh = nw     = Math.round( selection.w / 2 );
				crop_right  = nw + crop_left;
				crop_top    = 0;//( selection.h - nh ) / 2;
				crop_bottom = nh + crop_top;
			}

			// Add the cropping interface
			tocrop.Jcrop( {
				onChange: _.bind( self.showPreview, self ),
				onSelect: _.bind( self.showPreview, self ),
				aspectRatio: self.options.aspectRatio,
				setSelect: [ crop_left, crop_top, crop_right, crop_bottom ]
			}, function() {
				// Get the Jcrop API
				bp.CoverImage.jcropapi = this;
			} );
		},

		cropCoverImage: function( event ) {
			event.preventDefault();

			bp.CoverImage.setCoverImage( this.model );
		},

		showPreview: function( coords ) {
			// console.log( 'showPreview', coords );
			if ( ! coords.w || ! coords.h ) {
				return;
			}

			if ( parseInt( coords.w, 10 ) > 0 ) {
				var fw = $('#cover-image-to-crop').width(); // this.options.full_w;
				var fh = fw * 0.2423; // this.options.full_h;
				var rx = fw / coords.w;
				var ry = fh / coords.h;
				
				// Update the model

				this.model.set( { 
					x: coords.x * 1.642228739, 
					y: coords.y * 1.642228739, 
					w:  coords.w * 1.642228739, 
					h:  coords.h * 1.642228739 
				} );

				$( '#cover-image-crop-preview' ).css( {
					maxWidth: 'none',
					width: Math.round( rx *  (this.model.get( 'width' ))  )+ 'px',
					height: Math.round( ry * (this.model.get( 'height' ))  )+ 'px',
					marginLeft: '-' + Math.round( (rx * this.model.get( 'x' )) ) + 'px',
					marginTop: '-' + Math.round( (ry * this.model.get( 'y' )) ) + 'px'
				} );
				
				
				
			}
		}
	} );

	// SportsZone Cover Image Feedback view
	bp.Views.CoverImageStatus = bp.View.extend( {
		tagName: 'p',
		className: 'updated',
		id: 'sz-cover-image-feedback',

		initialize: function() {
			this.el.className += ' ' + this.options.type;
			this.value = this.options.value;
		},

		render: function() {
			this.$el.html( this.value );
			return this;
		}
	} );

	// SportsZone Cover Image Delete view
	bp.Views.DeleteCoverImage = bp.View.extend( {
		tagName: 'div',
		id: 'sz-delete-cover-image-container',
		template: bp.template( 'sz-cover-image-delete' ),

		events: {
			'click #sz-delete-cover-image': 'deleteCoverImage'
		},

		deleteCoverImage: function( event ) {
			event.preventDefault();

			bp.CoverImage.deleteCoverImage( this.model );
		}
	} );

	bp.CoverImage.start();

})( bp, jQuery );
