<html>
<head>
    <script src="assets/chart.js/dist/Chart.bundle.js"></script>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/app.js"></script>
    <style>
        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
</head>
<body>
<div id="upTimeDiv">
    <h2>Host Internet</h2>
    <table style="width: 100%">
        <tr>
            <td style="vertical-align: top; text-align: center; width: 33%" id="upTimeHourly">
                <img src="assets/images/loading.gif" style="width:75px"/>
            </td>
            <td style="vertical-align: top; text-align: center; width: 33%" id="upTimeWeekly">
                <img src="assets/images/loading.gif" style="width:75px"/>
            </td>
            <td style="vertical-align: top; text-align: center; width: 33%" id="upTimeMonthly">
                <img src="assets/images/loading.gif" style="width:75px"/>
            </td>
        </tr>
    </table>
</div>
<div id="siteDivs">
</div>
</body>

</html>
