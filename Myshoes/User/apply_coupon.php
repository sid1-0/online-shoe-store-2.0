<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coupon_code = trim($_POST['coupon_code'] ?? '');

    if (empty($coupon_code)) {
        $_SESSION['coupon_error'] = "Please enter a coupon code.";
        header("Location: mycart.php");
        exit();
    }

    $stmt = $connection->prepare("SELECT discount_percentage FROM coupons WHERE code = ? AND is_active = 1 LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $coupon_code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $_SESSION['coupon_code'] = $coupon_code;
            $_SESSION['coupon_discount'] = $row['discount_percentage'];
            $_SESSION['coupon_success'] = "Coupon '{$coupon_code}' applied successfully!";
        } else {
            $_SESSION['coupon_error'] = "Invalid or inactive coupon code.";
            unset($_SESSION['coupon_code']);
            unset($_SESSION['coupon_discount']);
        }
        $stmt->close();
    } else {
        $_SESSION['coupon_error'] = "An error occurred while verifying the coupon.";
    }
}

header("Location: mycart.php");
exit();
