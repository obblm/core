$(document).ready(function () {
    $('.skill-tooltip').tooltip({
        content: function() {
            var element = $( this );
            if ( element.is( '.skill-tooltip' ) ) {
                return element.attr( "title" );
            }
        }
    });
});