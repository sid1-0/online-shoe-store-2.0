<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in before any HTML output
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include('header.php');

// Check if shoe ID is provided in the URL
if (isset($_GET["shoe_id"])) {
    $shoe_id = $_GET["shoe_id"];
    require_once __DIR__ . '/../classes/Product.php';
    require_once 'connection.php'; // still used by association mining baskets

    $productModel = new Product();
    $row = $productModel->findById($shoe_id);

    if ($row) {
        $shoe_name = $row["name"];
        $brand = $row["brand"];
        $price = $row["price"];
        $original_price = isset($row["original_price"]) ? $row["original_price"] : null;
        $description = $row["description"];
        $status = ($row["status"] == 1 ? "Available" : "Out of Stock");
        $image_path = "../shoeImage/" . $row["image_col"];
        $current_category = strtolower(trim($row["category"] ?? "shoes"));
        if ($current_category === "accessories") {
            $current_category = "socks";
        }
    } else {
        echo "Shoe details not found.";
        exit();
    }
} else {
    echo "Shoe ID not provided.";
    exit();
}

// ================= Advanced Association-Rule Mining Algorithm (Hybrid Market Basket & Attribute Suite) =================
// Cross-category recommendation policy (A => B):
//   - Viewing shoes            => suggest socks + sneaker cleaner
//   - Viewing socks            => suggest shoes
//   - Viewing sneaker cleaner  => suggest shoes
// Metrics: Support, Confidence, Lift, Leverage, Conviction + brand/price attribute fallback for cold-start.

$fbt_recommendations = [];

// Directional target categories for cross-sell
if ($current_category === 'socks') {
    $target_categories = ['socks'];
    $fbt_title = 'You Might Also Like — Socks';
} elseif ($current_category === 'shoe_care') {
    $target_categories = ['shoe_care'];
    $fbt_title = 'You Might Also Like — Shoe Care';
} else {
    // Default / shoes: recommend companion accessories
    $target_categories = ['socks', 'shoe_care'];
    $fbt_title = 'Frequently Bought Together — Socks & Shoe Care';
    $current_category = 'shoes';
}

// Fetch all available products for candidate matching & fallback evaluation
$all_shoes_map = $productModel->getAllMappedById();

$basket_result = $connection->query("SELECT user_id, shoe_id FROM purchase_request WHERE user_id IS NOT NULL AND user_id != ''");

$baskets = []; // user_id => [shoe_id => true]
if ($basket_result && $basket_result->num_rows > 0) {
    while ($b = $basket_result->fetch_assoc()) {
        $baskets[$b['user_id']][$b['shoe_id']] = true;
    }
}

$total_baskets = count($baskets);
$baskets_with_current = 0;
$co_occurrence = [];     // other_shoe_id => count of user baskets containing both
$shoe_basket_count = []; // other_shoe_id => count of user baskets containing that shoe

if ($total_baskets > 0) {
    foreach ($baskets as $items) {
        $has_current = isset($items[$shoe_id]);
        if ($has_current) $baskets_with_current++;

        foreach (array_keys($items) as $other_id) {
            if ($other_id == $shoe_id) continue;
            $shoe_basket_count[$other_id] = ($shoe_basket_count[$other_id] ?? 0) + 1;
            if ($has_current) {
                $co_occurrence[$other_id] = ($co_occurrence[$other_id] ?? 0) + 1;
            }
        }
    }
}

$scored_rules = [];
$support_A = ($total_baskets > 0 && $baskets_with_current > 0) ? ($baskets_with_current / $total_baskets) : 0.05;
$current_price = (float)($all_shoes_map[$shoe_id]['price'] ?? 1.0);

/**
 * Returns true when candidate category is an allowed cross-sell target for the current product.
 */
$is_valid_cross_sell = function ($candidate_category) use ($target_categories) {
    $cat = strtolower(trim($candidate_category ?? 'shoes'));
    if ($cat === 'accessories') {
        $cat = 'socks';
    }
    return in_array($cat, $target_categories, true);
};

