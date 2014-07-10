<?php
require 'shared.php';

$orderStates = array('Ordered', 'Paid', 'Shipped', 'Canceled');
$canEdit = in_array('past-orders', $shared->employeePermissions);

if (!$shared->isEmployee || !$canEdit || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: /');
    die;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$status = isset($_POST['status']) && in_array($_POST['status'], $orderStates) ?
    $_POST['status'] : $orderStates[0];

$pdo->beginTransaction();

$query = $pdo->prepare('SELECT status FROM StoreOrder WHERE id = ? LIMIT 1');
$query->execute(array($id));

if ($query->rowCount() > 0 && !in_array($query->fetchObject()->status, array('Canceled', 'Shipped'))) {

    $query = $pdo->prepare('UPDATE StoreOrder SET status = ? WHERE id = ?');
    $query->execute(array($status, $id));

    if ($status == 'Canceled') {
        $query = $pdo->prepare('SELECT quantity, product_id FROM OrderContents WHERE order_id = ?');
        $query->execute(array($id));
        $totals = $query->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_GROUP, 1);

        foreach ($totals as $product_id => $arr) {
            $quantity = $arr[0];
            $query = $pdo->prepare('UPDATE Product SET quantity = quantity + ? WHERE id = ?');
            $query->execute(array($quantity, $product_id));
        }
    }
}

$pdo->commit();

header('Location: /order-history/' . $id);
