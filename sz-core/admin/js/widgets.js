jQuery(document).ready(function($){

	// Orderby affects order select in widget options
	$("body").on("change", ".sz-select-orderby", function() {
		$(this).closest(".widget-content").find(".sz-select-order").prop("disabled", $(this).val() == "default");
	});

	// Calendar affects view all link checkbox in widget options
	$("body").on("change", ".sz-event-calendar-select", function() {
		$el = $(this).closest(".widget-content").find(".sz-event-calendar-show-all-toggle");
		if($(this).val() == 0)
			$el.hide();
		else
			$el.show();
	});

	// Show or hide datepicker
	$("body").on("change", ".sz-date-selector select", function() {
		if ( $(this).val() == "range" ) {
			$(this).closest(".sz-date-selector").find(".sz-date-range").show();
		} else {
			$(this).closest(".sz-date-selector").find(".sz-date-range").hide();
		}
	});
	$(".sz-date-selector select").trigger("change");

	// Toggle date range selectors
	$("body").on("change", ".sz-date-relative input", function() {
		$relative = $(this).closest(".sz-date-relative").siblings(".sz-date-range-relative").toggle(0, $(this).attr("checked"));
		$absolute = $(this).closest(".sz-date-relative").siblings(".sz-date-range-absolute").toggle(0, $(this).attr("checked"));

		if ($(this).attr("checked")) {
			$relative.show();
			$absolute.hide();
		} else {
			$absolute.show();
			$relative.hide();
		}
	});
	$(".sz-date-selector input").trigger("change");
});