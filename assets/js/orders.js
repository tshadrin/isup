import {orderDeleter, OrderMessager} from './OrderDeleter.class';
const autosize = require('autosize');
require('X-editable/dist/bootstrap3-editable/css/bootstrap-editable.css');
require('X-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js');
// Автоматическое изменение размера поля textarea с помощью библиотеки
jQuery(document).ready(function() {
    // Шаблон кнопок формы
    jQuery.fn.editableform.buttons =
        '<button type="submit" class="btn btn-primary btn-sm btn-primary-sham ml-1">\n'+
            '<i class="fas fa-check"></i>\n'+
        '</button>\n'+
        '<button type="button" class="btn btn-secondary btn-sm editable-cancel ml-1">\n'+
            '<i class="fa fa-times"></i>\n'+
        '</button>\n';
    
    // Шаблон формы
    jQuery.fn.editableform.template =
        '<form class="form-inline editableform">\n' +
            '<div class="row w-100 p-0 m-1">\n'+
                '<div class="col-12 editable-input p-0 mt-1 text-right"></div>\n'+
                '<div class="col-12 editable-buttons p-0 mt-1 text-nowrap text-right"></div>\n'+
                '<div class="editable-error-block"></div>\n'+
            '</div>\n'+
        '</form>\n';

    // Обработчик редактируемых полей форм
    var editable_field =  jQuery('.x-editable');
    editable_field.editable({inputclass: 'form-control form-control-sm w-100'});
    /**
     * автоматически раздвигать textarea
     */
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
