<?php include('header.php') ?>
<?php

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Include the database connection file
require_once 'connection.php';

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM users WHERE user_id = ?";

$stmt = $connection->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($connection->error));
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    exit("User details not found.");
}

$stmt->close();

$sql = "SELECT MIN(pr.id) as id, COALESCE(pr.transaction_uuid, pr.id) as group_id, GROUP_CONCAT(s.name SEPARATOR ', ') AS shoe_name, pr.name, pr.address, pr.phone, pr.status, pr.payimg, MIN(pr.payment_method) as payment_method, pr.transaction_uuid 
        FROM purchase_request pr
        JOIN shoes s ON pr.shoe_id = s.shoe_id
        WHERE pr.user_id = ?
        GROUP BY COALESCE(pr.transaction_uuid, pr.id), pr.name, pr.address, pr.phone, pr.status, pr.payimg, pr.transaction_uuid
        ORDER BY id DESC";
$stmt = $connection->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($connection->error));
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - My Shoe Store</title>
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

        .orders-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
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

        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .page-header p {
            font-size: 1.2rem;
            color: #7f8c8d;
        }

        .user-details-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-details-card h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .user-details-card h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .user-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .user-info-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .user-info-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .user-info-item strong {
            color: #2c3e50;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }

        .user-info-item span {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .orders-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.8s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .orders-section h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .orders-section h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .no-orders {
            text-align: center;
            padding: 60px 20px;
        }

        .no-orders-icon {
            font-size: 120px;
            color: #e1e8ed;
            margin-bottom: 30px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .no-orders h3 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .no-orders p {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        .shop-now-btn {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .shop-now-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .orders-table-wrapper {
            overflow-x: auto;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }

        .orders-table thead {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .orders-table th,
        .orders-table td {
            padding: 18px 15px;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }

        .orders-table th {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .orders-table tbody tr {
            transition: all 0.3s ease;
            opacity: 0;
            animation: slideInUp 0.5s ease forwards;
        }

        .orders-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
        .orders-table tbody tr:nth-child(2) { animation-delay: 0.2s; }
        .orders-table tbody tr:nth-child(3) { animation-delay: 0.3s; }
        .orders-table tbody tr:nth-child(4) { animation-delay: 0.4s; }
        .orders-table tbody tr:nth-child(5) { animation-delay: 0.5s; }

        @keyframes slideInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
            from {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        .orders-table tbody tr:hover {
            background: #f8f9fa;
            transform: scale(1.02);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .order-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .order-status.pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .order-status.processing {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .order-status.accepted {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .order-status.cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .view-image-link {
            display: inline-block;
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .view-image-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(52, 152, 219, 0.4);
        }

        .cancel-button {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cancel-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(231, 76, 60, 0.4);
        }

        .cancel-button:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .orders-container {
                padding: 0 15px;
                margin: 20px auto;
            }

            .page-header,
            .user-details-card,
            .orders-section {
                padding: 25px 20px;
            }

            .page-header h1 {
                font-size: 2.5rem;
            }

            .user-info {
                grid-template-columns: 1fr;
            }

            .orders-table-wrapper {
                font-size: 0.9rem;
            }

            .orders-table th,
            .orders-table td {
                padding: 12px 8px;
            }
        }

        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .orders-table {
                font-size: 0.8rem;
            }

            .orders-table th,
            .orders-table td {
                padding: 10px 6px;
            }

            .order-status,
            .view-image-link,
            .cancel-button {
                padding: 6px 10px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <div class="page-header">
            <h1>My Orders</h1>
            <p>Track and manage your order history</p>
        </div>

        <div class="user-details-card">
            <h2>Account Information</h2>
            <div class="user-info">
                <div class="user-info-item">
                    <strong>Username:</strong>
                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="user-info-item">
                    <strong>Email:</strong>
                    <span><?php echo htmlspecialchars($user['email'] ?? 'Not provided'); ?></span>
                </div>
            </div>
        </div>

        <div class="orders-section">
            <h2>Order History</h2>
            <?php if (empty($orders)) : ?>
                <div class="no-orders">
                    <div class="no-orders-icon">📦</div>
                    <h3>No Orders Found</h3>
                    <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
                    <a href="index.php" class="shop-now-btn">Start Shopping</a>
                </div>
            <?php else : ?>
                <div class="orders-table-wrapper">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Shoe Name</th>
                                <th>Customer Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order) : ?>
                                <tr id="order-row-<?php echo htmlspecialchars($order['group_id']); ?>">
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['shoe_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['address']); ?></td>
                                    <td><?php echo htmlspecialchars($order['phone']); ?></td>
                                    <td>
                                        <span class="order-status <?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (strtolower($order['status']) == 'pending') : ?>
                                            <button class="cancel-button" onclick="cancelOrder('<?php echo htmlspecialchars($order['group_id']); ?>')">Cancel</button>
                                        <?php elseif (strtolower($order['status']) == 'accepted') : ?>
                                            <span style="color: #27ae60; font-weight: 600; display:block; margin-bottom: 5px;">Completed</span>
                                        <?php elseif (strtolower($order['status']) == 'cancelled') : ?>
                                            <span style="color: #7f8c8d; font-weight: 600; display:block; margin-bottom: 5px;">Cancelled</span>
                                        <?php else : ?>
                                            <span style="color: #e74c3c; font-weight: 600; display:block; margin-bottom: 5px;"><?php echo htmlspecialchars($order['status']); ?></span>
                                        <?php endif; ?>
                                        <a href="receipt.php?group_id=<?php echo urlencode($order['group_id']); ?>" class="view-image-link" style="margin-top:5px; background:linear-gradient(45deg, #27ae60, #2ecc71); display:inline-block; text-align:center;">Receipt</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function cancelOrder(orderId) {
            const row = document.getElementById("order-row-" + orderId);
            const statusCell = row.getElementsByTagName("td")[5];
            const status = statusCell.textContent.trim().toLowerCase();
            
            if (status !== "accepted" && confirm("Are you sure you want to cancel this order?")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "cancel_order.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        if (xhr.responseText.trim() == "Order cancelled successfully.") {
                            // Add fade out animation
                            row.style.transition = "all 0.5s ease";
                            row.style.opacity = "0";
                            row.style.transform = "translateX(-100%)";
                            
                            setTimeout(() => {
                                row.remove();
                                
                                // Check if table is empty
                                const tbody = document.querySelector('.orders-table tbody');
                                if (tbody.children.length === 0) {
                                    location.reload(); // Reload to show "no orders" message
                                }
                            }, 500);
                        } else {
                            alert("Error: " + xhr.responseText);
                        }
                    }
                };
                
                xhr.send("order_id=" + orderId);
            }
        }

        // Add loading animation for cancel buttons
        document.querySelectorAll('.cancel-button').forEach(button => {
            button.addEventListener('click', function() {
                this.style.opacity = '0.7';
                this.textContent = 'Cancelling...';
                this.disabled = true;
            });
        });
    </script>
</body>
</html>
<?php include('footer.php')?>
