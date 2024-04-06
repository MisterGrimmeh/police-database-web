<?php

/*

    ===========================================================================
        NYU : CS-GY 6083
        Spring 2024
    ===========================================================================
        Simple UI for a single table

        I am not a developer, sorry for the mess!
    ===========================================================================

*/

require_once('config.php');
require_once('database.php');

$page_title = "Event CRUD";
include('header.php');

$db_describe_event_stmt = $db_conn->prepare('DESCRIBE `event`');
try {
    $db_describe_event_stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage(); // TODO: meaningful database exceptions
}

$request_url;
$request_query = array();
$request_action = 'view';
$request_action_param = 'view';
if (isset($_SERVER['REQUEST_URI'])) {
    $request_url = parse_url($_SERVER['REQUEST_URI']);

    if (isset($request_url['query'])) {
        parse_str($request_url['query'], $request_query);
        $request_action_param = $request_query['action'];
    }
}
if (isset($_POST['action'])) {
    $request_action_param = $_POST['action'];
}
switch ($request_action_param) {
    case 'edit':
        $request_action = 'edit';
        break;
    case 'add':
        $request_action = 'add';
        break;
    case 'view':
        $request_action = 'view';
        break;
    case 'delete':
        $request_action = 'delete';
        break;
    default:
        $request_action = 'undefined';
}

define('REQUEST_ACTION_VIEW', 1);
define('REQUEST_ACTION_EDIT_SUBMIT', 2);
define('REQUEST_ACTION_EDIT_FORM', 3);
define('REQUEST_ACTION_DELETE', 4);
define('REQUEST_ACTION_ADD_SUBMIT', 5);
define('REQUEST_ACTION_ADD_FORM', 6);
$request_mode = REQUEST_ACTION_VIEW; // default

define('VIEW_TABLE', 1);
define('VIEW_FORM', 2);
$view_mode = VIEW_TABLE; // default

if (($request_action == 'view' || empty($request_action)) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $request_mode = REQUEST_ACTION_VIEW;
    $view_mode = VIEW_TABLE;
} elseif ($request_action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $request_mode = REQUEST_ACTION_EDIT_FORM;
    $view_mode = VIEW_FORM;
} elseif ($request_action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_mode = REQUEST_ACTION_EDIT_SUBMIT;
    $view_mode = VIEW_TABLE;
} elseif ($request_action == 'add' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $request_mode = REQUEST_ACTION_ADD_FORM;
    $view_mode = VIEW_FORM;
} elseif ($request_action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_mode = REQUEST_ACTION_ADD_SUBMIT;
    $view_mode = VIEW_TABLE;
} elseif ($request_action == 'delete' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $request_mode = REQUEST_ACTION_DELETE;
    $view_mode = VIEW_TABLE;
} else {
    http_response_code(400);
}

$request_id = null;
if (isset($request_query['id']) && is_numeric($request_query['id'])) {
    $request_id = $request_query['id'];
}

if ($request_mode == REQUEST_ACTION_EDIT_SUBMIT) {
    // r = raw, s = safe, p = processed (and ready)

    $form_datetime_format = 'Y-m-d\TH:i';
    $database_datetime_format = 'Y-m-d H:i:s';

    $form_p_event_id = null;
    $form_p_event_occurance_began = null;
    $form_p_event_occurance_ceased = null;

    if (isset($_POST['event_id']) && is_numeric($_POST['event_id'])) {
        $form_p_event_id = $_POST['event_id'];
    }

    if (isset($_POST['request_id']) && is_numeric($_POST['request_id'])) {
        $request_id = $_POST['request_id'];
    } else {
        // TODO: no request_id should fail with an error
    }

    if (isset($_POST['event_occurance_began'])) {
        $form_r_event_occurance_began = $_POST['event_occurance_began'];

        $form_s_event_occurance_began = DateTime::createFromFormat($form_datetime_format, $form_r_event_occurance_began);
        if ($form_s_event_occurance_began && $form_s_event_occurance_began->format($form_datetime_format) === $form_r_event_occurance_began) {
            $form_p_event_occurance_began = $form_s_event_occurance_began->format($database_datetime_format);
        }
    }

    if (isset($_POST['event_occurance_ceased'])) {
        $form_r_event_occurance_ceased = $_POST['event_occurance_ceased'];

        $form_s_event_occurance_ceased = DateTime::createFromFormat($form_datetime_format, $form_r_event_occurance_ceased);
        if ($form_s_event_occurance_ceased && $form_s_event_occurance_ceased->format($form_datetime_format) === $form_r_event_occurance_ceased) {
            $form_p_event_occurance_ceased = $form_s_event_occurance_ceased->format($database_datetime_format);
        }
    }

    $db_update_event_stmt = $db_conn->prepare('UPDATE `event` SET `id` = :new_id, `occurance_began` = :occurance_began, `occurance_ceased` = :occurance_ceased WHERE `id` = :existing_id');
    $db_update_event_stmt->bindParam(':new_id', $form_p_event_id, PDO::PARAM_INT);
    $db_update_event_stmt->bindParam(':occurance_began', $form_p_event_occurance_began, PDO::PARAM_STR);
    $db_update_event_stmt->bindParam(':occurance_ceased', $form_p_event_occurance_ceased, PDO::PARAM_STR);
    $db_update_event_stmt->bindParam(':existing_id', $request_id, PDO::PARAM_INT);

    try {
        $db_update_event_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }
}

