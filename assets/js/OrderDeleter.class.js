'use strict';

import {OrderMessager} from './OrderMessager.class';
import {FadeOut} from './Fadeout.class';

/**
 * Удаляет заявку при отправке формы
 */
class OrderDeleter
{
    /**
     * @param form_class
     */
    constructor(form_class='test') {
        this.form_class = form_class;
    }

    /**
     * @param form_class
     */
    setFormClass(form_class) {
        this.form_class = form_class;
    }

    bind() {
        let forms = document.querySelectorAll(this.form_class);
        var myobject = this;
        if(forms.length > 0) {
            forms.forEach(function (form) {
                form.addEventListener("submit", function (event) {
                    let response = fetch(
                        this.action + '/ajax',
                        {
                            method: this.method,
                            body: new FormData(this)
                        })
                        .then(response => response.json())
                        .then(function (data) {
                            myobject.handleOrderDelete(form, data);
                        });
                    event.preventDefault();
                }, myobject);
            });
        }
    }

    /**
     * @param form
     * @param data
     */
    handleOrderDelete(form, data) {
        if(data.result === 'success') {
            this.hideOrder(form);
        }
        OrderMessager.showMessageBelowOrder(
            form,
            OrderMessager.prepareOrderMessage(
                data.message,
                data.result === 'success' ? 'alert-success': 'alert-danger'
            )
        );
    }

    /**
     * @param elem
     */
    hideOrder(elem) {
        var tr = elem.closest('tr');
        FadeOut.bindElement(tr, 2000);
    }
}
export const orderDeleter = new OrderDeleter();