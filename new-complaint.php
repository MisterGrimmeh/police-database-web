<?php

require_once('config.php');
require_once('database.php');

$page_title = "New Complaint";
include('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_datetime_format = 'Y-m-d\TH:i';
    $database_datetime_format = 'Y-m-d H:i:s';

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
        $occurance_location_id = intval($_POST['location_id']);
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
        var_dump($form_s_event_id);
        var_dump($form_s_crime_id);
        var_dump($form_d_perpetrator_entity_id);
        var_dump($form_d_perpetrator_identity_id);
        var_dump($form_d_victim_entity_id);
        var_dump($form_d_victim_identity_id);
        var_dump($form_d_reporter_entity_id);
        var_dump($form_d_reporter_identity_id);
        var_dump($taken_by_employee_id);
        var_dump($form_s_entered_by_employee_id);
        var_dump($form_d_occurance_location_id);
        var_dump($form_p_occurance_began);
        var_dump($form_p_occurance_ceased);
    }

    $db_insert_complaint_stmt = $db_conn->prepare('INSERT INTO `complaint_report` (
        `event_id`,
        `crime_id`,
        `perpetrator_entity_id`,
        `perpetrator_identity_id`,
        `victim_entity_id`,
        `victim_identity_id`,
        `reporter_entity_id`,
        `reporter_identity_id`,
        `taken_by_employee_id`,
        `entered_by_employee_id`,
        `occurance_location_id`,
        `occurance_began`,
        `occurance_ceased`
        ) VALUES (
        :event_id,
        :crime_id,
        :perpetrator_entity_id,
        :perpetrator_identity_id,
        :victim_entity_id,
        :victim_identity_id,
        :reporter_entity_id,
        :reporter_identity_id,
        :taken_by_employee_id,
        :entered_by_employee_id,
        :occurance_location_id,
        :occurance_began,
        :occurance_ceased)');
    $db_insert_complaint_stmt->bindParam(':event_id', $form_s_event_id);
    $db_insert_complaint_stmt->bindParam(':crime_id', $form_s_crime_id);
    $db_insert_complaint_stmt->bindParam(':perpetrator_entity_id', $form_d_perpetrator_entity_id);
    $db_insert_complaint_stmt->bindParam(':perpetrator_identity_id', $form_d_perpetrator_identity_id);
    $db_insert_complaint_stmt->bindParam(':victim_entity_id', $form_d_victim_entity_id);
    $db_insert_complaint_stmt->bindParam(':victim_identity_id', $form_d_victim_identity_id);
    $db_insert_complaint_stmt->bindParam(':reporter_entity_id', $form_d_reporter_entity_id);
    $db_insert_complaint_stmt->bindParam(':reporter_identity_id', $form_d_reporter_identity_id);
    $db_insert_complaint_stmt->bindParam(':taken_by_employee_id', $taken_by_employee_id);
    $db_insert_complaint_stmt->bindParam(':entered_by_employee_id', $form_s_entered_by_employee_id);
    $db_insert_complaint_stmt->bindParam(':occurance_location_id', $form_d_occurance_location_id);
    $db_insert_complaint_stmt->bindParam(':occurance_began', $form_p_occurance_began);
    $db_insert_complaint_stmt->bindParam(':occurance_ceased', $form_p_occurance_ceased);

    try {
        $db_insert_complaint_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    $redirect_show_id = $db_conn->lastInsertId();
    header('Location: show-complaint.php?id='. $redirect_show_id, true, 303);

} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

?>

    <section>

        <header>
            <h1><a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">New Complaint</a></h1>
            <?php

            if ($dev_mode) {
                print('<br><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
                if (isset($_SERVER['REQUEST_METHOD'])) {
                    print_r($_SERVER['REQUEST_METHOD']);
                }
                echo '</p>';
            }

            ?>
        </header>

        <form id="enter_new_complaint" name="enter_new_complaint" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <fieldset>
                <legend>Occurance information</legend>
                <div>
                    <label for="occurance_began">Occurance began:</label>
                    <input type="datetime-local" id="occurance_began" name="occurance_began">
                </div>
                <div>
                    <label for="occurance_ceased">Occurance ceased:</label>
                    <input type="datetime-local" id="occurance_ceased" name="occurance_ceased">
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
                        <option value="" selected></option>
                        <?php

                        foreach ($events as $event) {

                        ?>
                            <option value="<?php echo $event['id']; ?>"><?php echo "Event " . $event['id'];
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
                            <option value="<?php echo $crime['id']; ?>"><?php echo $crime['code'] . " (" . $crime['title'] . ")"; ?></option>

                        <?php

                        }

                        ?>
                    </select>
                </div>
                <fieldset>
                    <legend>Location</legend>
                    <div>
                        <label for="location_id">Location ID:</label>
                        <input type="number" id="location_id" name="location_id">
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
                        <option value="" selected></option>
                        <?php

                        foreach ($entities as $entity) {

                        ?>
                            <option value="<?php echo $entity['id']; ?>"><?php echo $entity['first_name'] . " " . $entity['last_name']; ?></option>

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
                            <option value="<?php echo $entity['id']; ?>"><?php echo $entity['first_name'] . " " . $entity['last_name']; ?></option>

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
                            <option value="<?php echo $entity['id']; ?>"><?php echo $entity['first_name'] . " " . $entity['last_name']; ?></option>

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
                    <label for="employee_taking">Employee taking report:</label>
                    <select id="employee_taking" name="employee_taking" required>
                        <option value="" selected></option>
                        <?php

                        foreach ($employees as $employee) {

                        ?>
                            <option value="<?php echo $employee['employee_id']; ?>"><?php echo $employee['first_name'] . " " . $employee['last_name']; ?></option>

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
                            <option value="<?php echo $employee['employee_id']; ?>"><?php echo $employee['first_name'] . " " . $employee['last_name']; ?></option>

                        <?php

                        }

                        ?>
                    </select>
                </div>
            </fieldset>
            <div>
                <input type="submit" value="Submit new complaint">
            </div>
        </form>

    </section>

<?php

}

include('footer.php');

?>