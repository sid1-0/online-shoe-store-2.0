<?php
session_start();

// Check if the shoe ID is provided via POST
if(isset($_POST['shoe_id'])) {
    $shoe_id = $_POST['shoe_id'];
    
    // Check if the shoe exists in the cart
    if(isset($_SESSION['cart'][$shoe_id])) {
        // Remove the item from the cart
        unset($_SESSION['cart'][$shoe_id]);
        
        header("Location: mycart.php");
        exit();
    } else {
        echo "Error: Unable to remove item from cart.";
    }
} else {
    // If shoe ID is not provided via POST, display an error message
    echo "Error: Shoe ID not provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove from Cart - My Shoe Store</title>
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

        .message-container {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            margin: 20px;
            position: relative;
            overflow: hidden;
        }

        .message-container::before {
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

        .message-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .message-text {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(52, 152, 219, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(149, 165, 166, 0.4);
        }

        .loading {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 5px solid rgba(52, 152, 219, 0.3);
            border-radius: 50%;
            border-top-color: #3498db;
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .message-container {
                padding: 40px 25px;
            }

            .message-title {
                font-size: 2rem;
            }

            .message-text {
                font-size: 1rem;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="message-container">
        <div class="loading"></div>
        <h1 class="message-title">Processing</h1>
        <p class="message-text">
            We're processing your request. If you're seeing this page, there might be an issue with the removal process.
        </p>
        
        <div class="action-buttons">
            <a href="mycart.php" class="btn btn-primary">Go to Cart</a>
            <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </div>

    <script>
        // Auto redirect after 3 seconds if somehow the PHP redirect fails
        setTimeout(function() {
            window.location.href = 'mycart.php';
        }, 3000);
    </script>
</body>
</html>
