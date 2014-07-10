<?php
require 'shared.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    if (!array_key_exists($id, $shared->currentCart)) {
        $shared->currentCart[$id] = 0;
    }
    $shared->currentCart[$id]++;

    setcookie('cart', serialize($shared->currentCart), strtotime('+1 month'));
}

header('Location: /product/' . $id);
