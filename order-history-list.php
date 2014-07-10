<?php
require 'shared.php';

if (empty($shared->studentId)) {
    header('Location: /sso');
    die;
}

$tpl = new RainTpl();

$query = $pdo->prepare('
SELECT StoreOrder.*, (SELECT SUM(quantity) FROM OrderContents WHERE order_id = id) AS count
FROM StoreOrder
WHERE student_id = ?
ORDER BY datestamp DESC');
$query->execute(array($shared->studentId));

$orders = $query->fetchAll(PDO::FETCH_OBJ);

$tpl->assign('orders', $orders);
$tpl->assign('shared', $shared);
$tpl->draw("order-history-list");
