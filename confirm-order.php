<?php
require 'shared.php';

$canOrder = !empty($shared->studentId) || $shared->isEmployee;
$canProxyOrder = in_array('checkout', $shared->employeePermissions);

if (!$canOrder) {
    header('Location: /sso');
    die;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $c = $pdo->beginTransaction();

    $toProcess = array();
    $errors = array();

    foreach ($shared->currentCart as $id => $quantity) {
        if ($quantity <= 0) {
            continue;
        }
        $query = $pdo->prepare('SELECT id, price FROM Product WHERE id = ? AND quantity >= ? AND enabled IS TRUE LIMIT 1');
        $query->execute(array($id, $quantity));
        if ($query->rowCount() == 0) {
            $errors[$id] = true;
            continue;
        }
        $row = $query->fetchObject();

        $toProcess[$id] = array('quantity' => $quantity, 'price' => $row->price);
    }
    if (!empty($errors) || empty($toProcess)) {
        $pdo->rollback();
        header('Location: /cart/error/' . implode('.', array_keys($errors)));
        die;
    }

    $query = $pdo->prepare('INSERT INTO StoreOrder (status, student_id, employee_id) VALUES (\'Ordered\', ?, ?)');
    $studentId = $shared->studentId;
    if (!empty($_POST['student_id']) && $canProxyOrder) {
        $studentId = $_POST['student_id'];
    }
    $employee = $shared->employee;
    if (empty($employee)) {
        $employee = NULL;
    }
    $query->execute(array($studentId, $employee));

    if ($query->rowCount() == 0) {
        $pdo->rollback();
        header('Location: /cart/error/');
        die;
    }

    $orderId = $pdo->lastInsertId();

    foreach ($toProcess as $id => $value) {
        $quantity = $value['quantity'];
        $price = $value['price'];
        $query = $pdo->prepare('UPDATE Product SET quantity = quantity - ? WHERE id = ?');
        $query->execute(array($quantity, $id));

        $query = $pdo->prepare('INSERT INTO OrderContents (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)');
        $query->execute(array($orderId, $id, $price, $quantity));
    }
    $pdo->commit();

    setcookie('cart', '', time() - 3600);
    
    header('Location: /order-history/' . $orderId);
    die;
}

header('Location: /cart');
