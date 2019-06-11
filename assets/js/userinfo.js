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
            elem.html(success);
            elem.data('success',new_success);
        });
        e.preventDefault();
    });

    // Шаблон кнопок формы
    jQuery.fn.editableform.buttons =
        '<button type="submit" class="btn btn-primary btn-sm btn-primary-sham ml-0">\n'+
        '<i class="fas fa-check"></i>\n'+
        '</button>\n'+
        '<button type="button" class="btn btn-secondary btn-sm editable-cancel ml-0">\n'+
        '<i class="fa fa-times"></i>\n'+
        '</button>\n';


    // Шаблон формы
    jQuery.fn.editableform.template =
        '<form class="form-inline editableform">\n' +
        '<div class="row justify-content-between w-100 p-0 m-1">\n'+
        '<div class="col-8 editable-input pr-2"></div>\n'+
        '<div class="col-4 editable-buttons pl-2"></div>\n'+
        '<div class="editable-error-block"></div>\n'+
        '</div>\n'+
        '</form>\n';

    // Обработчик редактируемых полей форм
    var editable_field =  jQuery('.x-editable');
    editable_field.editable();

    editable_field.on('save', function(e, params) {
        var elem = jQuery(this);
        var msg = params.response.message;
        var header = jQuery('header.main-header');
        jQuery(header).after(
            '<div class="alert alert-success m-2 dynamic-flash" role="alert">\n' +
                 params.response.message +
            '    <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
            '        <span aria-hidden="true">&times;</span>\n' +
            '    </button>\n' +
            '</div>'
        );
        setTimeout(function() { jQuery('.dynamic-flash').fadeOut('slow') },5000);
    });
});