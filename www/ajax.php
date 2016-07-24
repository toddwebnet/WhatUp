<?php
require_once "../config.php";
require_once "../includes/helpers.php";
require_once "../includes/mysql.class.php";
require_once "../includes/whatupdb.class.php";

$whatUp = new WhatupDb(new Mysql());
$months = array(
    "01" => "Jan", "02" => "Feb", "03" => "Mar",
    "04" => "Apr", "05" => "May", "06" => "Jun",
    "07" => "Jul", "08" => "Aug", "09" => "Sep",
    "10" => "Oct", "11" => "Nov", "12" => "Dec"
);
if ($_POST['f'] == "loadUpTime")
{

    if ($_POST['mode'] == "Hourly")
    {
        $endDate = date("Y-m-d H:i", time());
        $startDate = date("Y-m-d H:i", strtotime($endDate . " - 12 hours"));
        $dataJunk = $whatUp->getUpTimeGroup($startDate, $endDate, "hour");
        $labels = array();
        $values = array();
        $sum = 0;
        $count = 0;
        foreach ($dataJunk as $label => $value)
        {
            $count++;
            $sum += $value['percent'];
            $labels[] = hourLabel($label);
            $values[] = $value['percent'];
        }
        $avg = round($sum / $count, 2);
        $array = array(
            "dataPointLabels" => $labels,
            "dataPointValues" => $values,
            "title" => "WhatUp Host Up Type By Hour (" . $avg . ")",
            "xAxis" => "Hour",
            "yAxis" => "Percent Up",
            "canvas" => $_POST['canvas']
        );
    }
    else if ($_POST['mode'] == "Weekly")
    {
        $endDate = date("Y-m-d H:i", time());
        $startDate = date("Y-m-d H:i", strtotime($endDate . " - 12 weeks"));
        $dataJunk = $whatUp->getUpTimeGroup($startDate, $endDate, "week");
        $labels = array();
        $values = array();
        $sum = 0;
        $count = 0;
        foreach ($dataJunk as $label => $value)
        {
            $count++;
            $sum += $value['percent'];
            $labels[] = weekLabel($label);
            $values[] = $value['percent'];
        }
        $avg = round($sum / $count, 2);

        $array = array(
            "dataPointLabels" => $labels,
            "dataPointValues" => $values,
            "title" => "WhatUp Host Up Type By Week (" . $avg . ")",
            "xAxis" => "Year - Week",
            "yAxis" => "Percent Up",
            "canvas" => $_POST['canvas']
        );
    }
    else if ($_POST['mode'] == "Monthly")
    {
        $endDate = date("Y-m-d H:i", strtotime(date("Y-m-01", time()) . " + 1 month"));
        $startDate = date("Y-m-d H:i", strtotime($endDate . " - 12 months"));
        $dataJunk = $whatUp->getUpTimeGroup($startDate, $endDate, "month");
        $labels = array();
        $values = array();
        $sum = 0;
        $count = 0;
        foreach ($dataJunk as $label => $value)
        {
            $count++;
            $sum += $value['percent'];
            $labels[] = $months[$label];
            $values[] = $value['percent'];
        }
        $avg = round($sum / $count, 2);

        $array = array(
            "dataPointLabels" => $labels,
            "dataPointValues" => $values,
            "title" => "WhatUp Host Up Type By Month (" . $avg . ")",
            "xAxis" => "Month",
            "yAxis" => "Percent Up",
            "canvas" => $_POST['canvas']
        );
    }

    print json_encode($array);
}
if ($_POST['f'] == "loadSiteStats")
{
    if ($_POST['mode'] == "Hourly")
    {
        $endDate = date("Y-m-d H:i", time());
        $startDate = date("Y-m-d H:i", strtotime($endDate . " - 12 hours"));
        $dataJunk = $whatUp->getSiteStats($_POST['report'], $startDate, $endDate, "hour");
        $labels = array();
        $values = array();
        $sum = 0;
        $count = 0;
        foreach ($dataJunk as $label => $value)
        {
            $count++;
            $sum += $value['percent'];
            $labels[] = hourLabel($label);
            $values[] = $value['percent'];
        }
        $avg = round($sum / $count, 2);

        $array = array(
            "dataPointLabels" => $labels,
            "dataPointValues" => $values,
            "title" => $_POST['report'] . " By Hour (" . $avg . ")",
            "xAxis" => "Hour",
            "yAxis" => "Percent Up",
            "canvas" => str_replace(".", "_", $_POST['canvas'])
        );
    }
    else if ($_POST['mode'] == "Weekly")
    {
        $endDate = date("Y-m-d H:i", time());
        $startDate = date("Y-m-d H:i", strtotime($endDate . " - 12 weeks"));
        $dataJunk = $whatUp->getSiteStats($_POST['report'], $startDate, $endDate, "week");
        $labels = array();
        $values = array();
        $sum = 0;
        $count = 0;
        foreach ($dataJunk as $label => $value)
        {
            $count++;
            $sum += $value['percent'];
            $labels[] = weekLabel($label);
            $values[] = $value['percent'];
        }
        $avg = round($sum / $count, 2);

        $array = array(
            "dataPointLabels" => $labels,
            "dataPointValues" => $values,
            "title" => $_POST['report'] . " By Week (" . $avg . ")",
            "xAxis" => "Week",
            "yAxis" => "Percent Up",
            "canvas" => str_replace(".", "_", $_POST['canvas'])
        );
    }
    else if ($_POST['mode'] == "Monthly")
    {
        $endDate = date("Y-m-d H:i", strtotime(date("Y-m-01", time()) . " + 1 month"));
        $startDate = date("Y-m-d H:i", strtotime($endDate . " - 12 months"));
        $dataJunk = $whatUp->getSiteStats($_POST['report'], $startDate, $endDate, "month");

        $labels = array();
        $values = array();
        $sum = 0;
        $count = 0;
        foreach ($dataJunk as $label => $value)
        {
            $count++;
            $sum += $value['percent'];
            $labels[] = $months[$label];
            $values[] = $value['percent'];
        }
        $avg = round($sum / $count, 2);
        $array = array(
            "dataPointLabels" => $labels,
            "dataPointValues" => $values,
            "title" => $_POST['report'] . " Up Type By Month (" . $avg . ")",
            "xAxis" => "Month",
            "yAxis" => "Percent Up",
            "canvas" => str_replace(".", "_", $_POST['canvas'])
        );
    }

    print json_encode($array);
}

