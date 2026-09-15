<?php
session_start();
require_once 'connection.php';

// Auth check — only logged-in users can cancel orders
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized.";
    exit;
}

if(isset($_POST['order_id'])) {
    
    $order_id = $_POST['order_id'];
    $user_id = $_SESSION['user_id'];

    // Update the status to 'Cancelled' instead of deleting it so admins can see it
    $sql = "UPDATE purchase_request SET status = 'Cancelled' WHERE (transaction_uuid = ? OR (transaction_uuid IS NULL AND id = ?)) AND user_id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ssi", $order_id, $order_id, $user_id);

    // Execute the statement
    if($stmt->execute() && $stmt->affected_rows > 0) {
        $stmt->close();
        // Order successfully cancelled
        echo "Order cancelled successfully.";
        exit;
    } else {
        $stmt->close();
        // No matching order found or error occurred
        echo "Error cancelling order. Order not found or already processed.";
        exit;
    }

} else {
    
    echo "Order ID not provided.";
    exit;
}
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        color: #333;
        line-height: 1.6;
        padding: 20px;
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .cancel-order-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 40px;
        width: 100%;
        max-width: 500px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .cancel-order-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 30px;
        font-size: 28px;
        font-weight: 700;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    label {
        font-weight: 600;
        color: #34495e;
        font-size: 16px;
        display: block;
        text-align: left;
        margin-bottom: 8px;
    }

    input[type="text"] {
        width: 100%;
        padding: 15px;
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    input[type="text"]:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }

    button {
        background: linear-gradient(45deg, #3498db, #2980b9);
        color: white;
        border: none;
        padding: 15px 25px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        margin-top: 10px;
    }

    button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(41, 128, 185, 0.3);
    }

    button:active {
        transform: translateY(-1px);
    }

    .message {
        margin-top: 20px;
        padding: 15px;
        border-radius: 8px;
        font-weight: 600;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .back-link {
        display: inline-block;
        margin-top: 30px;
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s;
    }

    .back-link:hover {
        color: #2980b9;
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .cancel-order-container {
            padding: 30px 20px;
            margin: 0 15px;
        }
        
        h2 {
            font-size: 24px;
        }
        
        button {
            padding: 12px 20px;
        }
    }
</style>

<div class="cancel-order-container">
    <h2>Cancel Your Order</h2>
    
    <form method="POST" action="cancel_order.php">
        <div>
            <label for="order_id">Order ID</label>
            <input type="text" id="order_id" name="order_id" placeholder="Enter your order ID" required>
        </div>
        
        <button type="submit">Cancel Order</button>
    </form>
    
    <?php if(isset($_POST['order_id'])): ?>
        <?php if($stmt && $stmt->affected_rows > 0): ?>
            <div class="message success">
                Order cancelled successfully.
            </div>
        <?php else: ?>
            <div class="message error">
                <?php echo isset($stmt) ? "Error cancelling order." : "Order ID not provided."; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <a href="order.php" class="back-link">← Back to My Orders</a>
</div>
