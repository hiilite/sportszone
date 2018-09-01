jQuery(document).ready(function($){

	// Icon picker
	$('.sz-icons input').on('change', function() {
		if ('' == $(this).val()) {
			$('.sz-custom-colors').hide();
			$('.sz-custom-thumbnail').show();
		} else {
			$('.sz-custom-thumbnail').hide();
			$('.sz-custom-colors').show();
		}
	});

	// Color picker
	$('.colorpick').iris( {
		change: function(event, ui){
			$(this).css( { backgroundColor: ui.color.toString() } );
		},
		hide: true,
		border: true
	} ).each( function() {
		$(this).css( { backgroundColor: $(this).val() } );
	})
	.click(function(){
		$('.iris-picker').hide();
		$(this).closest('.sz-icon-color-box, td').find('.iris-picker').show();
	});

	$('body').click(function() {
		$('.iris-picker').hide();
	});

	$('.sz-icon-color-box, .colorpick').click(function(event){
	    event.stopPropagation();
	});

});