<?php
require_once("config.php");
require_once "includes/helpers.php";
require_once "includes/mysql.class.php";
require_once "includes/testing.class.php";

for($x=0;$x<100;$x++)
{
    print "run $x\n";

    $obj = new Testing(new Mysql());
    sleep(1);
}

