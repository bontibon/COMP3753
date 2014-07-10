<?php
require 'shared.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$canDelete = in_array('delete-product', $shared->employeePermissions);

if (!$canDelete || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /product/' . $id);
    die;
}

$query = $pdo->prepare('DELETE FROM Product WHERE id = ? LIMIT 1');
$query->execute(array($id));

if ($query->rowCount() == 0) {
    header('Location: /product/' . $id);
} else {
    header('Location: /');
}
