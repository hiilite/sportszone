jQuery(document).ready(function($){
	

var localized_strings = {"none":"None","remove_text":"\u2014 Remove \u2014","days":"days","hrs":"hrs","mins":"mins","secs":"secs","displaying_posts":"Displaying %s\u2013%s of %s","no_results_found":"No results found.","select_all":"Select All","show_all":"Show all","loading":"Loading\u2026","option_filter_by_league":"no","option_filter_by_season":"no"};


	// Tiptip
	$(".sz-tip").tipTip({
		delay: 200,
		fadeIn: 100,
		fadeOut: 100
	});
	$(".sz-desc-tip").tipTip({
		delay: 200,
		fadeIn: 100,
		fadeOut: 100,
		defaultPosition: 'right'
	});

	// Chosen select
	$(".chosen-select, #poststuff #post_author_override").chosen({
		allow_single_deselect: true,
		search_contains: true,
		single_backstroke_delete: false,
		disable_search_threshold: 10,
		placeholder_text_multiple: localized_strings.none
	});

	// Auto key placeholder
	$("#poststuff #title").on("keyup", function() {
		val = $(this).val()
		lc = val.replace(/[^a-z]/gi,"").toLowerCase();
		$("#sz_key").attr("placeholder", lc);
		$("#sz_default_key").val(lc);
		$("#sz_singular").attr("placeholder", val);
	});

	// Activate auto key placeholder
	$("#poststuff #title").keyup();

	// Radio input toggle
	$(".sz-radio-toggle").click(function() {
		if($(this).data("sz-checked")) {
			$(this).attr("checked", false );
			$(this).data("sz-checked", false );
		} else {
			$(this).data("sz-checked", true );
		}
	});

	// Table switcher
	$(".sz-table-panel").siblings(".sz-table-bar").find("a").click(function() {
		$(this).closest("li").find("a").addClass("current").closest("li").siblings().find("a").removeClass("current").closest(".sz-table-bar").siblings($(this).attr("href")).show().siblings(".sz-table-panel").hide();
		return false;
	});

	// Tab switcher
	$(".sz-tab-panel").siblings(".sz-tab-bar").find("a").click(function() {
		$(this).closest("li").addClass("tabs").siblings().removeClass("tabs").closest(".sz-tab-bar").siblings($(this).attr("href")).show().trigger('checkCheck').siblings(".sz-tab-panel").hide();
		return false;
	});

	// Tab filter
	$(".sz-tab-filter-panel").siblings(".sz-tab-select").find("select").change(function() {
		var val = $(this).val();
		var filter = ".sz-filter-"+val;
		var $filters = $(this).closest(".sz-tab-select").siblings(".sz-tab-select");
		if($filters.length) {
			$filters.each(function() {
				filterval = $(this).find("select").val();
				if(filterval !== undefined)
					filter += ".sz-filter-"+filterval;
			});
		}
		$panel = $(this).closest(".sz-tab-select").siblings(".sz-tab-panel");
		$panel.each(function() {
			$(this).find(".sz-post").hide(0, function() {
				$(this).find("input").prop("disabled", true);
				$(this).filter(filter).show(0, function() {
					$(this).find("input").prop("disabled", false);
				});
			});
			if($(this).find(".sz-post:visible").length > 0) {
				$(this).find(".sz-select-all-container").show();
				$(this).find(".sz-show-all-container").show();
				$(this).find(".sz-not-found-container").hide();
			} else {
				$(this).find(".sz-select-all-container").hide();
				$(this).find(".sz-show-all-container").hide();
				$(this).find(".sz-not-found-container").show();
			}
		});
	});

	// Trigger tab filter
	$(".sz-tab-filter-panel").siblings(".sz-tab-select").find("select").change();

	// Dropdown filter
	$(".sz-dropdown-target").siblings(".sz-dropdown-filter").find("select").change(function() {
		var val = $(this).val();
		var filter = ".sz-filter-"+val;
		var $filters = $(this).closest(".sz-dropdown-filter").siblings(".sz-dropdown-filter");
		if($filters.length) {
			$filters.each(function() {
				filterval = $(this).find("select").val();
				if(filterval !== undefined)
					filter += ".sz-filter-"+filterval;
			});
		}
		$target = $(this).closest(".sz-dropdown-filter").siblings(".sz-dropdown-target").find("select");
		$target.find(".sz-post").prop("disabled", true).each(function() {
			$(this).filter(filter).prop("disabled", false);
		});
	});

	// Trigger dropdown filter
	$(".sz-dropdown-target").siblings(".sz-dropdown-filter").find("select").change();

	// Filter show all action links
	$(".sz-tab-panel").find(".sz-post input:checked").each(function() {
		$(this).prop("disabled", false).closest("li").show().siblings(".sz-not-found-container").hide().siblings(".sz-show-all-container").show();
	});

	// Show all filter
	$(".sz-tab-panel").on("click", ".sz-show-all", function() {
		$(this).closest("li").hide().siblings(".sz-post, .sz-select-all-container").show().find("input").prop("disabled", false);
	});

	// Self-cloning
	$(".sz-clone:last").find("select").change(function() {
		$(this).closest(".sz-clone").siblings().find("select").change(function() {
			if($(this).val() == "0") $(this).closest(".sz-clone").remove();
		}).find("option:first").text(localized_strings.remove_text);
		if($(this).val() != "0") {
			$original = $(this).closest(".sz-clone");
			$original.before($original.clone().find("select").attr("name", $original.attr("data-clone-name") + "[]").val($(this).val()).closest(".sz-clone")).attr("data-clone-num", parseInt($original.attr("data-clone-num")) + 1).find("select").val("0").change();
		}
	});

	// Activate self-cloning
	$(".sz-clone:last").find("select").change();

	// Custom value editor
	$(".sz-data-table .sz-default-value").click(function() {
		$(this).hide().siblings(".sz-custom-value").show().find(".sz-custom-value-input").focus();
	});

	// Define custom value editor saving
	$(".sz-data-table .sz-custom-value .sz-custom-value-input").on("saveInput", function() {
		$val = $(this).val();
		if($val == "") $val = $(this).attr("placeholder");
		$(this).closest(".sz-custom-value").hide().siblings(".sz-default-value").show().find(".sz-default-value-input").html($val);
	});

	// Define custom value editor cancellation
	$(".sz-data-table .sz-custom-value .sz-custom-value-input").on("cancelInput", function() {
		$val = $(this).closest(".sz-custom-value").siblings(".sz-default-value").find(".sz-default-value-input").html();
		if($val == $(this).attr("placeholder")) $(this).val("");
		else $(this).val($val);
		$(this).closest(".sz-custom-value").hide().siblings(".sz-default-value").show();
	});

	// Custom value editor save
	$(".sz-data-table .sz-custom-value .sz-save").click(function() {
		$(this).siblings(".sz-custom-value-input").trigger("saveInput");
	});

	// Custom value editor cancel
	$(".sz-data-table .sz-custom-value .sz-cancel").click(function() {
		$(this).siblings(".sz-custom-value-input").trigger("cancelInput");
	});

	// Prevent custom value editor input from submitting form
	$(".sz-data-table .sz-custom-value .sz-custom-value-input").keypress(function(event) {
		if(event.keyCode == 13){
			event.preventDefault();
			$(this).trigger("saveInput");
			return false;
		}
	});

	// Cancel custom value editor form on escape
	$(".sz-data-table .sz-custom-value .sz-custom-value-input").keyup(function(event) {
		if(event.keyCode == 27){
			event.preventDefault();
			$(this).trigger("cancelInput");
			return false;
		}
	});

	// Data table adjustments
	$(".sz-table-adjustments input").change(function() {
		matrix = $(this).attr("data-matrix");
		$el = $(this).closest(".sz-table-adjustments").siblings(".sz-table-values").find("input[data-matrix="+matrix+"]");
		placeholder = $el.attr("data-placeholder");
		current_adjustment = parseFloat($el.attr("data-adjustment"));
		adjustment = parseFloat($(this).val());
		if(! isNaN(adjustment) && adjustment != 0) {
			placeholder = parseFloat(placeholder);
			if(isNaN(placeholder)) placeholder = 0;
			if(isNaN(current_adjustment)) current_adjustment = 0;
			placeholder += adjustment - current_adjustment;
		}
		$el.attr("placeholder", placeholder);
	}).change();

	// Data table keyboard navigation
	$(".sz-data-table tbody tr td input:text").keydown(function(event) {
		if(! $(this).parent().hasClass("chosen-search") && [37,38,39,40].indexOf(event.keyCode) > -1){
			$el = $(this).closest("td");
			var col = $el.parent().children().index($el)+1;
			var row = $el.parent().parent().children().index($el.parent())+1;
			if(event.keyCode == 37){
				if ( $(this).caret().start != 0 )
					return true;
				col -= 1;
			}
			if(event.keyCode == 38){
				row -= 1;
			}
			if(event.keyCode == 39){
				if ( $(this).caret().start != $(this).val().length )
					return true;
				col += 1;
			}
			if(event.keyCode == 40){
				row += 1;
			}
			$el.closest("tbody").find("tr:nth-child("+row+") td:nth-child("+col+") input:text").first().focus();
		}
	});

	// Prevent data table from submitting form
	$(".sz-data-table tbody tr td input:text").keypress(function(event) {
		if(! $(this).parent().hasClass("chosen-search") && event.keyCode == 13){
			event.preventDefault();
			$el = $(this).closest("td");
			var col = $el.parent().children().index($el)+1;
			var row = $el.parent().parent().children().index($el.parent())+2;
			$el.closest("tbody").find("tr:nth-child("+row+") td:nth-child("+col+") input:text").focus();
			return false;
		}
	});

	// Total stats calculator
	$(".sz-data-table .sz-total input[data-sz-format=number][data-sz-total-type!=average]").on("updateTotal", function() {
		index = $(this).parent().index();
		var sum = 0;
		$(this).closest(".sz-data-table").find(".sz-post").each(function() {
			val = $(this).find("td").eq(index).find("input").val();
			if(val == "") {
				val = $(this).find("td").eq(index).find("input").attr("placeholder");
			}
			if($.isNumeric(val)) {
				sum += parseFloat(val, 10);
			}
		});
		$(this).attr("placeholder", sum);
	});

	// Activate total stats calculator
	if($(".sz-data-table .sz-total").size()) {
		$(".sz-data-table .sz-post td input").on("keyup", function() {
			$(this).closest(".sz-data-table").find(".sz-total td").eq($(this).parent().index()).find("input[data-sz-format=number][data-sz-total-type!=average]").trigger("updateTotal");
		});
	}

	// Trigger total stats calculator
	$(".sz-data-table .sz-total input[data-sz-format=number][data-sz-total-type!=average]").trigger("updateTotal");

	// Sync inputs
	$(".sz-sync-input").on("keyup", function() {
		name = $(this).attr("name");
		$el = $("input[name='"+name+"']");
		if ( $el.length > 1 ) {
			val = $(this).val();
			$el.val(val);
		}
	});

	// Sync selects
	$(".sz-sync-select").on("change", function() {
		name = $(this).attr("name");
		$el = $("select[name='"+name+"']")
		if ( $el.length > 1 ) {
			val = $(this).val();
			$el.val(val);
		}
	});

	// Select all checkboxes
	$(".sz-select-all-range").on("change", ".sz-select-all", function() {
		$range = $(this).closest(".sz-select-all-range");
		$range.find("input[type=checkbox]").prop("checked", $(this).prop("checked"));
	});

	// Check if all checkboxes are checked already
	$(".sz-select-all-range").on("checkCheck", function() {
		$(this).each(function() {
			$(this).find(".sz-select-all").prop("checked", $(this).find("input[type=checkbox]:checked:not(.sz-select-all)").length != 0 && $(this).find("input[type=checkbox]:checked:not(.sz-select-all)").length == $(this).find("input[type=checkbox]:visible:not(.sz-select-all)").length);
		});
	});

	// Activate check check when a checkbox is checked
	$(".sz-select-all-range input[type=checkbox]:not(.sz-select-all)").change(function() {
		$(this).closest(".sz-select-all-range").trigger("checkCheck");
	});

	// Activate check check on page load
	$(".sz-select-all-range").trigger("checkCheck");

	// Trigger check check
	$(".sz-data-table").trigger("checkCheck");

	// Sortable tables
	$(".sz-sortable-table tbody").sortable({
		handle: ".icon",
		axis: "y"
	});
	
	// Sortable lists
    $( ".sz-sortable-list" ).sortable({
    	handle: ".sz-item-handle",
		placeholder: "sz-item-placeholder",
		connectWith: ".sz-connected-list"
    });

	// Autosave
	$(".sz-autosave").change(function() {
		$(this).attr("readonly", true).closest("form").submit();
	});

	// Video embed
	$(".sz-add-video").click(function() {
		$(this).closest("fieldset").hide().siblings(".sz-video-field").show();
		return false;
	});

	// Removing video embed
	$(".sz-remove-video").click(function() {
		$(this).closest("fieldset").hide().siblings(".sz-video-adder").show().siblings(".sz-video-field").find("input").val(null);
		return false;
	});

	// Equation selector
	$(".sz-equation-selector select:last").change(function() {
		$(this).siblings().change(function() {
			if($(this).val() == "") $(this).remove();
		}).find("option:first").text(localized_strings.remove_text);
		if($(this).val() != "") {
			$(this).before($(this).clone().val($(this).val())).val("").change();
		}
	});

	// Trigger equation selector
	$(".sz-equation-selector select:last").change().siblings().change();

	// Order selector
	$(".sz-order-selector select:first").change(function() {
		if($(this).val() == "0") {
			$(this).siblings().prop( "disabled", true );
		} else {
			$(this).siblings().prop( "disabled", false )
		}
	});

	// Trigger order selector
	$(".sz-order-selector select:first").change();

	// Format selector
	$(".sz-format-selector select:first").change(function() {

		$precisiondiv = $("#sz_precisiondiv");
		$precisioninput = $("#sz_precision");
		$timeddiv = $("#sz_timeddiv");
		$equationdiv = $("#sz_equationdiv");

		// Equation settings
		if ($(this).val() == "equation") {
			$equationdiv.show();
			$precisiondiv.show();
			$timeddiv.hide();
			$precisioninput.prop( "disabled", false );
		} else if ($(this).val() == "number") {
			$equationdiv.hide();
			$precisiondiv.hide();
			$timeddiv.show();
			$precisioninput.prop( "disabled", true );
		} else {
			$equationdiv.hide();
			$precisiondiv.hide();
			$timeddiv.hide();
			$precisioninput.prop( "disabled", true );
		}

	});

	// Trigger format selector
	$(".sz-format-selector select:first").change();

	// Team era selector
	$(".sz-team-era-selector select:first-child").change(function() {

		$subselector = $(this).siblings();

		// Sub settings
		if($(this).val() == 0) {
			$subselector.hide();
		} else {
			$subselector.show();
		}

	});

	// Trigger team era selector
	$(".sz-team-era-selector select:first-child").change();

	// Status selector
	$(".sz-status-selector select:first-child").change(function() {

		$subselector = $(this).siblings();

		// Sub settings
		if($(this).val() == "sub") {
			$subselector.show();
		} else {
			$subselector.hide();
		}

	});

	// Trigger status selector
	$(".sz-status-selector select:first-child").change();

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
		example = format.replace("__val__", val);
		$(this).siblings(".example").html(example);
	});

	// Prevent address input from submitting form
	$(".sz-address").keypress(function(event) {
		return event.keyCode != 13;
	});

	// Dashboard countdown
	$("#sportszone_dashboard_status .sz_status_list li.countdown").each(function() {
		var $this = $(this), finalDate = $(this).data('countdown');
		$this.countdown(finalDate, function(event) {
			$this.find('strong').html(event.strftime("%D "+localized_strings.days+" %H:%M:%S"));
		});
	});

	// Event format affects data
	$(".post-type-sz_event #post-formats-select input.post-format").change(function() {
		layout = $(".post-type-sz_event #post-formats-select input:checked").val();
		if ( layout == "friendly" ) {
			$(".sz_event-sz_league-field").show().find("select").prop("disabled", false);
			$(".sz_event-sz_season-field").show().find("select").prop("disabled", false);
		} else {
			$(".sz_event-sz_league-field").show().find("select").prop("disabled", false);
			$(".sz_event-sz_season-field").show().find("select").prop("disabled", false);
		}
	});

	// Trigger event format change
	$(".post-type-sz_event #post-formats-select input.post-format").trigger("change");

	// Calendar layout affects data
	$(".post-type-sz_calendar #post-formats-select input.post-format").change(function() {
		layout = $(".post-type-sz_calendar #post-formats-select input:checked").val();
		$(".sz-calendar-table tr").each(function() {
			if ( layout == "list" ) {
				$(this).find("th input[type=checkbox]").show();
				$(this).find("th select").prop("disabled", false);
			} else {
				$(this).find("th input[type=checkbox]").hide();
				$(this).find("th select").prop('selectedIndex', 0).prop("disabled", true);
			}
		});
	});

	// Trigger calendar layout change
	$(".post-type-sz_calendar #post-formats-select input.post-format").trigger("change");

	// Player list layout affects data
	$(".post-type-sz_list #post-formats-select input.post-format").change(function() {
		layout = $(".post-type-sz_list #post-formats-select input:checked").val();
		$(".sz-player-list-table tr").each(function() {
			if ( layout == "list" ) {
				$(this).find("th input[type=checkbox]").show();
			} else {
				$(this).find("th input[type=checkbox]").hide();
			}
		});
	});

	// Trigger player list layout change
	$(".post-type-sz_list #post-formats-select input.post-format").trigger("change");

	// Configure primary result option (Ajax)
	$(".sz-admin-config-table").on("click", ".sz-primary-result-option", function() {
		$.post( ajaxurl, {
			action:         "sz-save-primary-result",
			primary_result: $(this).val(),
			nonce:          $("#sz-primary-result-nonce").val()
		});
	});

	// Configure primary performance option (Ajax)
	$(".sz-admin-config-table").on("click", ".sz-primary-performance-option", function() {
		$.post( ajaxurl, {
			action:              "sz-save-primary-performance",
			primary_performance: $(this).val(),
			nonce:               $("#sz-primary-performance-nonce").val()
		});
	});

	// Update importer post count
	$(".sz-import-table").on("updatePostCount", function() {
		$(".sz-post-count").text(localized_strings.displaying_posts.replace("%s", 1).replace(/%s/g, count = $(this).find("tbody tr").length));
	});

	// Delete importer row
	$(".sz-import-table").on("click", ".sz-delete-row", function() {
		$self = $(this);
		$self.closest("tr").css("background-color", "#f99").fadeOut(400, function() {
			$table = $self.closest(".sz-import-table");
			$(this).remove();
			$table.trigger("updatePostCount");
		});
		return false;
	});

	// Add importer row
	$(".sz-import-table").on("click", ".sz-add-row", function() {
		$self = $(this);
		$table = $self.closest(".sz-import-table");
		if ( $self.hasClass("sz-add-first") ) {
			$tr = $table.find("tbody tr:first-child");
			$row = $tr.clone();
			$row.insertBefore($tr).find("input").val("");
		} else {
			$tr = $self.closest("tr");
			$row = $tr.clone();
			$tr.find("input").val("");
			$row.insertBefore($tr);
		}
		$table.trigger("updatePostCount");
		return false;
	});

	// Enable or disable importer inputs based on column label
	$(".sz-import-table").on("change", "select", function() {
		$self = $(this);
		$table = $self.closest(".sz-import-table");
		index = parseInt($self.data("index"));
		if ( $self.val() == 0 ) {
			$table.find("tbody tr td:nth-child("+parseInt(index+1)+") input").prop("disabled", true);
		} else {
			$table.find("tbody tr td:nth-child("+parseInt(index+1)+") input").prop("disabled", false);
			$self.closest("th").siblings().find("select").each(function() {
				if ( $(this).val() == $self.val() ) $(this).val("0").trigger("change");
			});
		}
	});

	// Datepicker
	$(".sz-datepicker").datepicker({
		dateFormat : "yy-mm-dd"
	});
	$(".sz-datepicker-from").datepicker({
		dateFormat : "yy-mm-dd",
		onClose: function( selectedDate ) {
			$(this).closest(".sz-date-selector").find(".sz-datepicker-to").datepicker("option", "minDate", selectedDate);
		}
	});
	$(".sz-datepicker-to").datepicker({
		dateFormat : "yy-mm-dd",
		onClose: function( selectedDate ) {
			$(this).closest(".sz-date-selector").find(".sz-datepicker-from").datepicker("option", "maxDate", selectedDate);
		}
	});

	// Show or hide datepicker
	$(".sz-date-selector select").change(function() {
		if ( $(this).val() == "range" ) {
			$(this).closest(".sz-date-selector").find(".sz-date-range").show();
		} else {
			$(this).closest(".sz-date-selector").find(".sz-date-range").hide();
		}
	});
	$(".sz-date-selector select").trigger("change");

	// Toggle date range selectors
	$(".sz-date-relative input").change(function() {
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

	// Apply color scheme
	$(".sz-color-option").on("click", function() {
		colors = $(this).find("label").data("sz-colors").split(",");
		$(".sz-custom-colors").find(".sz-color-box").each(function(index) {
			$(this).find("input").val("#"+colors[index]).css("background-color", "#"+colors[index]);
		});;
	});

	// Edit inline results
	$("#the-list").on("click, focus", ".sz-result, .sz-edit-results", function(){
		team = $(this).data("team");
		$column = $(this).closest(".column-sz_team");
		$column.find(".sz-result, .sz-row-actions").hide();
		$column.find(".sz-edit-result, .sz-inline-edit-save").show();
		if ( team != undefined ) {
			$column.find(".sz-edit-result[data-team='"+team+"']").select();
		}
		return false;
	});

	// Cancel inline results
	$("#the-list").on("click", ".sz-inline-edit-save .cancel", function(){
		$column = $(this).closest(".column-sz_team");
		$column.find(".sz-edit-result, .sz-inline-edit-save").hide();
		$column.find(".sz-result, .sz-row-actions").show();
		return false;
	});

	// Save inline results
	$("#the-list").on("click", ".sz-inline-edit-save .save", function(){
		$column = $(this).closest(".column-sz_team");
		results = {};
		$column.find(".sz-edit-result").each(function() {
			id = $(this).data("team");
			result = $(this).val();
			results[id] = result;
		});
		$.post( ajaxurl, {
			action:         "sz-save-inline-results",
			post_id: 		$column.find("input[name='sz_post_id']").val(),
			results: 		results,
			nonce:          $("#sz-inline-nonce").val()
		}, function(response) {
			$column.find(".sz-edit-result").each(function() {
				val = $(this).val();
				$column.find(".sz-result[data-team='"+$(this).data("team")+"']").html(val==''?'-':val);
			});
			$column.find(".sz-edit-result, .sz-inline-edit-save").hide();
			$column.find(".sz-result, .sz-row-actions").show();
			return false;
		});
	});

	// Override inline form submission
	$("#the-list").on("keypress", ".sz-edit-result", function(e) {
		if ( e.which == 13 ) {
			$(this).closest(".column-sz_team").find(".sz-inline-edit-save .save").trigger("click");
			return false;
		}
	});

	// Fitvids
	$(".sz-fitvids").fitVids();

	// Display configure sport button
	$(".sz-select-sport").change(function() {
		$(".sz-configure-sport").hide();
	});

	// Ajax checklist
	$(".sz-ajax-checklist").siblings(".sz-tab-select").find("select").change(function() {
		$(this).closest(".sz-tab-select").siblings(".sz-ajax-checklist").find("ul").html("<li>" + localized_strings.loading + "</li>");
		$.post( ajaxurl, {
			action:         "sz-get-players",
			team: 			$(this).val(),
			league: 		('yes' == localized_strings.option_filter_by_league) ? $("select[name=\"tax_input[sz_league][]\"]").val() : null,
			season: 		('yes' == localized_strings.option_filter_by_season) ? $("select[name=\"tax_input[sz_season][]\"]").val() : null,
			index: 			$(this).closest(".sz-instance").index(),
			nonce:          $("#sz-get-players-nonce").val()
		}).done(function( response ) {
			index = response.data.index;
			$target = $(".sz-instance").eq(index).find(".sz-ajax-checklist ul");
			if ( response.success ) {
				$target.html("");
				i = 0;
				if(-1 == response.data.sections) {
					if(response.data.players.length) {
						$target.eq(0).append("<li class=\"sz-select-all-container\"><label class=\"selectit\"><input type=\"checkbox\" class=\"sz-select-all\"><strong>" + localized_strings.select_all + "</strong></li>");
						$(response.data.players).each(function( key, value ) {
							$target.eq(0).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_player[" + index + "][]\">" + value.post_title + "</li>");
						});
						$target.eq(0).append("<li class=\"sz-ajax-show-all-container\"><a class=\"sz-ajax-show-all\" href=\"#show-all-sz_players\">" + localized_strings.show_all + "</a></li>");
					} else {
						$target.eq(0).html("<li>" + localized_strings.no_results_found + " <a class=\"sz-ajax-show-all\" href=\"#show-all-sz_players\">" + localized_strings.show_all + "</a></li>");
					}
				} else {
					if ( 1 == response.data.sections ) {
						defense = i;
						offense = i+1;
					} else {
						offense = i;
						defense = i+1;
					}
					if(response.data.players.length) {
						$target.eq(offense).append("<li class=\"sz-select-all-container\"><label class=\"selectit\"><input type=\"checkbox\" class=\"sz-select-all\"><strong>" + localized_strings.select_all + "</strong></li>");
						$target.eq(defense).append("<li class=\"sz-select-all-container\"><label class=\"selectit\"><input type=\"checkbox\" class=\"sz-select-all\"><strong>" + localized_strings.select_all + "</strong></li>");
						$(response.data.players).each(function( key, value ) {
							$target.eq(offense).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_offense[" + index + "][]\">" + value.post_title + "</li>");
							$target.eq(defense).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_defense[" + index + "][]\">" + value.post_title + "</li>");
						});
						$target.eq(offense).append("<li class=\"sz-ajax-show-all-container\"><a class=\"sz-ajax-show-all\" href=\"#show-all-sz_offense\">" + localized_strings.show_all + "</a></li>");
						$target.eq(defense).append("<li class=\"sz-ajax-show-all-container\"><a class=\"sz-ajax-show-all\" href=\"#show-all-sz_defense\">" + localized_strings.show_all + "</a></li>");
					} else {
						$target.eq(offense).html("<li>" + localized_strings.no_results_found + " <a class=\"sz-ajax-show-all\" href=\"#show-all-sz_offense\">" + localized_strings.show_all + "</a></li>");
						$target.eq(defense).html("<li>" + localized_strings.no_results_found + " <a class=\"sz-ajax-show-all\" href=\"#show-all-sz_defense\">" + localized_strings.show_all + "</a></li>");
					}
					i++;
				}
				i++;
				if(response.data.staff.length) {
					$target.eq(i).append("<li class=\"sz-select-all-container\"><label class=\"selectit\"><input type=\"checkbox\" class=\"sz-select-all\"><strong>" + localized_strings.select_all + "</strong></li>");
					$(response.data.staff).each(function( key, value ) {
						$target.eq(i).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_staff[" + index + "][]\">" + value.post_title + "</li>");
					});
					$target.eq(i).append("<li class=\"sz-ajax-show-all-container\"><a class=\"sz-ajax-show-all\" href=\"#show-all-sz_staffs\">" + localized_strings.show_all + "</a></li>");
				} else {
					$target.eq(i).html("<li>" + localized_strings.no_results_found + " <a class=\"sz-ajax-show-all\" href=\"#show-all-sz_staffs\">" + localized_strings.show_all + "</a></li>");
				}
			} else {
				$target.html("<li>" + localized_strings.no_results_found + "</li>");
			}
		});
	});

	// Activate Ajax trigger
	$(".sz-ajax-trigger").change(function() {
		$(".sz-ajax-checklist").siblings(".sz-tab-select").find("select").change();
	});

	// Ajax show all filter
	$(".sz-tab-panel").on("click", ".sz-ajax-show-all", function() {
		index = $(this).closest(".sz-instance").index();
		$(this).parent().html(localized_strings.loading);
		$.post( ajaxurl, {
			action:         "sz-get-players",
			index: 			index,
			nonce:          $("#sz-get-players-nonce").val()
		}).done(function( response ) {
			index = response.data.index;

			$target = $(".sz-instance").eq(index).find(".sz-ajax-checklist ul");
			$target.find(".sz-ajax-show-all-container").hide();
			if ( response.success ) {
				i = 0;

				if ( -1 == response.data.sections ) {
					if(response.data.players.length) {
						$(response.data.players).each(function( key, value ) {
							if($target.eq(i).find("input[value=" + value.ID + "]").length) return true;
							$target.eq(i).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_player[" + index + "][]\"> " + value.post_title + "</li>");
						});
					} else {
						$target.eq(i).html("<li>" + localized_strings.no_results_found + "</li>");
					}
				} else {
					if(response.data.players.length) {
						if ( 1 == response.data.sections ) {
							defense = i;
							offense = i+1;
						} else {
							offense = i;
							defense = i+1;
						}
						$(response.data.players).each(function( key, value ) {
							$target.eq(offense).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_offense[" + index + "][]\"> " + value.post_title + "</li>");
							$target.eq(defense).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_defense[" + index + "][]\"> " + value.post_title + "</li>");
						});
					} else {
						$target.eq(offense).html("<li>" + localized_strings.no_results_found + "</li>");
						$target.eq(defense).html("<li>" + localized_strings.no_results_found + "</li>");
					}
					i++;
				}
				i++;
				if(response.data.staff.length) {
					$(response.data.staff).each(function( key, value ) {
						$target.eq(i).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_staff[" + index + "][]\"> " + value.post_title + "</li>");
					});
				} else {
					$target.eq(i).html("<li>" + localized_strings.no_results_found + "</li>");
				}
			} else {
				$target.html("<li>" + localized_strings.no_results_found + "</li>");
			}
		});
	});

	// Event status selector
	$('.sz-edit-event-status').click(function(e) {
		e.preventDefault();
		$select = $(this).siblings('.sz-event-status-select');
		if ( $select.is(':hidden') ) {
			$select.slideDown( 'fast', function() {
				$select.find( 'input[type="radio"]' ).first().focus();
			} );
			$(this).hide();
		}
	});

	$('.sz-save-event-status').click(function(e) {
		e.preventDefault();
		$select = $(this).closest('.sz-event-status-select');
		$input = $select.find('input[name=sz_status]:checked');
		val = $input.val();
		label = $input.data('sz-event-status');
		$select.slideUp('fast').siblings('.sz-edit-event-status').show().siblings('.sz-event-status').find('.sz-event-status-display').data('sz-event-status', val).html(label);
	});

	$('.sz-cancel-event-status').click(function(e) {
		e.preventDefault();
		$select = $(this).closest('.sz-event-status-select');
		val = $select.siblings('.sz-event-status').find('.sz-event-status-display').data('sz-event-status');
		$select.find('input[value='+val+']').attr('checked', true);
		$select.slideUp('fast').siblings('.sz-edit-event-status').show();
	});

	// Box score time converter
	$('.sz-convert-time-input').change(function() {
		var s = 0;
		var val = $(this).val();
		if (val === '') {
			$(this).siblings('.sz-convert-time-output').val('');
			return;
		}
		var a = val.split(':').reverse();
		$.each(a, function( index, value ) {
			s += parseInt(value) * Math.pow(60, index);
		});
		$(this).siblings('.sz-convert-time-output').val(s);
	});

	// Trigger box score time converter
	$('.sz-convert-time-input').change();
	
	
	// Matches load team checklist on team selection 
	
	// loop through each match grouping

		
	
	$("#sz_matches_group_repeat").on('change', '.cmb-repeat-group-field.cmb-type-select-team select', function(e) {
		
		var $team_select_container 		= $(this).closest(".cmb-repeat-group-field.cmb-type-select-team");
		
		var team_select_container_id	= $team_select_container.find('select').attr('team_index');
		
		var team_index					= (team_select_container_id[team_select_container_id.length - 1]) - 1;
		var $player_select_container 	= $team_select_container.next();
		
		var data_iterator				= $team_select_container.closest(".cmb-repeatable-grouping").data('iterator');
		
		var match_id 					= $('#sz_matches_group_'+data_iterator+'_match_id').val();
		
		$player_select_container.find("ul").html("<li>" + localized_strings.loading + "</li>");
		
		$.post( ajaxurl, {
			action:         "sz-get-players",
			team: 			$(this).val(),
			index: 			team_index,
			nonce:          $("#_sz_event_edit_nonce_matches").val(),
			match_id:		match_id
		}).done(function( response ) {
			index = response.data.index;
			player_index = parseInt(index) + 1;
			$target = $player_select_container.find("ul");
			if ( response.success ) {
				$target.html("");
				i = 0;
				if(-1 == response.data.sections) {
					if(response.data.players.length) {
						$target.eq(i).append("<li class=\"sz-select-all-container\" style=\"display:none;\"><label class=\"selectit\"><input type=\"checkbox\" class=\"sz-select-all\"><strong>" + localized_strings.select_all + "</strong></li>");
						$(response.data.players).each(function( key, value ) {
							console.log(value);
							if ( response.data.selected != null) {
								if (response.data.selected.indexOf( value.ID + "") != -1) is_checked = 'checked=checked';
							} else {
								is_checked = '';
							}
							$target.eq(0).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_matches_group["+data_iterator+"][sz_players" + player_index + "][]\" "+is_checked+">" + value.post_title + "</li>");
						});
					} 
				} else {
					if ( 1 == response.data.sections ) {
						defense = i;
						offense = i+1;
					} else {
						offense = i;
						defense = i+1;
					}
					if(response.data.players.length) {
						
						$(response.data.players).each(function( key, value ) {
							$target.eq(offense).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_offense[" + index + "][]\">" + value.post_title + "</li>");
							$target.eq(defense).append("<li><label class=\"selectit\"><input type=\"checkbox\" value=\"" + value.ID + "\" name=\"sz_defense[" + index + "][]\">" + value.post_title + "</li>");
						});
						
					} 
					i++;
				}
				i++;
			} else {
				$target.html("<li>" + localized_strings.no_results_found + "</li>");
			}
		});
	});

	$("#sz_matches_group_repeat").find('.cmb-repeat-group-field.cmb-type-select-team select').each( function(){
		$(this).trigger('change'); 
	});
	
});