<?php
require 'shared.php';

if (!in_array('edit-acl', $shared->employeePermissions)) {
    header('Location: /');
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo->beginTransaction();

    $aclList = isset($_POST['acl']) ? $_POST['acl'] : array();

    foreach ($aclList as $id => $acl) {
        $query = $pdo->prepare('DELETE FROM Acl WHERE employee_id = ?');
        $query->execute(array($id));

        $items = array_map('trim', explode(",", $acl));
        foreach ($items as $item) {
            $query = $pdo->prepare('INSERT INTO Acl (employee_id, permission_id) VALUES (?, ?)');
            $query->execute(array($id, $item));
        }
    }

    $pdo->commit();

    header('Location: /admin/acl');
    die;
}

$tpl = new RainTpl();

$query = $pdo->prepare('SELECT id, name FROM Employee');
$query->execute();

$employees = $query->fetchAll(PDO::FETCH_OBJ);

$query = $pdo->prepare('SELECT permission_id, employee_id FROM Acl');
$query->execute();
$acl = $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP, 1);

$query = $pdo->prepare('SELECT * FROM Permission ORDER BY id');
$query->execute();
$permissions = $query->fetchAll(PDO::FETCH_OBJ);

$tpl->assign('employees', $employees);
$tpl->assign('acl', $acl);
$tpl->assign('permissions', $permissions);
$tpl->assign('shared', $shared);
$tpl->draw("admin-acl");
