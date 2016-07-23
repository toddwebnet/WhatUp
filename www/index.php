<?php
require_once "../config.php";
require_once "../includes/helpers.php";
require_once "../includes/mysql.class.php";
require_once "../includes/whatupdb.class.php";
$whatUp = new WhatupDb(new Mysql());
$outagesByMonth = $whatUp->getOutagesByMonth('2016-01-01', date("Y-m-d", time()));
foreach ($outagesByMonth as $date => $data) {
    $labelsChart[] = '"' . $date . '"';
    $dataChart[] = $data['percent'];
}
$labelsToRender = implode(',', $labelsChart);
$dataChartToRender = implode(',', $dataChart);
$siteStats = $whatUp->getSiteStats('2016-01-01', date("Y-m-d", time()));


?>
<html>
<head>
    <script src="/assets/chart.js/dist/Chart.bundle.js"></script>
    <script src="/assets/js/jquery.js"></script>
    <style>
        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
</head>
<body>
<div style="width:75%;">
    <canvas id="canvas"></canvas>
    <canvas id="canvas1"></canvas>
</div>
<br>
<br>
<script>

    var config = {
        type: 'line',
        data: {
            labels: [<?=$labelsToRender?>],
            datasets: [{
                label: "Data By Month",
                data: [<?=$dataChartToRender?>],
                fill: false,
                borderColor: "#666666",
                backgroundColor: "#ffffff",
                pointBorderColor: "#000000",
                pointBackgroundColor: "#cccccc",
                pointBorderWidth: 1,

                }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Percentage Uptime'
            },
            tooltips: {
                mode: 'label',
                callbacks: {}
            },
            hover: {
                mode: 'dataset'
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    },
                    ticks: {}
                }]
            }
        }
    };


    window.onload = function () {
        var ctx = document.getElementById("canvas").getContext("2d");
        window.myLine = new Chart(ctx, config);
    };


</script>
</body>
