<?php
ini_set('date.timezone', 'America/Chicago');
function print_rr()
{
    foreach (func_get_args() as $arg) {
        print "<pre>--------------------------\n";
        print_r($arg);
        print "\n--------------------------\n</pre>";
    }
}

function var_dumpr()
{
    foreach (func_get_args() as $arg) {
        print "<pre>--------------------------\n";
        var_dump($arg);
        print "\n--------------------------\n</pre>";
    }
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function gimmie_curl($url)
{ // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $url);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);
    return $output;
}

function ping($host, $timeout = 1)
{
    /* ICMP ping packet with a pre-calculated checksum */
    $package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
    $socket = socket_create(AF_INET, SOCK_RAW, 1);
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
    socket_connect($socket, $host, null);

    $ts = microtime(true);
    socket_send($socket, $package, strLen($package), 0);
    if (socket_read($socket, 255))
        $result = microtime(true) - $ts;
    else    $result = false;
    socket_close($socket);

    return $result;
}

function getChartBlocks()
{

    $labels = "01, 02, 03";
    $dataValues = "22,44,11";
    $title = "title";
    $label = "Months";
    $canvas = "canvas";
    $valueLabel = "value";
    $JSBlock = "
    var config_" . $canvas . " = {
        type: 'line',
        data: {
            labels: [" . $labels . "],
            datasets: [{
                label: 'Data By Month',
                data: [" . $dataValues . "],
                fill: false,
                borderColor: '#666666',
                backgroundColor: '#ffffff',
                pointBorderColor: '#000000',
                pointBackgroundColor: '#cccccc',
                pointBorderWidth: 1,

                }]
        },
        options: {
            responsive: true,
                    title: {
                display: true,
                        text: '" . $title . "'
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
                                labelString: '" . $label . "'
                            }
                        }],
                        yAxes: [{
                    display: true,
                            scaleLabel: {
                        display: true,
                                labelString: '" . $valueLabel . "'
                            },
                            ticks: {}
                        }]
                    }
                }
        };
    ";

    $onLoad = "
        var ctx_" . $canvas . " = document.getElementById('" . $canvas . "').getContext('2d');
        window.myLine = new Chart(ctx_" . $canvas . ", config_" . $canvas . ");
    ";
    $canvasHTML = "<canvas id='" . $canvas . "'></canvas>";
    return array(
        'JSBlock' => $JSBlock,
        'onLoad' => $onLoad,
        'canvasHTML' => $canvasHTML
    );

}
