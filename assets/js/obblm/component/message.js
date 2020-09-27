$(document).ready(function () {
    $closables = $('.message.closable');
    $closables.each(function (i,el) {
        $(el).prepend($('<a class="closer close"></a>'));
        $('.close', el).click(function() {
           $(el).hide('fade');
        });
    });
});