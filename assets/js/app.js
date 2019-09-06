require('../css/app.scss');

require('bootstrap');
require('bootstrap-confirmation2');
global.jQuery = require('jquery');
import {FadeOut} from './Fadeout.class';
const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
Routing.setRoutingData(routes);
global.Routing = Routing;
const toastr = require('toastr');
const Centrifuge = require('centrifuge');


toastr.options.timeOut = "50000";
toastr.options.progressBar = true;
var arrow = document.getElementById("arrow");
document.body.onscroll = function(e){
    if(pageYOffset >=70){
        arrow.hidden= document.getElementById("arrow").hidden = false;
    }
    else{
        arrow.hidden =true;
    }
};
arrow.onclick = function(e){
    window.scrollTo(0,0);
};

document.addEventListener('DOMContentLoaded', function () {
    let url = document.querySelector('meta[name=centrifugo-url]').getAttribute('content');
    let user = document.querySelector('meta[name=centrifugo-user]').getAttribute('content');
    let token = document.querySelector('meta[name=centrifugo-token]').getAttribute('content');
    let centrifuge = new Centrifuge(url);
    centrifuge.setToken(token);
    centrifuge.subscribe('calls#' + user, function (message) {
        toastr.info(message.data.message, message.data.title);
    });
    centrifuge.subscribe("calls", function(message) {
        toastr.info(message.data.message, message.data.title);
    });
    centrifuge.connect();

    FadeOut.bind('.alert-secondary', 5000);
    FadeOut.bind('.alert-danger', 5000);
    FadeOut.bind('.alert-success', 5000);

    const show_hide_buttons = document.querySelectorAll(".show-hide-button");
    show_hide_buttons.forEach(function (e) {
        e.addEventListener('click', function (event) {
            const table  = this.closest('table');
            const tbody = table.querySelector("tbody");
            const thead = table.querySelector("thead");
            const icon_classes = JSON.parse(this.dataset.iconclass);
            const state = this.dataset.state;
            const icon = this.querySelector('i');

            var value = false;
            if (state === 'visible') {
                this.dataset.state = 'hidden';
                icon.classList.remove(icon_classes.visible_class);
                icon.classList.add(icon_classes.hidden_class);
                tbody.classList.add('d-none');
                thead.classList.add('d-none');
                value = true;
            } else if(state === 'hidden') {
                this.dataset.state = 'visible';
                icon.classList.remove(icon_classes.hidden_class);
                icon.classList.add(icon_classes.visible_class);
                tbody.classList.remove('d-none');
                thead.classList.remove('d-none');
            }

            const hide_url = Routing.generate('ajax_showhide', {block_name: this.id, value: value});
            let response = fetch(hide_url, {
                method: "GET",
            })
                .then(response => response.json())
                .then(function (data) {
                    if(data.result  === 'error')
                        alert("Ошибка при выполнении запроса.");
                });
        })
    });

    const confirm = document.querySelectorAll('[data-toggle="confirmation"]');
    confirm.forEach(function (element) {
        jQuery(element).confirmation({
            rootSelector: '[data-toggle="confirmation"]',
            title: 'Вы хотите удалить эту запись?',
            btnCancelLabel: 'Нет',
            btnOkLabel: 'Да',
            btnOkClass: 'h-100 d-flex align-items-center btn btn-sm btn-primary btn-confirmation-sham'
        });
    });
});