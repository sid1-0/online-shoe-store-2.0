<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Shoe Store - Premium Footwear Collection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .carousel {
            position: relative;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .carousel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 70%, rgba(255, 255, 255, 0.1), transparent 50%);
            pointer-events: none;
            z-index: 1;
        }

        .carousel .list {
            position: relative;
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .carousel .list .item {
            position: absolute;
            inset: 0;
            opacity: 0;
            visibility: hidden;
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5%;
            transform: translateX(100px);
        }

        .carousel .list .item.active {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        .carousel .list .item.prev {
            transform: translateX(-100px);
        }

        .carousel .list .item img {
            width: 50%;
            height: 80%;
            object-fit: cover;
            border-radius: 25px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            transition: transform 0.8s ease;
            filter: brightness(1.1) contrast(1.1);
        }

        .carousel .list .item.active img {
            animation: imageFloat 6s ease-in-out infinite;
        }

        @keyframes imageFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.02); }
        }

        .carousel .list .item .content {
            width: 45%;
            color: white;
            padding-left: 50px;
            animation: contentSlideIn 1s ease 0.3s both;
        }

        @keyframes contentSlideIn {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .carousel .list .item .content .author {
            font-weight: 800;
            letter-spacing: 8px;
            font-size: 1.1rem;
            margin-bottom: 15px;
            opacity: 0.9;
            text-transform: uppercase;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .carousel .list .item .content .title {
            font-size: 4.5rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 25px;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .carousel .list .item .content .des {
            font-size: 1.3rem;
            margin-bottom: 35px;
            opacity: 0.95;
            line-height: 1.7;
            max-width: 500px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .carousel .list .item .content .buttons {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .carousel .list .item .content .buttons button {
            padding: 18px 35px;
            border: 3px solid rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1rem;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .carousel .list .item .content .buttons button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s;
        }

        .carousel .list .item .content .buttons button:hover {
            background: rgba(255, 255, 255, 0.95);
            color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.4);
            border-color: rgba(255, 255, 255, 1);
        }

        .carousel .list .item .content .buttons button:hover::before {
            left: 100%;
        }

        /* Enhanced Thumbnail Navigation */
        .carousel .thumbnail {
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 25px;
            z-index: 10;
        }

        .carousel .thumbnail .item {
            width: 160px;
            height: 240px;
            flex-shrink: 0;
            position: relative;
            cursor: pointer;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s ease;
            border: 3px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .carousel .thumbnail .item.active {
            transform: scale(1.15);
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.4);
            border-color: rgba(255, 255, 255, 0.8);
        }

        .carousel .thumbnail .item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .carousel .thumbnail .item:hover img {
            transform: scale(1.1);
        }

        .carousel .thumbnail .item::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .carousel .thumbnail .item.active::after {
            opacity: 1;
        }

        /* Enhanced Arrow Navigation */
        .carousel .arrows {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 60px;
            z-index: 10;
        }

        .carousel .arrows button {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            border: 3px solid rgba(255, 255, 255, 0.6);
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            transition: all 0.4s ease;
            backdrop-filter: blur(15px);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .carousel .arrows button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .carousel .arrows button:hover {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            transform: scale(1.1);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        }

        .carousel .arrows button:hover::before {
            opacity: 1;
        }

        /* Progress Indicator */
        .carousel .progress {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }

        .carousel .progress .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel .progress .dot.active {
            background: white;
            transform: scale(1.3);
        }

        /* Enhanced Main Content Styles */
        .main-content {
            padding: 100px 0;
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
            position: relative;
        }

        .section-header::before {
            content: '';
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .section-header h2 {
            font-size: 3.5rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
            display: inline-block;
            background: linear-gradient(45deg, #2c3e50, #34495e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 3px;
        }

        .section-header p {
            font-size: 1.3rem;
            color: #7f8c8d;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
            font-weight: 500;
        }

        /* Enhanced Product Grid */
        .shoes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 35px;
            margin-bottom: 100px;
        }

        .shoe {
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            animation: fadeInUp 0.8s ease forwards;
            opacity: 0;
            transform: translateY(40px);
            cursor: pointer;
        }

        .shoe:nth-child(1) { animation-delay: 0.1s; }
        .shoe:nth-child(2) { animation-delay: 0.2s; }
        .shoe:nth-child(3) { animation-delay: 0.3s; }
        .shoe:nth-child(4) { animation-delay: 0.4s; }
        .shoe:nth-child(5) { animation-delay: 0.5s; }
        .shoe:nth-child(6) { animation-delay: 0.6s; }
        .shoe:nth-child(7) { animation-delay: 0.7s; }
        .shoe:nth-child(8) { animation-delay: 0.8s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .shoe::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .shoe:hover {
            transform: translateY(-20px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        .shoe:hover::before {
            opacity: 1;
        }

        .shoe-image-wrapper {
            position: relative;
            height: 280px;
            overflow: hidden;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 25px 25px 0 0;
        }

        .shoe-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.6s ease;
            padding: 25px;
            position: relative;
            z-index: 2;
        }

        .shoe:hover .shoe-image {
            transform: scale(1.15) rotate(8deg);
        }

        .shoe-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 10px 18px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 3;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .shoe-details {
            padding: 30px;
            position: relative;
            z-index: 2;
        }

        .shoe-details h3 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 15px;
            transition: color 0.3s ease;
            line-height: 1.3;
        }

        .shoe:hover .shoe-details h3 {
            color: #667eea;
        }

        .shoe-details h3 a {
            text-decoration: none;
            color: inherit;
        }

        .shoe-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .shoe-brand {
            font-size: 0.95rem;
            color: #7f8c8d;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #f8f9fa;
            padding: 5px 12px;
            border-radius: 15px;
        }

        .shoe-price {
            font-size: 1.6rem;
            font-weight: 900;
            color: #667eea;
            text-shadow: 1px 1px 2px rgba(102, 126, 234, 0.2);
        }

        .shoe-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .shoe-status.available {
            background: linear-gradient(45deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 2px solid #27ae60;
        }

        .shoe-status.out-of-stock {
            background: linear-gradient(45deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 2px solid #e74c3c;
        }

        /* Enhanced View All Button */
        .view-all-btn {
            display: block;
            width: 250px;
            margin: 60px auto;
            padding: 20px 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 800;
            text-align: center;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 1.1rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .view-all-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .view-all-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.5);
        }

        .view-all-btn:hover::before {
            left: 100%;
        }

        /* Brand Showcase Section */
        .brand-showcase {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 80px 0;
            margin: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .brand-showcase::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 70% 30%, rgba(255, 255, 255, 0.1), transparent 50%);
            pointer-events: none;
        }

        .brand-showcase .container {
            position: relative;
            z-index: 2;
        }

        .brand-showcase h2 {
            text-align: center;
            color: white;
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 50px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .brand-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .brand-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .brand-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-10px);
        }

        .brand-item h3 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .brand-item p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .carousel .list .item .content .title {
                font-size: 3.5rem;
            }
            
            .shoes-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 30px;
            }
        }

        @media (max-width: 768px) {
            .carousel {
                height: 80vh;
            }
            
            .carousel .list .item {
                flex-direction: column;
                text-align: center;
                padding: 30px 20px;
            }
            
            .carousel .list .item img {
                width: 90%;
                height: 45%;
                margin-bottom: 30px;
            }
            
            .carousel .list .item .content {
                width: 100%;
                padding-left: 0;
            }
            
            .carousel .list .item .content .title {
                font-size: 2.8rem;
            }
            
            .carousel .thumbnail {
                display: none;
            }
            
            .carousel .arrows {
                padding: 0 30px;
            }
            
            .carousel .arrows button {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .section-header h2 {
                font-size: 2.8rem;
            }
            
            .shoes-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 25px;
            }

            .brand-showcase h2 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 480px) {
            .carousel .list .item .content .title {
                font-size: 2.2rem;
            }
            
            .carousel .list .item .content .des {
                font-size: 1.1rem;
            }
            
            .shoes-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .section-header h2 {
                font-size: 2.2rem;
            }

            .view-all-btn {
                width: 200px;
                padding: 15px 30px;
                font-size: 1rem;
            }

            .brand-showcase h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Enhanced Hero Carousel Section -->
    <section class="carousel">
        <div class="list">
            <div class="item active">
                <img src="images/img1.jpg" alt="Featured Shoe 1">
                <div class="content">
                    <div class="author">Welcome To</div>
                    <div class="title">My Shoe Store</div>
                    <div class="des">
                        Discover the perfect blend of style, comfort, and quality. From athletic performance to everyday elegance, find your ideal footwear companion that matches your lifestyle.
                    </div>
                    <div class="buttons">
                        <button class="see-more" onclick="document.querySelector('.main-content').scrollIntoView({behavior: 'smooth'})">Explore Collection</button>
                        <button onclick="window.location.href='aboutus.php'">About Us</button>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="images/shoes.jpg" alt="Featured Shoe 2">
                <div class="content">
                    <div class="author">Premium Quality</div>
                    <div class="title">Latest Arrivals</div>
                    <div class="des">
                        Step into the future of footwear with our newest collection. Featuring cutting-edge designs and innovative comfort technology that redefines modern style.
                    </div>
                    <div class="buttons">
                        <button onclick="document.querySelector('#new-arrivals').scrollIntoView({behavior: 'smooth'})">View New Arrivals</button>
                        <button onclick="window.location.href='mycart.php'">My Cart</button>
                    </div>
                </div>
            </div>
            <div class="item">
                <img src="images/img1.jpg" alt="Featured Shoe 3">
                <div class="content">
                    <div class="author">Best Sellers</div>
                    <div class="title">Top Rated</div>
                    <div class="des">
                        Discover our customer favorites and best-selling footwear. These are the shoes that have earned their place at the top through quality and style.
                    </div>
                    <div class="buttons">
                        <button onclick="document.querySelector('.main-content').scrollIntoView({behavior: 'smooth'})">Shop Best Sellers</button>
                        <button onclick="window.location.href='order.php'">My Orders</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="thumbnail">
            <div class="item active"><img src="images/img1.jpg" alt="Thumbnail 1"></div>
            <div class="item"><img src="images/shoes.jpg" alt="Thumbnail 2"></div>
            <div class="item"><img src="images/img1.jpg" alt="Thumbnail 3"></div>
        </div>
        
        <div class="arrows">
            <button id="prev">‹</button>
            <button id="next">›</button>
        </div>

        <div class="progress">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </section>

    <div class="main-content">
        <div class="container">
            <div class="section-header">
                <h2>Featured Collection</h2>
                <p>Explore our handpicked selection of premium footwear from top brands worldwide, curated for style and comfort</p>
            </div>
            
            <div class="shoes-grid">
                <?php
                require_once 'connection.php';
                $sql = "SELECT * FROM shoes LIMIT 8";
                $result = $connection->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $statusClass = $row['status'] == 1 ? 'available' : 'out-of-stock';
                        $statusText = $row['status'] == 1 ? 'Available' : 'Out of Stock';
                        $badgeText = $row['status'] == 1 ? 'New' : 'Sold Out';
                        if (!empty($row['original_price']) && $row['original_price'] > $row['price']) { $discount = round((($row['original_price'] - $row['price']) / $row['original_price']) * 100); $badgeText = "SALE {$discount}% OFF"; }
                        
                        echo "<div class='shoe'>
                                <div class='shoe-image-wrapper'>
                                    <div class='shoe-badge'>{$badgeText}</div>
                                    <a href='shoe_details.php?shoe_id={$row['shoe_id']}'>
                                        <img class='shoe-image' src='../shoeImage/{$row['image_col']}' alt='{$row['name']} Image' loading='lazy'>
                                    </a>
                                </div>
                                <div class='shoe-details'>
                                    <h3><a href='shoe_details.php?shoe_id={$row['shoe_id']}' style='text-decoration: none; color: inherit;'>{$row['name']}</a></h3>
                                    <div class='shoe-meta'>
                                        <span class='shoe-brand'>{$row['brand']}</span>
                                        " . (!empty($row['original_price']) && $row['original_price'] > $row['price'] ? 
                                          "<span class='shoe-price' style='display:flex; flex-direction:column; align-items:flex-end; line-height:1.2;'>
                                            <span style='text-decoration:line-through; color:#95a5a6; font-size:1rem; font-weight:600;'>Rs {$row['original_price']}</span>
                                            <span>Rs {$row['price']}</span>
                                          </span>" 
                                          : "<span class='shoe-price'>Rs {$row['price']}</span>") . "
                                    </div>
                                    <span class='shoe-status {$statusClass}'>{$statusText}</span>
                                </div>
                              </div>";
                    }
                } else {
                    echo "<p style='text-align: center; grid-column: 1 / -1; font-size: 1.2rem; color: #7f8c8d;'>No shoes found.</p>";
                }
                ?>
            </div>

            <a href="all_shoes.php" class="view-all-btn">View All Products</a>

            <div class="brand-showcase">
                <div class="container">
                    <h2>Premium Brands</h2>
                    <div class="brand-grid">
                        <div class="brand-item">
                            <h3>Nike</h3>
                            <p>Just Do It</p>
                        </div>
                        <div class="brand-item">
                            <h3>Adidas</h3>
                            <p>Impossible is Nothing</p>
                        </div>
                        <div class="brand-item">
                            <h3>Puma</h3>
                            <p>Forever Faster</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Arrivals Section -->
            <div id="new-arrivals" class="section-header">
                <h2>New Arrivals</h2>
                <p>Be the first to step into the latest trends and innovations in footwear technology</p>
            </div>
            
            <div class="shoes-grid">
                <?php
                $sql = "SELECT * FROM shoes ORDER BY created_at DESC LIMIT 4";
                $result = $connection->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $statusClass = $row['status'] == 1 ? 'available' : 'out-of-stock';
                        $statusText = $row['status'] == 1 ? 'Available' : 'Out of Stock';
                        
                        echo "<div class='shoe'>
                                <div class='shoe-image-wrapper'>
                                    <div class='shoe-badge'>New Arrival</div>
                                    <a href='shoe_details.php?shoe_id={$row['shoe_id']}'>
                                        <img class='shoe-image' src='../shoeImage/{$row['image_col']}' alt='{$row['name']} Image' loading='lazy'>
                                    </a>
                                </div>
                                <div class='shoe-details'>
                                    <h3><a href='shoe_details.php?shoe_id={$row['shoe_id']}' style='text-decoration: none; color: inherit;'>{$row['name']}</a></h3>
                                    <div class='shoe-meta'>
                                        <span class='shoe-brand'>{$row['brand']}</span>
                                        " . (!empty($row['original_price']) && $row['original_price'] > $row['price'] ? 
                                          "<span class='shoe-price' style='display:flex; flex-direction:column; align-items:flex-end; line-height:1.2;'>
                                            <span style='text-decoration:line-through; color:#95a5a6; font-size:1rem; font-weight:600;'>Rs {$row['original_price']}</span>
                                            <span>Rs {$row['price']}</span>
                                          </span>" 
                                          : "<span class='shoe-price'>Rs {$row['price']}</span>") . "
                                    </div>
                                    <span class='shoe-status {$statusClass}'>{$statusText}</span>
                                </div>
                              </div>";
                    }
                } else {
                    echo "<p style='text-align: center; grid-column: 1 / -1; font-size: 1.2rem; color: #7f8c8d;'>No new arrivals found.</p>";
                }
                ?>
            </div>

            <!-- Best Sellers Section -->
            <div class="section-header">
                <h2>Best Sellers</h2>
                <p>Customer favorites that have earned their place at the top through exceptional quality and style</p>
            </div>
            
            <div class="shoes-grid">
                <?php
                // ================= Weighted Best-Sellers Ranking Algorithm =================
                // Instead of a plain "order count", each shoe gets a popularity SCORE built from:
                //   1) Status weighting -> a confirmed (Accepted) sale counts far more than a
                //      still-Pending one, and a Rejected/cancelled order counts for nothing.
                //   2) Recency (time) decay -> since purchase_request has no timestamp column,
                //      its auto-increment `id` is used as a chronological proxy. Older orders
                //      lose half their weight every $half_life orders, so a shoe that sold well
                //      recently outranks one that only sold well a long time ago.
                // Final score per shoe = sum over its orders of (status_weight * decay_factor).
                // This is the same idea used by "hot"/"trending" rankings on e-commerce and
                // news sites, adapted here for order history instead of upvotes.

                $status_weight = [
                    'Accepted' => 3.0,  // confirmed sale -> weighs the most
                    'Pending'  => 1.0,  // awaiting confirmation -> still counts, but less
                    'Rejected' => 0.0,  // cancelled/declined -> excluded entirely
                ];
                $half_life = 30; // an order's contribution halves every 30 orders "back in time"

                $orders_result = $connection->query("SELECT id, shoe_id, status FROM purchase_request");

                $order_rows = [];
                $max_id = 0;
                if ($orders_result && $orders_result->num_rows > 0) {
                    while ($r = $orders_result->fetch_assoc()) {
                        $order_rows[] = $r;
                        if ((int)$r['id'] > $max_id) {
                            $max_id = (int)$r['id'];
                        }
                    }
                }

                $scores = []; // shoe_id => weighted popularity score
                foreach ($order_rows as $r) {
                    $weight = $status_weight[$r['status']] ?? 1.0;
                    if ($weight <= 0) continue; // rejected orders don't contribute

                    $age = $max_id - (int)$r['id'];
                    $decay = pow(0.5, $age / $half_life);
                    $points = $weight * $decay;

                    $sid = $r['shoe_id'];
                    $scores[$sid] = ($scores[$sid] ?? 0.0) + $points;
                }

                arsort($scores); // rank shoes by score, highest first
                $top_ids = array_slice(array_keys($scores), 0, 4);

                if (!empty($top_ids)) {
                    $placeholders = implode(',', array_fill(0, count($top_ids), '?'));
                    $types = str_repeat('i', count($top_ids));
                    $stmt = $connection->prepare("SELECT * FROM shoes WHERE shoe_id IN ($placeholders)");
                    $stmt->bind_param($types, ...$top_ids);
                    $stmt->execute();
                    $shoes_result = $stmt->get_result();

                    $shoes_by_id = [];
                    while ($row = $shoes_result->fetch_assoc()) {
                        $shoes_by_id[$row['shoe_id']] = $row;
                    }
                    $stmt->close();

                    $rank = 1;
                    foreach ($top_ids as $sid) {
                        if (!isset($shoes_by_id[$sid])) continue;
                        $row = $shoes_by_id[$sid];
                        $statusClass = $row['status'] == 1 ? 'available' : 'out-of-stock';
                        $statusText = $row['status'] == 1 ? 'Available' : 'Out of Stock';

                        echo "<div class='shoe'>
                                <div class='shoe-image-wrapper'>
                                    <div class='shoe-badge'>#{$rank} Best Seller</div>
                                    <a href='shoe_details.php?shoe_id={$row['shoe_id']}'>
                                        <img class='shoe-image' src='../shoeImage/{$row['image_col']}' alt='{$row['name']} Image' loading='lazy'>
                                    </a>
                                </div>
                                <div class='shoe-details'>
                                    <h3><a href='shoe_details.php?shoe_id={$row['shoe_id']}' style='text-decoration: none; color: inherit;'>{$row['name']}</a></h3>
                                    <div class='shoe-meta'>
                                        <span class='shoe-brand'>{$row['brand']}</span>
                                        " . (!empty($row['original_price']) && $row['original_price'] > $row['price'] ? 
                                          "<span class='shoe-price' style='display:flex; flex-direction:column; align-items:flex-end; line-height:1.2;'>
                                            <span style='text-decoration:line-through; color:#95a5a6; font-size:1rem; font-weight:600;'>Rs {$row['original_price']}</span>
                                            <span>Rs {$row['price']}</span>
                                          </span>" 
                                          : "<span class='shoe-price'>Rs {$row['price']}</span>") . "
                                    </div>
                                    <span class='shoe-status {$statusClass}'>{$statusText}</span>
                                </div>
                              </div>";
                        $rank++;
                    }
                } else {
                    echo "<p style='text-align: center; grid-column: 1 / -1; font-size: 1.2rem; color: #7f8c8d;'>No best sellers found.</p>";
                }

                $connection->close();
                ?>
            </div>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel .list .item');
        const thumbnails = document.querySelectorAll('.carousel .thumbnail .item');
        const dots = document.querySelectorAll('.carousel .progress .dot');
        const nextBtn = document.getElementById('next');
        const prevBtn = document.getElementById('prev');

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active', 'prev');
                if (i === index) {
                    slide.classList.add('active');
                } else if (i < index) {
                    slide.classList.add('prev');
                }
            });

            thumbnails.forEach((thumb, i) => {
                thumb.classList.toggle('active', i === index);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }

        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        let autoPlayInterval = setInterval(nextSlide, 6000);

        const carousel = document.querySelector('.carousel');
        carousel.addEventListener('mouseenter', () => {
            clearInterval(autoPlayInterval);
        });

        carousel.addEventListener('mouseleave', () => {
            autoPlayInterval = setInterval(nextSlide, 6000);
        });

        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            }
        });

        let startX = 0;
        let endX = 0;

        carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        carousel.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = startX - endX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.shoe').forEach(shoe => {
            observer.observe(shoe);
        });
    </script>
</body>
</html>
<?php include('footer.php'); ?>
