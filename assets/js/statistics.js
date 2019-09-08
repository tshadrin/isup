import Chart from 'chart.js';
require('bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');
require('bootstrap-datepicker');
require('bootstrap-datepicker/js/locales/bootstrap-datepicker.ru');


document.addEventListener("DOMContentLoaded", function () {
    const input = jQuery('input[name="date"]');
    input.datepicker({
        language: "ru",
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });
    input.on("change", function (event) {
        this.form.submit();
    });

    //stream-page
    const graphs = document.querySelectorAll("canvas.graph");
    graphs.forEach(function (graph) {
        const context = graph.getContext('2d');
        const labels = graph.dataset.labels.split(",");
        const counts = graph.dataset.counts.split(",");
        const myLineChart = new Chart(context, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Пользователи онлайн',
                    data: counts,
                    backgroundColor: graph.dataset.hourly?'rgb(255, 99, 132)':'rgb(56, 140, 17)'
                }//next dataset in {}
                ]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: "Сервер " + graph.dataset.server
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: graph.dataset.hourly?false:true,
                        }
                    }]
                }
            }
        });
    });
});