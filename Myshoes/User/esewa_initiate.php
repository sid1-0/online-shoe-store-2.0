<?php
session_start();
require_once 'esewa_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['pending_esewa_order'])) {
    header("Location: mycart.php");
    exit();
}

$order = $_SESSION['pending_esewa_order'];

$total_amount     = number_format($order['total'], 2, '.', '');
$transaction_uuid = $order['transaction_uuid'];
$product_code     = ESEWA_MERCHANT_CODE;

// eSewa expects these exact fields, signed in this exact order.
$signed_field_names = 'total_amount,transaction_uuid,product_code';

$fieldsToSign = [
    'total_amount'     => $total_amount,
    'transaction_uuid' => $transaction_uuid,
    'product_code'     => $product_code,
];

$signature = esewa_generate_signature($fieldsToSign, ESEWA_SECRET_KEY);

$formFields = [
    'amount'                 => $total_amount,
    'tax_amount'              => '0',
    'total_amount'            => $total_amount,
    'transaction_uuid'        => $transaction_uuid,
    'product_code'            => $product_code,
    'product_service_charge'  => '0',
    'product_delivery_charge' => '0',
    'success_url'             => ESEWA_SUCCESS_URL,
    'failure_url'             => ESEWA_FAILURE_URL,
    'signed_field_names'      => $signed_field_names,
    'signature'               => $signature,
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to eSewa...</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .box {
            background: white;
            padding: 50px 60px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        .spinner {
            width: 48px;
            height: 48px;
            border: 5px solid #e1e8ed;
            border-top-color: #60bb46;
            border-radius: 50%;
            margin: 0 auto 20px;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        h2 { color: #2c3e50; margin: 0 0 10px; }
        p { color: #7f8c8d; margin: 0; }
    </style>
</head>
<body>
    <div class="box">
        <div class="spinner"></div>
        <h2>Redirecting to eSewa</h2>
        <p>Please wait, do not close this window...</p>
    </div>

    <form id="esewaForm" action="<?php echo htmlspecialchars(ESEWA_PAYMENT_URL); ?>" method="POST">
        <?php foreach ($formFields as $key => $value) : ?>
            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
        <?php endforeach; ?>
    </form>

    <script>
        document.getElementById('esewaForm').submit();
    </script>
</body>
</html>
