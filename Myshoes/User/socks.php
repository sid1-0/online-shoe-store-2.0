<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Socks Collection - My Family Store</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
        }

        .puma-hero {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(255, 193, 7, 0.8)), url('/placeholder.svg?height=400&width=1200');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            position: relative;
            overflow: hidden;
        }

        .puma-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #ffc107, #ff9800, #ff5722);
            opacity: 0.1;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.1; }
            50% { opacity: 0.2; }
        }

        .puma-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .puma-hero h1 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 20px;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .puma-hero p {
            font-size: 1.4rem;
            margin-bottom: 30px;
            opacity: 0.95;
            line-height: 1.6;
        }

        .puma-logo {
            font-size: 6rem;
            margin-bottom: 20px;
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .container {
            max-width: 1400px;
            margin: -50px auto 80px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        .brand-description {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .brand-description::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ffc107, #ff9800, #ff5722);
        }

        .brand-description h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .brand-description h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ffc107, #ff9800);
            border-radius: 2px;
        }

        .brand-description p {
            font-size: 1.2rem;
            color: #7f8c8d;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .filter-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 12px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 25px;
            font-size: 1rem;
            color: #555;
            background-color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .filter-select:focus {
            outline: none;
            border-color: #ffc107;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
        }

        .sort-options {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .view-toggle {
            display: flex;
            gap: 10px;
        }

        .view-btn {
            background: none;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .view-btn.active,
        .view-btn:hover {
            background-color: #ffc107;
            color: white;
            border-color: #ffc107;
        }

        .shoes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .shoe {
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.4s ease;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            position: relative;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .shoe:nth-child(1) { animation-delay: 0.1s; }
        .shoe:nth-child(2) { animation-delay: 0.2s; }
        .shoe:nth-child(3) { animation-delay: 0.3s; }
        .shoe:nth-child(4) { animation-delay: 0.4s; }
        .shoe:nth-child(5) { animation-delay: 0.5s; }
        .shoe:nth-child(6) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .shoe:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .shoe-image-wrapper {
            overflow: hidden;
            position: relative;
            height: 280px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .shoe-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
            padding: 20px;
        }

        .shoe:hover .shoe-image {
            transform: scale(1.1) rotate(-3deg);
        }

        .shoe-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 2;
        }

        .shoe-details {
            padding: 25px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-top: 1px solid #f0f0f0;
        }

        .shoe-details h2 {
            margin: 0 0 15px;
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            transition: color 0.3s ease;
        }

        .shoe-details h2 a {
            text-decoration: none;
            color: inherit;
        }

        .shoe-details h2 a:hover {
            color: #ffc107;
        }

        .shoe-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .shoe-brand {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .price {
            font-size: 1.4rem;
            font-weight: 800;
            color: #ffc107;
        }

        .status {
            font-weight: 700;
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status.available {
            background-color: #d4edda;
            color: #155724;
        }

        .status.not-available {
            background-color: #f8d7da;
            color: #721c24;
        }

        .shoe-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .action-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .view-btn-action {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: white;
        }

        .view-btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(255, 193, 7, 0.4);
        }

        .wishlist-btn {
            background-color: #f8f9fa;
            color: #333;
            border: 2px solid #e1e8ed;
        }

        .wishlist-btn:hover {
            background-color: #e9ecef;
            border-color: #ffc107;
            color: #ffc107;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .empty-state-icon {
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

        .empty-state h3 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #333;
        }

        .empty-state p {
            font-size: 1.2rem;
            max-width: 500px;
            margin: 0 auto 30px;
            color: #7f8c8d;
            line-height: 1.6;
        }

        .browse-all-btn {
            display: inline-block;
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .browse-all-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 193, 7, 0.4);
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 50px;
            gap: 10px;
        }

        .page-item {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: white;
            color: #333;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
        }

        .page-item.active,
        .page-item:hover {
            background: linear-gradient(45deg, #ffc107, #ff9800);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 193, 7, 0.3);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .shoes-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 25px;
            }
        }

        @media (max-width: 768px) {
            .puma-hero {
                padding: 60px 20px;
            }

            .puma-hero h1 {
                font-size: 2.5rem;
            }

            .puma-hero p {
                font-size: 1.1rem;
            }

            .puma-logo {
                font-size: 4rem;
            }

            .container {
                margin: -30px auto 60px;
                padding: 0 15px;
            }

            .brand-description,
            .filter-section {
                padding: 25px 20px;
            }

            .brand-description h2 {
                font-size: 2rem;
            }

            .filter-bar {
                flex-direction: column;
                gap: 15px;
            }

            .filter-options,
            .sort-options {
                width: 100%;
                justify-content: space-between;
            }

            .shoes-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }

            .shoe-details {
                padding: 20px;
            }

            .shoe-details h2 {
                font-size: 1.1rem;
            }

            .price {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .puma-hero h1 {
                font-size: 2rem;
            }

            .shoes-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .filter-options,
            .sort-options {
                flex-wrap: wrap;
                gap: 10px;
            }

            .filter-select {
                flex-grow: 1;
                min-width: 120px;
            }

            .shoe-image-wrapper {
                height: 220px;
            }

            .shoe-actions {
                flex-direction: column;
                gap: 8px;
            }

            .action-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="puma-hero" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(102, 126, 234, 0.8)), url('../shoeImage/shoes.jpg'); background-size: cover; background-position: center;">
        <div class="puma-hero-content">
            <div class="puma-logo">🧦</div>
            <h1>Socks Collection</h1>
            <p>Complete your athletic look with our premium collection of comfortable, breathable, and supportive socks.</p>
        </div>
    </div>

    <div class="container">
        <div class="brand-description">
            <h2>Premium Socks Collection</h2>
            <p>
                Step into comfort with our wide range of premium athletic and casual socks. Engineered for breathability, moisture-wicking, and durability, our socks provide the perfect foundation for any pair of shoes.
            </p>
        </div>
        
        <div class="filter-section">
            <div class="filter-bar">
                <div class="filter-options">
                    <select class="filter-select">
                        <option>All Categories</option>
                        <option>Running</option>
                        <option>Football</option>
                        <option>Lifestyle</option>
                        <option>Training</option>
                    </select>
                    <select class="filter-select">
                        <option>All Sizes</option>
                        <option>7</option>
                        <option>8</option>
                        <option>9</option>
                        <option>10</option>
                        <option>11</option>
                        <option>12</option>
                    </select>
                    <select class="filter-select">
                        <option>Price Range</option>
                        <option>Under Rs 1000</option>
                        <option>Rs 10000 - Rs 15000</option>
                        <option>Rs 15000 - Rs 20000</option>
                        <option>Over Rs 20000</option>
                    </select>
                </div>
                <div class="sort-options">
                    <select class="filter-select">
                        <option>Sort by: Featured</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Newest First</option>
                        <option>Best Sellers</option>
                    </select>
                    <div class="view-toggle">
                        <button class="view-btn active">Grid</button>
                        <button class="view-btn">List</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="shoes-grid">
            <?php
            require_once 'connection.php';

            $stmt = $connection->prepare("SELECT shoe_id, name, price, original_price, image_col, status, brand FROM shoes WHERE name LIKE '%sock%'");
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $shoeId      = htmlspecialchars($row['shoe_id']);
                    $shoeName    = htmlspecialchars($row['name']);
                    $shoePrice   = number_format($row['price'], 2);
                    $imageUrl    = "../shoeImage/" . htmlspecialchars($row['image_col']);
                    $status      = intval($row['status']);
                    $statusClass = $status === 1 ? 'available' : 'not-available';
                    $statusText  = $status === 1 ? 'Available' : 'Out of Stock';
                    $badgeText   = $status === 1 ? 'New' : 'Sold Out';
                    if (!empty($row['original_price']) && $row['original_price'] > $row['price']) {
                        $discount = round((($row['original_price'] - $row['price']) / $row['original_price']) * 100);
                        $badgeText = "SALE {$discount}% OFF";
                        $shoePriceHTML = "<span style='text-decoration:line-through; color:#95a5a6; font-size:0.9rem;'>Rs " . number_format($row['original_price'], 2) . "</span><br>Rs " . $shoePrice;
                    } else {
                        $shoePriceHTML = "Rs " . $shoePrice;
                    }
                    ?>
                    <div class="shoe" onclick="window.location.href='shoe_details.php?shoe_id=<?php echo $shoeId; ?>'">
                        <div class="shoe-image-wrapper">
                            <div class="shoe-badge"><?php echo $badgeText; ?></div>
                            <img class="shoe-image" src="<?php echo $imageUrl; ?>" alt="<?php echo $shoeName; ?> Image" loading="lazy" />
                        </div>
                        <div class="shoe-details">
                            <div>
                                <h2><a href="shoe_details.php?shoe_id=<?php echo $shoeId; ?>"><?php echo $shoeName; ?></a></h2>
                                <div class="shoe-meta">
                                    <span class="shoe-brand"><?php echo htmlspecialchars($row['brand']); ?></span>
                                    <span class="price"><?php echo $shoePriceHTML; ?></span>
                                </div>
                                <span class="status <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            </div>
                            <div class="shoe-actions">
                                <button class="action-btn view-btn-action" onclick="event.stopPropagation(); window.location.href='shoe_details.php?shoe_id=<?php echo $shoeId; ?>'">View Details</button>
                                <button class="action-btn wishlist-btn" onclick="event.stopPropagation();">Add to Wishlist</button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="empty-state">
                        <div class="empty-state-icon">🧦</div>
                        <h3>No Socks Found</h3>
                        <p>We couldn\'t find any socks at the moment. Check back later or browse our amazing shoe collection.</p>
                        <a href="index.php" class="browse-all-btn">Browse All Products</a>
                      </div>';
            }

            $stmt->close();
            $connection->close();
            ?>
        </div>
        
        <?php if (isset($result) && $result->num_rows > 0): ?>
        <div class="pagination">
            <div class="page-item">←</div>
            <div class="page-item active">1</div>
            <div class="page-item">2</div>
            <div class="page-item">3</div>
            <div class="page-item">→</div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // View toggle functionality
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Filter functionality
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                // Add filter logic here
                console.log('Filter changed:', this.value);
            });
        });

        // Pagination functionality
        document.querySelectorAll('.page-item').forEach(item => {
            item.addEventListener('click', function() {
                if (!this.textContent.includes('←') && !this.textContent.includes('→')) {
                    document.querySelectorAll('.page-item').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
<?php include('footer.php'); ?>

