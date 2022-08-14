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

    // Team edit form
    $("#editTeamForm").each(function (i, form) {
        $( ".accordion", form ).accordion({
            heightStyle: "content",
            classes: {
                "ui-accordion": "container"
            },
            icons: null,
            collapsible: true
        });
        $( ".sortable", form ).sortable({
            axis: "y",
            cursor: "move",
            items: "> .item",
            opacity: 0.8,
            handle: ".move"
        }).bind('sortupdate', function(event, ui) {
            var list = $(event.target);

            $('> .item', list).each(function(j, el) {
                $('.number span', el).text( j+1 );
                $('.number input', el).val( j+1 );
            });
        });
        $( ".sortable", form ).disableSelection();
    });
});
