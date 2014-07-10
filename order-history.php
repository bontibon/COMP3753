<?php
require 'shared.php';

$canEdit = in_array('past-orders', $shared->employeePermissions);
$orderStates = array('Ordered', 'Paid', 'Shipped', 'Canceled');

if (empty($shared->studentId) && (!$shared->isEmployee || !$canEdit)) {
    header('Location: /sso');
    die;
}

$tpl = new RainTpl();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id == 0) {
    header('Location: /');
    die;
}

$query = $pdo->prepare('SELECT * FROM StoreOrder WHERE id = ? AND (? IS TRUE OR student_id = ?) LIMIT 1');
$query->execute(array($id, $canEdit, $shared->studentId));

if ($query->rowCount() == 0) {
    header('Location: /order-history');
    die;
}

$order = $query->fetchObject();

$query = $pdo->prepare('
SELECT Product.id AS id, Product.name AS name, OrderContents.quantity AS quantity,
    OrderContents.returned AS returned, OrderContents.price AS price
FROM OrderContents
LEFT JOIN Product
    ON OrderContents.product_id = Product.id
WHERE OrderContents.order_id = ?');
$query->execute(array($order->id));

$items = $query->fetchAll(PDO::FETCH_OBJ);

$tpl->assign('orderStates', $orderStates);
$tpl->assign('canEdit', $canEdit);
$tpl->assign('order', $order);
$tpl->assign('items', $items);

$tpl->assign('shared', $shared);
$tpl->draw("order-history");
