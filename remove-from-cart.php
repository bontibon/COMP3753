<?php
require 'shared.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    $current_cart = isset($_COOKIE['cart']) ? $_COOKIE['cart'] : '';

    $current_cart = @unserialize($current_cart);
    if ($current_cart == NULL) {
        $current_cart = array();
    }
    if (array_key_exists($id, $current_cart)) {
        $current_cart[$id]--;
        if ($current_cart[$id] <= 0) {
            unset($current_cart[$id]);
        }
    }

    setcookie('cart', serialize($current_cart), strtotime('+1 month'));
}

header('Location: /cart');
