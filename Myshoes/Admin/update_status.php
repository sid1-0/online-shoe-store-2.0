<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION["admin_id"])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Include the database connection file
require_once 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve order ID and status from the form
    $order_id = $_POST["order_id"];
    $status = $_POST["status"];

    // Prepare and execute the SQL update statement
    $sql = "UPDATE purchase_request SET status = ? WHERE transaction_uuid = ? OR (transaction_uuid IS NULL AND id = ?)";
    $stmt = $connection->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $status, $order_id, $order_id);
        if ($stmt->execute()) {
            // Status updated successfully - show a nice success page
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Status Updated</title>
                <style>
                    body {
                        font-family: 'Poppins', sans-serif;
                        background-color: #f5f5f5;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        padding: 20px;
                    }
                    .success-container {
                        background: white;
                        border-radius: 10px;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                        padding: 40px;
                        text-align: center;
                        max-width: 500px;
                        width: 100%;
                    }
                    .success-icon {
                        font-size: 60px;
                        color: #4CAF50;
                        margin-bottom: 20px;
                    }
                    h1 {
                        color: #333;
                        margin-bottom: 15px;
                        font-weight: 600;
                    }
                    p {
                        color: #666;
                        margin-bottom: 25px;
                        line-height: 1.6;
                    }
                    .status-badge {
                        display: inline-block;
                        padding: 8px 16px;
                        border-radius: 20px;
                        font-weight: 500;
                        margin-bottom: 20px;
                    }
                    .status-pending {
                        background-color: #FFC107;
                        color: #333;
                    }
                    .status-accepted {
                        background-color: #4CAF50;
                        color: white;
                    }
                    .status-rejected {
                        background-color: #F44336;
                        color: white;
                    }
                    .status-cancelled {
                        background-color: #e2e3e5;
                        color: #383d41;
                    }
                    .back-button {
                        background: linear-gradient(90deg, #4CAF50, #8BC34A);
                        color: white;
                        border: none;
                        padding: 12px 25px;
                        border-radius: 5px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        text-decoration: none;
                        display: inline-block;
                    }
                    .back-button:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
                    }
                    .redirect-message {
                        font-size: 14px;
                        color: #999;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="success-container">
                    <div class="success-icon">✓</div>
                    <h1>Status Updated Successfully</h1>
                    <p>Order #<?php echo $order_id; ?> status has been updated to:</p>
                    <div class="status-badge status-<?php echo strtolower($status); ?>">
                        <?php echo $status; ?>
                    </div>
                    <a href="orders.php" class="back-button">Back to Orders</a>
                    <p class="redirect-message">You will be redirected automatically in 3 seconds...</p>
                </div>
                
                <script>
                    // Redirect after 3 seconds
                    setTimeout(function() {
                        window.location.href = 'orders.php';
                    }, 3000);
                </script>
            </body>
            </html>
            <?php
            exit();
        } else {
            // Error updating status - show error page
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Update Error</title>
                <style>
                    body {
                        font-family: 'Poppins', sans-serif;
                        background-color: #f5f5f5;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        padding: 20px;
                    }
                    .error-container {
                        background: white;
                        border-radius: 10px;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                        padding: 40px;
                        text-align: center;
                        max-width: 500px;
                        width: 100%;
                    }
                    .error-icon {
                        font-size: 60px;
                        color: #F44336;
                        margin-bottom: 20px;
                    }
                    h1 {
                        color: #333;
                        margin-bottom: 15px;
                        font-weight: 600;
                    }
                    p {
                        color: #666;
                        margin-bottom: 25px;
                        line-height: 1.6;
                    }
                    .error-details {
                        background-color: #f8f8f8;
                        padding: 15px;
                        border-radius: 5px;
                        text-align: left;
                        margin-bottom: 25px;
                        font-family: monospace;
                        color: #F44336;
                    }
                    .back-button {
                        background: linear-gradient(90deg, #F44336, #FF5722);
                        color: white;
                        border: none;
                        padding: 12px 25px;
                        border-radius: 5px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        text-decoration: none;
                        display: inline-block;
                    }
                    .back-button:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
                    }
                </style>
            </head>
            <body>
                <div class="error-container">
                    <div class="error-icon">❌</div>
                    <h1>Status Update Failed</h1>
                    <p>There was an error updating the status for Order #<?php echo $order_id; ?>.</p>
                    <div class="error-details">
                        <?php echo $stmt->error; ?>
                    </div>
                    <a href="orders.php" class="back-button">Back to Orders</a>
                </div>
            </body>
            </html>
            <?php
            exit();
        }
        $stmt->close();
    } else {
        // Handle query preparation error - show error page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Update Error</title>
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    background-color: #f5f5f5;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                    padding: 20px;
                }
                .error-container {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                    padding: 40px;
                    text-align: center;
                    max-width: 500px;
                    width: 100%;
                }
                .error-icon {
                    font-size: 60px;
                    color: #F44336;
                    margin-bottom: 20px;
                }
                h1 {
                    color: #333;
                    margin-bottom: 15px;
                    font-weight: 600;
                }
                p {
                    color: #666;
                    margin-bottom: 25px;
                    line-height: 1.6;
                }
                .error-details {
                    background-color: #f8f8f8;
                    padding: 15px;
                    border-radius: 5px;
                    text-align: left;
                    margin-bottom: 25px;
                    font-family: monospace;
                    color: #F44336;
                }
                .back-button {
                    background: linear-gradient(90deg, #F44336, #FF5722);
                    color: white;
                    border: none;
                    padding: 12px 25px;
                    border-radius: 5px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: inline-block;
                }
                .back-button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">❌</div>
                <h1>Query Preparation Failed</h1>
                <p>There was an error preparing the database query.</p>
                <div class="error-details">
                    <?php echo $connection->error; ?>
                </div>
                <a href="orders.php" class="back-button">Back to Orders</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
} else {
    // Redirect to the orders page if the form is not submitted
    header("Location: orders.php");
    exit();
}
?>
