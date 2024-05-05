<?php

require_once('config.php');
require_once('database.php');

$page_title = "Addresss";
$request_id;
$db_select_address_stmt;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $db_select_address_stmt = $db_conn->prepare('SELECT * FROM `address` WHERE `id` = :id');
    $db_select_address_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
    $page_title = "Address #" . $request_id;
} else {
    $db_select_address_stmt = $db_conn->prepare('SELECT * FROM `address`');
}

include('header.php');

try {
    $db_select_address_stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage(); // TODO: meaningful database exceptions
}

?>
<section>
    <header>
        <?php if (isset($request_id)) {
        ?>
            <h1><a href="show-address.php">Address #<?php print($request_id); ?></a></h1>
        <?php
        } else {
        ?>
            <h1>All Addresss</h1>
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
                <td>Building</td>
                <td>Street</td>
                <td>Unit</td>
                <td>City</td>
                <td>State/province</td>
                <td>Postal code</td>
                <td>Formatted alternate</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php

            $address = $db_select_address_stmt->fetchAll(PDO::FETCH_BOTH);

            foreach ($address as $row) {

            ?>
                <tr>
                    <td><?php print($row['id']); ?></td>
                    <td><?php print($row['building_number']); ?></td>
                    <td><?php print($row['street_name']); ?></td>
                    <td><?php print($row['unit_number']); ?></td>
                    <td><?php print($row['city_name']); ?></td>
                    <td><?php print($row['state_code']); ?></td>
                    <td><?php print($row['postal_code']); ?></td>
                    <td><?php print($row['mailing_address_fulltext']); ?></td>
                    <td><?php if (!isset($request_id)) { ?><a href="?id=<?php echo $row['id']; ?>">show</a> <?php } ?><a href="edit-address.php?a=delete&id=<?php echo $row['id']; ?>">delete</a> <a href="edit-address.php?a=edit&id=<?php echo $row['id']; ?>">edit</a></td>
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
                    <td colspan="13"><a href="new-address.php">new</a></td>
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