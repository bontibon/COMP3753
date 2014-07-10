<?php
require 'shared.php';

if (!$shared->isEmployee || !in_array('add-product', $shared->employeePermissions)
    || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /');
    die;
}

$pdo->beginTransaction();

$query = $pdo->prepare('
INSERT INTO Product (name, description, quantity, price, enabled)
VALUES ("", "", 0, 0.00, FALSE)');
$query->execute();

$id = $pdo->lastInsertId();

$pdo->commit();

header('Location: /product/' . $id);
