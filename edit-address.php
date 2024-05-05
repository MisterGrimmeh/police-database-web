<?php

require_once('config.php');
require_once('database.php');

$page_title = "Edit Address";
include('header.php');

$form_datetime_format = 'Y-m-d\TH:i';
$database_datetime_format = 'Y-m-d H:i:s';

$request_id = null;
$request_action = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_s_building_number = null;

    if (isset($_POST['building_number']) && strlen($_POST['building_number']) <= 20) {
        $form_s_building_number = $_POST['building_number'];
    }

    if (isset($_POST['unit_number']) && strlen($_POST['unit_number']) <= 20) {
        $form_s_unit_number = $_POST['unit_number'];
    }

    if (isset($_POST['street_name']) && strlen($_POST['street_name']) <= 35) {
        $form_s_street_name = $_POST['street_name'];
    }

    if (isset($_POST['city']) && strlen($_POST['city']) <= 35) {
        $form_s_city = $_POST['city'];
    }

    if (isset($_POST['postal_code']) && strlen($_POST['postal_code']) <= 10) {
        $form_s_postal_code = $_POST['postal_code'];
    }

    if (isset($_POST['state_code']) && strlen($_POST['state_code']) <= 3) {
        $form_s_state_code = $_POST['state_code'];
    }

    if (isset($_POST['mailing_address_fulltext']) && is_numeric($_POST['mailing_address_fulltext']) && strlen($_POST['mailing_address_fulltext']) <= 255) {
        $form_s_mailing_address_fulltext = $_POST['mailing_address_fulltext'];
    }

    if ($dev_mode) {
    }

    $db_update_address_stmt = $db_conn->prepare('UPDATE `address` SET
        `building_number` = :building_number,
        `unit_number` = :unit_number,
        `street_name` = :street_name,
        `city_name` = :city,
        `postal_code` = :postal_code,
        `state_code` = :state_code,
        `mailing_address_fulltext` = :mailing_address_fulltext
        WHERE `id` = :id');
    $db_update_address_stmt->bindParam(':building_number', $form_s_building_number, PDO::PARAM_STR);
    $db_update_address_stmt->bindParam(':unit_number', $form_s_unit_number, PDO::PARAM_STR);
    $db_update_address_stmt->bindParam(':street_name', $form_s_street_name, PDO::PARAM_STR);
    $db_update_address_stmt->bindParam(':city', $form_s_city, PDO::PARAM_STR);
    $db_update_address_stmt->bindParam(':postal_code', $form_s_postal_code, PDO::PARAM_STR);
    $db_update_address_stmt->bindParam(':state_code', $form_s_state_code, PDO::PARAM_STR);
    $db_update_address_stmt->bindParam(':mailing_address_fulltext', $form_s_mailing_address_fulltext, PDO::PARAM_STR);
    $db_update_address_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

    try {
        $db_update_address_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    header('Location: show-address.php?id=' . $request_id, true, 303);
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
        $db_select_location_check_stmt = $db_conn->prepare('UPDATE `location` SET `address_id` = NULL WHERE `address_id` = :id');
        $db_select_location_check_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
        try {
            $db_select_location_check_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $db_delete_address_stmt = $db_conn->prepare('DELETE FROM `address` WHERE `id` = :id');
        $db_delete_address_stmt->bindParam(':id', $request_id);

        try {
            $db_delete_address_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        header('Location: show-address.php', true, 303);
    } elseif ($request_action == 'e') {

        $db_select_address_stmt = $db_conn->prepare('SELECT * FROM `address` WHERE `id` = :id LIMIT 1');
        $db_select_address_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

        try {
            $db_select_address_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $address = $db_select_address_stmt->fetchAll(PDO::FETCH_BOTH);

?>

        <section>

            <header>
                <?php if (isset($request_id)) {
                ?>
                    <h1>Editing Address #<?php print($request_id); ?></h1>
                <?php
                }

                if ($dev_mode) {
                    print('<p><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
                    if (isset($_SERVER['REQUEST_METHOD'])) {
                        print_r($_SERVER['REQUEST_METHOD']);
                    }
                    print('<br><b>$address:</b> ');
                    var_dump($address);
                    print('<br><b>$request_id:</b> ');
                    var_dump($request_id);
                    print('<br><b>$address[\'id\']:</b> ');
                    var_dump($address[0]['id']);
                    echo '</p>';
                }

                ?>
            </header>

            <form id="edit_address" name="edit_address" method="post" action="<?php print(htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $request_id); ?>">
                <fieldset>
                    <div>
                        <label for="building_number">Building number:</label>
                        <input type="text" id="building_number" name="building_number" maxlength="20" value="<?php print($address[0]['building_number']); ?>">
                    </div>
                    <div>
                        <label for="unit_number">Unit:</label>
                        <input type="text" id="unit_number" name="unit_number" maxlength="35" value="<?php print($address[0]['unit_number']); ?>">
                    </div>
                    <div>
                        <label for="street_name">Street:</label>
                        <input type="text" id="street_name" name="street_name" maxlength="35" value="<?php print($address[0]['street_name']); ?>">
                    </div>
                    <div>
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" maxlength="35" value="<?php print($address[0]['city_name']); ?>">
                    </div>
                    <div>
                        <label for="state_code">State:</label>
                        <input type="text" id="state_code" name="state_code" maxlength="3" value="<?php print($address[0]['state_code']); ?>">
                    </div>
                    <div>
                        <label for="postal_code">Postal code:</label>
                        <input type="text" id="postal_code" name="postal_code" maxlength="10" value="<?php print($address[0]['postal_code']); ?>">
                    </div>
                    <div>
                        <label for="mailing_address_fulltext">Mailing address (formatted alt):</label>
                        <textarea id="mailing_address_fulltext" name="mailing_address_fulltext" maxlength="512"><?php print($address[0]['mailing_address_fulltext']); ?></textarea>
                    </div>
                    <div>
                        <input type="submit" value="Update address">
                    </div>
            </form>

        </section>

<?php

    }
}

include('footer.php');

?>