var baseConfig;
var baseLoading;
$(document).ready(function ()
{
    baseConfig = getBaseConfig();
    baseLoading = getBaseLoading();
    loadSiteBoxes();


});

function loadEntity(f, mode, report)
{
    canvasName = report + mode  ;

    $("#" + canvasName).html(baseLoading);
    url2Post = "/ajax.php";
    PostVars = "f=" + f  + "&report=" + report + "&mode=" + mode + "&canvas=" + canvasName;
    $.ajax({
        url: url2Post,
        type: "POST",
        data: PostVars,
        dataType: "json",
        cache: false,
        async: false
    }).done(function (data)
    {
        canvasHTMLId = "#" + data.canvas +"Canvas";
        canvasHTML = "<canvas id='" + canvasHTMLId + "'></canvas>";
        config = baseConfig;

        config.data.labels =data.dataPointLabels;
        config.data.datasets[0].data = data.dataPointValues;
        config.options.title.text = data.title;
        config.options.scales.xAxes[0].scaleLabel.labelString = data.xAxis;
        config.options.scales.yAxes[0].scaleLabel.labelString = data.yAxis;

        $("#" + data.canvas).html(canvasHTML);
        var ctx = document.getElementById(canvasHTMLId).getContext("2d");
        window.myLine = new Chart(ctx, config);

    });
}

function getBaseLoading()
{
    return "<img src=\"/assets/images/loading.gif\" style=\"width:75px\"/>";
}
function getBaseConfig()
{

    return {
        type: 'line',
        data: {
            labels: [],//<?=$labelsToRender?>
            datasets: [{
                label: "",//Data By Month
                data: [],//<?=$dataChartToRender?>
                fill: false,
                borderColor: "#339933",
                backgroundColor: "#000066",

            }]
        },
        options: {

            legend: {
                display: false,
            },
            responsive: true,
            title: {
                display: true,
                text: ''//Percentage Uptime
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
                        labelString: ''//Month
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: ''//Value
                    },
                    ticks: {}
                }]
            }
        }
    };

}

function loadSiteBoxes()
{
    url2Post = "/ajax.php";
    PostVars = "f=loadSiteDivs";
    $.ajax({
        url: url2Post,
        type: "POST",
        data: PostVars,
        dataType: "json",
        cache: false
    }).done(function (data)
    {

        html = "";
        for(x=0;x<data.length;x++)
        {
            html += data[x].div;
        }
        $("#siteDivs").html(html);
        loadEntity('loadUpTime', 'Hourly', 'upTime');
        loadEntity('loadUpTime', 'Weekly', 'upTime');
        loadEntity('loadUpTime', 'Monthly', 'upTime');
        for(x=0;x<data.length;x++)
        {
            loadEntity('loadSiteStats', 'Hourly', data[x].siteName);
            loadEntity('loadSiteStats', 'Weekly', data[x].siteName);
            loadEntity('loadSiteStats', 'Monthly', data[x].siteName);
        }
    });
}