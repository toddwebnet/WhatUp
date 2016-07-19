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