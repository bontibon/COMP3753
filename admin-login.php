<?php
require 'shared.php';

if ($shared->isEmployee) {
    header('Location: /');
    die;
}

$tpl = new RainTpl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $query = $pdo->prepare('SELECT name, password FROM Employee WHERE id = ?');
    $query->execute(array($username));

    $obj = $query->fetchObject();

    if ($query->rowCount() == 0 || !password_verify($password, $obj->password)) {
        header('Location: /admin/login/failure/');
        die;
    }

    $query = $pdo->prepare('SELECT permission_id FROM Acl WHERE employee_id = ?');
    $query->execute(array($username));

    $permissions = array();

    while ($obj = $query->fetchObject()) {
        $permissions[] = $obj->permission_id;
    }

    if (!in_array('login', $permissions)) {
        header('Location: /admin/login/failure/');
        die;
    }

    $_SESSION['employee'] = $username;
    $_SESSION['isEmployee'] = true;
    $_SESSION['employeeName'] = $obj->name;
    $_SESSION['employeePermissions'] = $permissions;

    header('Location: /');
    die;
}

$tpl->assign('failure', isset($_GET['failure']) && $_GET['failure'] == 'true');
$tpl->assign('shared', $shared);

$tpl->draw("admin-login");
