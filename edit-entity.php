<?php

require_once('config.php');
require_once('database.php');

$page_title = "Edit Entity";
include('header.php');

$form_datetime_format = 'Y-m-d\TH:i';
$database_datetime_format = 'Y-m-d H:i:s';

$request_id = null;
$request_action = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_s_primary_identity_id = null;

    if (isset($_POST['primary_identity_id']) && is_numeric($_POST['primary_identity_id'])) {
        $form_s_primary_identity_id = intval($_POST['primary_identity_id']);
    }

    if ($dev_mode) {
        print('<b>$form_s_primary_identity_id =</b>');
        var_dump($form_s_primary_identity_id);
    }

    $db_update_entity_stmt = $db_conn->prepare('UPDATE `entity` SET
        `primary_identity_id` = :primary_identity_id
        WHERE `id` = :id');
    $db_update_entity_stmt->bindParam(':primary_identity_id', $form_s_primary_identity_id, PDO::PARAM_INT);
    $db_update_entity_stmt->bindParam(':id', $request_id);

    try {
        $db_update_entity_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    header('Location: show-entity.php?id=' . $request_id, true, 303);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $request_action;

    if (isset($_GET['a']) && ctype_alpha($_GET['a'])) {
        switch ($_GET['a']) {
            case 'edit':
                $request_action = 'e';
                break;
            case 'delete':
                $request_action = 'd';
                break;
            default:
                $request_action = 'e';
        }
    }

    if ($request_action == 'd') {
        $db_delete_entity_stmt = $db_conn->prepare('DELETE FROM `entity` WHERE `id` = :id');
        $db_delete_entity_stmt->bindParam(':id', $request_id);

        try {
            $db_delete_entity_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        header('Location: show-entity.php', true, 303);
    } elseif ($request_action == 'e') {

        $db_select_entity_stmt = $db_conn->prepare('SELECT * FROM `entity` WHERE `id` = :id LIMIT 1');
        $db_select_entity_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

        try {
            $db_select_entity_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $entity = $db_select_entity_stmt->fetchAll(PDO::FETCH_BOTH);

?>

        <section>

            <header>
                <?php if (isset($request_id)) {
                ?>
                    <h1>Editing Entity #<?php print($request_id); ?></h1>
                <?php
                }

                if ($dev_mode) {
                    print('<p><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
                    if (isset($_SERVER['REQUEST_METHOD'])) {
                        print_r($_SERVER['REQUEST_METHOD']);
                    }
                    print('<br><b>$entity:</b> ');
                    var_dump($entity);
                    print('<br><b>$request_id:</b> ');
                    var_dump($request_id);
                    print('<br><b>$entity[\'id\']:</b> ');
                    var_dump($entity[0]['id']);
                    echo '</p>';
                }

                ?>
            </header>

            <form id="edit_entity" name="edit_entity" method="post" action="<?php print(htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $request_id); ?>">
                <fieldset>

                    <?php

                    $db_select_identities_stmt = $db_conn->prepare('SELECT * FROM `identity`');

                    try {
                        $db_select_identities_stmt->execute();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }

                    $identities = $db_select_identities_stmt->fetchAll(PDO::FETCH_BOTH);

                    ?>
                    <div>
                        <label for="primary_identity_id">Primary identity ID:</label>
                        <select id="primary_identity_id" name="primary_identity_id">
                            <option value=""></option>
                            <?php

                            foreach ($identities as $id) {

                            ?>
                                <option value="<?php echo $id['id']; ?>" <?php if ($entity[0]['id'] == $id['primary_entity_id']) {
                                                                                print('selected');
                                                                            } ?>><?php echo $id['first_name'] . " " . $id['last_name'] . " (" . $id['id'] . ")"; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                    <div>
                        <input type="submit" value="Update entity">
                    </div>
            </form>

        </section>

<?php

    }
}

include('footer.php');

?>