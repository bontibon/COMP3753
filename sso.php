<?php
require 'shared.php';

$tpl = new RainTpl();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $query = $pdo->prepare('SELECT id FROM Student WHERE id = ? LIMIT 1');
    $query->execute(array($username));
    if ($query->rowCount() == 0) {
        header('Location: /sso');
    } else {
        $_SESSION['student_id'] =  $query->fetchObject()->id;
        header('Location: /');
    }
    die;
}

$tpl->draw("sso");
