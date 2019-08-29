//autosize textarea lib
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
        '<div class="col-12 editable-input p-0 mt-1"></div>\n'+
        '<div class="col-12 editable-buttons p-0 mt-1"></div>\n'+
        '<div class="editable-error-block"></div>\n'+
        '</div>\n'+
        '</form>\n';

    // Обработчик редактируемых полей форм
    var editable_field =  jQuery('.x-editable');
    editable_field.editable({inputclass: 'form-control form-control-sm'});
    editable_field.on('shown', function(e, editable) {
        if('textarea' === editable.options.type) {
           var ta = jQuery('textarea');
           ta.on('focus', function(){ autosize(ta); });
        }
    });
    editable_field.on('save', function(e, params) {
        var elem = jQuery(this);
        var msg = params.response.message;
        var tr = jQuery(elem).parents('tr').first();
        jQuery(tr).after(
            '<tr class="dynamic-flash">\n' +
            '    <td colspan="8">\n' +
            '        <div class="alert alert-success m-0" role="alert">\n' +
                         params.response.message +
            '            <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
            '                <span aria-hidden="true">&times;</span>\n' +
            '            </button>\n' +
            '        </div>' +
            '    </td>' +
            '</tr>'
        );
        setTimeout(function() { jQuery('.dynamic-flash').fadeOut('slow') },5000);
    });

    jQuery('.delete-order-form').submit(function(e) {
        $form = jQuery(this);
        jQuery.ajax({
            type: $form.attr('method'),
            url: $form.attr('action') + '/ajax',
            data: $form.serialize()
        }).done(function(data) {
            var tr = jQuery($form).parents('tr').first();
            if(data.result === 'success') {
                var alert_class = 'alert-success';
                jQuery(tr).fadeOut('slow');
            } else if (data.result === 'error') {
                var alert_class = 'alert-danger';
            }
            jQuery(tr).after(
                '<tr class="dynamic-flash">\n' +
                '    <td colspan="8">\n' +
                '        <div class="alert ' + alert_class + ' m-0" role="alert">\n' +
                data.message +
                '            <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                '                <span aria-hidden="true">&times;</span>\n' +
                '            </button>\n' +
                '        </div>' +
                '    </td>' +
                '</tr>'
            );
            setTimeout(function() { jQuery('.dynamic-flash').fadeOut('slow') },7000);
        }).fail(function() {
            console.log('fail');
        });
        e.preventDefault();
    });
});