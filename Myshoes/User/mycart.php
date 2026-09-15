<?php 
include('header.php');


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart_empty = empty($_SESSION['cart']);

if ($cart_empty) {
    $recommendations = [];
} else {
    require_once 'connection.php';

    $all_products = [];
    $sql_all = "SELECT shoe_id, name, price, image_col FROM shoes";
    $result_all = $connection->query($sql_all);

    if ($result_all && $result_all->num_rows > 0) {
        while ($row = $result_all->fetch_assoc()) {
            $all_products[$row['shoe_id']] = $row;
        }
    }

    // Jaccard helper functions
    function price_category_set($price) {
        $set = [];
        if ($price < 1000) $set[] = 'budget';
        if ($price >= 1000 && $price < 5000) $set[] = 'mid_range';
        if ($price >= 5000) $set[] = 'premium';
        
        if ($price < 2500) $set[] = 'affordable';
        if ($price >= 2500) $set[] = 'expensive';
        
        return $set;
    }

    function jaccard_similarity(array $set1, array $set2): float {
        $intersection = count(array_intersect($set1, $set2));
        $union = count(array_unique(array_merge($set1, $set2)));
        return ($union === 0) ? 0 : ($intersection / $union);
    }

    function price_range_similarity($price1, $price2) {
        $diff = abs($price1 - $price2);
        $max_price = max(1.0, max((float)$price1, (float)$price2));
        return max(0, 1 - ($diff / $max_price));
    }

    function product_similarity(array $prod1, array $prod2): float {
        // 1. Jaccard Similarity on Price Category Sets
        $jaccard_price = jaccard_similarity(price_category_set($prod1['price']), price_category_set($prod2['price']));
        
        // 2. Absolute Price Range Similarity
        $range_price = price_range_similarity($prod1['price'], $prod2['price']);
        
        // We emphasize price in this custom Jaccard model
        $total_similarity = ($jaccard_price * 0.60) + ($range_price * 0.40);
        
        return max(0.10, $total_similarity);
    }

    $recommendations = [];

    // Get recommendations for each item in the cart and merge them
    foreach ($_SESSION['cart'] as $cart_shoe_id => $qty) {
        if (!isset($all_products[$cart_shoe_id])) continue;
        $cart_prod = $all_products[$cart_shoe_id];

        foreach ($all_products as $other_id => $other_prod) {
            if (isset($_SESSION['cart'][$other_id])) continue; // Skip if already in cart
            if ($other_id == $cart_shoe_id) continue;

            $sim = product_similarity($cart_prod, $other_prod);
            if ($sim > 0) {
                if (!isset($recommendations[$other_id]) || $sim > $recommendations[$other_id]['score']) {
                    $recommendations[$other_id] = [
                        'shoe' => $other_prod,
                        'score' => $sim
                    ];
                }
            }
        }
    }
    
    // Sort merged recommendations by score
    uasort($recommendations, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });
    
    // Take top 4
    $recommendations = array_slice($recommendations, 0, 4, true);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Cart</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .cart-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .cart-header {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cart-header::before {
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

        .cart-header h1 {
            font-size: 3rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cart-header p {
            font-size: 1.2rem;
            color: #7f8c8d;
        }

        .cart-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-cart-icon {
            font-size: 120px;
            color: #bdc3c7;
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

        .empty-cart h2 {
            font-size: 2rem;
            color: #34495e;
            margin-bottom: 15px;
        }

        .empty-cart p {
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
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .shop-now-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .cart-items {
            margin-bottom: 40px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 25px;
            border-bottom: 1px solid #ecf0f1;
            transition: all 0.3s ease;
            animation: slideInLeft 0.5s ease forwards;
            opacity: 0;
            transform: translateX(-30px);
        }

        .cart-item:nth-child(1) { animation-delay: 0.1s; }
        .cart-item:nth-child(2) { animation-delay: 0.2s; }
        .cart-item:nth-child(3) { animation-delay: 0.3s; }
        .cart-item:nth-child(4) { animation-delay: 0.4s; }
        .cart-item:nth-child(5) { animation-delay: 0.5s; }

        @keyframes slideInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .cart-item:hover {
            background-color: #f8f9fa;
            transform: translateX(10px);
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .shoe-image-wrapper {
            width: 100px;
            height: 100px;
            border-radius: 15px;
            overflow: hidden;
            margin-right: 25px;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .shoe-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
            padding: 10px;
        }

        .shoe-image:hover {
            transform: scale(1.1);
        }

        .shoe-details {
            flex-grow: 1;
            margin-right: 20px;
        }

        .shoe-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .shoe-price {
            font-size: 1.4rem;
            font-weight: 800;
            color: #667eea;
        }

        .remove-btn {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .remove-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(231, 76, 60, 0.4);
        }

        .cart-summary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .cart-total {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .checkout-btn {
            display: inline-block;
            background: white;
            color: #667eea;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
        }

        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 255, 255, 0.3);
        }

        .checkout-btn:disabled {
            background: #bdc3c7;
            color: #7f8c8d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .recommendations {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .recommendations h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .recommendation-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #ecf0f1;
            transition: all 0.3s ease;
        }

        .recommendation-item:hover {
            background-color: #f8f9fa;
            transform: translateX(10px);
        }

        .recommendation-item:last-child {
            border-bottom: none;
        }

        .similarity-badge {
            background: linear-gradient(45deg, #f093fb, #f5576c);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-left: 15px;
        }

        .add-to-cart-btn {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(46, 204, 113, 0.4);
        }

        @media (max-width: 768px) {
            .cart-container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .cart-header,
            .cart-content,
            .recommendations {
                padding: 25px 20px;
            }

            .cart-header h1 {
                font-size: 2.5rem;
            }

            .cart-item,
            .recommendation-item {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .shoe-image-wrapper {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .shoe-details {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .remove-btn,
            .add-to-cart-btn {
                width: 100%;
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>My Cart</h1>
            <p>Review your selected items and proceed to checkout</p>
        </div>

        <div class="cart-content">
            <?php if ($cart_empty): ?>
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any shoes to your cart yet. Start shopping to fill it up!</p>
                    <a href="index.php" class="shop-now-btn">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php
                    $total_price = 0;
                    foreach ($_SESSION['cart'] as $shoe_id => $qty):
                        $stmt = $connection->prepare("SELECT name, price, image_col FROM shoes WHERE shoe_id = ?");
                        $stmt->bind_param("i", $shoe_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows === 1):
                            $row = $result->fetch_assoc();
                            $shoe_name = $row['name'];
                            $shoe_price = $row['price'];
                            $image_url = "../shoeImage/" . $row['image_col'];
                            $total_price += $shoe_price;
                    ?>
                    <div class="cart-item">
                        <div class="shoe-image-wrapper">
                            <img class="shoe-image" src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($shoe_name) ?> Image" />
                        </div>
                        <div class="shoe-details">
                            <div class="shoe-name"><?= htmlspecialchars($shoe_name) ?></div>
                            <div class="shoe-price">Rs <?= number_format($shoe_price, 2) ?></div>
                        </div>
                        <form action="remove_from_cart.php" method="post">
                            <input type="hidden" name="shoe_id" value="<?= $shoe_id ?>" />
                            <button type="submit" class="remove-btn">Remove</button>
                        </form>
                    </div>
                    <?php
                        else:
                            echo "<p>Error fetching shoe details.</p>";
                        endif;
                    endforeach;
                    ?>
                </div>

                <div class="cart-summary">
                    <?php 
                    $discount_amount = 0;
                    if (isset($_SESSION['coupon_code'])) {
                        $discount_pct = $_SESSION['coupon_discount'];
                        $discount_amount = ($total_price * $discount_pct) / 100;
                        $final_price = $total_price - $discount_amount;
                        echo "<div class='cart-total' style='font-size:1.5rem; text-decoration:line-through; color:#bdc3c7;'>Total: Rs " . number_format($total_price, 2) . "</div>";
                        echo "<div class='cart-total' style='color:#a8e6cf;'>Discount ({$discount_pct}%): - Rs " . number_format($discount_amount, 2) . "</div>";
                        echo "<div class='cart-total'>Final Total: Rs " . number_format($final_price, 2) . "</div>";
                    } else {
                        echo "<div class='cart-total'>Total: Rs " . number_format($total_price, 2) . "</div>";
                    }
                    ?>
                    
                    <form action="apply_coupon.php" method="post" style="margin-bottom: 20px;">
                        <input type="text" name="coupon_code" placeholder="Enter Coupon Code" style="padding: 10px; border-radius: 50px; border: none; width: 60%; max-width: 250px; outline: none; padding-left: 20px;" required>
                        <button type="submit" style="padding: 10px 20px; border-radius: 50px; border: none; background: #fff; color: #667eea; font-weight: bold; cursor: pointer; margin-left: 10px;">Apply</button>
                    </form>
                    <?php if (isset($_SESSION['coupon_error'])): ?>
                        <div style="color: #ffb8b8; margin-bottom: 15px; font-weight: bold;"><?= htmlspecialchars($_SESSION['coupon_error']) ?></div>
                        <?php unset($_SESSION['coupon_error']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['coupon_success'])): ?>
                        <div style="color: #a8e6cf; margin-bottom: 15px; font-weight: bold;"><?= htmlspecialchars($_SESSION['coupon_success']) ?></div>
                        <?php unset($_SESSION['coupon_success']); ?>
                    <?php endif; ?>

                    <a href="place_order.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($recommendations)): ?>
            <div class="recommendations">
                <h2>Recommended for You</h2>
                <div class="recommendation-items">
                    <?php
                    $count = 0;
                    foreach ($recommendations as $rec):
                        if ($count >= 7) break;
                        $shoe = $rec['shoe'];
                        $sim_score = number_format($rec['score'] * 100, 2);
                        $image_url = "../shoeImage/" . ($shoe['image_col'] ?? 'default.jpg');
                    ?>
                    <div class="recommendation-item">
                        <div class="shoe-image-wrapper">
                            <img class="shoe-image" src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($shoe['name']) ?> Image" />
                        </div>
                        <div class="shoe-details">
                            <div class="shoe-name"><?= htmlspecialchars($shoe['name']) ?></div>
                            <div class="shoe-price">Rs <?= number_format($shoe['price'], 2) ?></div>
                        </div>
                        <div class="similarity-badge"><?= $sim_score ?>% Match</div>
                        <form action="addtocart.php" method="get">
                            <input type="hidden" name="shoe_id" value="<?= $shoe['shoe_id'] ?>" />
                            <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                        </form>
                    </div>
                    <?php
                        $count++;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php include('footer.php'); ?>