(function ($) {
    'use strict';
    // tos checkbox.
    $(document).on('click', '.szxcftr-tos-checkbox', function () {
        var $this = $(this);
        var $hidden_tos = $this.parents('.editfield').find('.szxcftr-tos-checkbox-hidden');
        if ($this.is(':checked')) {
            $hidden_tos.val('1');
        } else {
            $hidden_tos.val('0');
        }
    });

    // colors
    if (!Modernizr.inputtypes.color) {
        // No html5 field colorpicker => Calling jscolor.
        $('.szxcftr-color').addClass('color');
    }

    $('#profile-edit-form').attr('enctype', 'multipart/form-data');
    $('#signup-form').attr('enctype', 'multipart/form-data');
    $('#your-profile').attr('enctype', 'multipart/form-data');

    // Slider.
    $('input.szxcftr-slider').on('input', function () {
        $('#output-' + $(this).attr('id')).html($(this).val());
    });

})(jQuery);