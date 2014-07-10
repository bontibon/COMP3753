<?php
require 'shared.php';

if (!$shared->isEmployee || !in_array('past-orders', $shared->employeePermissions)) {
    header('Location: /');
    die;
}

$tpl = new RainTpl();

$query = $pdo->prepare('
SELECT StoreOrder.*, (SELECT SUM(quantity) FROM OrderContents WHERE order_id = id) AS count
FROM StoreOrder
ORDER BY datestamp DESC');
$query->execute();

$orders = $query->fetchAll(PDO::FETCH_OBJ);

$tpl->assign('orders', $orders);
$tpl->assign('shared', $shared);
$tpl->draw("order-history-list");
