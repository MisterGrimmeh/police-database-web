<?php

require_once('config.php');
require_once('database.php');

$page_title = "Edit Complaint";
include('header.php');

$form_datetime_format = 'Y-m-d\TH:i';
$database_datetime_format = 'Y-m-d H:i:s';

$request_id = null;
$request_action = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_s_event_id = null;
    $form_s_crime_id = null;
    $form_d_perpetrator_entity_id = null;
    $form_d_perpetrator_identity_id = null;
    $form_d_victim_entity_id = null;
    $form_d_victim_identity_id = null;
    $form_d_reporter_entity_id = null;
    $form_d_reporter_identity_id = null;
    $taken_by_employee_id = null;
    $form_s_entered_by_employee_id = null;
    $form_d_occurance_location_id = null;
    $form_p_event_occurance_began = null;
    $form_p_event_occurance_ceased = null;

    if (isset($_POST['event_id_select']) && is_numeric($_POST['event_id_select'])) {
        $form_s_event_id = intval($_POST['event_id_select']);
    } elseif (isset($_POST['event_id_input']) && is_numeric($_POST['event_id_input'])) {
        $form_s_event_id = intval($_POST['event_id_input']);
    }

    if (isset($_POST['crime']) && is_numeric($_POST['crime'])) {
        $form_s_crime_id = intval($_POST['crime']);
    }

    if (isset($_POST['occurance_began'])) {
        $form_s_occurance_began = DateTime::createFromFormat($form_datetime_format, $_POST['occurance_began']);
        if ($form_s_occurance_began && $form_s_occurance_began->format($form_datetime_format) === $_POST['occurance_began']) {
            $form_p_occurance_began = $form_s_occurance_began->format($database_datetime_format);
        }
    }

    if (isset($_POST['occurance_ceased'])) {
        $form_s_occurance_ceased = DateTime::createFromFormat($form_datetime_format, $_POST['occurance_ceased']);
        if ($form_s_occurance_ceased && $form_s_occurance_ceased->format($form_datetime_format) === $_POST['occurance_ceased']) {
            $form_p_occurance_ceased = $form_s_occurance_ceased->format($database_datetime_format);
        }
    }

    if (isset($_POST['location_id']) && is_numeric($_POST['location_id'])) {
        print('<b>$_POST["location_id"] =</b>');
        var_dump($_POST['location_id']);
        $form_d_occurance_location_id = intval($_POST['location_id']);
    }

    if (isset($_POST['perp_id']) && is_numeric($_POST['perp_id'])) {
        $form_d_perpetrator_entity_id = intval($_POST['perp_id']);

        $db_select_perp_id_stmt = $db_conn->prepare('SELECT `id` FROM `identity` WHERE `primary_entity_id` = :entity_id LIMIT 1');
        $db_select_perp_id_stmt->bindParam(':entity_id', $form_d_perpetrator_entity_id, PDO::PARAM_INT);

        try {
            $db_select_perp_id_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $perp_id = $db_select_perp_id_stmt->fetchAll(PDO::FETCH_BOTH);
        $form_d_perpetrator_identity_id = intval($perp_id[0]['id']);
    }

    if (isset($_POST['victim_id']) && is_numeric($_POST['victim_id'])) {
        $form_d_victim_entity_id = intval($_POST['victim_id']);

        $db_select_victim_id_stmt = $db_conn->prepare('SELECT `id` FROM `identity` WHERE `primary_entity_id` = :entity_id LIMIT 1');
        $db_select_victim_id_stmt->bindParam(':entity_id', $form_d_victim_entity_id, PDO::PARAM_INT);

        try {
            $db_select_victim_id_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $victim_id = $db_select_victim_id_stmt->fetchAll(PDO::FETCH_BOTH);
        $form_d_victim_identity_id = intval($victim_id[0]['id']);
    }

    if (isset($_POST['reporter_id']) && is_numeric($_POST['reporter_id'])) {
        $form_d_reporter_entity_id = intval($_POST['reporter_id']);

        $db_select_reporter_id_stmt = $db_conn->prepare('SELECT `id` FROM `identity` WHERE `primary_entity_id` = :entity_id LIMIT 1');
        $db_select_reporter_id_stmt->bindParam(':entity_id', $form_d_reporter_entity_id, PDO::PARAM_INT);

        try {
            $db_select_reporter_id_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $reporter_id = $db_select_reporter_id_stmt->fetchAll(PDO::FETCH_BOTH);
        $form_d_reporter_identity_id = intval($reporter_id[0]['id']);
    }

    if (isset($_POST['employee_taking']) && is_numeric($_POST['employee_taking'])) {
        $taken_by_employee_id = intval($_POST['employee_taking']);
    }

    if (isset($_POST['employee_entered']) && is_numeric($_POST['employee_entered'])) {
        $form_s_entered_by_employee_id = intval($_POST['employee_entered']);
    }

    if ($dev_mode) {
        print('<b>$form_s_event_id =</b>');
        var_dump($form_s_event_id);
        print('<b>$form_s_crime_id =</b>');
        var_dump($form_s_crime_id);
        print('<b>$form_d_perpetrator_entity_id =</b>');
        var_dump($form_d_perpetrator_entity_id);
        print('<b>$form_d_perpetrator_identity_id =</b>');
        var_dump($form_d_perpetrator_identity_id);
        print('<b>$form_d_victim_entity_id =</b>');
        var_dump($form_d_victim_entity_id);
        print('<b>$form_d_victim_identity_id =</b>');
        var_dump($form_d_victim_identity_id);
        print('<b>$form_d_reporter_entity_id =</b>');
        var_dump($form_d_reporter_entity_id);
        print('<b>$form_d_reporter_identity_id =</b>');
        var_dump($form_d_reporter_identity_id);
        print('<b>$taken_by_employee_id =</b>');
        var_dump($taken_by_employee_id);
        print('<b>$form_s_entered_by_employee_id =</b>');
        var_dump($form_s_entered_by_employee_id);
        print('<b>$form_d_occurance_location_id =</b>');
        var_dump($form_d_occurance_location_id);
        print('<b>$form_p_occurance_began =</b>');
        var_dump($form_p_occurance_began);
        print('<b>$form_p_occurance_ceased =</b>');
        var_dump($form_p_occurance_ceased);
    }

    $db_update_complaint_stmt = $db_conn->prepare('UPDATE `complaint_report` SET
        `event_id` = :event_id,
        `crime_id` = :crime_id,
        `perpetrator_entity_id` = :perpetrator_entity_id,
        `perpetrator_identity_id` = :perpetrator_identity_id,
        `victim_entity_id` = :victim_entity_id,
        `victim_identity_id` = :victim_identity_id,
        `reporter_entity_id` = :reporter_entity_id,
        `reporter_identity_id` = :reporter_identity_id,
        `taken_by_employee_id` = :taken_by_employee_id,
        `entered_by_employee_id` = :entered_by_employee_id,
        `occurance_location_id` = :occurance_location_id,
        `occurance_began` = :occurance_began,
        `occurance_ceased` = :occurance_ceased
        WHERE `id` = :id');
    $db_update_complaint_stmt->bindParam(':event_id', $form_s_event_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':crime_id', $form_s_crime_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':perpetrator_entity_id', $form_d_perpetrator_entity_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':perpetrator_identity_id', $form_d_perpetrator_identity_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':victim_entity_id', $form_d_victim_entity_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':victim_identity_id', $form_d_victim_identity_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':reporter_entity_id', $form_d_reporter_entity_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':reporter_identity_id', $form_d_reporter_identity_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':taken_by_employee_id', $taken_by_employee_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':entered_by_employee_id', $form_s_entered_by_employee_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':occurance_location_id', $form_d_occurance_location_id, PDO::PARAM_INT);
    $db_update_complaint_stmt->bindParam(':occurance_began', $form_p_occurance_began);
    $db_update_complaint_stmt->bindParam(':occurance_ceased', $form_p_occurance_ceased);
    $db_update_complaint_stmt->bindParam(':id', $request_id);

    try {
        $db_update_complaint_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    header('Location: show-complaint.php?id=' . $request_id, true, 303);
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
        $db_delete_complaint_stmt = $db_conn->prepare('DELETE FROM `complaint_report` WHERE `id` = :id');
        $db_delete_complaint_stmt->bindParam(':id', $request_id);

        try {
            $db_delete_complaint_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        header('Location: show-complaint.php', true, 303);
    } elseif ($request_action == 'e') {

        $db_select_complaint_stmt = $db_conn->prepare('SELECT * FROM `complaint_report` WHERE `id` = :id LIMIT 1');
        $db_select_complaint_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

        try {
            $db_select_complaint_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $complaint = $db_select_complaint_stmt->fetchAll(PDO::FETCH_BOTH);

?>

        <section>

            <header>
                <?php if (isset($request_id)) {
                ?>
                    <h1>Editing Complaint #<?php print($request_id); ?></h1>
                <?php
                } else {
                    ?>
                    <h1>Edting Complaint</h1>
                    <?php
                }

                if ($dev_mode) {
                    print('<p><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
                    if (isset($_SERVER['REQUEST_METHOD'])) {
                        print_r($_SERVER['REQUEST_METHOD']);
                    }
                    print('<br><b>$complaint:</b> ');
                    var_dump($complaint);
                    print('<br><b>$request_id:</b> ');
                    var_dump($request_id);
                    print('<br><b>$complaint[\'id\']:</b> ');
                    var_dump($complaint[0]['id']);
                    echo '</p>';
                }

                ?>
            </header>

            <form id="enter_new_complaint" name="enter_new_complaint" method="post" action="<?php print(htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $request_id); ?>">
                <fieldset>
                    <legend>Occurance information</legend>
                    <?php

                    if (isset($complaint[0]['occurance_began'])) {
                        $occurance_began_db = DateTime::createFromFormat($database_datetime_format, $complaint[0]['occurance_began']);
                        if ($occurance_began_db && $occurance_began_db->format($database_datetime_format) === $complaint[0]['occurance_began']) {
                            $occurance_began_form = $occurance_began_db->format($form_datetime_format);
                        }
                    }

                    if (isset($complaint[0]['occurance_ceased'])) {
                        $occurance_ceased_db = DateTime::createFromFormat($database_datetime_format, $complaint[0]['occurance_ceased']);
                        if ($occurance_ceased_db && $occurance_ceased_db->format($database_datetime_format) === $complaint[0]['occurance_ceased']) {
                            $occurance_ceased_form = $occurance_ceased_db->format($form_datetime_format);
                        }
                    }

                    ?>
                    <div>
                        <label for="occurance_began">Occurance began:</label>
                        <input type="datetime-local" id="occurance_began" name="occurance_began" <?php if (isset($occurance_began_form)) {
                                                                                                        print(' value="' . $occurance_began_form . '"');
                                                                                                    } ?>>
                    </div>
                    <div>
                        <label for="occurance_ceased">Occurance ceased:</label>
                        <input type="datetime-local" id="occurance_ceased" name="occurance_ceased" <?php if (isset($occurance_ceased_form)) {
                                                                                                        print(' value="' . $occurance_ceased_form . '"');
                                                                                                    } ?>>
                    </div>
                    <?php

                    $db_select_events_stmt = $db_conn->prepare('SELECT `id`, `occurance_began`, `occurance_ceased` FROM `event` LIMIT 10');

                    try {
                        $db_select_events_stmt->execute();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }

                    $events = $db_select_events_stmt->fetchAll(PDO::FETCH_BOTH);

                    ?>
                    <div>
                        <label for="event_id_select">Event ID:</label>
                        <select id="event_id_select" name="event_id_select">
                            <option value=""></option>
                            <?php

                            foreach ($events as $event) {

                            ?>
                                <option value="<?php echo $event['id']; ?>" <?php if ($complaint[0]['event_id'] == $event['id']) {
                                                                                print(' selected');
                                                                            } ?>><?php echo "Event " . $event['id'];
                                                                                    if (!empty($event['occurance_began']) || !empty($event['occurance_ceased'])) {
                                                                                        echo " (" . $event['occurance_began'] . "&mdash;" . $event['occurance_ceased'] . ")";
                                                                                    } ?></option>

                            <?php

                            }

                            ?>
                        </select>
                        <label for="event_id_input">or:</label>
                        <input type="number" id="event_id_input" name="event_id_input">
                        <?php

                        $db_select_crimes_stmt = $db_conn->prepare('SELECT `id`, `code`, `title` FROM `crime`');

                        try {
                            $db_select_crimes_stmt->execute();
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }

                        $crimes = $db_select_crimes_stmt->fetchAll(PDO::FETCH_BOTH);

                        ?>
                    </div>
                    <div>
                        <label for="crime">Crime:</label>
                        <select id="crime" name="crime">
                            <option value=""></option>
                            <?php

                            foreach ($crimes as $crime) {

                            ?>
                                <option value="<?php echo $crime['id']; ?>" <?php if ($complaint[0]['crime_id'] == $crime['id']) {
                                                                                print('selected');
                                                                            } ?>><?php echo $crime['code'] . " (" . $crime['title'] . ")"; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                    <fieldset>
                        <legend>Location</legend>
                        <div>
                            <label for="location_id">Location ID:</label>
                            <input type="number" id="location_id" name="location_id" <?php if (isset($complaint[0]['occurance_location_id'])) {
                                                                                            print('value="' . $complaint[0]['occurance_location_id'] . '"');
                                                                                        } ?>>
                        </div>
                    </fieldset>
                </fieldset>
                <?php

                $db_select_entities_stmt = $db_conn->prepare('SELECT entity.id, identity.first_name, identity.middle_name, identity.last_name FROM identity JOIN entity ON identity.id = entity.primary_identity_id');

                try {
                    $db_select_entities_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }

                $entities = $db_select_entities_stmt->fetchAll(PDO::FETCH_BOTH);

                ?>
                <fieldset>
                    <legend>Perpetrator</legend>
                    <div>
                        <label for="perp_id">Perpetrator ID:</label>
                        <select id="perp_id" name="perp_id">
                            <option value=""></option>
                            <?php

                            foreach ($entities as $entity) {

                            ?>
                                <option value="<?php echo $entity['id']; ?>" <?php if ($complaint[0]['perpetrator_entity_id'] == $entity['id']) {
                                                                                    print('selected');
                                                                                } ?>><?php echo $entity['first_name'] . " " . $entity['last_name']; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Victim</legend>
                    <div>
                        <label for="victim_id">Victim ID:</label>
                        <select id="victim_id" name="victim_id">
                            <option value="" selected></option>
                            <?php

                            foreach ($entities as $entity) {

                            ?>
                                <option value="<?php echo $entity['id']; ?>" <?php if ($complaint[0]['victim_entity_id'] == $entity['id']) {
                                                                                    print('selected');
                                                                                } ?>><?php echo $entity['first_name'] . " " . $entity['last_name']; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Reporter</legend>
                    <div>
                        <label for="reporter_id">Reporter ID:</label>
                        <select id="reporter_id" name="reporter_id">
                            <option value="" selected></option>
                            <?php

                            foreach ($entities as $entity) {

                            ?>
                                <option value="<?php echo $entity['id']; ?>" <?php if ($complaint[0]['reporter_entity_id'] == $entity['id']) {
                                                                                    print('selected');
                                                                                } ?>><?php echo $entity['first_name'] . " " . $entity['last_name']; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Employees</legend>
                    <?php

                    // $db_select_employees_stmt = $db_conn->prepare('SELECT * FROM identity JOIN entity ON identity.id = entity.primary_identity_id JOIN employee ON entity.id = employee.entity_id');
                    $db_select_employees_stmt = $db_conn->prepare('SELECT * FROM `show_employees`');

                    try {
                        $db_select_employees_stmt->execute();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }

                    $employees = $db_select_employees_stmt->fetchAll(PDO::FETCH_BOTH);

                    ?>
                    <div>
                        <label for="employee_taking">Employee taking report:</label>
                        <select id="employee_taking" name="employee_taking" required>
                            <option value=""></option>
                            <?php

                            foreach ($employees as $employee) {

                            ?>
                                <option value="<?php echo $employee['employee_id']; ?>" <?php if ($complaint[0]['taken_by_employee_id'] == $employee['entity_id']) {
                                                                                    print('selected');
                                                                                } ?>><?php echo $employee['first_name'] . " " . $employee['last_name']; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                    <div>
                        <label for="employee_entered">Employee entering report:</label>
                        <select id="employee_entered" name="employee_entered" required>
                            <option value="" selected></option>
                            <?php

                            foreach ($employees as $employee) {

                            ?>
                                <option value="<?php echo $employee['employee_id']; ?>" <?php if ($complaint[0]['entered_by_employee_id'] == $employee['entity_id']) {
                                                                                    print('selected');
                                                                                } ?>><?php echo $employee['first_name'] . " " . $employee['last_name']; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                </fieldset>
                <div>
                    <input type="submit" value="Update complaint">
                </div>
            </form>

        </section>

<?php

    }
}

include('footer.php');

?>