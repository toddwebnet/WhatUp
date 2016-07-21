<?php
error_reporting(E_ERROR | E_PARSE);

require_once("config.php");
require_once "includes/helpers.php";
require_once "includes/mysql.class.php";
require_once "includes/testing.class.php";

$obj = new Testing(new Mysql());


