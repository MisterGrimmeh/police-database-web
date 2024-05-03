<?php

require_once('config.php');
require_once('database.php');

$page_title = "Arrest Reports";
$request_id;
$db_select_arrest_stmt;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $db_select_arrest_stmt = $db_conn->prepare('SELECT * FROM `arrest_report` WHERE `id` = :id');
    $db_select_arrest_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
    $page_title = "Arrest Report #" . $request_id;
} else {
    $db_select_arrest_stmt = $db_conn->prepare('SELECT * FROM `arrest_report`');
}

include('header.php');

try {
    $db_select_arrest_stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage(); // TODO: meaningful database exceptions
}

?>
<section>
    <header>
        <?php if (isset($request_id)) {
        ?>
            <h1><a href="show-arrest.php">Arrest Report #<?php print($request_id); ?></a></h1>
        <?php
        } else {
        ?>
            <h1>All Arrest Reports</h1>
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
                <td>Crime</td>
                <td>Occurance</td>
                <td>Location</td>
                <td>Arrestee</td>
                <td>Arresting employee</td>
                <td>Entered by</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php

            $arrest = $db_select_arrest_stmt->fetchAll(PDO::FETCH_BOTH);

            foreach ($arrest as $row) {
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
                $db_select_arrestee_data_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id');
                $db_select_arrestee_data_stmt->bindParam(':id', $row['arrestee_identity_id'], PDO::PARAM_INT);
                try {
                    $db_select_arrestee_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $arrestee = $db_select_arrestee_data_stmt->fetchAll(PDO::FETCH_BOTH);

                // fetch the taking employee's identity ID
                $db_select_taking_employee_id_stmt = $db_conn->prepare('SELECT `primary_identity_id` FROM `entity` WHERE `id` = :id LIMIT 1');
                $db_select_taking_employee_id_stmt->bindParam(':id', $row['arresst_by_employee_id'], PDO::PARAM_INT);
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
                    <td><?php
                        foreach ($crime as $element) {
                            print($element['code'] . ' (' . $element['class'] . ')');
                        } ?></td>
                    <td><time><?php echo $row['when_occurred']; ?></time></td>
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
                        foreach ($arrestee as $element) {
                            print($element['first_name'] . ' ');
                            if (isset($element['middle_name'])) {
                                print(substr($element['middle_name'] . ' ', 0, 1));
                            }
                            print($element['last_name']);
                        }
                        if (isset($row['arrestee_entity_id'])) {
                            print(' (' . $row['arrestee_entity_id'] . ')');
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
                    <td><a href="?id=<?php echo $row['id']; ?>">show</a> <a href="edit-arrest.php?a=delete&id=<?php echo $row['id']; ?>">delete</a> <a href="edit-arrest.php?a=edit&id=<?php echo $row['id']; ?>">edit</a></td>
                </tr>
            <?php

            }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="13"><a href="new-arrest.php">new</a></td>
            </tr>
        </tfoot>
    </table>
</section>
<?php

include('footer.php');

?>