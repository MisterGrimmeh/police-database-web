<?php

require_once('config.php');
require_once('database.php');

$page_title = "Edit Location";
include('header.php');

$form_datetime_format = 'Y-m-d\TH:i';
$database_datetime_format = 'Y-m-d H:i:s';

$request_id = null;
$request_action = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_s_address_id = null;

    if (isset($_POST['address_id']) && is_numeric($_POST['address_id'])) {
        $form_s_address_id = intval($_POST['address_id']);
    }

    if (isset($_POST['geo_lat']) && is_numeric($_POST['geo_lat']) && strlen($_POST['geo_lat']) <= 10) {
        $form_s_geo_lat = $_POST['geo_lat'];
    }

    if (isset($_POST['geo_long']) && is_numeric($_POST['geo_long']) && strlen($_POST['geo_long']) <= 10) {
        $form_s_geo_long = $_POST['geo_long'];
    }

    if (isset($_POST['primary_street']) && strlen($_POST['primary_street']) <= 35) {
        $form_s_primary_street = $_POST['primary_street'];
    }

    if (isset($_POST['secondary_street']) && strlen($_POST['secondary_street']) <= 35) {
        $form_s_secondary_street = $_POST['secondary_street'];
    }

    if (isset($_POST['tertiary_street']) && strlen($_POST['tertiary_street']) <= 35) {
        $form_s_secondary_street = $_POST['tertiary_street'];
    }

    if (isset($_POST['city']) && strlen($_POST['city']) <= 35) {
        $form_s_city = $_POST['city'];
    }

    if (isset($_POST['region']) && strlen($_POST['region']) <= 35) {
        $form_s_region = $_POST['region'];
    }

    if (isset($_POST['fulltext_desc']) && is_numeric($_POST['fulltext_desc']) && strlen($_POST['fulltext_desc']) <= 512) {
        $form_s_fulltext_desc = $_POST['fulltext_desc'];
    }

    if ($dev_mode) {
    }

    $db_update_location_stmt = $db_conn->prepare('UPDATE `location` SET
        `address_id` = :address_id,
        `geo_lat` = :geo_lat,
        `geo_long` = :geo_long,
        `primary_street` = :primary_street,
        `secondary_street` = :secondary_street,
        `tertiary_street` = :tertiary_street,
        `city` = :city,
        `region` = :region,
        `fulltext_desc` = :fulltext_desc
        WHERE `id` = :id');
    $db_update_location_stmt->bindParam(':address_id', $form_s_address_id, PDO::PARAM_INT);
    $db_update_location_stmt->bindParam(':geo_lat', $form_s_geo_lat, PDO::PARAM_STR);
    $db_update_location_stmt->bindParam(':geo_long', $form_s_geo_long, PDO::PARAM_STR);
    $db_update_location_stmt->bindParam(':primary_street', $form_s_primary_street, PDO::PARAM_STR);
    $db_update_location_stmt->bindParam(':secondary_street', $form_s_secondary_street, PDO::PARAM_STR);
    $db_update_location_stmt->bindParam(':tertiary_street', $form_s_tertiary_street, PDO::PARAM_STR);
    $db_update_location_stmt->bindParam(':city', $form_s_fulltext_desc, PDO::PARAM_STR);
    $db_update_location_stmt->bindParam(':region', $form_s_city, PDO::PARAM_STR);
    $db_update_location_stmt->bindParam(':fulltext_desc', $form_s_region, PDO::PARAM_STR);
    $db_update_location_stmt->bindParam(':id', $request_id);

    try {
        $db_update_location_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    header('Location: show-location.php?id=' . $request_id, true, 303);
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
        $db_select_arrests_stmt = $db_conn->prepare('SELECT `id` FROM `arrest_report` WHERE `occurance_location_id` = :id');
        $db_select_arrests_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
        try {
            $db_select_arrests_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $arrest_blocks = $db_select_arrests_stmt->fetchAll(PDO::FETCH_BOTH);
        if (count($arrest_blocks, 0) == 0) {

            $db_delete_location_stmt = $db_conn->prepare('DELETE FROM `location` WHERE `id` = :id');
            $db_delete_location_stmt->bindParam(':id', $request_id);

            try {
                $db_delete_location_stmt->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }

            header('Location: show-location.php', true, 303);
        } else {
?>
            <header>
                <h2>Cannot delete</h2>
                <p>The following arrest reports use this location.</p>
            </header>
            <table>
                <thead>
                    <tr>
                        <td>Arrest report #</td>
                        <td>Actions</td>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    foreach ($arrest_blocks as $block) {

                    ?>
                        <tr>
                            <td><?php print($block['id']); ?></td>
                            <td><a href="show-arrest.php?id=<?php echo $block['id']; ?>">show</a> <a href="edit-arrest.php?a=delete&id=<?php echo $block['id']; ?>">delete</a> <a href="edit-arrest.php?a=edit&id=<?php echo $block['id']; ?>">edit</a></td>

                        </tr>
                    <?php

                    }

                    ?>
                </tbody>
            </table>

        <?php
        }
    } elseif ($request_action == 'e') {

        $db_select_location_stmt = $db_conn->prepare('SELECT * FROM `location` WHERE `id` = :id LIMIT 1');
        $db_select_location_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

        try {
            $db_select_location_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $location = $db_select_location_stmt->fetchAll(PDO::FETCH_BOTH);

        ?>

        <section>

            <header>
                <?php if (isset($request_id)) {
                ?>
                    <h1>Editing Location #<?php print($request_id); ?></h1>
                <?php
                }

                if ($dev_mode) {
                    print('<p><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
                    if (isset($_SERVER['REQUEST_METHOD'])) {
                        print_r($_SERVER['REQUEST_METHOD']);
                    }
                    print('<br><b>$location:</b> ');
                    var_dump($location);
                    print('<br><b>$request_id:</b> ');
                    var_dump($request_id);
                    print('<br><b>$location[\'id\']:</b> ');
                    var_dump($location[0]['id']);
                    echo '</p>';
                }

                ?>
            </header>

            <form id="edit_location" name="edit_location" method="post" action="<?php print(htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $request_id); ?>">
                <fieldset>
                    <div>
                        <label for="address_id">Address:</label>
                        <input type="number" step="1" id="address_id" name="address_id" maxlength="10" value="<?php print($location[0]['address_id']); ?>">
                    </div>
                    <div>
                        <label for="geo_lat">Latitude (decimal):</label>
                        <input type="number" id="geo_lat" name="geo_lat" maxlength="10" value="<?php print($location[0]['geo_lat']); ?>">
                    </div>
                    <div>
                        <label for="geo_long">Longitude (decimal):</label>
                        <input type="number" step=".00000001" id="geo_long" name="geo_long" maxlength="10" value="<?php print($location[0]['geo_long']); ?>">
                    </div>
                    <div>
                        <label for="primary_street">Primary street:</label>
                        <input type="text" id="primary_street" name="primary_street" maxlength="35" value="<?php print($location[0]['primary_street']); ?>">
                    </div>
                    <div>
                        <label for="secondary_street">Secondary street:</label>
                        <input type="text" id="secondary_street" name="secondary_street" maxlength="35" value="<?php print($location[0]['secondary_street']); ?>">
                    </div>
                    <div>
                        <label for="tertiary_street">Tertiary street:</label>
                        <input type="text" id="tertiary_street" name="tertiary_street" maxlength="35" value="<?php print($location[0]['tertiary_street']); ?>">
                    </div>
                    <div>
                        <label for="fulltext_desc">Description:</label>
                        <textarea id="fulltext_desc" name="fulltext_desc" maxlength="512"><?php print($location[0]['fulltext_desc']); ?></textarea>
                    </div>
                    <div>
                        <input type="submit" value="Update location">
                    </div>
            </form>

        </section>

<?php

    }
}

include('footer.php');

?>