if ($_POST['f'] == "loadSiteDivs")
{
    $sites = $whatUp->getSites();
    $ret = array();
    foreach ($sites as $site)
    {
        $siteCode = str_replace('.', '_', $site['address']);
        $ret[] = array(
            'siteName' => $site['address'],
            'siteCode' => $siteCode,
            'div' => siteDivHTML($site['address'], $siteCode)
        );
    }
    print json_encode($ret);
}

function hourLabel($label)
{
    list($day, $hour) = explode(" ", $label);
    return $hour . ":00";

}

function weekLabel($label)
{
    list($year, $week) = explode(" ", $label);
    return $week;
}

function siteDivHTML($address, $siteName)
{
    return "<h2>" . $address . "</h2>
    <div id=\"" . $siteName . "Div\">
    <table style=\"width: 100%\">
        <tr>
            <td style=\"vertical-align: top; text-align: center; width: 33%\" id=\"" . $siteName . "Hourly\">
            <img src=\"/assets/images/loading.gif\" style=\"width:75px\"/>
            </td>
            <td style=\"vertical-align: top; text-align: center; width: 33%\" id=\"" . $siteName . "Weekly\">
            <img src=\"/assets/images/loading.gif\" style=\"width:75px\"/>
            </td>
            <td style=\"vertical-align: top; text-align: center; width: 33%\" id=\"" . $siteName . "Monthly\">
            <img src=\"/assets/images/loading.gif\" style=\"width:75px\"/>
            </td>
        </tr>
    </table>
    </div>";
}