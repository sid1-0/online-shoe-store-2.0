<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('location:index.php?err=1');
    exit();
}

require_once 'connection.php';

$success_message = '';
$error_message = '';

// Handle add coupon
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_coupon'])) {
    $code = strtoupper(trim($_POST['code']));
    $discount = floatval($_POST['discount']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($code) || $discount <= 0 || $discount > 100) {
        $error_message = "Invalid coupon code or discount percentage.";
    } else {
        $stmt = $connection->prepare("INSERT INTO coupons (code, discount_percentage, is_active) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $code, $discount, $is_active);
        
        if ($stmt->execute()) {
            $success_message = "Coupon added successfully!";
        } else {
            if ($connection->errno == 1062) { // Duplicate entry
                $error_message = "Coupon code already exists.";
            } else {
                $error_message = "Error adding coupon: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

// Handle delete/toggle
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == 'toggle') {
        $stmt = $connection->prepare("UPDATE coupons SET is_active = NOT is_active WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: coupons.php");
        exit();
    } elseif ($_GET['action'] == 'delete') {
        $stmt = $connection->prepare("DELETE FROM coupons WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: coupons.php");
        exit();
    }
}

// Fetch all coupons
$coupons = [];
$result = $connection->query("SELECT * FROM coupons ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $coupons[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons - Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            padding: 20px 0;
            color: #333;
        }

        .container {
            max-width: 1000px;
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
            font-weight: 800;
        }

        .content-box {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-family: inherit;
        }

        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-danger {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-inactive { background: #f8d7da; color: #721c24; }

        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php require_once 'admin_menu.php'; ?>
    <div class="container">
        <div class="page-header">
            <h1>Manage Coupons</h1>
            <p>Create and track promotional discount codes</p>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="content-box">
            <h2>Add New Coupon</h2>
            <form action="" method="POST" style="margin-top: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Coupon Code (e.g., DASHAIN20)</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Discount Percentage (%)</label>
                        <input type="number" name="discount" class="form-control" min="1" max="100" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" checked> Active immediately
                    </label>
                </div>
                <button type="submit" name="add_coupon" class="btn">Create Coupon</button>
            </form>
        </div>

        <div class="content-box">
            <h2>Active & Past Coupons</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($coupons) > 0): ?>
                        <?php foreach ($coupons as $c): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($c['code']); ?></strong></td>
                            <td><?php echo number_format($c['discount_percentage'], 0); ?>%</td>
                            <td>
                                <?php if ($c['is_active']): ?>
                                    <span class="badge badge-active">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($c['created_at'])); ?></td>
                            <td>
                                <a href="coupons.php?action=toggle&id=<?php echo $c['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 0.9rem;">Toggle</a>
                                <a href="coupons.php?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.9rem;" onclick="return confirm('Are you sure you want to delete this coupon?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No coupons found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
