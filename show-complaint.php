<?php

require_once('config.php');
require_once('database.php');

$page_title = "Complaint Reports";
$request_id;
$db_select_complaint_stmt;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $db_select_complaint_stmt = $db_conn->prepare('SELECT * FROM `complaint_report` WHERE `id` = :id');
    $db_select_complaint_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
    $page_title = "Complaint Report #" . $request_id;
} else {
    $db_select_complaint_stmt = $db_conn->prepare('SELECT * FROM `complaint_report`');
}

include('header.php');

try {
    $db_select_complaint_stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage(); // TODO: meaningful database exceptions
}

?>
<section>
    <header>
        <?php if (isset($request_id)) {
        ?>
            <h1><a href="show-complaint.php">Complaint Report #<?php print($request_id); ?></a></h1>
        <?php
        } else {
            ?>
            <h1>All Complaint Reports</h1>
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
                <td>Event</td>
                <td>Crime</td>
                <td>Occurance began</td>
                <td>Occurance ceased</td>
                <td>Perpetrator</td>
                <td>Victim</td>
                <td>Reporter</td>
                <td>Location ID</td>
                <td>Location address</td>
                <td>Report taker</td>
                <td>Entered by</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php

            $complaint = $db_select_complaint_stmt->fetchAll(PDO::FETCH_BOTH);

            foreach ($complaint as $row) {
                // fetch location's address_id
                $db_select_location_address_stmt = $db_conn->prepare('SELECT `address_id` FROM `location` WHERE `id` = :id');
                $db_select_location_address_stmt->bindParam(':id', $row['occurance_location_id'], PDO::PARAM_INT);
                try {
                    $db_select_location_address_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $address_id = $db_select_location_address_stmt->fetchColumn();

                // fetch the above's address data
                $db_select_address_stmt = $db_conn->prepare('SELECT * FROM `address` WHERE `id` = :id');
                $db_select_address_stmt->bindParam(':id', $address_id, PDO::PARAM_INT);
                try {
                    $db_select_address_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $address = $db_select_address_stmt->fetchAll(PDO::FETCH_BOTH);

                // fetch crime
                $db_select_crime_stmt = $db_conn->prepare('SELECT * FROM `crime` WHERE `id` = :id');
                $db_select_crime_stmt->bindParam(':id', $row['crime_id'], PDO::PARAM_INT);
                try {
                    $db_select_crime_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $crime = $db_select_crime_stmt->fetchAll(PDO::FETCH_BOTH);

                // fetch the perp's identity data
                $db_select_perp_data_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id');
                $db_select_perp_data_stmt->bindParam(':id', $row['perpetrator_identity_id'], PDO::PARAM_INT);
                try {
                    $db_select_perp_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $perp = $db_select_perp_data_stmt->fetchAll(PDO::FETCH_BOTH);

                // fetch the vic's identity data
                $db_select_vic_data_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id');
                $db_select_vic_data_stmt->bindParam(':id', $row['victim_identity_id'], PDO::PARAM_INT);
                try {
                    $db_select_vic_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $vic = $db_select_vic_data_stmt->fetchAll(PDO::FETCH_BOTH);

                // fetch the reporter's identity data
                $db_select_reporter_data_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id');
                $db_select_reporter_data_stmt->bindParam(':id', $row['reporter_identity_id'], PDO::PARAM_INT);
                try {
                    $db_select_reporter_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $reporter = $db_select_reporter_data_stmt->fetchAll(PDO::FETCH_BOTH);

            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['event_id']; ?></td>
                    <td><?php
                        foreach ($crime as $element) {
                            print($element['code'] . ' (' . $element['class'] . ')');
                        } ?></td>
                    <td><time><?php echo $row['occurance_began']; ?></time></td>
                    <td><time><?php echo $row['occurance_ceased']; ?></time></td>
                    <td><?php
                        foreach ($perp as $element) {
                            print($element['first_name'] . ' ');
                            if (isset($element['middle_name'])) {
                                print(substr($element['middle_name'] . ' ', 0, 1));
                            }
                            print($element['last_name']);
                        }
                        if (isset($row['perpetrator_entity_id'])) {
                            print(' (' . $row['perpetrator_entity_id'] . ')');
                        }
                        ?></td>
                    <td><?php
                        foreach ($vic as $element) {
                            print($element['first_name'] . ' ');
                            if (isset($element['middle_name'])) {
                                print(substr($element['middle_name'] . ' ', 0, 1));
                            }
                            print($element['last_name']);
                        }
                        if (isset($row['victim_entity_id'])) {
                            print(' (' . $row['victim_entity_id'] . ')');
                        }
                        ?></td>
                    <td><?php
                        foreach ($reporter as $element) {
                            print($element['first_name'] . ' ');
                            if (isset($element['middle_name'])) {
                                print(substr($element['middle_name'] . ' ', 0, 1));
                            }
                            print($element['last_name']);
                        }
                        if (isset($row['reporter_entity_id'])) {
                            print(' (' . $row['reporter_entity_id'] . ')');
                        }
                        ?></td>
                    <td><?php echo $row['occurance_location_id']; ?></td>
                    <td><?php
                        foreach ($address as $element) {
                            print($element['building_number'] . ' ' . $element['street_name']);
                            if (isset($element['unit_number'])) {
                                print(' ' . $element['unit_number']);
                            }
                            print('<br>' . $element['city_name'] . ', ' . $element['state_code'] . ' ' . $element['postal_code']);
                        }
                        ?></td>
                    <td><?php echo $row['taken_by_employee_id']; ?></td>
                    <td><?php echo $row['entered_by_employee_id']; ?></td>
                    <td><a href="?id=<?php echo $row['id']; ?>">show</a> <a href="edit-complaint.php?a=delete&id=<?php echo $row['id']; ?>">delete</a> <a href="edit-complaint.php?a=edit&id=<?php echo $row['id']; ?>">edit</a></td>
                </tr>
            <?php

            }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="13"><a href="new-complaint.php">new</a></td>
            </tr>
        </tfoot>
    </table>
</section>
<?php

include('footer.php');

?>