<?php
require 'shared.php';

$canEdit = in_array('past-orders', $shared->employeePermissions);

if (!$shared->isEmployee || !$canEdit || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /');
    die;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

$pdo->beginTransaction();

$query = $pdo->prepare('SELECT status FROM StoreOrder WHERE id = ?');
$query->execute(array($id));

if ($query->rowCount() > 0 && $query->fetchObject()->status == 'Shipped') {
    if (isset($_POST['return'])) {
        $product = (int) $_POST['return'];

        $query = $pdo->prepare('UPDATE OrderContents SET returned = returned + 1 WHERE returned < quantity AND product_id = ? AND order_id = ? LIMIT 1');
        $query->execute(array($product, $id));
        
    } else if (isset($_POST['undo-return'])) {
        $product = (int) $_POST['undo-return'];

        $query = $pdo->prepare('UPDATE OrderContents SET returned = returned - 1 WHERE returned > 0 AND product_id = ? AND order_id = ? LIMIT 1');
        $query->execute(array($product, $id));
        if ($query->rowCount() == 1) {

        }
    }
}

$pdo->commit();

header('Location: /order-history/' . $id);