// Direct transaction co-occurrence mining (only valid cross-category candidates)
if (!empty($co_occurrence)) {
    foreach ($co_occurrence as $other_id => $count) {
        $other_item = $all_shoes_map[$other_id] ?? [];
        if (!$is_valid_cross_sell($other_item['category'] ?? '')) {
            continue;
        }

        $other_price = (float)($other_item['price'] ?? 1.0);
        $max_p = max(1.0, max($current_price, $other_price));
        $price_sim = max(0.15, 1.0 - (abs($current_price - $other_price) / $max_p));

        $support_AB = $count / max(1, $total_baskets);
        $support_B = ($shoe_basket_count[$other_id] ?? 1) / max(1, $total_baskets);
        $confidence = $count / max(1, $baskets_with_current); // P(B|A)

        $lift = ($support_A * $support_B) > 0 ? ($support_AB / ($support_A * $support_B)) : 1.0;
        $leverage = $support_AB - ($support_A * $support_B);

        $denom = 1.0 - $confidence;
        $conviction = ($denom > 0.0001) ? (1.0 - $support_B) / $denom : 5.0;
        $conviction = min(10.0, max(0.0, $conviction));

        $same_brand = isset($other_item['brand']) && strcasecmp($brand, $other_item['brand']) === 0;
        $brand_bonus = $same_brand ? 1.25 : 1.0;
        $cross_bonus = 1.6;

        $composite_score = (($lift * 0.35) + ($confidence * 0.25) + ($price_sim * 0.25) + (max(0, $leverage) * 0.15)) * $cross_bonus * $brand_bonus;

        $scored_rules[$other_id] = [
            'confidence' => $confidence,
            'lift' => $lift,
            'leverage' => $leverage,
            'conviction' => $conviction,
            'price_sim' => $price_sim,
            'category' => strtolower($other_item['category'] ?? 'shoes'),
            'score' => $composite_score + 100.0 // Prioritize direct co-purchases
        ];
    }
}

// Attribute-Association Fallback for sparse data / new products
if (count($scored_rules) < 4 && isset($all_shoes_map[$shoe_id])) {
    $current_shoe_data = $all_shoes_map[$shoe_id];

    foreach ($all_shoes_map as $other_id => $other_shoe) {
        if ($other_id == $shoe_id || isset($scored_rules[$other_id])) continue;
        if (!$is_valid_cross_sell($other_shoe['category'] ?? '')) continue;

        $other_price = (float)$other_shoe['price'];
        $max_p = max(1.0, max($current_price, $other_price));
        $price_sim = max(0.15, 1.0 - (abs($current_price - $other_price) / $max_p));

        $same_brand = (strcasecmp($current_shoe_data['brand'], $other_shoe['brand']) === 0);
        $brand_conf = $same_brand ? 0.90 : 0.55;

        $attr_confidence = ($brand_conf * 0.50) + ($price_sim * 0.50);
        $attr_lift = 1.0 + ($attr_confidence * 1.5);
        $attr_leverage = $attr_confidence * 0.05;
        $attr_conviction = 1.0 + $attr_confidence;

        $brand_bonus = $same_brand ? 1.25 : 1.0;
        $composite_score = (($attr_lift * 0.35) + ($attr_confidence * 0.25) + ($price_sim * 0.25) + ($attr_leverage * 0.15)) * 1.6 * $brand_bonus;

        $scored_rules[$other_id] = [
            'confidence' => $attr_confidence,
            'lift' => $attr_lift,
            'leverage' => $attr_leverage,
            'conviction' => $attr_conviction,
            'price_sim' => $price_sim,
            'category' => strtolower($other_shoe['category'] ?? 'shoes'),
            'score' => $composite_score
        ];
    }
}

uasort($scored_rules, function($a, $b) {
    return $b['score'] <=> $a['score'];
});

// Build top recommendations; for shoes try to mix socks + cleaner
$top_ids = [];
if ($current_category === 'shoes') {
    $socks_picked = 0;
    $cleaner_picked = 0;
    foreach (array_keys($scored_rules) as $oid) {
        $cat = $scored_rules[$oid]['category'] ?? '';
        if ($cat === 'accessories') $cat = 'socks';
        if ($cat === 'socks' && $socks_picked < 2) {
            $top_ids[] = $oid;
            $socks_picked++;
        } elseif ($cat === 'shoe_care' && $cleaner_picked < 2) {
            $top_ids[] = $oid;
            $cleaner_picked++;
        }
        if (count($top_ids) >= 4) break;
    }
    // Fill remaining slots from any valid scored rule
    if (count($top_ids) < 4) {
        foreach (array_keys($scored_rules) as $oid) {
            if (in_array($oid, $top_ids, true)) continue;
            $top_ids[] = $oid;
            if (count($top_ids) >= 4) break;
        }
    }
} else {
    $top_ids = array_slice(array_keys($scored_rules), 0, 4);
}

if (!empty($top_ids)) {
    foreach ($top_ids as $oid) {
        if (!isset($all_shoes_map[$oid])) continue;
        $fbt_recommendations[] = [
            'shoe' => $all_shoes_map[$oid],
            'confidence' => $scored_rules[$oid]['confidence'],
            'lift' => $scored_rules[$oid]['lift'],
            'leverage' => $scored_rules[$oid]['leverage'],
            'conviction' => $scored_rules[$oid]['conviction'],
            'price_sim' => $scored_rules[$oid]['price_sim'],
            'score' => $scored_rules[$oid]['score']
        ];
    }
}

