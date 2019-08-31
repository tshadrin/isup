'use strict';

import {FadeOut} from './Fadeout.class';

class HeaderMessager
{
    /**
     * @param header
     * @param message
     */
    static showMessageAfterHeader(header, message) {
        header.insertAdjacentHTML('afterEnd', message);
        FadeOut.bind('div.dynamic-flash', 3000);
    }

    /**
     * @param message
     * @returns {string}
     */
    static prepareHeaderMessage(message) {
        return `<div class="alert alert-success m-2 dynamic-flash" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>`;
    }
}

export { HeaderMessager };