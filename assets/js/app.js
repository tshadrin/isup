global.jQuery = require('jquery');
const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
Routing.setRoutingData(routes);
global.Routing = Routing;
require('@fortawesome/fontawesome-free/css/all.min.css');
require('../css/bootstrap.scss');
require('bootstrap');
require('bootstrap-confirmation2');
require('../css/style.css');
require('../css/media.css');

jQuery(document).ready(function() {
    // Автоматическое скрытие уведомлений.
    setTimeout(function() { jQuery('.alert-secondary').fadeOut('slow') },5000);
    setTimeout(function() { jQuery('.alert-danger').fadeOut('slow')    },5000);
    setTimeout(function() { jQuery('.alert-success').fadeOut('slow')   },5000);

    // Изменение количества отображаемых элементов на странице.
    jQuery('#rows-on-page-select').change(function () {
        var url  = jQuery(this).parent().attr('action');
        var data = jQuery(this).serialize();
        jQuery.post(url, data, location.reload());
        return false;
    });

    // Подтверждение удаления записи.
    jQuery('[data-toggle="confirmation"]').confirmation({
        rootSelector: '[data-toggle="confirmation"]',
        title: 'Вы хотите удалить эту запись?',
        btnCancelLabel: 'Нет',
        btnOkLabel: 'Да',
        btnOkClass: 'h-100 d-flex align-items-center btn btn-sm btn-primary btn-confirmation-sham'
    });

    // Показ или скрытие таблицы при клике.
    jQuery('.show-hide-button').click(function(){
        const plus_classes  = 'fa fa-plus';
        const minus_classes = 'fa fa-minus';

        var tbody = jQuery(this).parent().parent().children('tbody');
        var thead = jQuery(this).parent().parent().children('thead');
        var value = false;

        if('none' === thead.css('display') && 'none' === tbody.css('display')) {
            tbody.css('display', 'table-row-group');
            thead.css('display', 'table-header-group');
            jQuery(this).children('i').attr('class', minus_classes);
        } else {
            tbody.css('display', 'none');
            thead.css('display', 'none');
            jQuery(this).children('i').attr('class', plus_classes);
            value = true;
        }

        const hide_url = Routing.generate('ajax_showhide', {block_name: jQuery(this).attr('id'), value: value});

        jQuery.get(hide_url, null, function (data, status) {
            if ('success' === status) {
                if (data.result && 'error' === data.result) {
                    alert("Ошибка при изменении параметров.");
                }
            }
        });
    });
});
