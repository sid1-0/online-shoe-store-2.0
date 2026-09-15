<?php
session_start();
if(!isset($_SESSION['admin_id']))
{
    header('location:index.php?err=1');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Products</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 28px;
            position: relative;
            padding-bottom: 15px;
        }

        h2:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ff6f61, #de6262);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Shoes grid styles */
        .shoes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            grid-gap: 30px;
        }

        .shoe {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .shoe:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .shoe-image-container {
            height: 220px;
            overflow: hidden;
            position: relative;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        }

        .shoe-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .shoe:hover .shoe-image {
            transform: scale(1.05);
        }

        .shoe-details {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .shoe-details h3 {
            margin: 0 0 10px;
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }

        .shoe-details p {
            margin: 0;
            margin-bottom: 8px;
            color: #6c757d;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .shoe-details p strong {
            min-width: 90px;
            display: inline-block;
            color: #495057;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 5px;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            margin-top: auto;
        }

        .button-container a {
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .edit-btn {
            background-color: #e9ecef;
            color: #495057;
        }

        .edit-btn:hover {
            background-color: #dee2e6;
        }

        .view-btn {
            background-color: #cce5ff;
            color: #0d6efd;
        }

        .view-btn:hover {
            background-color: #b8daff;
        }

        .delete-btn {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .delete-btn:hover {
            background-color: #f5c2c7;
        }

        .price {
            font-weight: 600;
            color: #ff6f61 !important;
            font-size: 16px !important;
        }

        .brand {
            display: inline-block;
            background-color: #e9ecef;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .no-shoes {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            color: #6c757d;
            font-size: 16px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .shoes {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                grid-gap: 20px;
            }
            
            .container {
                padding: 0 15px;
                margin: 20px auto;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .shoe-image-container {
                height: 180px;
            }
        }

        @media (max-width: 480px) {
            .shoes {
                grid-template-columns: 1fr;
            }
            
            .button-container {
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .button-container a {
                flex: 1;
                min-width: 80px;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'admin_menu.php' ?>
    <div class="container">
        <?php
        $allowed_categories = ['shoes' => 'Shoes', 'socks' => 'Socks', 'shoe_care' => 'Shoe Care'];
        $category = isset($_GET['category']) ? $_GET['category'] : 'shoes';
        if (!array_key_exists($category, $allowed_categories)) {
            $category = 'shoes';
        }
        $section_title = $allowed_categories[$category];
        ?>
        <h2><?php echo htmlspecialchars($section_title); ?></h2>
        <div style="display:flex; justify-content:space-between; align-items:center; margin:-15px 0 25px;">
            <a href="add_shoes.php?category=<?php echo urlencode($category); ?>" style="color:#667eea; font-weight:600; text-decoration:none;">+ Add <?php echo htmlspecialchars($section_title); ?></a>
            
            <div style="display:flex; align-items:center; gap:10px;">
                <label for="statusFilter" style="font-weight:600; color:#495057;">Filter by Status:</label>
                <select id="statusFilter" style="padding:8px 12px; border:1px solid #ced4da; border-radius:6px; font-family:'Poppins', sans-serif;">
                    <option value="all">All</option>
                    <option value="active">Available (Active)</option>
                    <option value="inactive">Unavailable (Inactive)</option>
                </select>
            </div>
        </div>
        <div class="shoes">
            <?php
            require_once __DIR__ . '/../classes/Product.php';
            $productModel = new Product();
            $products = $productModel->getByCategory($category);

            if (count($products) > 0) {
                foreach ($products as $row) {
                    $statusClass = $row["status"] == 1 ? "status-active" : "status-inactive";
                    $statusText = $row["status"] == 1 ? "Active" : "Inactive";
                    
                    echo "<div class='shoe'>";
                    echo "<div class='shoe-image-container'>";
                    echo "<img class='shoe-image' src='../shoeImage/" . rawurlencode($row["image_col"]) . "' alt='Product Image'>";
                    echo "</div>";
                    echo "<div class='shoe-details'>";
                    echo "<span class='brand'>" . htmlspecialchars($row["brand"]) . "</span>";
                    echo "<h3>" . htmlspecialchars($row["name"]) . "</h3>";
                    echo "<p class='price'><strong>Price:</strong> Rs" . htmlspecialchars($row["price"]) . "</p>";
                    echo "<p><strong>Description:</strong> " . htmlspecialchars(strlen($row["description"]) > 50 ? substr($row["description"], 0, 50) . "..." : $row["description"]) . "</p>";
                    echo "<p><strong>Status:</strong> <span class='status-badge " . $statusClass . "'>" . $statusText . "</span></p>";
                    echo "</div>";
                    echo "<div class='button-container'>";
                    echo "<a href='edit_shoe.php?shoe_id=" . $row['shoe_id'] . "&category=" . urlencode($category) . "' class='edit-btn'>Edit</a>";
                    echo "<a href='view_shoes.php?shoe_id=" . $row['shoe_id'] . "' target='_blank' class='view-btn'>View</a>";
                    echo "<a href='delete_shoe.php?shoe_id=" . $row['shoe_id'] . "&category=" . urlencode($category) . "' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this item?')\">Delete</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='no-shoes'>No " . htmlspecialchars(strtolower($section_title)) . " found. Add some to get started!</div>";
            }
            ?>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('statusFilter').addEventListener('change', function() {
                var filter = this.value;
                var shoes = document.querySelectorAll('.shoe');
                shoes.forEach(function(shoe) {
                    var isActive = shoe.querySelector('.status-active') !== null;
                    if (filter === 'all') {
                        shoe.style.display = 'flex';
                    } else if (filter === 'active' && isActive) {
                        shoe.style.display = 'flex';
                    } else if (filter === 'inactive' && !isActive) {
                        shoe.style.display = 'flex';
                    } else {
                        shoe.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
