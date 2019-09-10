import Chart from 'chart.js';

require('moment');
require('jquery-date-range-picker/src/daterangepicker.scss');
require('jquery-date-range-picker');

document.addEventListener("DOMContentLoaded", function () {
    jQuery('input[name="date"]')
        .dateRangePicker({
        autoClose: true,
        singleDate : true,
        showShortcuts: false,
        singleMonth: true,
        startOfWeek: 'monday',
        format: 'DD-MM-YYYY',
        language: 'ru',
        time: {
            enabled: false
        }
    })
        .bind('datepicker-change', function() {
        this.form.submit();
    });

    jQuery('input[name="week"]')
        .dateRangePicker({
        startOfWeek: 'monday',
        format: 'DD-MM-YYYY',
        language: 'ru',
        autoClose: true,
        batchMode: 'week',
        separator : ' - ',
        showShortcuts: false,
        time: {
            enabled: false
        }
    })
        .bind('datepicker-change', function() {
            this.form.submit();
    });
    jQuery('input[name="month"]')
        .dateRangePicker({
        autoClose: true,
        singleDate : true,
        batchMode: 'month',
        showShortcuts: false,
        singleMonth: true,
        startOfWeek: 'monday',
        format: 'MM-YYYY',
        language: 'ru',
        time: {
            enabled: false
        },
        monthSelect: true,
        yearSelect: true
    })
        .bind('datepicker-change', function() {
        this.form.submit();
    });

    //stream-page
    const graphs = document.querySelectorAll("canvas.graph");
    graphs.forEach(function (graph) {
        const context = graph.getContext('2d');
        const labels = graph.dataset.labels.split(",");
        const counts = graph.dataset.counts.split(",");
        const myLineChart = new Chart(context, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: graph.dataset.label,
                    data: counts,
                    backgroundColor: Color(graph.dataset.hourly?'rgb(255, 99, 132)':'rgb(56, 140, 17)').alpha(0.5).rgbString(),
                    borderColor: graph.dataset.hourly?'rgb(255, 99, 132)':'rgb(56, 140, 17)'
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
                elements: {
                    line: {
                        lineTension: 0.000001
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            max: Math.round(graph.dataset.max * 1.03 / 10) * 10,
                            min: Math.round(graph.dataset.min * 0.97/ 10) * 10
                        }
                    }]
                }
            }
        });
    });
});