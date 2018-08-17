jQuery(document).ready( function() {
	events_widget_click_handler();

	// WP 4.5 - Customizer selective refresh support.
	if ( 'undefined' !== typeof wp && wp.customize && wp.customize.selectiveRefresh ) {
		wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function() {
			events_widget_click_handler();
		} );
	}
});

function events_widget_click_handler() {
	jQuery('.widget div#events-list-options a').on('click',
		function() {
			var link = this;
			jQuery(link).addClass('loading');

			jQuery('.widget div#events-list-options a').removeClass('selected');
			jQuery(this).addClass('selected');

			jQuery.post( ajaxurl, {
				action: 'widget_events_list',
				'cookie': encodeURIComponent(document.cookie),
				'_wpnonce': jQuery('input#_wpnonce-events').val(),
				'max_events': jQuery('input#events_widget_max').val(),
				'filter': jQuery(this).attr('id')
			},
			function(response)
			{
				jQuery(link).removeClass('loading');
				events_widget_response(response);
			});

			return false;
		}
	);
}

function events_widget_response(response) {
	response = response.substr(0, response.length-1);
	response = response.split('[[SPLIT]]');

	if ( response[0] !== '-1' ) {
		jQuery('.widget ul#events-list').fadeOut(200,
			function() {
				jQuery('.widget ul#events-list').html(response[1]);
				jQuery('.widget ul#events-list').fadeIn(200);
			}
		);

	} else {
		jQuery('.widget ul#events-list').fadeOut(200,
			function() {
				var message = '<p>' + response[1] + '</p>';
				jQuery('.widget ul#events-list').html(message);
				jQuery('.widget ul#events-list').fadeIn(200);
			}
		);
	}
}
