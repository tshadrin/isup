'use strict';

class HeaderMessager {
    static showMessageAfterHeader(header, message) {
        header.after(message);
        setTimeout(function() { jQuery('.dynamic-flash').fadeOut('slow') },5000);
    }
    static prepareHeaderMessage(message) {
        return '<div class="alert alert-success m-2 dynamic-flash" role="alert">\n' +
            message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
            '<span aria-hidden="true">&times;</span>\n' +
            '</button>\n' +
            '</div>';
    }
}
export { HeaderMessager };