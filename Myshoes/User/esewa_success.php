<?php
session_start();
require_once 'connection.php';
require_once 'esewa_helper.php';

function esewa_error_page($message) {
    include('header.php');
    ?>
    <div style="max-width:600px;margin:80px auto;background:#fff;padding:40px;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.1);text-align:center;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
        <div style="font-size:60px;color:#e74c3c;margin-bottom:20px;">⚠️</div>
        <h1 style="color:#2c3e50;">Payment Verification Failed</h1>
        <p style="color:#7f8c8d;line-height:1.6;"><?php echo htmlspecialchars($message); ?></p>
        <a href="mycart.php" style="display:inline-block;margin-top:20px;background:linear-gradient(45deg,#667eea,#764ba2);color:#fff;text-decoration:none;padding:14px 30px;border-radius:50px;font-weight:700;">Back to Cart</a>
    </div>
    <?php
    include('footer.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_GET['data'])) {
    esewa_error_page('No payment data was received from eSewa.');
}

$verified = esewa_verify_response($_GET['data']);
if ($verified === false) {
    esewa_error_page('The payment response could not be verified. If money was deducted, please contact support with your transaction details.');
}

if (empty($_SESSION['pending_esewa_order'])) {
    esewa_error_page('We could not find a pending order for this payment. If money was deducted, please contact support.');
}

$order = $_SESSION['pending_esewa_order'];

// Make sure this callback actually corresponds to the order we created.
if ($verified['transaction_uuid'] !== $order['transaction_uuid']) {
    esewa_error_page('Transaction mismatch. Please contact support with your payment details.');
}

if ($verified['status'] !== 'COMPLETE') {
    esewa_error_page('eSewa reported this payment as "' . $verified['status'] . '", not completed. Your order was not placed.');
}

// Belt-and-braces: re-confirm directly with eSewa's status API too.
$statusCheck = esewa_check_transaction_status($order['transaction_uuid'], number_format($order['total'], 2, '.', ''));
if (!$statusCheck || ($statusCheck['status'] ?? '') !== 'COMPLETE') {
    esewa_error_page('We could not independently confirm this payment with eSewa. If money was deducted, please contact support.');
}

// Everything checks out — write the order to the database.
$user_id           = $order['user_id'];
$name              = $order['name'];
$address           = $order['address'];
$phone             = $order['phone'];
$transaction_uuid  = $order['transaction_uuid'];

$sql = "INSERT INTO purchase_request (user_id, shoe_id, name, address, phone, payment_method, payment_status, transaction_uuid, amount) VALUES (?, ?, ?, ?, ?, 'eSewa', 'Paid', ?, ?)";
$stmt = $connection->prepare($sql);
if ($stmt === false) {
    esewa_error_page('Payment succeeded, but there was a server error saving your order. Please contact support with your transaction ID: ' . $transaction_uuid);
}

foreach ($order['line_items'] as $item) {
    $shoe_id = $item['shoe_id'];
    $amount  = $item['amount'];
    $stmt->bind_param("iissssd", $user_id, $shoe_id, $name, $address, $phone, $transaction_uuid, $amount);
    $stmt->execute();
}
$stmt->close();

// Clean up: clear the cart and the pending-order session data.
unset($_SESSION['cart']);
unset($_SESSION['pending_esewa_order']);
unset($_SESSION['coupon_code']);
unset($_SESSION['coupon_discount']);

include('header.php');
?>
<div style="max-width:600px;margin:80px auto;background:#fff;padding:40px;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.1);text-align:center;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <div style="font-size:60px;color:#27ae60;margin-bottom:20px;">✅</div>
    <h1 style="color:#2c3e50;">Payment Successful!</h1>
    <p style="color:#7f8c8d;line-height:1.6;">
        Your payment of Rs <?php echo number_format($order['total'], 2); ?> was received via eSewa.<br>
        Transaction ID: <strong><?php echo htmlspecialchars($transaction_uuid); ?></strong>
    </p>
    <a href="order.php" style="display:inline-block;margin-top:20px;background:linear-gradient(45deg,#27ae60,#2ecc71);color:#fff;text-decoration:none;padding:14px 30px;border-radius:50px;font-weight:700;">View My Orders</a>
</div>
<?php
include('footer.php');
