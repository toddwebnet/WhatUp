<?php
require_once("db_config.php");
require_once "includes/helpers.php";
require_once "includes/mysql.class.php";
require_once "includes/UpdateDb.class.php";


$hulkSmash = false;
if (isset($argv[1])) {
    if ($argv[1] == "smash") {
        $hulkSmash = true;
    }
}
$x = new UpdateDb(new Mysql(), $hulkSmash);

