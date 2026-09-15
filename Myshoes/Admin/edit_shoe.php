<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:index.php?err=1');
    exit();
}

require_once __DIR__ . '/../classes/Product.php';
$productModel = new Product();

$allowed_categories = [
    'shoes' => 'Shoe',
    'socks' => 'Socks',
    'shoe_care' => 'Shoe Care'
];

// Get shoe ID from URL
if (isset($_GET['shoe_id']) && is_numeric($_GET['shoe_id'])) {
    $shoe_id = $_GET['shoe_id'];
    $row = $productModel->findById($shoe_id);
    if (!$row) {
        $row = [];
    }
} else {
    header('location:list_shoes.php?category=shoes&msg=1');
    exit();
}

// Check if form is submitted
if(isset($_POST['btnUpdate'])){
    $err = [];

    if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
        $name = $_POST['name'];
    }else{
        $err['name'] = 'Please enter name';
    }

    if(isset($_POST['brand']) && !empty($_POST['brand']) && trim($_POST['brand'])){
        $brand = $_POST['brand'];
    }else{
        $err['brand'] = 'Please enter brand';
    }

    if(isset($_POST['price']) && !empty($_POST['price']) && trim($_POST['price'])){
        $price = $_POST['price'];
    }else{
        $err['price'] = 'Please enter price';
    }

    $original_price = !empty($_POST['original_price']) ? $_POST['original_price'] : null;

    if(isset($_POST['description']) && !empty($_POST['description']) && trim($_POST['description'])){
        $description = $_POST['description'];
    }else{
        $err['description'] = 'Please enter description';
    }

    $category = isset($_POST['category']) ? $_POST['category'] : 'shoes';
    if (!array_key_exists($category, $allowed_categories)) {
        $err['category'] = 'Please select a valid category';
        $category = 'shoes';
    }

    $sub_category = isset($_POST['sub_category']) ? $_POST['sub_category'] : 'Running';
    $sizes = isset($_POST['sizes']) ? $_POST['sizes'] : '';

    $status = $_POST['status'];
    if($status != '1' && $status != '0'){
        $err['status'] = 'Please select a valid status';
    }

    $simage = isset($row['image_col']) ? $row['image_col'] : '';

    if (isset($_FILES['simage']) && $_FILES['simage']['error'] == 0) {
        if ($_FILES['simage']['size'] < 5242880) {
            $filetype = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($_FILES['simage']['type'], $filetype)) {
                $filename = uniqid() . '_' . $_FILES['simage']['name'];
                $filepath = '../shoeImage/' . $filename;
                if (move_uploaded_file($_FILES['simage']['tmp_name'], $filepath)) {
                    $simage = $filename;
                } else {
                    $err['img'] = 'Upload Failed';
                }
            } else {
                $err['img'] = 'File type must be JPG, PNG, or WEBP';
            }
        } else {
            $err['img'] = 'File size must be less than 5MB';
        }
    }

    if (count($err) == 0){
        $updated_by = $_SESSION['admin_id'];

        if ($productModel->update($shoe_id, $name, $brand, $price, $original_price, $description, $status, $simage, $category, $sub_category, $sizes, $updated_by)) {
            $success = 'Product details updated successfully';
            // Update row array with new data so the form shows the latest values
            $row['name'] = $name;
            $row['brand'] = $brand;
            $row['price'] = $price;
            $row['original_price'] = $original_price;
            $row['description'] = $description;
            $row['status'] = $status;
            $row['category'] = $category;
            $row['sub_category'] = $sub_category;
            $row['sizes'] = $sizes;
            $row['image_col'] = $simage;
        } else {
            $error = 'Product details update failed: ' . $productModel->getLastError();
        }
    }
}

