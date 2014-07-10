<?php
require 'shared.php';

$canDownload = in_array('download-dump', $shared->employeePermissions);

if (!$shared->isEmployee || !$canDownload) {
    header('Location: /');
    die;
}

header('Content-Disposition: attachment; filename="unibookdb-' . time() . '.csv"');
header('Content-type: text/csv');

$output = fopen('php://output', 'w');
$tables = array('Category', 'OrderContents', 'Product', 'StoreOrder');

foreach ($tables as $table) {
    fputcsv($output, array($table));
    $query = sprintf('SELECT * FROM %s', $table);
    $query = $pdo->prepare($query);
    $query->execute();

    $cols = array();
    for ($i = 0; $i < $query->columnCount(); $i++) {
        $meta = $query->getColumnMeta($i);
        $cols[] = $meta['name'];
    }
    fputcsv($output, $cols);
    while ($arr = $query->fetch(PDO::FETCH_NUM)) {
        fputcsv($output, $arr);
    }
}
