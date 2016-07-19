<?php
require_once("db_config.php");
require_once "includes/helpers.php";
require_once "includes/mysql.class.php";
require_once "includes/UpdateDb.class.php";

$x = new UpdateDb(new Mysql());