if ($request_mode == REQUEST_ACTION_ADD_SUBMIT) {
    $db_insert_event_stmt = $db_conn->prepare('INSERT INTO `event` (`id`, `occurance_began`, `occurance_ceased`) VALUES (:id, :occurance_began, :occurance_ceased)');
    $db_insert_event_stmt->bindParam(':id', $form_s_event_id);
    $db_insert_event_stmt->bindParam(':occurance_began', $form_p_event_occurance_began);
    $db_insert_event_stmt->bindParam(':occurance_ceased', $form_p_event_occurance_ceased);

    try {
        $db_insert_event_stmt->execute();
    } catch (PDOException $e) {
        echo $e->getMessage(); // TODO: meaningful database exceptions
    }
}

?>
<section>
    <header>
        <h1>Event CRUD Dashboard <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">&larr;</a></h1>
        <?php

        if ($dev_mode) {
            print('<p><b>$request_action:</b> ');
            if (isset($request_action)) {
                print_r($request_action);
            }
            print('<br><b>$request_mode:</b> ');
            if (isset($request_mode)) {
                print_r($request_mode);
                print(' (');
                switch ($request_mode) {
                    case 1:
                        print('REQUEST_ACTION_VIEW');
                        break;
                    case 2:
                        print('REQUEST_ACTION_EDIT_SUBMIT');
                        break;
                    case 3:
                        print('REQUEST_ACTION_EDIT_FORM');
                        break;
                    case 4:
                        print('REQUEST_ACTION_DELETE');
                        break;
                    case 5:
                        print('REQUEST_ACTION_ADD_SUBMIT');
                        break;
                    case 6:
                        print('REQUEST_ACTION_ADD_FORM');
                        break;
                    default:
                        print('UNDEFINED');
                        break;
                }
                print(')');
            }
            print('<br><b>$view_mode:</b> ');
            if (isset($view_mode)) {
                print_r($view_mode);
                print(' (');
                switch ($view_mode) {
                    case 1:
                        print('VIEW_TABLE');
                        break;
                    case 2:
                        print('VIEW_FORM');
                        break;
                    default:
                        print('UNDEFINED');
                }
                print(')');
            }
            print('<br><b>$_SERVER[\'REQUEST_METHOD\']:</b> ');
            if (isset($_SERVER['REQUEST_METHOD'])) {
                print_r($_SERVER['REQUEST_METHOD']);
            }
            echo '</p>';
        }

        ?>
    </header>
    <?php

    if (!isset($view_mode) || ($view_mode == VIEW_TABLE) || empty($view_mode)) {

        if ($request_mode == REQUEST_ACTION_DELETE) {
            $db_delete_event_stmt = $db_conn->prepare('DELETE FROM `event` WHERE `id` = :id');
            $db_delete_event_stmt->bindParam(':id', $request_id);

            try {
                $db_delete_event_stmt->execute();
            } catch (PDOException $e) {
                echo $e->getMessage(); // TODO: meaningful database exceptions
            }
        }

    ?>
        <table>
            <thead>
                <tr>
                    <?php

                    while ($table_meta = $db_describe_event_stmt->fetchAll(PDO::FETCH_COLUMN)) {

                        foreach ($table_meta as $th) {
                    ?>
                            <td><?php echo $th; ?></td>
                    <?php

                        }
                    }

                    ?>
                    <td>actions</td>
                </tr>
            </thead>
            <tbody>
                <?php

                $db_select_event_stmt = $db_conn->prepare('SELECT * FROM `event`');

                try {
                    $db_select_event_stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }

                $db_select_event_col_count = 0; // for html colspan

                while ($event = $db_select_event_stmt->fetch(PDO::FETCH_ASSOC)) {

                ?>
                    <tr>
                        <?php

                        foreach ($event as $item) {

                            $db_select_event_col_count++;

                        ?>
                            <td><?php echo $item; ?></td>
                        <?php

                        }

                        ?>
                        <td><a href="?action=edit&id=<?php echo $event['id']; ?>">edit</a> <a href="?action=delete&id=<?php echo $event['id']; ?>">delete</a></td>
                    </tr>
                <?php

                }

                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="<?php echo $db_select_event_col_count + 1; ?>"><a href="?action=add">add</a></td>
                </tr>
            </tfoot>
        </table>

    <?php

    } elseif ($view_mode == VIEW_FORM) {

    ?>
        <form id="edit_table" name="edit_table" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="action" value="<?php if ($request_mode == REQUEST_ACTION_ADD_FORM) {
                                                            echo "add";
                                                        } elseif ($request_mode == REQUEST_ACTION_EDIT_FORM) {
                                                            echo "edit";
                                                        } ?>">
            <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
            <?php

            if ($request_mode == REQUEST_ACTION_EDIT_FORM) {
                $db_select_event = $db_conn->prepare('SELECT * FROM `event` WHERE `id` = :id');
                $db_select_event->bindParam(':id', $request_id);

                try {
                    $db_select_event->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); // TODO: meaningful database exceptions
                }

                while ($event = $db_select_event->fetch(PDO::FETCH_ASSOC)) {

                    foreach ($event as $key => $row) {
                        $form_name = "event_" . $key;
                        $meta = $db_select_event->getColumnMeta(array_search($key, array_keys($event)));
                        $type;

                        switch ($meta['native_type']) {
                            case 'LONGLONG':
                            case 'LONG':
                            case 'SHORT':
                            case 'DOUBLE':
                                $type = 'number';
                                break;
                            case 'DATETIME':
                                $type = 'datetime-local';
                                break;
                            case 'DATE':
                                $type = 'date';
                                break;
                            case 'TIMESTAMP':
                                $type = 'time';
                                break;
                            case 'VAR_STRING':
                            case 'STRING':
                            default:
                                $type = 'text';
                        }

            ?>
                        <div>
                            <label for="<?php echo $form_name; ?>"><?php echo $key; ?>:</label>
                            <input type="<?php echo $type; ?>" value="<?php if ($request_mode == REQUEST_ACTION_EDIT_FORM) {
                                                                            echo $row;
                                                                        } ?>" name="<?php echo $form_name; ?>" id="<?php echo $form_name; ?>">
                        </div>
                    <?php

                    }
                }
            } elseif ($request_mode == REQUEST_ACTION_ADD_FORM) {
                while ($table_meta = $db_describe_event_stmt->fetch(PDO::FETCH_ASSOC)) {

                    $form_field = $table_meta['Field'];
                    $form_name = "event_" . $form_field;
                    $form_type;

                    switch ($table_meta['Type']) {
                        case 'int':
                            $form_type = 'number';
                            break;
                        case 'datetime':
                            $form_type = 'datetime-local';
                            break;
                        case 'date':
                            $form_type = 'date';
                            break;
                        case 'timestamp':
                            $form_type = 'time';
                            break;
                        case 'varchar':
                        default:
                            $form_type = 'text';
                    }

                    ?>
                    <div>
                        <label for="<?php echo $form_name; ?>"><?php echo $form_field; ?>:</label>
                        <input type="<?php echo $form_type; ?>" name="<?php echo $form_name; ?>" id="<?php echo $form_name ?>">
                    </div>
            <?php

                }
            }

            ?>
            <input type="submit" value="Submit">
        </form>
    <?php

    }

    ?>
</section>
<?php

include('footer.php');

?>