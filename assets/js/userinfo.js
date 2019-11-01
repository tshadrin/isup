import { HeaderMessager } from './HeaderMessager.class';
import {orderDeleter} from './OrderDeleter.class';
import {OrderMessager} from './OrderMessager.class';
import {EditableTemplates} from './EditableTemplates.class';
const autosize = require('autosize');
require('X-editable/dist/bootstrap3-editable/css/bootstrap-editable.css');
require('X-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js');

jQuery(document).ready(function() {
    // Скрывает и показывает элементы изменяя высоту и прозрачность блока при этом не трогая ширину
    jQuery.fn.slideFadeToggle = function(speed, easing, callback){
        return this.animate({opacity: 'toggle', height: 'toggle'}, speed, easing, callback);
    };

    // При клике на кнопку показа комментариев меняет её название и скрывает или показывает блок
    jQuery('[data-toggle="show-hide-block"]').click(function(){
        var e = jQuery(this);
        const show_text = e.data('show');
        const hide_text = e.data('hide');
        show_text === e.html()? e.html(hide_text): e.html(show_text);

        var target = jQuery(e.data('target'));
        target.slideFadeToggle('slow');
    });

    // Выполнение диагностических команд на сервере
    jQuery('a.diagnostic-command').click(function() {
        var target = jQuery(jQuery(this).data('target'));
        if('#' === jQuery(this).attr('href')) {
            target.html('');
        } else {
            jQuery.get(jQuery(this).attr('href'), null, function (data, status) {
                if ('success' === status) {
                    if (data.ping_data) {
                        target.html("<pre>" + data.ping_data + "</pre>");
                    } else {
                        target.html('');
                    }
                }
            });
        }
        return false;
    });

    /**
     * Функция при клике переключает значение поля
     * При этом текст ссылки меняется на значение аттрибута data-success
     * а значение data-success становится старым значением ссылки
     * Переход по ссылке блокируется
     */
    jQuery('a.ajax-switch-field').click(function (e) {
        var elem = jQuery(this);
        var new_success = elem.html();
        var success = elem.data('success');
        jQuery.get(elem.attr('href'),null, function (data) {
            if('success' === data.result) {
                elem.html(success);
                elem.data('success', new_success);
            }
        });
        e.preventDefault();
    });

    jQuery.fn.editableform.template = EditableTemplates.getFormTemplate();
    jQuery.fn.editableform.buttons = EditableTemplates.getButtonTemplate();

    // Обработчик редактируемых полей форм
    var editable_field =  jQuery('.x-editable');
    editable_field.editable({
        inputclass: 'form-control form-control-sm',
        escape: false,
        success: function (response, newValue) {
            if(response.result === 'error') {
                return response.message;
            }
            if(response.result === 'success') {
                newValue = response.newValue;
                HeaderMessager.showMessageAfterHeader(
                    document.querySelector('header.main-header'),
                    HeaderMessager.prepareHeaderMessage(response.message)
                );
            }
        }
    });

    // Обработчик редактируемых полей форм заявки
    var editable_order_field = jQuery('.x-editable-order');
    editable_order_field.editable({
        inputclass: 'form-control form-control-sm w-100',
        escape: false,
        success: function(response, newValue) {
            if('error' === response.result) {
                return response.message;
            }
        },
        error: function(response, newValue) {
            if(404 === response.status) {
                return "Order not found";
            }
            return response.responseJSON.message;
        },
    });
    /**
     * автоматически раздвигать textarea
     */
    editable_order_field.on('shown', function(e, editable) {
        if('textarea' === editable.options.type) {
            var editable_order_field_textarea = document.querySelector('textarea');
            editable_order_field_textarea.addEventListener('focus', function(){ autosize(editable_order_field_textarea); });
        }
    });
    editable_order_field.on('save', function(e, params) {
        OrderMessager.showMessageBelowOrder(
            this,
            OrderMessager.prepareOrderMessage(params.response.message)
        );
    });
    orderDeleter.setFormClass(".delete-order-form");
    orderDeleter.bind();

    const callform = document.getElementById('typical_call_form_add');
    callform.addEventListener('click', function(event) {
        const form = this.closest('form');
        let response = fetch(
            form.action + '/ajax',
            {
                method: form.method,
                body: new FormData(form)
            })
            .then(response => response.json())
            .then(function (data) {
                HeaderMessager.showMessageAfterHeader(
                    document.querySelector('header.main-header'),
                    data.result === 'success' ?
                        HeaderMessager.prepareHeaderMessage(data.message) :
                        HeaderMessager.prepareHeaderErrorMessage(data.message)
                );
            });
        event.preventDefault();
    });
});