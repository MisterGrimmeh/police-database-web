<?php

require_once('config.php');
require_once('database.php');

$page_title = "Locations";
$request_id;
$db_select_location_stmt;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $db_select_location_stmt = $db_conn->prepare('SELECT * FROM `location` WHERE `id` = :id');
    $db_select_location_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
    $page_title = "Location #" . $request_id;
} else {
    $db_select_location_stmt = $db_conn->prepare('SELECT * FROM `location`');
}

include('header.php');

try {
    $db_select_location_stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage(); // TODO: meaningful database exceptions
}

?>
<section>
    <header>
        <?php if (isset($request_id)) {
        ?>
            <h1><a href="show-location.php">Location #<?php print($request_id); ?></a></h1>
        <?php
        } else {
        ?>
            <h1>All Locations</h1>
        <?php
        }

        if ($dev_mode) {
            print('<p><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
            if (isset($_SERVER['REQUEST_METHOD'])) {
                print_r($_SERVER['REQUEST_METHOD']);
            }
            print('<br><b>$request_id:</b> ');
            if (isset($request_id)) {
                var_dump($request_id);
            }
            echo '</p>';
        }

        ?>
    </header>
    <table>
        <thead>
            <tr>
                <td>ID</td>
                <td>Address ID</td>
                <td>Address</td>
                <td>Latitude</td>
                <td>Longitude</td>
                <td>Cross streets</td>
                <td>City</td>
                <td>Region</td>
                <td>Country</td>
                <td>Description</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php

            $location = $db_select_location_stmt->fetchAll(PDO::FETCH_BOTH);

            foreach ($location as $row) {

                $db_select_primary_id_data_stmt = $db_conn->prepare('SELECT * FROM `address` WHERE `id` = :id');
                $db_select_primary_id_data_stmt->bindParam(':id', $row['address_id'], PDO::PARAM_INT);
                try {
                    $db_select_primary_id_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $address_return = $db_select_primary_id_data_stmt->fetchAll(PDO::FETCH_BOTH);
                if (isset($address_return[0])) {
                    $address = $address_return[0];
                }

            ?>
                <tr>
                    <td><?php print($row['id']); ?></td>
                    <td><?php print($row['address_id']); ?></td>
                    <td><?php if (isset($address_return[0])) {
                            print($address['building_number'] . ' ' . $address['street_name']);
                            if (isset($address['unit_number'])) {
                                print(' ' . $address['unit_number']);
                            }
                            print('<br>' . $address['city_name'] . ' ' . $address['state_code'] . ' ' . $address['postal_code']);
                        }
                        ?></td>
                    <td><?php print($row['geo_lat']); ?></td>
                    <td><?php print($row['geo_long']); ?></td>
                    <td><?php print($row['primary_street']);
                        if (isset($row['secondary_street'])) {
                            print("<br>" . $row['secondary_street']);
                        }
                        if (isset($row['tertiary_street'])) {
                            print("<br>" . $row['tertiary_street']);
                        } ?></td>
                    <td><?php print($row['city']); ?></td>
                    <td><?php print($row['region']); ?></td>
                    <td><?php print($row['country_code']); ?></td>
                    <td><?php print($row['fulltext_desc']); ?></td>
                    <td><?php if (!isset($request_id)) { ?><a href="?id=<?php echo $row['id']; ?>">show</a> <?php } ?><a href="edit-location.php?a=delete&id=<?php echo $row['id']; ?>">delete</a> <a href="edit-location.php?a=edit&id=<?php echo $row['id']; ?>">edit</a></td>
                </tr>
            <?php

            }

            ?>
        </tbody>
        <?php

        if (!isset($request_id)) {

        ?>
            <tfoot>
                <tr>
                    <td colspan="13"><a href="new-location.php">new</a></td>
                </tr>
            </tfoot>
        <?php

        }

        ?>
    </table>
</section>
<?php

include('footer.php');

?>