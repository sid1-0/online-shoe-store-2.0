<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$allowed_categories = ['shoes', 'socks', 'shoe_care'];
$category = isset($_GET['category']) && in_array($_GET['category'], $allowed_categories, true)
    ? $_GET['category']
    : 'shoes';
$list_url = 'list_shoes.php?category=' . urlencode($category);

if (isset($_GET['shoe_id']) && is_numeric($_GET['shoe_id'])) {
    require_once __DIR__ . '/../classes/Product.php';
    $productModel = new Product();

    if ($productModel->delete($_GET['shoe_id'])) {
        header('Location: ' . $list_url . '&msg=deleted');
        exit();
    }

    $error_message = urlencode('Error: Could not delete product. It might be part of other records.');
    header('Location: ' . $list_url . '&msg=' . $error_message);
    exit();
}

header('Location: ' . $list_url);
exit();
?>
