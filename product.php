<?php
require 'shared.php';

$tpl = new RainTpl();

$canEdit = in_array('edit-product', $shared->employeePermissions);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$query = $pdo->prepare('
SELECT * FROM Product
WHERE id = ? AND (enabled IS TRUE OR ? IS TRUE)
LIMIT 1');
$query->execute(array($id, $shared->isEmployee));

$product = $query->fetchObject();

if ($product) {
    $query = $pdo->prepare('SELECT name FROM Category WHERE product_id = ?');
    $query->execute(array($product->id));

    $categories = $query->fetchAll(PDO::FETCH_COLUMN, 0);
} else {
    $categories = array();
}

if (isset($_GET['query'])) {
    $tpl->assign('search_query', clean_search_query($_GET['query']));
}

$tpl->assign('canEdit', $canEdit);

$tpl->assign('categories', $categories);
$tpl->assign('product', $product);
$tpl->assign('shared', $shared);

$tpl->draw("product");
