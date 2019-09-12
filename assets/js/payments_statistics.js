import Chart from 'chart.js';

document.addEventListener("DOMContentLoaded", function () {

    //stream-page
    const graphs = document.querySelectorAll("canvas.graph");
    graphs.forEach(function (graph) {
        const context = graph.getContext('2d');
        const labels = graph.dataset.labels.split(",");
        const counts = graph.dataset.counts.split(",");
        //const sums = graph.dataset.sums.split(",");
        const myLineChart = new Chart(context, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: graph.dataset.count_label,
                    data: counts,
                    backgroundColor: Color(graph.dataset.hourly?'rgb(255, 99, 132)':'rgb(56, 140, 17)').alpha(0.5).rgbString(),
                    borderColor: graph.dataset.hourly?'rgb(255, 99, 132)':'rgb(56, 140, 17)',
                }]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: "Месяц " + graph.dataset.server
                },
                elements: {
                    line: {
                        lineTension: 0.000001
                    }
                },
            }
        });
    });
});