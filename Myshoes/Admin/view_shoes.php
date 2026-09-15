<?php
//calling function to get admin name
require_once 'function.php';

// Check if ID is numeric
if (isset($_GET['shoe_id']) && is_numeric($_GET['shoe_id'])) {
    $id = $_GET['shoe_id'];
} else {
    // Redirect to shoe category listing page with error message
    header('location: list_shoes_cat.php?msg=1');
    exit();
}

require_once 'connection.php';

// Query to select data from table
$sql = "SELECT * FROM shoes WHERE shoe_id = $id";

// Execute query
$result = $connection->query($sql);
$data = [];

// Checking if the database has data for fetching the details
if ($result->num_rows > 0) {
    // Fetching data from result object
    $row = $result->fetch_assoc();
} else {
    $row = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shoe Details</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style type="text/css">
        /* Modern styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .wrapper {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        h2 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }
        
        h2:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .shoe-details-container {
            display: flex;
            flex-wrap: wrap;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .shoe-image {
            flex: 1;
            min-width: 300px;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .shoe-image img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .shoe-image img:hover {
            transform: scale(1.05);
        }
        
        .shoe-info {
            flex: 1;
            min-width: 300px;
            padding: 30px;
        }
        
        .shoe-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .shoe-brand {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .shoe-price {
            font-size: 22px;
            font-weight: 600;
            color: #ff6b6b;
            margin-bottom: 20px;
        }
        
        .shoe-description {
            margin-bottom: 25px;
            color: #666;
            line-height: 1.8;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 25px;
        }
        
        .status-active {
            background-color: #4CAF50;
            color: white;
        }
        
        .status-inactive {
            background-color: #F44336;
            color: white;
        }
        
        .details-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        
        .details-table th {
            text-align: left;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            font-weight: 500;
            color: #555;
            width: 40%;
        }
        
        .details-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            color: #666;
        }
        
        .details-table tr:last-child th,
        .details-table tr:last-child td {
            border-bottom: none;
        }
        
        .back-button {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
        
        .no-record {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .no-record-icon {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .no-record-text {
            font-size: 18px;
            color: #999;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .shoe-details-container {
                flex-direction: column;
            }
            
            .shoe-image, .shoe-info {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php require_once 'admin_menu.php'; ?>
<div class="wrapper">
    <h2>Shoe Details</h2>
    
    <?php if (!empty($row)) { ?>
        <div class="shoe-details-container">
            <div class="shoe-image">
                <?php if (!empty($row['image_col'])) { ?>
                    <img src="../shoeImage/<?php echo htmlspecialchars($row['image_col']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                <?php } else { ?>
                    <div class="no-image">No image available</div>
                <?php } ?>
            </div>
            
            <div class="shoe-info">
                <h3 class="shoe-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                <div class="shoe-brand">Brand: <?php echo htmlspecialchars($row['brand']); ?></div>
                <div class="shoe-price">Rs <?php echo number_format($row['price'], 2); ?></div>
                
                <div class="status-badge <?php echo $row['status'] == 1 ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo $row['status'] == 1 ? 'Active' : 'Inactive'; ?>
                </div>
                
                <p class="shoe-description"><?php echo htmlspecialchars($row['description']); ?></p>
                
                <table class="details-table">
                    <tr>
                        <th>Created By</th>
                        <td><?php echo getNameByAdminId($row['created_by']); ?></td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td><?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php if (!empty($row['updated_by'])) { ?>
                    <tr>
                        <th>Updated By</th>
                        <td><?php echo getNameByAdminId($row['updated_by']); ?></td>
                    </tr>
                    <?php } ?>
                    <?php if (!empty($row['updated_at'])) { ?>
                    <tr>
                        <th>Updated At</th>
                        <td><?php echo date('F j, Y, g:i a', strtotime($row['updated_at'])); ?></td>
                    </tr>
                    <?php } ?>
                </table>
                
                <a href="list_shoes.php" class="back-button">Back to Shoes List</a>
            </div>
        </div>
    <?php } else { ?>
        <div class="no-record">
            <div class="no-record-icon">🔍</div>
            <div class="no-record-text">No shoe found with this ID</div>
            <a href="list_shoes.php" class="back-button">Back to Shoes List</a>
        </div>
    <?php } ?>
</div>
</body>
</html>
