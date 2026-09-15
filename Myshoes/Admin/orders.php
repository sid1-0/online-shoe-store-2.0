<?php
session_start();
// Check if the admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header('location:index.php?err=1');
    exit(); // Add exit to prevent further execution
}

// Include the database connection file
require_once 'connection.php';

$sql = "SELECT MIN(pr.id) as id, COALESCE(pr.transaction_uuid, pr.id) as group_id, GROUP_CONCAT(shoes.name SEPARATOR ', ') AS shoe_name, pr.name, pr.address, pr.phone, pr.status, users.username AS customer_name, users.email AS customer_email, pr.payimg, pr.payment_method, pr.payment_status, pr.transaction_uuid
        FROM purchase_request pr
        JOIN users ON pr.user_id = users.user_id
        JOIN shoes ON pr.shoe_id = shoes.shoe_id
        GROUP BY COALESCE(pr.transaction_uuid, pr.id), pr.name, pr.address, pr.phone, pr.status, users.username, users.email, pr.payimg, pr.payment_method, pr.payment_status, pr.transaction_uuid
        ORDER BY id DESC";

$stmt = $connection->prepare($sql);

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $orders = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $orders = [];
    }

    $stmt->close();
} else {
    // Handle query preparation error
    echo "Error preparing order: " . $connection->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 28px;
            position: relative;
            padding-bottom: 15px;
        }

        h1:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ff6f61, #de6262);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .order-list {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-top: 30px;
        }

        .order-list h2 {
            padding: 20px;
            margin: 0;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
            font-size: 18px;
            font-weight: 600;
        }

        .order-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .order-list th,
        .order-list td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .order-list th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-list tr:hover {
            background-color: #f8f9fa;
        }

        .order-list td {
            font-size: 14px;
            color: #6c757d;
        }

        .order-list tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            min-width: 100px;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-cancelled {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .action-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-form select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            color: #495057;
            background-color: #fff;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }

        .action-form select:focus {
            border-color: #ff6f61;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 111, 97, 0.1);
        }

        .action-form button {
            padding: 8px 15px;
            background-color: #ff6f61;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .action-form button:hover {
            background-color: #e8635b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .view-payment {
            display: inline-block;
            padding: 8px 15px;
            background-color: #cce5ff;
            color: #0d6efd;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .view-payment:hover {
            background-color: #b8daff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .no-orders {
            padding: 40px;
            text-align: center;
            color: #6c757d;
            font-size: 16px;
        }

        .customer-info {
            display: flex;
            flex-direction: column;
        }

        .customer-name {
            font-weight: 500;
            color: #495057;
        }

        .customer-email {
            font-size: 12px;
            color: #6c757d;
            margin-top: 3px;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .order-list {
                overflow-x: auto;
            }
            
            .order-list table {
                min-width: 900px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
                margin: 20px auto;
            }
            
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'admin_menu.php'; ?>
    <div class="container">
        <h1>Manage Orders</h1>
        <div class="order-list">
            <h2>Customer Orders</h2>
            <?php if (empty($orders)) : ?>
                <div class="no-orders">No orders found. New orders will appear here.</div>
            <?php else : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Product</th>
                            <th>Shipping Details</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) : 
                            $statusClass = '';
                            switch($order['status']) {
                                case 'Pending':
                                    $statusClass = 'status-pending';
                                    break;
                                case 'Accepted':
                                    $statusClass = 'status-accepted';
                                    break;
                                case 'Rejected':
                                    $statusClass = 'status-rejected';
                                    break;
                                case 'Cancelled':
                                    $statusClass = 'status-cancelled';
                                    break;
                                default:
                                    $statusClass = 'status-pending';
                            }
                        ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <div class="customer-info">
                                        <span class="customer-name"><?php echo $order['customer_name']; ?></span>
                                        <span class="customer-email"><?php echo $order['customer_email']; ?></span>
                                    </div>
                                </td>
                                <td><?php echo $order['phone']; ?></td>
                                <td><?php echo $order['shoe_name']; ?></td>
                                <td>
                                    <div>
                                        <strong><?php echo $order['name']; ?></strong><br>
                                        <?php echo $order['address']; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $pm = $order['payment_method'] ?? 'Manual QR';
                                    $ps = $order['payment_status'] ?? 'Unverified';
                                    $pmColor = $pm === 'eSewa' ? '#60bb46' : '#6c757d';
                                    $psColor = $ps === 'Paid' ? '#155724' : ($ps === 'Failed' ? '#721c24' : '#856404');
                                    ?>
                                    <div style="font-weight:600;color:<?php echo $pmColor; ?>;"><?php echo htmlspecialchars($pm); ?></div>
                                    <div style="font-size:12px;color:<?php echo $psColor; ?>;"><?php echo htmlspecialchars($ps); ?></div>
                                    <?php if (!empty($order['transaction_uuid'])) : ?>
                                        <div style="font-size:11px;color:#adb5bd;">Txn: <?php echo htmlspecialchars($order['transaction_uuid']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $order['status']; ?></span></td>
                                <td>
                                    <form action="update_status.php" method="post" class="action-form">
                                        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['group_id']); ?>">
                                        <select name="status">
                                            <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Accepted" <?php echo $order['status'] == 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                                            <option value="Rejected" <?php echo $order['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