$current_category = isset($category) ? $category : (isset($row['category']) && array_key_exists($row['category'], $allowed_categories) ? $row['category'] : 'shoes');
$current_sub_category = isset($sub_category) ? $sub_category : (isset($row['sub_category']) ? $row['sub_category'] : 'Running');
$current_sizes = isset($sizes) ? $sizes : (isset($row['sizes']) ? $row['sizes'] : '7,8,9,10,11,12');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Shoe - Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .form-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .form-content {
            padding: 40px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 500;
            animation: fadeIn 0.5s ease;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px 20px;
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #6c757d;
            font-weight: 500;
        }

        .file-upload:hover .file-upload-label {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
            color: #667eea;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .radio-item:hover {
            border-color: #667eea;
            background: white;
        }

        .radio-item input[type="radio"] {
            width: auto;
            margin: 0;
            accent-color: #667eea;
        }

        .radio-item.active {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            font-weight: 500;
        }

        .btn {
            width: 100%;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            transform: translateX(-5px);
            opacity: 0.8;
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
            padding-left: 40px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .form-content {
                padding: 30px 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'admin_menu.php'; ?>
    <div class="container">
        <a href="list_shoes.php?category=<?php echo urlencode($current_category); ?>" class="back-link">
            ← Back to Product List
        </a>
        
        <div class="page-header">
            <h1>Edit Product</h1>
            <p>Update shoe information and details</p>
        </div>

        <div class="form-container">
            <div class="form-header">
                <h2>Edit Shoe Details</h2>
                <p>Modify the information below to update the shoe</p>
            </div>

            <div class="form-content">
                <?php if(isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="shoes" <?php echo $current_category === 'shoes' ? 'selected' : ''; ?>>Shoes</option>
                                <option value="socks" <?php echo $current_category === 'socks' ? 'selected' : ''; ?>>Socks</option>
                                <option value="shoe_care" <?php echo $current_category === 'shoe_care' ? 'selected' : ''; ?>>Shoe Care</option>
                            </select>
                            <?php if (isset($err['category'])): ?>
                                <div class="error-message"><?php echo $err['category']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($row['name']) ? $row['name'] : ''; ?>" required>
                            <?php if (isset($err['name'])): ?>
                                <div class="error-message"><?php echo $err['name']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="sub_category">Sub Category</label>
                            <select id="sub_category" name="sub_category" class="form-control">
                                <option value="Running" <?php echo $current_sub_category === 'Running' ? 'selected' : ''; ?>>Running</option>
                                <option value="Football" <?php echo $current_sub_category === 'Football' ? 'selected' : ''; ?>>Football</option>
                                <option value="Lifestyle" <?php echo $current_sub_category === 'Lifestyle' ? 'selected' : ''; ?>>Lifestyle</option>
                                <option value="Training" <?php echo $current_sub_category === 'Training' ? 'selected' : ''; ?>>Training</option>
                                <option value="Originals" <?php echo $current_sub_category === 'Originals' ? 'selected' : ''; ?>>Originals</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sizes">Available Sizes (Comma separated)</label>
                            <input type="text" id="sizes" name="sizes" class="form-control" placeholder="e.g., 7,8,9,10" value="<?php echo htmlspecialchars($current_sizes); ?>">
                        </div>

                        <div class="form-group">
                            <label for="brand">Brand</label>
                            <input type="text" name="brand" id="brand" class="form-control" value="<?php echo isset($row['brand']) ? $row['brand'] : ''; ?>" required>
                            <?php if (isset($err['brand'])): ?>
                                <div class="error-message"><?php echo $err['brand']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="price">Current Sale Price</label>
                            <div class="price-input">
                                <input type="number" name="price" id="price" class="form-control" value="<?php echo isset($row['price']) ? $row['price'] : ''; ?>" step="0.01" min="0" required>
                            </div>
                            <?php if (isset($err['price'])): ?>
                                <div class="error-message"><?php echo $err['price']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="original_price">Original Price (Optional - for SALE items)</label>
                            <div class="price-input">
                                <input type="number" name="original_price" id="original_price" class="form-control" value="<?php echo isset($row['original_price']) ? $row['original_price'] : ''; ?>" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="simage">Update Image</label>
                            <div class="file-upload">
                                <input type="file" name="simage" id="simage" accept="image/*">
                                <div class="file-upload-label">
                                    📷 Choose New Image File
                                </div>
                            </div>
                            <?php if (isset($err['img'])): ?>
                                <div class="error-message"><?php echo $err['img']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" required><?php echo isset($row['description']) ? $row['description'] : ''; ?></textarea>
                        <?php if (isset($err['description'])): ?>
                            <div class="error-message"><?php echo $err['description']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Product Status</label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" name="status" value="1" id="active" <?php echo $row['status'] == 1 ? 'checked' : ''; ?>>
                                <label for="active">Active</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="status" value="0" id="inactive" <?php echo $row['status'] == 0 ? 'checked' : ''; ?>>
                                <label for="inactive">Inactive</label>
                            </div>
                        </div>
                        <?php if (isset($err['status'])): ?>
                            <div class="error-message"><?php echo $err['status']; ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" name="btnUpdate" class="btn">
                        Update Product Details
                    </button>
                </form>
                <p style="margin-top:16px; text-align:center;">
                    <a href="list_shoes.php?category=<?php echo urlencode($current_category); ?>" style="color:#667eea; font-weight:600; text-decoration:none;">← Back to list</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // File upload preview
        document.getElementById('simage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const label = document.querySelector('.file-upload-label');
            
            if (file) {
                label.innerHTML = `📷 ${file.name}`;
                label.style.color = '#667eea';
            } else {
                label.innerHTML = '📷 Choose New Image File';
                label.style.color = '#6c757d';
            }
        });

        // Radio button styling
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
    </script>
</body>
</html>
