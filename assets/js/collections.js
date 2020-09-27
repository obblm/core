jQuery(document).ready(function () {
    var $collections = $('.attach-collection-actions');
    $collections.each(function (i, el) {
        $( el ).delegate( '.add-another-collection-widget', "click", function (e) {
            e.preventDefault();
            var list = jQuery(jQuery(this).attr('data-list-selector'), el);
            // Try to find the counter of the list or use the length of the list
            var counter = $(list).data('widget-counter') || list.children().length;

            // grab the prototype template
            var newWidget = $(list).attr('data-prototype');
            // replace the "__name__" used in the id and name of the prototype
            // with a number that's unique to your emails
            // end name attribute looks like name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, counter);
            // Increase the counter
            counter++;
            // And store it, the length cannot be used if deleting widgets is allowed
            $(list).data('widget-counter', counter);

            // create a new list element and add it to the list
            jQuery(newWidget).appendTo(list);
            return false;
        });
        $( el ).delegate( '.remove-collection-widget', "click", function (e) {
            // Try to find the counter of the list or use the length of the list
            $(this).closest(jQuery(this).attr('data-item-selector'))
                .remove();
            e.preventDefault();
            return false;
        });
    });
});