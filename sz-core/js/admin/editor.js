/* global tinymce */
( function () {
	tinymce.PluginManager.add( 'sz_shortcodes_button', function( editor, url ) {
		var ed = tinymce.activeEditor;

		var groups = ed.getLang( 'sportszone.shortcodes' ).split("]");
		var menu = new Array();

		groups.forEach(function(g) {
			if ( "" == g ) return;
			var p = g.split("[");
			var label = p.shift();
			var variations = p.shift();
			var shortcodes = variations.split("|");
			var submenu = new Array();
			shortcodes.forEach(function(s) {
				submenu.push({
					text: ed.getLang( 'sportszone.' + s ),
					onclick: function() {
                        // triggers the thickbox
                        var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                        W = W - 80;
                        H = H - 84;
                        tb_show( ed.getLang( 'sportszone.' + label ) + ' - ' + ed.getLang( 'sportszone.' + s ), 'admin-ajax.php?action=sportszone_' + label + '_' + s + '_shortcode&width=' + W + '&height=' + H );
					}
				});
			});
			menu.push({
				text: ed.getLang( 'sportszone.' + label ),
				menu: submenu
			});
		});

		editor.addButton( 'sz_shortcodes_button', {
			title: ed.getLang('sportszone.insert'),
			text: false,
			icon: false,
			type: 'menubutton',
			menu: menu
		});
	});
})();
