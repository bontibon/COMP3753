<?php
require 'shared.php';

$tpl = new RainTpl();
$items = array();
$grand_total = 0;
$errors = isset($_GET['error']) ? explode(".", $_GET['error']) : array();
$canOrder = !empty($shared->studentId) || $shared->isEmployee;
$canProxyOrder = in_array('checkout', $shared->employeePermissions);

if (is_array($shared->currentCart)) {
    foreach ($shared->currentCart as $item_id => $quantity) {
        if (!preg_match('/^\d+$/', $item_id)) {
            continue;
        }
        if (!array_key_exists($item_id, $items)) {
            $query = $pdo->prepare('SELECT * FROM Product WHERE id = ? LIMIT 1');
            $query->execute(array($item_id));
            if ($query->rowCount() == 0) {
                continue;
            }
            $items[$item_id]['product'] = $query->fetchObject();
            $items[$item_id]['quantity'] = $quantity;
            $items[$item_id]['error'] = in_array($items[$item_id]['product']->id, $errors);
        } else {
            $items[$item_id]['quantity'] += $quantity;
        }
        $items[$item_id]['total'] =
            $items[$item_id]['product']->price * $items[$item_id]['quantity'];
        $items[$item_id]['total'] = sprintf("%.2f", $items[$item_id]['total']);
        $grand_total += $items[$item_id]['total'];
    }
}

$tpl->assign('canOrder', $canOrder);
$tpl->assign('canProxyOrder', $canProxyOrder);
$tpl->assign('failure', !empty($errors));
$tpl->assign('grand_total', sprintf('%.2f', $grand_total));
$tpl->assign('items', $items);
$tpl->assign('shared', $shared);
$tpl->draw("cart");
