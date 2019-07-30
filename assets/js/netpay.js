require('moment');
require('jquery-date-range-picker/src/daterangepicker.scss');
require('jquery-date-range-picker');

jQuery(document).ready(function() {
    jQuery('#form_interval').dateRangePicker({
        startOfWeek: 'monday',
        format: 'DD-MM-YYYY',
        language: 'ru',
        separator : ' - ',
        autoClose: true,
        time: {
            enabled: false
        }
    });
});