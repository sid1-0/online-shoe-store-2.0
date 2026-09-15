<?php
require_once 'connection.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    $sql = "SELECT payimg FROM purchase_request WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($payimg);
    $stmt->fetch();
    $stmt->close();

    if ($payimg) {
 
        header("Content-type: image/jpeg"); 
        echo $payimg;
        exit;
    }
}

header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Not Found - My Shoe Store</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            margin: 20px;
            position: relative;
            overflow: hidden;
        }

        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #e74c3c, #c0392b);
        }

        .error-icon {
            font-size: 120px;
            color: #e74c3c;
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

        .error-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .error-message {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .back-btn {
            display: inline-block;
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(52, 152, 219, 0.4);
        }

        @media (max-width: 480px) {
            .error-container {
                padding: 40px 25px;
            }

            .error-title {
                font-size: 2rem;
            }

            .error-message {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🖼️</div>
        <h1 class="error-title">Image Not Found</h1>
        <p class="error-message">
            Sorry, the payment proof image you're looking for could not be found or the order ID is invalid.
        </p>
        <a href="javascript:history.back()" class="back-btn">Go Back</a>
    </div>
</body>
</html>
