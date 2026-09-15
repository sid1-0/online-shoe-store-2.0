<?php
session_start();

if(!isset($_SESSION['admin_id']))
{
    header('location:index.php?err=1');
    exit();
}

$allowed_categories = [
    'shoes' => 'Shoe',
    'socks' => 'Socks',
    'shoe_care' => 'Shoe Care'
];

$category = isset($_POST['category']) ? $_POST['category'] : (isset($_GET['category']) ? $_GET['category'] : 'shoes');
if (!array_key_exists($category, $allowed_categories)) {
    $category = 'shoes';
}
$category_label = $allowed_categories[$category];

$success_message = '';
$error_message = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $original_price = !empty($_POST['original_price']) ? $_POST['original_price'] : null;
    $description = $_POST['description'];
    $status = $_POST['status'];
    $sub_category = isset($_POST['sub_category']) ? $_POST['sub_category'] : 'Running';
    $sizes = isset($_POST['sizes']) ? $_POST['sizes'] : '7,8,9,10,11,12';
    $created_at = date('Y-m-d H:i:s');
    $created_by = $_SESSION['admin_id'];

    // Validate form data (you can add more validation as needed)
    if (empty($name) || empty($brand) || empty($price) || empty($description)) {
        $error_message = "All fields are required";
    } else {
        // Check if image is uploaded
        if ($_FILES['photo']['error'] == 0) {
            if ($_FILES['photo']['size'] < 5242880) { // 5MB limit
                $filetype = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($_FILES['photo']['type'], $filetype)) {
                    $filename = uniqid() . '_' . $_FILES['photo']['name'];
                    $filepath = '../shoeImage/' . $filename;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
                        $vimage = $filename; // Store filename in $vimage variable
                    } else {
                        $error_message = 'Upload Failed';
                    }
                } else {
                    $error_message = 'File type must be JPG, PNG, or WEBP';
                }
            } else {
                $error_message = 'File size must be less than 5MB';
            }
        } else {
            $error_message = 'Please choose an image file';
        }

        if (empty($error_message)) {
            require_once __DIR__ . '/../classes/Product.php';
            $productModel = new Product();
            $newId = $productModel->create($name, $brand, $price, $original_price, $description, $vimage, $status, $category, $sub_category, $sizes, $created_by);

            if ($newId) {
                $success_message = $category_label . " added successfully!";
            } else {
                $error_message = "Error: " . $productModel->getLastError();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Shoe - Premium Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            padding: 20px 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 30px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .back-link:hover {
            transform: translateX(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
            color: white;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 15px;
            text-shadow: 2px 2px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-header p {
            font-size: 1.2rem;
            opacity: 0.95;
            font-weight: 400;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 30px;
            box-shadow: 
                0 30px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            overflow: hidden;
            animation: slideUp 0.8s ease;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .form-header {
            background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .form-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .form-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }

        .form-header p {
            opacity: 0.95;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .form-content {
            padding: 50px;
        }

        .alert {
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.5s ease;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.05rem;
            position: relative;
        }

        .form-group label::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 30px;
            height: 3px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .form-control {
            width: 100%;
            padding: 18px 25px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: inherit;
            font-weight: 400;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 140px;
            line-height: 1.6;
        }

        .file-upload {
            position: relative;
            display: block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 2;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 40px 25px;
            border: 3px dashed #e9ecef;
            border-radius: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            cursor: pointer;
            transition: all 0.3s ease;
            color: #6c757d;
            font-weight: 500;
            min-height: 160px;
        }

        .file-upload:hover .file-upload-label {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            color: #667eea;
            transform: translateY(-3px);
        }

        .file-icon {
            font-size: 3rem;
            opacity: 0.7;
        }

        .file-text {
            text-align: center;
        }

        .file-text .main {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .file-text .sub {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            flex: 1;
            justify-content: center;
            font-weight: 500;
        }

        .radio-item:hover {
            border-color: #667eea;
            background: white;
            transform: translateY(-2px);
        }

        .radio-item input[type="radio"] {
            width: 20px;
            height: 20px;
            margin: 0;
            accent-color: #667eea;
        }

        .radio-item.active {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 20px 40px;
            border: none;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .price-input {
            position: relative;
        }

        .price-input::before {
            content: 'Rs ';
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-weight: 600;
            z-index: 1;
            pointer-events: none;
        }

        .price-input .form-control {
            padding-left: 50px;
        }

        /* Enhanced responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .page-header h1 {
                font-size: 2.2rem;
            }

            .form-content {
                padding: 30px 25px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .radio-group {
                flex-direction: column;
                gap: 15px;
            }

            .file-upload-label {
                padding: 30px 20px;
                min-height: 140px;
            }
        }

        /* Loading animation */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .btn {
            background: #6c757d;
        }

        /* Success state */
        .success-state {
            text-align: center;
            padding: 40px;
        }

        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php require_once 'admin_menu.php'; ?>
    <div class="container">
        <a href="dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>
        
        <div class="page-header">
            <h1>Product Management</h1>
            <p>Add shoes, socks, or sneaker cleaner to your inventory</p>
        </div>

        <div class="form-container">
            <div class="form-header">
                <h2>Add New <?php echo htmlspecialchars($category_label); ?></h2>
                <p>Fill in the details below to add a new product to your store</p>
            </div>

            <div class="form-content">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <span>✅</span>
                        <?php echo $success_message; ?>
                        <div style="margin-top:8px;">
                            <a href="list_shoes.php?category=<?php echo urlencode($category); ?>" style="color:inherit; font-weight:700;">View <?php echo htmlspecialchars($allowed_categories[$category] === 'Shoe' ? 'Shoes' : $category_label); ?> list →</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <span>❌</span>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?category=<?php echo urlencode($category); ?>" method="post" enctype="multipart/form-data" id="shoeForm"  >
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="shoes" <?php echo $category === 'shoes' ? 'selected' : ''; ?>>Shoes</option>
                                <option value="socks" <?php echo $category === 'socks' ? 'selected' : ''; ?>>Socks</option>
                                <option value="shoe_care" <?php echo $category === 'shoe_care' ? 'selected' : ''; ?>>Shoe Care</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="e.g., Air Max 270 React" required>
                        </div>

                        <div class="form-group">
                            <label for="sub_category">Sub Category</label>
                            <select id="sub_category" name="sub_category" class="form-control">
                                <option value="Running">Running</option>
                                <option value="Football">Football</option>
                                <option value="Lifestyle">Lifestyle</option>
                                <option value="Training">Training</option>
                                <option value="Originals">Originals</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sizes">Available Sizes (Comma separated)</label>
                            <input type="text" id="sizes" name="sizes" class="form-control" placeholder="e.g., 7,8,9,10" value="7,8,9,10,11,12">
                        </div>

                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" id="brand" name="brand" class="form-control" placeholder="e.g., Nike, Adidas, Puma" required>
                        </div>

                        <div class="form-group">
                            <label for="price">Current Sale Price</label>
                            <div class="price-input">
                                <input type="number" id="price" name="price" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="original_price">Original Price (Optional - for SALE items)</label>
                            <div class="price-input">
                                <input type="number" id="original_price" name="original_price" class="form-control" placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="photo">Product Image</label>
                            <div class="file-upload">
                                <input type="file" name="photo" id="photo" accept="image/*" required>
                                <div class="file-upload-label">
                                    <div class="file-icon">📷</div>
                                    <div class="file-text">
                                        <div class="main">Choose Image File</div>
                                        <div class="sub">JPG, PNG, or WEBP (Max 5MB)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Product Description</label>
                        <textarea id="description" name="description" class="form-control" placeholder="Describe the product features, materials, and unique selling points..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Product Status</label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" name="status" value="1" id="active">
                                <label for="active">🟢 Active</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="status" value="0" id="inactive" checked>
                                <label for="inactive">🔴 Inactive</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="submit" class="btn">
                        Add <?php echo htmlspecialchars($category_label); ?> to Inventory
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Enhanced file upload preview
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.querySelector('.file-upload-label');
            
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                label.innerHTML = `
                    <div class="file-icon">✅</div>
                    <div class="file-text">
                        <div class="main">${file.name}</div>
                        <div class="sub">Size: ${fileSize} MB</div>
                    </div>
                `;
                label.style.borderColor = '#28a745';
                label.style.color = '#28a745';
            } else {
                label.innerHTML = `
                    <div class="file-icon">📷</div>
                    <div class="file-text">
                        <div class="main">Choose Image File</div>
                        <div class="sub">JPG, PNG, or WEBP (Max 5MB)</div>
                    </div>
                `;
                label.style.borderColor = '#e9ecef';
                label.style.color = '#6c757d';
            }
        });

        // Enhanced radio button styling
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.radio-item').forEach(item => {
                    item.classList.remove('active');
                });
                this.closest('.radio-item').classList.add('active');
            });
        });

        // Set initial active state
        document.addEventListener('DOMContentLoaded', function() {
            const checkedRadio = document.querySelector('input[type="radio"]:checked');
            if (checkedRadio) {
                checkedRadio.closest('.radio-item').classList.add('active');
            }
        });

        // Form submission loading state
        document.getElementById('shoeForm').addEventListener('submit', function() {
            this.classList.add('loading');
            const btn = this.querySelector('.btn');
            btn.innerHTML = 'Adding Shoe... ⏳';
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
