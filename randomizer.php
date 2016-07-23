<?php
error_reporting(E_ERROR | E_PARSE);

require_once("config.php");
require_once "includes/helpers.php";
require_once "includes/mysql.class.php";
require_once "includes/whatupdb.class.php";
require_once "includes/testing.class.php";
require_once "includes/randomizer.class.php";

//$obj = new Testing(new Mysql());
$obj = new Randomizer(new Mysql());

