<?php

require_once('config.php');
require_once('database.php');

$page_title = "Edit Medical";
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
    $form_s_complaint_id = null;
    $form_d_injured_entity_id = null;
    $form_d_injured_identity_id = null;
    $taken_by_employee_id = null;
    $form_s_entered_by_employee_id = null;
    $form_d_occurance_location_id = null;
    $form_p_when_injured = null;

    if (isset($_POST['event_id_select']) && is_numeric($_POST['event_id_select'])) {
        $form_s_event_id = intval($_POST['event_id_select']);
    } elseif (isset($_POST['event_id_input']) && is_numeric($_POST['event_id_input'])) {
        $form_s_event_id = intval($_POST['event_id_input']);
    }

    if (isset($_POST['complaint']) && is_numeric($_POST['complaint'])) {
        $form_s_complaint_id = intval($_POST['complaint']);
    }

    if (isset($_POST['when_injured'])) {
        $form_s_when_injured = DateTime::createFromFormat($form_datetime_format, $_POST['when_injured']);
        if ($form_s_when_injured && $form_s_when_injured->format($form_datetime_format) === $_POST['when_injured']) {
            $form_p_when_injured = $form_s_when_injured->format($database_datetime_format);
        }
    }

    if (isset($_POST['location_id']) && is_numeric($_POST['location_id'])) {
        print('<b>$_POST["location_id"] =</b>');
        var_dump($_POST['location_id']);
        $form_d_occurance_location_id = intval($_POST['location_id']);
    }

    if (isset($_POST['injured_id']) && is_numeric($_POST['injured_id'])) {
        $form_d_injured_entity_id = intval($_POST['injured_id']);

        $db_select_injured_id_stmt = $db_conn->prepare('SELECT `id` FROM `identity` WHERE `primary_entity_id` = :entity_id LIMIT 1');
        $db_select_injured_id_stmt->bindParam(':entity_id', $form_d_injured_entity_id, PDO::PARAM_INT);

        try {
            $db_select_injured_id_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $injured_id = $db_select_injured_id_stmt->fetchAll(PDO::FETCH_BOTH);
        $form_d_injured_identity_id = intval($injured_id[0]['id']);
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
        print('<b>$form_s_complaint_id =</b>');
        var_dump($form_s_complaint_id);
        print('<b>$form_d_injured_entity_id =</b>');
        var_dump($form_d_injured_entity_id);
        print('<b>$form_d_injured_identity_id =</b>');
        var_dump($form_d_injured_identity_id);
        print('<b>$taken_by_employee_id =</b>');
        var_dump($taken_by_employee_id);
        print('<b>$form_s_entered_by_employee_id =</b>');
        var_dump($form_s_entered_by_employee_id);
        print('<b>$form_d_occurance_location_id =</b>');
        var_dump($form_d_occurance_location_id);
        print('<b>$form_p_when_injured =</b>');
        var_dump($form_p_when_injured);
    }

    $db_update_medical_stmt = $db_conn->prepare('UPDATE `medical_report` SET
        `event_id` = :event_id,
        `complaint_id` = :complaint_id,
        `injured_entity_id` = :injured_entity_id,
        `inujured_identity_id` = :injured_identity_id,
        `taken_by_employee_id` = :taken_by_employee_id,
        `entered_by_employee_id` = :entered_by_employee_id,
        `occurance_location_id` = :occurance_location_id,
        `when_injured` = :when_injured
        WHERE `id` = :id');
    $db_update_medical_stmt->bindParam(':event_id', $form_s_event_id, PDO::PARAM_INT);
    $db_update_medical_stmt->bindParam(':complaint_id', $form_s_complaint_id, PDO::PARAM_INT);
    $db_update_medical_stmt->bindParam(':injured_entity_id', $form_d_injured_entity_id, PDO::PARAM_INT);
    $db_update_medical_stmt->bindParam(':injured_identity_id', $form_d_injured_identity_id, PDO::PARAM_INT);
    $db_update_medical_stmt->bindParam(':taken_by_employee_id', $taken_by_employee_id, PDO::PARAM_INT);
    $db_update_medical_stmt->bindParam(':entered_by_employee_id', $form_s_entered_by_employee_id, PDO::PARAM_INT);
    $db_update_medical_stmt->bindParam(':occurance_location_id', $form_d_occurance_location_id, PDO::PARAM_INT);
    $db_update_medical_stmt->bindParam(':when_injured', $form_p_when_injured);
    $db_update_medical_stmt->bindParam(':id', $request_id);

    try {
        $db_update_medical_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    header('Location: show-medical.php?id=' . $request_id, true, 303);
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
        $db_delete_medical_stmt = $db_conn->prepare('DELETE FROM `medical_report` WHERE `id` = :id');
        $db_delete_medical_stmt->bindParam(':id', $request_id);

        try {
            $db_delete_medical_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        header('Location: show-medical.php', true, 303);
    } elseif ($request_action == 'e') {

        $db_select_medical_stmt = $db_conn->prepare('SELECT * FROM `medical_report` WHERE `id` = :id LIMIT 1');
        $db_select_medical_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

        try {
            $db_select_medical_stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $medical = $db_select_medical_stmt->fetchAll(PDO::FETCH_BOTH);

?>

        <section>

            <header>
                <?php if (isset($request_id)) {
                ?>
                    <h1>Editing Medical #<?php print($request_id); ?></h1>
                <?php
                }

                if ($dev_mode) {
                    print('<p><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
                    if (isset($_SERVER['REQUEST_METHOD'])) {
                        print_r($_SERVER['REQUEST_METHOD']);
                    }
                    print('<br><b>$medical:</b> ');
                    var_dump($medical);
                    print('<br><b>$request_id:</b> ');
                    var_dump($request_id);
                    print('<br><b>$medical[\'id\']:</b> ');
                    var_dump($medical[0]['id']);
                    echo '</p>';
                }

                ?>
            </header>

            <form id="edit_new_medical" name="edit_new_medical" method="post" action="<?php print(htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $request_id); ?>">
                <fieldset>
                    <legend>Occurance information</legend>
                    <?php

                    if (isset($medical[0]['when_injured'])) {
                        $when_injured_db = DateTime::createFromFormat($database_datetime_format, $medical[0]['when_injured']);
                        if ($when_injured_db && $when_injured_db->format($database_datetime_format) === $medical[0]['when_injured']) {
                            $when_injured_form = $when_injured_db->format($form_datetime_format);
                        }
                    }

                    ?>
                    <div>
                        <label for="when_injured">Occurance time:</label>
                        <input type="datetime-local" id="when_injured" name="when_injured" <?php if (isset($when_injured_form)) {
                                                                                                    print(' value="' . $when_injured_form . '"');
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
                                <option value="<?php echo $event['id']; ?>" <?php if ($medical[0]['event_id'] == $event['id']) {
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
                        <?php

                        $db_select_complaints_stmt = $db_conn->prepare('SELECT `id` FROM `complaint_report`');

                        try {
                            $db_select_complaints_stmt->execute();
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }

                        $complaints = $db_select_complaints_stmt->fetchAll(PDO::FETCH_BOTH);

                        ?>
                        <label for="complaint">Complaint:</label>
                        <select id="complaint" name="complaint" required>
                            <option value=""></option>
                            <?php

                            foreach ($complaints as $complaint) {

                            ?>
                                <option value="<?php echo $complaint['id']; ?>" <?php if ($medical[0]['complaint_id'] == $complaint['id']) {
                                                                                    print('selected');
                                                                                } ?>><?php echo $complaint['id']; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                    <fieldset>
                        <legend>Location</legend>
                        <div>
                            <label for="location_id">Location ID:</label>
                            <input type="number" id="location_id" name="location_id" <?php if (isset($medical[0]['occurance_location_id'])) {
                                                                                            print('value="' . $medical[0]['occurance_location_id'] . '"');
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
                    <legend>Injured</legend>
                    <div>
                        <label for="injured_id">Injured ID:</label>
                        <select id="injured_id" name="injured_id">
                            <option value=""></option>
                            <?php

                            foreach ($entities as $entity) {

                            ?>
                                <option value="<?php echo $entity['id']; ?>" <?php if ($medical[0]['injured_entity_id'] == $entity['id']) {
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

                    $db_select_employees_stmt = $db_conn->prepare('SELECT * FROM `show_employees`');

                    try {
                        $db_select_employees_stmt->execute();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }

                    $employees = $db_select_employees_stmt->fetchAll(PDO::FETCH_BOTH);

                    ?>
                    <div>
                        <label for="employee_taking">Employee taking medical:</label>
                        <select id="employee_taking" name="employee_taking" required>
                            <option value=""></option>
                            <?php

                            foreach ($employees as $employee) {

                            ?>
                                <option value="<?php echo $employee['employee_id']; ?>" <?php if ($medical[0]['taken_by_employee_id'] == $employee['entity_id']) {
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
                                <option value="<?php echo $employee['employee_id']; ?>" <?php if ($medical[0]['entered_by_employee_id'] == $employee['entity_id']) {
                                                                                    print('selected');
                                                                                } ?>><?php echo $employee['first_name'] . " " . $employee['last_name']; ?></option>

                            <?php

                            }

                            ?>
                        </select>
                    </div>
                </fieldset>
                <div>
                    <input type="submit" value="Update medical">
                </div>
            </form>

        </section>

<?php

    }
}

include('footer.php');

?>