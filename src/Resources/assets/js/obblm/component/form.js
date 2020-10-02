$(document).ready(function () {
    $forms = $('.form');

    $forms.each(function (i, form) {
        $(form).delegate('.field', "blur", function(e) {
            $('.field', form).removeClass('focused');
            //$(e.currentTarget).addClass('focused');
        });
        $(form).delegate('.field', "focusin", function(e) {
            $('.field', form).removeClass('focused');
            $(e.currentTarget).addClass('focused');
        });
    });
});