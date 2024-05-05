<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    require_once('config.php');
    require_once('database.php');

    $db_insert_location_stmt = $db_conn->prepare('INSERT INTO `location` () VALUES ()');

    try {
        $db_insert_location_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    $redirect_show_id = $db_conn->lastInsertId();
    header('Location: edit-location.php?id=' . $request_id, true, 303);

}

?>