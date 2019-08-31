'use strict';

import {FadeOut} from './Fadeout.class';

/**
 * Добавляет сообщение после действия с заявкой
 */
class OrderMessager
{
    /**
     * @param elem
     * @param message
     */
    static showMessageBelowOrder(elem, message) {
        var tr = elem.closest('tr');
        tr.insertAdjacentHTML('afterEnd', message);
        FadeOut.bind("tr.dynamic-flash");
    }

    /**
     * @param message
     * @param alert_class
     * @returns {string}
     */
    static prepareOrderMessage(message, alert_class="alert-success") {
        return `<tr class="dynamic-flash">
                    <td colspan="8">
                        <div class="alert ${alert_class} m-0" role="alert">
                            ${message}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </td>
                </tr>`;
    }
}

export { OrderMessager };