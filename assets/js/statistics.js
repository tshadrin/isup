import Chart from 'chart.js';

document.addEventListener("DOMContentLoaded", function () {
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
                    //backgroundColor: 'rgb(255, 99, 132)'
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