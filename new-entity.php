<?php

require_once('config.php');
require_once('database.php');

$page_title = "New Entity";
include('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $form_s_primary_identity_id = null;

    if (isset($_POST['primary_identity_id']) && is_numeric($_POST['primary_identity_id'])) {
        $form_s_primary_identity_id = intval($_POST['primary_identity_id']);
    }

    if ($dev_mode) {
        print('<b>$form_s_primary_identity_id =</b>');
        var_dump($form_s_primary_identity_id);
    }

    $db_insert_entity_stmt = $db_conn->prepare('INSERT INTO `entity` (
        `primary_identity_id`
        ) VALUES (
        :primary_identity_id)');
    $db_insert_entity_stmt->bindParam(':primary_identity_id', $form_s_primary_identity_id);

    try {
        $db_insert_entity_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }

    $redirect_show_id = $db_conn->lastInsertId();
    header('Location: show-entity.php?id=' . $redirect_show_id, true, 303);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

?>

    <section>

        <header>
            <h1><a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">New Entity</a></h1>
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

        <form id="enter_new_entity" name="enter_new_entity" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <fieldset>
                <div>
                    <label for="primary_identity_id">Primary identity:</label>
                    <select id="primary_identity_id" name="primary_identity_id">
                        <option value="" selected></option>
                        <?php

                        $db_select_identities_stmt = $db_conn->prepare('SELECT * FROM `identity`');

                        try {
                            $db_select_identities_stmt->execute();
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }

                        $identities = $db_select_identities_stmt->fetchAll(PDO::FETCH_BOTH);

                        foreach ($identities as $entity) {

                        ?>
                            <option value="<?php echo $entity['id']; ?>"><?php
                                                                            print($entity['first_name'] . ' ');
                                                                            if (isset($entity['middle_name'])) {
                                                                                print(substr($entity['middle_name'] . ' ', 0, 1));
                                                                            }
                                                                            print($entity['last_name']);
                                                                            if (isset($entity['date_of_birth'])){ print(' ('. $entity['date_of_birth'] .')'); } ?></option>

                        <?php

                        }

                        ?>
                    </select>
                </div>
            </fieldset>
            <div>
                <input type="submit" value="Submit new entity">
            </div>
        </form>

    </section>

<?php

}

include('footer.php');

?>