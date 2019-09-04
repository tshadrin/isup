import Chart from 'chart.js';

document.addEventListener("DOMContentLoaded", function () {
    const labels = ['00', '01', '02','03', '04', '05', '06', '07', '08', '09', '10',
        '11', '12', '13','14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24'];
    const data = [1186, 1124, 859, 347, 977, 849, 864, 809, 925, 980, 969, 1128, 1364, 1849, 1843, 1764,
        1788, 1892, 1886, 1465, 1498, 1859, 1488, 1455, 1569];
    const data2 = [1186/2, 1124/2, 859/2, 347, 977/2, 849/2, 864/2, 809/2, 925/2, 980/2, 969/2, 1128/2, 1364/2, 1849/2, 1843/2, 1764/2,
        1788/2, 1892/2, 1886/2, 1465/2, 1498/2, 1859/2, 1488/2, 1455/2, 1569/2];
    const graphs = document.querySelectorAll("canvas.graph");
    graphs.forEach(function (graph) {
        const context = graph.getContext('2d');
        const labels = jQuery(graph).data('labels').split(",");
        const counts = jQuery(graph).data('counts').split(",");
        const myLineChart = new Chart(context, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Пользователи онлайн',
                    data: counts,
                    //backgroundColor: 'rgb(255, 99, 132)'
                    backgroundColor: 'rgb(56, 140, 17)'
                }]
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
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });
    /*
    var ctx2 = document.getElementById('myChart2').getContext('2d');
    var myLineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Пользователи онлайн',
                data: data,
                backgroundColor: 'rgb(255, 99, 132)'
            },
                {
                    label: 'Пользователи онлайн вчера',
                    data: data,
                    backgroundColor: 'rgb(255, 99, 248)'
                }
            ]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Agro'
            }
        }
    });
    var myLineChart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: data2,
                backgroundColor: 'rgb(255, 99, 132)'
            }]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'L4B2'
            }
        }
    });*/
});