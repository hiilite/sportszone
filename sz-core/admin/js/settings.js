jQuery(document).ready(function($){

	// Display custom sport name field as needed
	$("body.toplevel_page_sportszone #sportszone_sport").change(function() {
		$target = $("#sportszone_custom_sport_name");
		if ( $(this).val() == "custom" )
			$target.show();
		else
			$target.hide();
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
		$(this).closest('.sz-color-box, td').find('.iris-picker').show();
	});

	$('body').click(function() {
		$('.iris-picker').hide();
	});

	$('.sz-color-box, .colorpick').click(function(event){
	    event.stopPropagation();
	});

	// Chosen select
	$(".chosen-select").chosen({
		allow_single_deselect: true,
		single_backstroke_delete: false,
		placeholder_text_multiple: localized_strings.none
	});

	// Preset field modifier
	$(".sz-custom-input-wrapper .preset").click(function() {
		val = $(this).val();
		if(val == "\\c\\u\\s\\t\\o\\m") return true;
		example = $(this).attr("data-example");
		$(this).closest(".sz-custom-input-wrapper").find(".value").val(val).siblings(".example").html(example);
	});

	// Select custom preset when field is brought to focus
	$(".sz-custom-input-wrapper .value").focus(function() {
		$(this).siblings("label").find(".preset").prop("checked", true);
	});

	// Adjust example field when custom preset is entered
	$(".sz-custom-input-wrapper .value").on("keyup", function() {
		val = $(this).val();
		if ( val === undefined ) return true;
		format = $(this).attr("data-example-format");
		example = format.replace(/__val__/g, val);
		$(this).siblings(".example").html(example);
	});

});