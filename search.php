<?php
require 'shared.php';

if (isset($_POST['query'])) {
    $search_query = clean_search_query($_POST['query']);
    header('Location: /search/' . $search_query);
    die;
}

// TODO:  on sale

$tpl = new RainTpl();

$search_query = isset($_GET['query']) ? clean_search_query($_GET['query']) : '';

if (preg_match('/\[([^]]+)\]/', $search_query, $matches)) {
    $tagless_query = trim(str_replace($matches[0], '', $search_query));

    $query = $pdo->prepare('
    SELECT * FROM Category
    LEFT JOIN Product
        ON Category.product_id = Product.id
    WHERE Category.name = ? AND (Product.name LIKE ? OR Product.description LIKE ?
        OR Product.author LIKE ? OR Product.isbn = ?)
        AND (Product.enabled IS TRUE OR ? IS TRUE)');

    $query->execute(array($matches[1], '%' . $tagless_query . '%',
        '%' . $tagless_query . '%', '%' . $tagless_query . '%', $tagless_query,
        $shared->isEmployee));
} else {
    $query = $pdo->prepare('
    SELECT * FROM Product
    WHERE (Product.name LIKE ? OR Product.description LIKE ?
        OR Product.author LIKE ? OR Product.isbn = ?)
        AND (Product.enabled IS TRUE OR ? IS TRUE)');

    $query->execute(array('%' . $search_query . '%', '%' . $search_query . '%',
        '%' . $search_query . '%', $search_query, $shared->isEmployee));
}

$results = $query->fetchAll(PDO::FETCH_OBJ);

if ($query->rowCount() == 1 && $results[0]->isbn == $search_query) {
    header('Location: /product/' . $results[0]->id);
    die;
}

$tpl->assign('results', $results);

$tpl->assign('search_query', $search_query);
$tpl->assign('shared', $shared);
$tpl->draw("search");
