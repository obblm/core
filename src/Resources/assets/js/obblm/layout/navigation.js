$(document).ready(function () {
    var header = $('#header');
    var body = $('body.pushable');
    var overlay = $('<div class="overlay" />');

    $('.launch', header).click(function(event) {
        $(body).toggleClass('open');
    });
    $(body).append(overlay)
    $(overlay).click(function(event) {
        $(body).toggleClass('open');
    });
});