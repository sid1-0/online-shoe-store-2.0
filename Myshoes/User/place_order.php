<?php
session_start();
require_once 'connection.php';
require_once 'esewa_helper.php';

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// If the cart is empty, redirect them back to the cart page.
if (empty($_SESSION['cart']) && $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: mycart.php");
    exit();
}

$name = $address = $phone = $proof_of_payment = '';
$nameErr = $addressErr = $phoneErr = $proofErr = '';
$payment_method = 'manual'; // default

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $payment_method = ($_POST['payment_method'] ?? 'cod') === 'esewa' ? 'esewa' : 'cod';

    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
    }

    // Validate address
    if (empty($_POST["address"])) {
        $addressErr = "Address is required";
    } else {
        $address = test_input($_POST["address"]);
    }

    // Validate phone number
    if (empty($_POST["phone"])) {
        $phoneErr = "Phone number is required";
    } else {
        $phone = test_input($_POST["phone"]);
        // Check if phone number is well-formed
        if (!preg_match("/^\d{10}$/", $phone)) {
            $phoneErr = "Invalid phone number format";
        }
    }



    if (empty($nameErr) && empty($addressErr) && empty($phoneErr)) {

        $user_id = $_SESSION['user_id'];

        if (isset($_POST["shoe_ids"]) && !empty($_POST["shoe_ids"])) {

            $shoe_ids = $_POST["shoe_ids"];

            if ($payment_method === 'esewa') {
                // Compute the total so we know how much to charge on eSewa,
                // and stash the shipping details in the session until eSewa
                // confirms payment (esewa_success.php finishes the order).
                $total = 0;
                $line_items = [];
                foreach ($shoe_ids as $shoe_id) {
                    $qty = $_SESSION['cart'][$shoe_id] ?? 1;
                    $stmt = $connection->prepare("SELECT price FROM shoes WHERE shoe_id = ?");
                    $stmt->bind_param("i", $shoe_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($row = $res->fetch_assoc()) {
                        $line_total = $row['price'] * $qty;
                        $total += $line_total;
                        $line_items[] = ['shoe_id' => $shoe_id, 'amount' => $line_total];
                    }
                    $stmt->close();
                }

                if (isset($_SESSION['coupon_discount'])) {
                    $total = $total - ($total * $_SESSION['coupon_discount'] / 100);
                }

                if ($total <= 0) {
                    header("Location: cart_error.php");
                    exit();
                }

                $transaction_uuid = esewa_generate_transaction_uuid();

                $_SESSION['pending_esewa_order'] = [
                    'user_id'          => $user_id,
                    'name'             => $name,
                    'address'          => $address,
                    'phone'            => $phone,
                    'line_items'       => $line_items,
                    'total'            => $total,
                    'transaction_uuid' => $transaction_uuid,
                ];

                header("Location: esewa_initiate.php");
                exit();
            } else if ($payment_method === 'cod') {
                $transaction_uuid = esewa_generate_transaction_uuid();
                $sql = "INSERT INTO purchase_request (user_id, shoe_id, name, address, phone, payment_method, payment_status, transaction_uuid, amount) VALUES (?, ?, ?, ?, ?, 'COD', 'Pending', ?, ?)";
                $stmt = $connection->prepare($sql);
                
                foreach ($shoe_ids as $shoe_id) {
                    $qty = $_SESSION['cart'][$shoe_id] ?? 1;
                    $stmt_price = $connection->prepare("SELECT price FROM shoes WHERE shoe_id = ?");
                    $stmt_price->bind_param("i", $shoe_id);
                    $stmt_price->execute();
                    $res = $stmt_price->get_result();
                    if ($row = $res->fetch_assoc()) {
                        $amount = $row['price'] * $qty;
                        if (isset($_SESSION['coupon_discount'])) {
                            $amount = $amount - ($amount * $_SESSION['coupon_discount'] / 100);
                        }
                        $stmt->bind_param("iissssd", $user_id, $shoe_id, $name, $address, $phone, $transaction_uuid, $amount);
                        $stmt->execute();
                    }
                    $stmt_price->close();
                }
                $stmt->close();
                
                unset($_SESSION['cart']);
                unset($_SESSION['coupon_code']);
                unset($_SESSION['coupon_discount']);
                header("Location: order.php");
                exit();
            }
        } else {
            header("Location: order_error.php");
            exit();
        }
    }
}

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_items = $_SESSION['cart'];
} else {
    header("Location: cart_error.php");
    exit();
}

