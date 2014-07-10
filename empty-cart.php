<?php
require 'shared.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    setcookie('cart', '', time() - 3600);
}

header('Location: /cart');
