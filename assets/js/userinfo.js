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
});