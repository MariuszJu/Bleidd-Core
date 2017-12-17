$(function() {

    var formListeners = function() {
        addLines();
        setLinesHeight();
        floatLabels();

        $('body').on('focus', '.form-row .form-element input, .form-row .form-element textarea, .form-row .form-element select', function() {
            var parent = $(this).parents('.form-element');
            var lines = parent.find('.lines');
            var input = parent.find('input, textarea, select');
            var inputHeight = input.outerHeight();

            lines.addClass('focus');
            parent.addClass('focus');
            parent.parent().find('label').addClass('focus');
        });
        $('body').on('focusout', '.form-row .form-element input, .form-row .form-element textarea, .form-row .form-element select', function() {
            var parent = $(this).parents('.form-element');
            var lines = parent.find('.lines');
            var input = parent.find('input, textarea, select');

            lines.removeClass('focus');
            parent.removeClass('focus');

            if (!input.val()) {
                parent.parent().find('label').removeClass('focus');
            }
        });
    };

    var addLines = function() {
        var lines = '<div class="lines"><span class="underline"></span><span class="focus"></span><span class="left"></span><span class="right"></span><span class="top"></span></div>';

        $('.form-row .form-element').each(function() {
            if (!$(this).hasClass('no-lines')) {
                $(this).append(lines);
            }
        });
    };

    var setLinesHeight = function() {
        var lines = $('.form-row .form-element .lines');

        lines.each(function() {
            var input = $(this).parents('.form-element').find('input, textarea, select');
            $(this).height(input.outerHeight());
        });
    };

    var floatLabels = function() {
        var labels = $('.form-row label');
        var input;

        labels.each(function() {
            input = $(this).parents('.form-row').find('input, select, textarea');

            if (input.val()) {
                $(this).addClass('focus');
            }
        });
    };

    formListeners();

});