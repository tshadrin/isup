'use strict';

/**
 * Удаляет заявку при отправке формы
 */
class OrderDeleter
{
    constructor(form_class='test') {
        this.form_class = form_class;
    }

    setFormClass(form_class) {
        this.form_class = form_class;
    }

    bind() {
        let form = document.querySelector(this.form_class);
        var myobject = this;
        form.addEventListener("submit", function(event) {
            let response = fetch(
                this.action + '/ajax',
                {
                    method: this.method,
                    body: new FormData(this)
                })
                .then(response => response.json())
                .then(function(data) {
                    myobject.handleOrderDelete(form, data);
                });
            event.preventDefault();
        }, myobject);
    }

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

    hideOrder(elem) {
        var tr = elem.closest('tr');
        setTimeout(function() { OrderMessager.fadeOut(tr) },5000);
    }
}
export const orderDeleter = new OrderDeleter();

/**
 * Добавляет сообщение после действия с заявкой
 */
class OrderMessager {
    static showMessageBelowOrder(elem, message) {
        var tr = elem.closest('tr');
        tr.insertAdjacentHTML('afterEnd', message);
        setTimeout(function() { OrderMessager.fadeOut('.dynamic-flash') }, 3000);
    }
    static prepareOrderMessage(message, alert_class="alert-success") {
        return '<tr class="dynamic-flash">\n' +
            '    <td colspan="8">\n' +
            '        <div class="alert ' + alert_class + ' m-0" role="alert">\n' +
            message +
            '            <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
            '                <span aria-hidden="true">&times;</span>\n' +
            '            </button>\n' +
            '        </div>' +
            '    </td>' +
            '</tr>';
    }

    static fadeOut(selector, timeout=10) {
        var opacity = 1;
        var timer = setInterval(function() {
            if(opacity <= 0.1) {
                clearInterval(timer);
                document.querySelector(selector).style.display = "none";
            }
            document.querySelector(selector).style.opacity = opacity;
            opacity -= opacity * 0.1;
        }, timeout);
    }
}

export { OrderMessager };