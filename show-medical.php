<?php

require_once('config.php');
require_once('database.php');

$page_title = "Medical Reports";
$request_id;
$db_select_medical_stmt;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $db_select_medical_stmt = $db_conn->prepare('SELECT * FROM `medical_report` WHERE `id` = :id');
    $db_select_medical_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
    $page_title = "Medical Report #" . $request_id;
} else {
    $db_select_medical_stmt = $db_conn->prepare('SELECT * FROM `medical_report`');
}

include('header.php');

try {
    $db_select_medical_stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage(); // TODO: meaningful database exceptions
}

?>
<section>
    <header>
        <?php if (isset($request_id)) {
        ?>
            <h1><a href="show-medical.php">Medical Report #<?php print($request_id); ?></a></h1>
        <?php
        } else {
        ?>
            <h1>All Medical Reports</h1>
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
                <td>Complaint ID</td>
                <td>Event</td>
                <td>Occurance</td>
                <td>Location</td>
                <td>Injured</td>
                <td>Reporting employee</td>
                <td>Entered by</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php

            $medical = $db_select_medical_stmt->fetchAll(PDO::FETCH_BOTH);

            foreach ($medical as $row) {
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

                // fetch the injured's identity data
                $db_select_injured_data_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id');
                $db_select_injured_data_stmt->bindParam(':id', $row['inujured_identity_id'], PDO::PARAM_INT);
                try {
                    $db_select_injured_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $injured = $db_select_injured_data_stmt->fetchAll(PDO::FETCH_BOTH);

                // fetch the taking employee's identity ID
                $db_select_taking_employee_id_stmt = $db_conn->prepare('SELECT `primary_identity_id` FROM `entity` WHERE `id` = :id LIMIT 1');
                $db_select_taking_employee_id_stmt->bindParam(':id', $row['taken_by_employee_id'], PDO::PARAM_INT);
                try {
                    $db_select_taking_employee_id_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $taking_employee_id = $db_select_taking_employee_id_stmt->fetchColumn();

                // fetch the taking employee's identity data
                $db_select_taking_employee_data_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id');
                $db_select_taking_employee_data_stmt->bindParam(':id', $taking_employee_id, PDO::PARAM_INT);
                try {
                    $db_select_taking_employee_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $taking_employee = $db_select_taking_employee_data_stmt->fetchAll(PDO::FETCH_BOTH);

                // fetch the entering employee's identity ID
                $db_select_entering_employee_id_stmt = $db_conn->prepare('SELECT `primary_identity_id` FROM `entity` WHERE `id` = :id LIMIT 1');
                $db_select_entering_employee_id_stmt->bindParam(':id', $row['entered_by_employee_id'], PDO::PARAM_INT);
                try {
                    $db_select_entering_employee_id_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $entering_employee_id = $db_select_entering_employee_id_stmt->fetchColumn();

                // fetch the entering employee's identity data
                $db_select_entering_employee_data_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id');
                $db_select_entering_employee_data_stmt->bindParam(':id', $entering_employee_id, PDO::PARAM_INT);
                try {
                    $db_select_entering_employee_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $entering_employee = $db_select_entering_employee_data_stmt->fetchAll(PDO::FETCH_BOTH);

            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['complaint_id']; ?></td>
                    <td><?php echo $row['event_id']; ?></td>
                    <td><time><?php echo $row['when_injured']; ?></time></td>
                    <td><?php
                        foreach ($address as $element) {
                            print($element['building_number'] . ' ' . $element['street_name']);
                            if (isset($element['unit_number'])) {
                                print(' ' . $element['unit_number']);
                            }
                            print('<br>' . $element['city_name'] . ', ' . $element['state_code'] . ' ' . $element['postal_code']);
                        }
                        ?></td>
                    <td><?php
                        foreach ($injured as $element) {
                            print($element['first_name'] . ' ');
                            if (isset($element['middle_name'])) {
                                print(substr($element['middle_name'] . ' ', 0, 1));
                            }
                            print($element['last_name']);
                        }
                        if (isset($row['injured_entity_id'])) {
                            print(' (' . $row['injured_entity_id'] . ')');
                        }
                        ?></td>
                    <td><?php foreach ($taking_employee as $element) {
                            print($element['first_name'] . ' ');
                            if (isset($element['middle_name'])) {
                                print(substr($element['middle_name'] . ' ', 0, 1));
                            }
                            print($element['last_name']);
                        }

                        ?></td>
                    <td><?php foreach ($entering_employee as $element) {
                            print($element['first_name'] . ' ');
                            if (isset($element['middle_name'])) {
                                print(substr($element['middle_name'] . ' ', 0, 1));
                            }
                            print($element['last_name']);
                        }

                        ?></td>
                    <td><a href="?id=<?php echo $row['id']; ?>">show</a> <a href="edit-medical.php?a=delete&id=<?php echo $row['id']; ?>">delete</a> <a href="edit-medical.php?a=edit&id=<?php echo $row['id']; ?>">edit</a></td>
                </tr>
            <?php

            }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="13"><a href="new-medical.php">new</a></td>
            </tr>
        </tfoot>
    </table>
</section>
<?php

include('footer.php');

?>