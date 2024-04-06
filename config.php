<?php

$site_title = "Police Database";

$db_user = "nyu_db_user";
$db_password = "easypass";
$db_name = "nyu_project";
$db_host = "localhost";

$dev_mode = TRUE;

if($dev_mode) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

?>