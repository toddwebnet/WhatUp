<?php
error_reporting(E_ERROR | E_PARSE);


require_once __DIR__ . "/config.php";
require_once __DIR__ . "/includes/helpers.php";
require_once __DIR__ . "/includes/mysql.class.php";
require_once __DIR__ . "/includes/whatupdb.class.php";
require_once __DIR__ . "/includes/testing.class.php";
require_once __DIR__ . "/includes/randomizer.class.php";

$obj = new Testing(new Mysql());
//$obj = new Randomizer(new Mysql());

