<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    require_once('config.php');
    require_once('database.php');

    $request_id = null;
    $request_action = null;

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $request_id = intval($_GET['id']);
    }

    $db_insert_identity_stmt = $db_conn->prepare('INSERT INTO `identity` (
        `primary_entity_id`
        ) VALUES (
        :primary_entity_id)');
    $db_insert_identity_stmt->bindParam(':primary_entity_id', $request_id);

    try {
        $db_insert_identity_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    $redirect_show_id = $db_conn->lastInsertId();
    header('Location: edit-identity.php?id=' . $request_id, true, 303);

}

?>