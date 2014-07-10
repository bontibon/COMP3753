<?php
include 'shared.php';

if (!$shared->isEmployee || !in_array('change-password', $shared->employeePermissions)) {
    header('Location: /');
    die;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $current = isset($_POST['current-password']) ? $_POST['current-password'] : '';
    $new = isset($_POST['new-password']) ? $_POST['new-password'] : '';
    $cnew = isset($_POST['confirm-new-password']) ? $_POST['confirm-new-password'] : '';

    $query = $pdo->prepare('SELECT password FROM Employee WHERE id = ? LIMIT 1');
    $query->execute(array($shared->employee));

    $obj = $query->fetchObject();
    if (!password_verify($current, $obj->password) || $new == '' || $new != $cnew) {
        header('Location: /admin/change-password/failure/');
        die;
    }
    $query = $pdo->prepare('UPDATE Employee SET password = ? WHERE id = ? LIMIT 1');
    $query->execute(array(password_hash($new, PASSWORD_DEFAULT), $shared->employee));

    session_destroy();
    header('Location: /admin/login');
    die;
}

$tpl = new RainTpl();

$tpl->assign('failure', isset($_GET['failure']));
$tpl->assign('shared', $shared);
$tpl->draw("change-password");
