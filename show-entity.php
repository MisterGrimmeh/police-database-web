<?php

require_once('config.php');
require_once('database.php');

$page_title = "Entities";
$request_id;
$db_select_entity_stmt;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $request_id = intval($_GET['id']);
    $db_select_entity_stmt = $db_conn->prepare('SELECT * FROM `entity` WHERE `id` = :id');
    $db_select_entity_stmt->bindParam(':id', $request_id, PDO::PARAM_INT);
    $page_title = "Entity #" . $request_id;
} else {
    $db_select_entity_stmt = $db_conn->prepare('SELECT * FROM `entity`');
}

include('header.php');

try {
    $db_select_entity_stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage(); // TODO: meaningful database exceptions
}

?>
<section>
    <header>
        <?php if (isset($request_id)) {
        ?>
            <h1><a href="show-entity.php">Entity #<?php print($request_id); ?></a></h1>
        <?php
        } else {
        ?>
            <h1>All Entities</h1>
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
                <td>Primary identity ID</td>
                <td>Actions</td>
            </tr>
        </thead>
        <tbody>
            <?php

            $entity = $db_select_entity_stmt->fetchAll(PDO::FETCH_BOTH);

            foreach ($entity as $row) {

                $db_select_primary_id_data_stmt = $db_conn->prepare('SELECT * FROM `identity` WHERE `id` = :id');
                $db_select_primary_id_data_stmt->bindParam(':id', $row['primary_identity_id'], PDO::PARAM_INT);
                try {
                    $db_select_primary_id_data_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }
                $identity = $db_select_primary_id_data_stmt->fetchAll(PDO::FETCH_BOTH);

            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php
                        foreach ($identity as $element) {
                            print($element['first_name'] . ' ');
                            if (isset($element['middle_name'])) {
                                print(substr($element['middle_name'] . ' ', 0, 1));
                            }
                            print($element['last_name']);
                        }
                        if (isset($row['primary_identity_id'])) {
                            print(' (' . $row['primary_identity_id'] . ')');
                        }
                        ?></td>
                    <td><?php if (!isset($request_id)) { ?><a href="?id=<?php echo $row['id']; ?>">show</a> <?php } ?><a href="edit-entity.php?a=delete&id=<?php echo $row['id']; ?>">delete</a> <a href="edit-entity.php?a=edit&id=<?php echo $row['id']; ?>">edit</a></td>
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
                    <td colspan="13"><a href="new-entity.php">new</a></td>
                </tr>
            </tfoot>
        <?php

        }

        ?>
    </table>
    <?php if (isset($request_id)) {

        $db_select_all_identities_for_entity = $db_conn->prepare('CALL GetAllIdentitiesForEntity(:id)');
        $db_select_all_identities_for_entity->bindParam(':id', $request_id, PDO::PARAM_INT);
        try {
            $db_select_all_identities_for_entity->execute();
        } catch (PDOException $e) {
            echo $e->getMessage(); // TODO: meaningful database exceptions
        }
        $identities = $db_select_all_identities_for_entity->fetchAll(PDO::FETCH_BOTH);

    ?>
        <h2>All identities for entity</h2>
        <table>
            <thead>
                <tr>
                    <td>Identity ID</td>
                    <td>First name</td>
                    <td>Middle name</td>
                    <td>Last name</td>
                    <td>Alias</td>
                    <td>Date of birth</td>
                    <td>Last known residence</td>
                    <td>Telephone number</td>
                    <td>E-mail</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php

                foreach ($identities as $id) {

                ?>
                    <tr>
                        <td><?php print($id['id']); ?></td>
                        <td><?php print($id['first_name']); ?></td>
                        <td><?php print($id['middle_name']); ?></td>
                        <td><?php print($id['last_name']); ?></td>
                        <td><?php print($id['alias']); ?></td>
                        <td><?php print($id['date_of_birth']); ?></td>
                        <td><?php print($id['last_known_residence']); ?></td>
                        <td><?php print($id['tel_number']); ?></td>
                        <td><?php print($id['email']); ?></td>
                        <td><a href="edit-identity.php?a=delete&id=<?php print($id['id']); ?>">delete</a> <a href="edit-identity.php?a=edit&id=<?php print($id['id']); ?>">edit</a></td>
                    </tr>
                <?php

                }

                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10"><a href="new-identity.php?id=<?php print($request_id); ?>">new</a></td>
                </tr>
            </tfoot>
        </table>
    <?php
    }
    ?>
</section>
<?php

include('footer.php');

?>