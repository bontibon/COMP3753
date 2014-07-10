<?php
require 'shared.php';

$items = isset($_POST['item']) ? $_POST['item'] : array();

foreach ($items as $id => $_) {
    if (!is_numeric($id)) {
        continue;
    }
    if (!array_key_exists($id, $shared->currentCart)) {
        $shared->currentCart[$id] = 0;
    }
    $shared->currentCart[$id]++;
}

setcookie('cart', serialize($shared->currentCart), strtotime('+1 month'));

$path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);

header('Location: ' . $path);
