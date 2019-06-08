jQuery(document).ready(function() {
    // Добавление и удаление динамичекских полей.
    jQuery('.add-another-collection-widget').click(function (e) {
        var list = jQuery(jQuery(this).data('list'));
        var counter = list.data('widget-counter') | list.children().length;
        var new_widget = list.data('prototype');

        new_widget = new_widget.replace(/__name__/g, counter);
        new_widget =
            new_widget +
            '<div class="input-group-append">' +
            '   <button class="btn btn-secondary remove-field-button">-</button>' +
            '</div>';
        counter++;
        list.data('widget-counter', counter);

        var newElem = jQuery(list.data('widget-tags')).html(new_widget);
        newElem.appendTo(list);
    });
    jQuery("body").on("click", ".remove-field-button", function(){
        jQuery(this).parent().parent(".dynamic-filed").remove();
    });
});