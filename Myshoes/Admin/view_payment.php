<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php?err=1');
    exit();
}
require_once 'connection.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Retrieve image data from the database
    $sql = "SELECT payimg FROM purchase_request WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($payimg);
    $stmt->fetch();
    $stmt->close();

    // If image data exists, send it as the HTTP response
    if ($payimg) {
        // Set appropriate content type header
        header("Content-type: image/jpeg"); // Assuming the images are JPEG, adjust if necessary
        // Output the image data
        echo $payimg;
        exit;
    } else {
        // Display a nice error page if image not found
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Payment Image Not Found</title>
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
                    color: #ff6b6b;
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
                .back-button {
                    background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
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
                    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <h1>Payment Image Not Found</h1>
                <p>The payment image you're looking for could not be found. It may have been removed or the order ID might be incorrect.</p>
                <a href="orders.php" class="back-button">Back to Orders</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// If order ID is not provided, display a nice error page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Request</title>
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
            color: #ff6b6b;
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
        .back-button {
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
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
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">❌</div>
        <h1>Invalid Request</h1>
        <p>No order ID was provided or the request was invalid. Please go back to the orders page and try again.</p>
        <a href="orders.php" class="back-button">Back to Orders</a>
    </div>
</body>
</html>
