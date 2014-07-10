<?php
require 'shared.php';

$tpl = new RainTpl();

$query = $pdo->prepare('SELECT * FROM Product WHERE enabled IS TRUE ORDER BY id DESC LIMIT 8');
$query->execute();
$tpl->assign('newest', $query->fetchAll(PDO::FETCH_OBJ));

$tpl->assign('shared', $shared);
$tpl->draw("index");
