<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once 'connection.php';

if (!isset($_GET['group_id'])) {
    die("Invalid request.");
}

$group_id = $_GET['group_id'];
$user_id = $_SESSION["user_id"];

// Fetch order details
$sql = "SELECT pr.*, s.name as shoe_name, s.price 
        FROM purchase_request pr 
        JOIN shoes s ON pr.shoe_id = s.shoe_id 
        WHERE pr.user_id = ? AND (pr.transaction_uuid = ? OR pr.id = ?)";

$stmt = $connection->prepare($sql);
$stmt->bind_param("isi", $user_id, $group_id, $group_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Receipt not found or you do not have permission to view it.");
}

$items = [];
$totalAmount = 0;
$orderInfo = null;

while ($row = $result->fetch_assoc()) {
    if (!$orderInfo) {
        $orderInfo = [
            'name' => $row['name'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'date' => date('F j, Y', strtotime($row['created_at'])),
            'payment_method' => $row['payment_method'],
            'payment_status' => $row['payment_status'],
            'transaction_id' => $row['transaction_uuid'] ? $row['transaction_uuid'] : 'MANUAL-' . $row['id']
        ];
    }
    
    // If the amount was saved directly (like in eSewa orders), use it, otherwise fallback to product price
    $itemPrice = !empty($row['amount']) ? $row['amount'] : $row['price'];
    
    $items[] = [
        'name' => $row['shoe_name'],
        'price' => $itemPrice
    ];
    $totalAmount += $itemPrice;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?php echo htmlspecialchars($orderInfo['transaction_id']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f6;
            color: #333;
            padding: 40px 20px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #eee;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }
        .receipt-header h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin: 0 0 10px;
        }
        .receipt-header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            color: #667eea;
        }
        .customer-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .customer-details div {
            flex: 1;
        }
        .customer-details h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .customer-details p {
            margin: 5px 0;
            color: #555;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .items-table th, .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .items-table th {
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .items-table .text-right {
            text-align: right;
        }
        .total-section {
            border-top: 2px solid #2c3e50;
            padding-top: 20px;
            display: flex;
            justify-content: flex-end;
        }
        .total-box {
            width: 300px;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1rem;
            color: #555;
        }
        .total-line.grand-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 5px;
        }
        .actions {
            margin-top: 50px;
            text-align: center;
        }
        .btn {
            padding: 12px 30px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #95a5a6;
        }
        .btn-secondary:hover {
            box-shadow: 0 5px 15px rgba(149, 165, 166, 0.4);
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8rem;
            color: rgba(0,0,0,0.03);
            white-space: nowrap;
            pointer-events: none;
            font-weight: 900;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                padding: 0;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container" style="position:relative;">
        <div class="watermark"><?php echo strtoupper(htmlspecialchars($orderInfo['payment_status'] ?? '')); ?></div>
        
        <div class="receipt-header">
            <div>
                <div class="brand-logo">My Shoe Store</div>
                <p>123 Shoe Avenue, Kathmandu, Nepal</p>
                <p>support@myfamilystore.com | +977-1-4111111</p>
            </div>
            <div style="text-align: right;">
                <h1>RECEIPT</h1>
                <p><strong>Date:</strong> <?php echo $orderInfo['date']; ?></p>
                <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($orderInfo['transaction_id']); ?></p>
            </div>
        </div>

        <div class="customer-details">
            <div>
                <h3>Billed To:</h3>
                <p><strong><?php echo htmlspecialchars($orderInfo['name']); ?></strong></p>
                <p><?php echo htmlspecialchars($orderInfo['address']); ?></p>
                <p><?php echo htmlspecialchars($orderInfo['phone']); ?></p>
            </div>
            <div style="text-align: right;">
                <h3>Payment Info:</h3>
                <p><strong>Method:</strong> <?php echo htmlspecialchars($orderInfo['payment_method'] ? $orderInfo['payment_method'] : 'Cash on Delivery'); ?></p>
                <p><strong>Status:</strong> <span style="color: <?php echo ($orderInfo['payment_status'] === 'Paid' || $orderInfo['payment_status'] === 'Completed') ? '#27ae60' : '#e67e22'; ?>; font-weight: 700;"><?php echo htmlspecialchars($orderInfo['payment_status'] ? $orderInfo['payment_status'] : 'Pending'); ?></span></p>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td class="text-right">Rs <?php echo number_format($item['price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <div class="total-line">
                    <span>Subtotal:</span>
                    <span>Rs <?php echo number_format($totalAmount, 2); ?></span>
                </div>
                <div class="total-line">
                    <span>Tax (0%):</span>
                    <span>Rs 0.00</span>
                </div>
                <div class="total-line grand-total">
                    <span>Total:</span>
                    <span>Rs <?php echo number_format($totalAmount, 2); ?></span>
                </div>
            </div>
        </div>

        <div class="actions">
            <button onclick="window.print()" class="btn">🖨️ Print / Save PDF</button>
            <a href="order.php" class="btn btn-secondary">Back to Orders</a>
        </div>
    </div>
</body>
</html>