// Check if the "added" parameter is present in the URL
if (isset($_GET['added']) && $_GET['added'] == 'true') {
    // Display the "Item added successfully" message
    echo '<div></div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($shoe_name); ?> - My Shoe Store</title>
    
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

        .success-message {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
            animation: slideDown 0.5s ease forwards;
            position: relative;
            z-index: 1000;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .product-container {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: fadeInUp 0.8s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .product-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        .image-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 70%, rgba(102, 126, 234, 0.1), transparent 50%);
            pointer-events: none;
        }

        .shoe-image {
            max-width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 15px;
            transition: transform 0.5s ease;
            position: relative;
            z-index: 2;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.15));
        }

        .shoe-image:hover {
            transform: scale(1.05) rotate(2deg);
        }

        .details-section {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .brand-badge {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            width: fit-content;
        }

        .shoe-title {
            font-size: 3rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 20px;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .price-section {
            margin-bottom: 30px;
        }

        .sale-badge {
            display: inline-block;
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-transform: uppercase;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .original-price {
            text-decoration: line-through;
            color: #95a5a6;
            font-size: 1.5rem;
            margin-right: 15px;
            font-weight: 600;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 10px;
        }

        .status {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status.available {
            background: #d4edda;
            color: #155724;
            border: 2px solid #27ae60;
        }

        .status.out-of-stock {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #e74c3c;
        }

        .description-section {
            margin-bottom: 40px;
        }

        .description-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 10px;
        }

        .description-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .description-text {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.7;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
        }

        .action-section {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .add-to-cart-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
            text-decoration: none;
            padding: 18px 35px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(39, 174, 96, 0.3);
            position: relative;
            overflow: hidden;
        }

        .add-to-cart-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(39, 174, 96, 0.5);
        }

        .add-to-cart-btn:hover::before {
            left: 100%;
        }

        .out-of-stock-message {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            padding: 18px 35px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
        }

        .wishlist-btn {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 2px solid #667eea;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .wishlist-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .product-features {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 30px;
            margin-top: 40px;
        }

        .features-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .feature-item {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .feature-text {
            font-weight: 600;
            color: #2c3e50;
        }

.fbt-section {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 30px;
            margin-top: 40px;
        }

        .fbt-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .fbt-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .fbt-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .fbt-card:hover {
            transform: translateY(-5px);
        }

        .fbt-image {
            width: 100%;
            height: 180px;
            object-fit: contain;
            display: block;
            background: #f8f9fa;
            padding: 15px;
        }

        .fbt-info {
            padding: 15px;
            text-align: center;
        }

        .fbt-name {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .fbt-price {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .fbt-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .fbt-status.available {
            background: #d4edda;
            color: #155724;
        }

        .fbt-status.out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }

        .fbt-badge {
            display: block;
            background: linear-gradient(45deg, #f093fb, #f5576c);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            margin: 8px 0;
        }

        .fbt-add-btn {
            display: block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 8px;
            transition: all 0.3s ease;
        }

        .fbt-add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #764ba2;
            transform: translateX(-5px);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .product-layout {
                grid-template-columns: 1fr;
            }

            .image-section {
                padding: 30px;
                min-height: 400px;
            }

            .details-section {
                padding: 30px;
            }

            .shoe-title {
                font-size: 2.5rem;
            }

            .price {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
                margin: 20px auto;
            }

            .shoe-title {
                font-size: 2rem;
            }

            .price {
                font-size: 1.8rem;
            }

            .action-section {
                flex-direction: column;
                align-items: stretch;
            }

            .add-to-cart-btn,
            .wishlist-btn {
                text-align: center;
                justify-content: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .shoe-title {
                font-size: 1.8rem;
            }

            .add-to-cart-btn {
                padding: 15px 25px;
                font-size: 1rem;
            }

            .image-section {
                padding: 20px;
            }

            .details-section {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($_GET['added']) && $_GET['added'] == 'true'): ?>
        <div class="success-message">
            ✅ Item added successfully to your cart!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] == 'out_of_stock'): ?>
        <div class="error-message" style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; padding: 15px 20px; text-align: center; font-weight: 600; font-size: 1.1rem; box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3); animation: slideDown 0.5s ease forwards; position: relative; z-index: 1000;">
            ❌ Sorry, this item is currently out of stock.
        </div>
    <?php endif; ?>

    <div class="container">
        <a href="javascript:history.back()" class="back-link">
            ← Back to Products
        </a>

        <div class="product-container">
            <div class="product-layout">
                <div class="image-section">
                    <img class="shoe-image" src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($shoe_name); ?> Image">
                </div>
                
                <div class="details-section">
                    <div class="brand-badge"><?php echo htmlspecialchars($brand); ?></div>
                    
                    <h1 class="shoe-title"><?php echo htmlspecialchars($shoe_name); ?></h1>
                    
                    <div class="price-section">
                        <?php if (!empty($original_price) && $original_price > $price): ?>
                            <?php $discount_pct = round((($original_price - $price) / $original_price) * 100); ?>
                            <div class="sale-badge">SALE! <?php echo $discount_pct; ?>% OFF</div>
                            <div class="price">
                                <span class="original-price">Rs <?php echo number_format($original_price, 2); ?></span>
                                Rs <?php echo number_format($price, 2); ?>
                            </div>
                        <?php else: ?>
                            <div class="price">Rs <?php echo number_format($price, 2); ?></div>
                        <?php endif; ?>
                        <span class="status <?php echo $status == 'Available' ? 'available' : 'out-of-stock'; ?>">
                            <?php echo $status; ?>
                        </span>
                    </div>
                    
                    <div class="description-section">
                        <h3 class="description-title">Product Description</h3>
                        <p class="description-text"><?php echo htmlspecialchars($description); ?></p>
                    </div>
                    
                    <div class="action-section">
                        <?php if ($status == "Available") : ?>
                            <a href="addtocart.php?shoe_id=<?php echo $shoe_id; ?>" class="add-to-cart-btn">
                                🛒 Add to Cart
                            </a>
                            <?php
                            $inWishlist = in_array($shoe_id, $user_wishlist ?? []);
                            $heart = $inWishlist ? '♥ Added' : '♡ Wishlist';
                            $wishStyle = $inWishlist ? 'color: #e74c3c; border-color: #e74c3c;' : '';
                            ?>
                            <button class="wishlist-btn" style="<?php echo $wishStyle; ?>" onclick="toggleWishlist(<?php echo $shoe_id; ?>, this)"><?php echo $heart; ?></button>
                        <?php else : ?>
                            <div class="out-of-stock-message">
                                ❌ Out of Stock
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="product-features">
            <h3 class="features-title">Why Choose This Product?</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">🚚</div>
                    <div class="feature-text">Free Shipping</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔄</div>
                    <div class="feature-text">Easy Returns</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">✅</div>
                    <div class="feature-text">Authentic Product</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🛡️</div>
                    <div class="feature-text">1 Year Warranty</div>
                </div>
            </div>
        </div>

        <?php if (!empty($fbt_recommendations)): ?>
        <div class="fbt-section">
            <h3 class="fbt-title"><?php echo htmlspecialchars($fbt_title); ?></h3>
            <div class="fbt-grid">
                <?php foreach ($fbt_recommendations as $fbt):
                    $fshoe = $fbt['shoe'];
                    $fimg = "../shoeImage/" . $fshoe['image_col'];
                    $fStatusClass = $fshoe['status'] == 1 ? 'available' : 'out-of-stock';
                    $fStatusText = $fshoe['status'] == 1 ? 'Available' : 'Out of Stock';
                ?>
                <div class="fbt-card">
                    <a href="shoe_details.php?shoe_id=<?php echo $fshoe['shoe_id']; ?>">
                        <img class="fbt-image" src="<?php echo htmlspecialchars($fimg); ?>" alt="<?php echo htmlspecialchars($fshoe['name']); ?> Image">
                    </a>
                    <div class="fbt-info">
                        <div class="fbt-name"><?php echo htmlspecialchars($fshoe['name']); ?></div>
                        <div class="fbt-price">Rs <?php echo number_format($fshoe['price'], 2); ?></div>
                        <span class="fbt-status <?php echo $fStatusClass; ?>"><?php echo $fStatusText; ?></span>
                        <?php if ($fshoe['status'] == 1): ?>
                            <a href="addtocart.php?shoe_id=<?php echo $fshoe['shoe_id']; ?>" class="fbt-add-btn">Add to Cart</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Add to cart animation
        document.querySelector('.add-to-cart-btn')?.addEventListener('click', function(e) {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });



        // Image zoom effect
        const shoeImage = document.querySelector('.shoe-image');
        shoeImage.addEventListener('click', function() {
            if (this.style.transform.includes('scale(2)')) {
                this.style.transform = '';
                this.style.cursor = 'zoom-in';
            } else {
                this.style.transform = 'scale(2)';
                this.style.cursor = 'zoom-out';
            }
        });
    </script>
</body>
</html>
<?php include('footer.php'); ?>
