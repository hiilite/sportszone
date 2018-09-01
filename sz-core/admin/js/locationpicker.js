jQuery(document).ready(function($){
	$(".sz-location-picker").locationpicker({
		location: {
			latitude: Number($(".sz-latitude").val()),
			longitude: Number($(".sz-longitude").val())
		},
		radius: 0,
		inputBinding: {
	        latitudeInput: $(".sz-latitude"),
	        longitudeInput: $(".sz-longitude"),
	        locationNameInput: $(".sz-address")
	    },
	    enableAutocomplete: true
	});
});