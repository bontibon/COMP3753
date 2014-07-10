<?php
// Defines
define('BASEDIR', dirname(__FILE__) . '/');

// Template engine
require BASEDIR . 'vendor/raintpl/rain.tpl.class.php';
raintpl::configure(array(
    'tpl_dir'       => BASEDIR . 'templates/',
    'path_replace'  => false,
    'php_enabled'   => true
));

// Database connection
try {
    $pdo = new PDO('mysql:dbname=bookstore;host=127.0.0.1', 'tim', 'password');
} catch (PDOException $ex) {
    echo $ex;
    die;
}

// Session
session_start();

// Functions
function clean_search_query($str) {
    return trim(preg_replace('/[^][\w-:. -]/', '', $str));
}

// Shared variables
$shared = new stdClass();

$current_cart = isset($_COOKIE['cart']) ? $_COOKIE['cart'] : '';
$shared->currentCart = @unserialize($current_cart);
if ($shared->currentCart === false) {
    $shared->currentCart = array();
}
$shared->cartCount = is_array($shared->currentCart) ? array_sum($shared->currentCart) : 0;

$shared->studentId = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : '';

$shared->employee = isset($_SESSION['employee']) ? $_SESSION['employee'] : '';
$shared->isEmployee = isset($_SESSION['isEmployee']) && $_SESSION['isEmployee'];
$shared->employeeName = isset($_SESSION['employeeName']) ? $_SESSION['employeeName'] : '';
$shared->employeePermissions = isset($_SESSION['employeePermissions']) ? $_SESSION['employeePermissions'] : array();
