import {orderDeleter} from './OrderDeleter.class';
import {OrderMessager} from './OrderMessager.class';
import {EditableTemplates} from './EditableTemplates.class';
const autosize = require('autosize');
require('X-editable/dist/bootstrap3-editable/css/bootstrap-editable.css');
require('X-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js');
// Автоматическое изменение размера поля textarea с помощью библиотеки
jQuery(document).ready(function() {
    // Шаблон кнопок формы
    jQuery.fn.editableform.buttons = EditableTemplates.getButtonTemplate();
    // Шаблон формы
    jQuery.fn.editableform.template = EditableTemplates.getFormTemplate('text-right');

    // Обработчик редактируемых полей форм
    let editable_field =  jQuery('.x-editable');
    editable_field.editable({
        inputclass: 'form-control form-control-sm w-100'
    });
    //автоматически изменять размер textarea
    editable_field.on('shown', function(e, editable) {
        if('textarea' === editable.options.type) {
            var editable_order_field_textarea = document.querySelector('textarea');
            editable_order_field_textarea.addEventListener(
                'focus',
                function(){ autosize(editable_order_field_textarea); }
                );
        }
    });
    editable_field.on('save', function(e, params) {
        OrderMessager.showMessageBelowOrder(
            this,
            OrderMessager.prepareOrderMessage(params.response.message)
        );
    });
    orderDeleter.setFormClass(".delete-order-form");
    orderDeleter.bind();
});