include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - My Shoe Store</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
        }

        .checkout-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .checkout-header {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .checkout-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .checkout-header h1 {
            font-size: 3rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .checkout-header p {
            font-size: 1.2rem;
            color: #7f8c8d;
        }

        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            align-items: start;
        }

        .form-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeInLeft 0.6s ease forwards;
            opacity: 0;
            transform: translateX(-30px);
        }

        @keyframes fadeInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .payment-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: sticky;
            top: 20px;
            animation: fadeInRight 0.6s ease forwards;
            opacity: 0;
            transform: translateX(30px);
        }

        @keyframes fadeInRight {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 1rem;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        input[type="file"] {
            padding: 12px 15px;
            background-color: white;
            cursor: pointer;
        }

        input[type="file"]::-webkit-file-upload-button {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 10px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 5px;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 18px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(39, 174, 96, 0.4);
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .payment-info {
            margin-bottom: 30px;
        }

        .payment-info h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .payment-method-choice {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 8px;
        }

        .payment-method-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            color: #2c3e50;
        }

        .payment-method-option:has(input:checked) {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.06);
        }

        .payment-method-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
            cursor: pointer;
        }

        .esewa-badge {
            display: inline-block;
            background: #60bb46;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            margin-left: 8px;
            letter-spacing: 0.5px;
        }





        .security-notice {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border: 2px solid #27ae60;
            border-radius: 15px;
            padding: 20px;
            margin-top: 25px;
            text-align: left;
        }

        .security-notice h4 {
            color: #155724;
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .security-notice p {
            color: #155724;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.5;
        }

        .cart-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .cart-summary h3 {
            color: #2c3e50;
            font-size: 1.4rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .cart-item-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e1e8ed;
        }

        .cart-item-summary:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.1rem;
            color: #2c3e50;
        }

        @media (max-width: 992px) {
            .checkout-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .payment-section {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .checkout-container {
                padding: 0 15px;
                margin: 20px auto;
            }

            .checkout-header,
            .form-section,
            .payment-section {
                padding: 25px 20px;
            }

            .checkout-header h1 {
                font-size: 2.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 480px) {
            .checkout-header h1 {
                font-size: 2rem;
            }

            .submit-btn {
                padding: 15px;
                font-size: 1rem;
            }


        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout</h1>
            <p>Complete your order by filling out the form below</p>
        </div>

        <div class="checkout-content">
            <div class="form-section">
                <h2 class="section-title">Shipping Information</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                   
                    <?php foreach ($cart_items as $shoe_id => $quantity) : ?>
                        <input type="hidden" name="shoe_ids[]" value="<?php echo $shoe_id; ?>">
                    <?php endforeach; ?>

                    <input type="hidden" name="payment_method" id="payment_method_input" value="esewa">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($name); ?>" required>
                            <?php if($nameErr): ?>
                                <span class="error-message"><?php echo $nameErr; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="text" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($phone); ?>" required>
                            <?php if($phoneErr): ?>
                                <span class="error-message"><?php echo $phoneErr; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Shipping Address *</label>
                        <input type="text" id="address" name="address" placeholder="Enter your complete address" value="<?php echo htmlspecialchars($address); ?>" required>
                        <?php if($addressErr): ?>
                            <span class="error-message"><?php echo $addressErr; ?></span>
                        <?php endif; ?>
                    </div>


                    
                    <button type="submit" class="submit-btn" id="place_order_btn">Pay with eSewa</button>
                </form>
            </div>

            <div class="payment-section">
                <div class="payment-info">
                    <h2 class="section-title">Payment Information</h2>
                    
                    <div class="payment-method-choice" style="margin-bottom: 25px;">
                        <label class="payment-method-option">
                            <input type="radio" name="pm" id="pm_esewa" checked>
                            <span>Pay with eSewa <span class="esewa-badge">eSewa</span></span>
                        </label>
                        <label class="payment-method-option">
                            <input type="radio" name="pm" id="pm_cod">
                            <span>Cash on Delivery (COD)</span>
                        </label>
                    </div>

                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        <?php
                        $total = 0;
                        foreach ($cart_items as $shoe_id => $quantity):
                            $stmt = $connection->prepare("SELECT name, price FROM shoes WHERE shoe_id = ?");
                            $stmt->bind_param("i", $shoe_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows === 1):
                                $row = $result->fetch_assoc();
                                $item_total = $row['price'] * $quantity;
                                $total += $item_total;
                        ?>
                        <div class="cart-item-summary">
                            <span><?php echo htmlspecialchars($row['name']); ?> (×<?php echo $quantity; ?>)</span>
                            <span>Rs <?php echo number_format($item_total, 2); ?></span>
                        </div>
                        <?php
                            endif;
                        endforeach;
                        if (isset($_SESSION['coupon_discount'])):
                            $discount_amount = ($total * $_SESSION['coupon_discount']) / 100;
                            $final_total = $total - $discount_amount;
                        ?>
                        <div class="cart-item-summary" style="color: #27ae60;">
                            <span>Discount (<?php echo $_SESSION['coupon_discount']; ?>%)</span>
                            <span>- Rs <?php echo number_format($discount_amount, 2); ?></span>
                        </div>
                        <div class="cart-item-summary">
                            <span>Total</span>
                            <span>Rs <?php echo number_format($final_total, 2); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="cart-item-summary">
                            <span>Total</span>
                            <span>Rs <?php echo number_format($total, 2); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    </div>

                <div class="security-notice">
                    <h4>🔒 Secure Payment</h4>
                    <p>Your payment information is processed securely. We never store your banking details and all transactions are encrypted.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updatePaymentMethodUI() {
            const isEsewa = document.getElementById('pm_esewa').checked;
            document.getElementById('payment_method_input').value = isEsewa ? 'esewa' : 'cod';
            const submitBtn = document.getElementById('place_order_btn');

            if (isEsewa) {
                submitBtn.textContent = 'Pay with eSewa';
            } else {
                submitBtn.textContent = 'Place Order (COD)';
            }
        }

        document.getElementById('pm_esewa').addEventListener('change', updatePaymentMethodUI);
        document.getElementById('pm_cod').addEventListener('change', updatePaymentMethodUI);
        updatePaymentMethodUI();

        document.querySelector('form').addEventListener('submit', function(e) {
            const isEsewa = document.getElementById('pm_esewa').checked;
            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const address = document.getElementById('address').value.trim();

            if (!name || !phone || !address) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }

            if (!/^\d{10}$/.test(phone)) {
                e.preventDefault();
                alert('Please enter a valid 10-digit phone number.');
                return;
            }

            const submitBtn = document.querySelector('.submit-btn');
            submitBtn.textContent = isEsewa ? 'Redirecting to eSewa...' : 'Processing Order...';
        });
    </script>
</body>
</html>
