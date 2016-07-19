<?php

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
