<?php
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Cart.php';

User::requireLogin('login.php');

if (isset($_GET['shoe_id'])) {
    $shoe_id = (int) $_GET['shoe_id'];
    
    require_once __DIR__ . '/../classes/Product.php';
    $productModel = new Product();
    $product = $productModel->findById($shoe_id);

    if ($product && (int)$product['status'] === 1) {
        $cart = new Cart();
        $cart->add($shoe_id);
        header("Location: shoe_details.php?shoe_id=$shoe_id&added=true");
        exit();
    } else {
        // Handle out of stock
        header("Location: shoe_details.php?shoe_id=$shoe_id&error=out_of_stock");
        exit();
    }
}

echo 'Shoe ID not provided.';
exit();
?>
