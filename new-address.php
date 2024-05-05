<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    require_once('config.php');
    require_once('database.php');

    $db_insert_address_stmt = $db_conn->prepare('INSERT INTO `address` (`building_number`, `street_name`, `city_name`, `postal_code`, `state_code`) VALUES ("123", "Main Street", "Nowhere", "00000", "ZZ")');

    try {
        $db_insert_address_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    $redirect_show_id = $db_conn->lastInsertId();
    header('Location: edit-address.php?id=' . $request_id, true, 303);

}

?>