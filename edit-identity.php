<?php

require_once('config.php');
require_once('database.php');

$page_title = "Edit Identity";
include('header.php');

$form_datetime_format = 'Y-m-d\TH:i';
$database_datetime_format = 'Y-m-d H:i:s';

$request_id = null;
$request_action = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_s_primary_entity_id = null;

    if (isset($_POST['primary_entity_id']) && is_numeric($_POST['primary_entity_id'])) {
        $form_s_primary_entity_id = intval($_POST['primary_entity_id']);
    }

    if (isset($_POST['first_name']) && strlen($_POST['first_name']) <= 35) {
        $form_s_first_name = $_POST['first_name'];
    }

    if (isset($_POST['middle_name']) && strlen($_POST['middle_name']) <= 35) {
        $form_s_middle_name = $_POST['middle_name'];
    }

    if (isset($_POST['last_name']) && strlen($_POST['last_name']) <= 35) {
        $form_s_last_name = $_POST['last_name'];
    }

    if (isset($_POST['alias']) && strlen($_POST['alias']) <= 35) {
        $form_s_alias = $_POST['alias'];
    }

    if (isset($_POST['date_of_birth'])) {
        var_dump($_POST['date_of_birth']);
        $form_s_date_of_birth = $_POST['date_of_birth'];
    }

    if (isset($_POST['tel_number']) && is_numeric($_POST['tel_number']) && strlen((string)$_POST['tel_number']) <= 15) {
        var_dump($_POST['tel_number']);
        $form_s_tel_number = (string)$_POST['tel_number'];
        var_dump($form_s_tel_number);
    }

    if (isset($_POST['email']) && strlen($_POST['email']) <= 320) {
        $form_s_email = $_POST['email'];
    }

    if (isset($_POST['last_known_residence']) && is_numeric($_POST['last_known_residence'])) {
        $form_s_last_known_residence = intval($_POST['last_known_residence']);
    }

    if ($dev_mode) {
        print('<b>$form_s_primary_entity_id =</b>');
        var_dump($form_s_primary_entity_id);
    }

    $db_update_identity_stmt = $db_conn->prepare('UPDATE `identity` SET
        `primary_entity_id` = :primary_entity_id,
        `first_name` = :first_name,
        `middle_name` = :middle_name,
        `last_name` = :last_name,
        `alias` = :alias,
        `date_of_birth` = :date_of_birth,
        `last_known_residence` = :last_known_residence,
        `tel_number` = :tel_number,
        `email` = :email
        WHERE `id` = :id');
    $db_update_identity_stmt->bindParam(':primary_entity_id', $form_s_primary_entity_id, PDO::PARAM_INT);
    $db_update_identity_stmt->bindParam(':first_name', $form_s_first_name, PDO::PARAM_STR);
    $db_update_identity_stmt->bindParam(':middle_name', $form_s_middle_name, PDO::PARAM_STR);
    $db_update_identity_stmt->bindParam(':last_name', $form_s_last_name, PDO::PARAM_STR);
    $db_update_identity_stmt->bindParam(':alias', $form_s_alias, PDO::PARAM_STR);
    $db_update_identity_stmt->bindParam(':date_of_birth', $form_s_date_of_birth, PDO::PARAM_STR);
    $db_update_identity_stmt->bindParam(':last_known_residence', $form_s_last_known_residence, PDO::PARAM_INT);
    $db_update_identity_stmt->bindParam(':tel_number', $form_s_tel_number, PDO::PARAM_STR);
    $db_update_identity_stmt->bindParam(':email', $form_s_email, PDO::PARAM_STR);
    $db_update_identity_stmt->bindParam(':id', $request_id);

    try {
        $db_update_identity_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    header('Location: show-entity.php?id=' . $form_s_primary_entity_id, true, 303);
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
        $db_delete_identity_stmt = $db_conn->prepare('DELETE FROM `identity` WHERE `id` = :id');
        $db_delete_identity_stmt->bindParam(':id', $request_id);

        try {
            $db_delete_identity_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        header('Location: show-identity.php', true, 303);
    } elseif ($request_action == 'e') {

        $db_select_identity_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id LIMIT 1');
        $db_select_identity_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

        try {
            $db_select_identity_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $identity = $db_select_identity_stmt->fetchAll(PDO::FETCH_BOTH);

?>

        <section>

            <header>
                <?php if (isset($request_id)) {
                ?>
                    <h1>Editing Identity #<?php print($request_id); ?></h1>
                <?php
                }

                if ($dev_mode) {
                    print('<p><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
                    if (isset($_SERVER['REQUEST_METHOD'])) {
                        print_r($_SERVER['REQUEST_METHOD']);
                    }
                    print('<br><b>$identity:</b> ');
                    var_dump($identity);
                    print('<br><b>$request_id:</b> ');
                    var_dump($request_id);
                    print('<br><b>$identity[\'id\']:</b> ');
                    var_dump($identity[0]['id']);
                    echo '</p>';
                }

                ?>
            </header>

            <form id="edit_identity" name="edit_identity" method="post" action="<?php print(htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $request_id); ?>">
                <fieldset>

                    <?php

                    $db_select_entities_stmt = $db_conn->prepare('SELECT * FROM `entity`');

                    try {
                        $db_select_entities_stmt->execute();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }

                    $entities = $db_select_entities_stmt->fetchAll(PDO::FETCH_BOTH);

                    ?>
                    <div>
                        <label for="primary_entity_id">Primary entity:</label>
                        <select id="primary_entity_id" name="primary_entity_id">
                            <option value=""></option>
                            <?php

                            foreach ($entities as $row) {

                            ?>
                                <option value="<?php echo $row['id']; ?>" <?php if ($identity[0]['id'] == $row['primary_identity_id']) {
                                                                                print('selected');
                                                                            } ?>><?php echo $row['id']; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                    <div>
                        <label for="first_name">First name:</label>
                        <input type="text" id="first_name" name="first_name" maxlength="35" value="<?php print($identity[0]['first_name']); ?>">
                    </div>
                    <div>
                        <label for="middle_name">Middle name:</label>
                        <input type="text" id="middle_name" name="middle_name" maxlength="35" value="<?php print($identity[0]['middle_name']); ?>">
                    </div>
                    <div>
                        <label for="last_name">Last name:</label>
                        <input type="text" id="last_name" name="last_name" maxlength="35" value="<?php print($identity[0]['last_name']); ?>">
                    </div>
                    <div>
                        <label for="alias">Alias:</label>
                        <input type="text" id="alias" name="alias" maxlength="35" value="<?php print($identity[0]['alias']); ?>">
                    </div>
                    <div>
                        <label for="date_of_birth">Date of birth:</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="<?php print($identity[0]['date_of_birth']); ?>">
                    </div>
                    <div>
                        <label for="last_known_residence">Last known residence:</label>
                        <input type="number" id="last_known_residence" name="last_known_residence" maxlength="1" value="<?php print($identity[0]['last_known_residence']); ?>">
                    </div>
                    <div>
                        <label for="tel_number">Telephone number:</label>
                        <input type="tel" id="tel_number" name="tel_number" maxlength="15" value="<?php print($identity[0]['tel_number']); ?>">
                    </div>
                    <div>
                        <label for="email">E-mail address:</label>
                        <input type="email" id="email" name="email" maxlength="320" value="<?php print($identity[0]['email']); ?>">
                    </div>
                    <div>
                        <input type="submit" value="Update identity">
                    </div>
            </form>

        </section>

<?php

    }
}

include('footer.php');

?>