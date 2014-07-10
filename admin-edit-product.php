<?php
require 'shared.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if (in_array('edit-product', $shared->employeePermissions) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo->beginTransaction();

    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $price = isset($_POST['price']) ? (double) trim($_POST['price']) : 0;
    $quantity = isset($_POST['quantity']) ? (int) trim($_POST['quantity']) : 0;
    $categories = isset($_POST['categories']) ? trim($_POST['categories']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $isbn = isset($_POST['isbn']) ? trim($_POST['isbn']) : '';
    $enabled = isset($_POST['enabled']);

    $quantity = max(0, $quantity);
    $price = max(0, $price);

    $query = $pdo->prepare('SELECT name FROM Category WHERE product_id = ?');
    $query->execute(array($id));
    $currentCategories = $query->fetchAll(PDO::FETCH_COLUMN, 0);

    $categories = array_unique(array_map('trim', explode(",", $categories)));
    foreach ($categories as $category) {
        if ($category === "") {
            continue;
        }
        $i = array_search($category, $currentCategories, true);
        if ($i !== false) {
            unset($currentCategories[$i]);
        } else {
            $query = $pdo->prepare('INSERT INTO Category (name, product_id) VALUES(?, ?)');
            $query->execute(array($category, $id));
        }
    }
    foreach ($currentCategories as $category) {
        $query = $pdo->prepare('DELETE FROM Category WHERE name = ? AND product_id = ?');
        $query->execute(array($category, $id));
    }

    $query = $pdo->prepare('UPDATE Product SET name = ?, description = ?, price = ?,
        quantity = ?, author = ?, isbn = ?, enabled = ? WHERE id = ?');
    $query->execute(array($name, $description, $price, $quantity, $author, $isbn, $enabled, $id));

    $pdo->commit();
}

header('Location: /product/' . $id);
