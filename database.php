<?php

$db_dsn = "mysql:host=$db_host;dbname=$db_name;charset=UTF8";

try {
    $db_conn = new PDO($db_dsn, $db_user, $db_password);
} catch (PDOException $e) {
	echo $e->getMessage();
}

?>