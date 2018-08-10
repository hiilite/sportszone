(function($){
	$(document).ready(function() {
		$( '.sz-is-dismissible .notice-dismiss' ).click( function() {
			var $notice = $( this ).closest( '.notice' );
			var notice_id = $notice.data( 'noticeid' );
			$.post( {
				url: ajaxurl,
				data: {
					action: 'sz_dismiss_notice',
					nonce: $( '#sz-dismissible-nonce-' + notice_id ).val(),
					notice_id: $notice.data( 'noticeid' )
				}
			} );
		} );
	});
}(jQuery));
