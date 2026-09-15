<?php
//calling function to get admin name
require_once'function.php';
if(is_numeric($_GET['id'])){
	$id=$_GET['id'];
}else{
	header('location:list_shoes_cat.php?msg=1');
}
$id=$_GET['id'];
require_once 'connection.php';

//query to select data from table
$sql="SELECT * from shoe_categories where id=$id";

//execute query 
$result=$connection->query($sql);
$data=[];

//checking if the databse is empty for fetching the data
if($result->num_rows > 0){
	//fetching data from result object using while loop
	$row = $result->fetch_assoc();
	}else {
	$row=[];
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Shoe Category Details</title>
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
            max-width: 900px;
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
            text-transform: capitalize;
        }
        
        h2:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .category-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            padding: 30px;
        }
        
        .category-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .category-name {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
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
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .details-table th {
            text-align: left;
            padding: 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            font-weight: 500;
            color: #555;
            width: 40%;
        }
        
        .details-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #666;
        }
        
        .details-table tr:last-child th,
        .details-table tr:last-child td {
            border-bottom: none;
        }
        
        .action-buttons {
            display: flex;
            justify-content: flex-start;
            margin-top: 30px;
            gap: 15px;
        }
        
        .back-button, .edit-button {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .back-button {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
        }
        
        .edit-button {
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
            color: white;
        }
        
        .back-button:hover, .edit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
        
        .info-row {
            display: flex;
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 500;
            color: #555;
            width: 150px;
        }
        
        .info-value {
            color: #666;
            flex: 1;
        }
        
        .category-icon {
            font-size: 24px;
            margin-right: 10px;
            color: #6a11cb;
        }
        
        @media (max-width: 768px) {
            .category-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .details-table th, 
            .details-table td {
                padding: 12px 10px;
            }
            
            .action-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .back-button, .edit-button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
	<?php require_once'admin_menu.php';?>
	<div class="wrapper">
		<h2>Shoe Category Details</h2>
		
		<?php if (!empty($row)) { ?>
		    <div class="category-card">
		        <div class="category-header">
		            <div class="category-name">
		                <span class="category-icon">📁</span>
		                <?php echo htmlspecialchars($row['name']); ?>
		            </div>
		            <div class="status-badge <?php echo $row['status'] == 1 ? 'status-active' : 'status-inactive'; ?>">
		                <?php echo $row['status'] == 1 ? 'Active' : 'Inactive'; ?>
		            </div>
		        </div>
		        
		        <table class="details-table">
		            <tr>
		                <th>Category ID</th>
		                <td>#<?php echo $row['id']; ?></td>
		            </tr>
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
		        
		        <div class="action-buttons">
		            <a href="list_shoes_cat.php" class="back-button">Back to Categories</a>
		            <a href="edit_shoes_cat.php?id=<?php echo $row['id']; ?>" class="edit-button">Edit Category</a>
		        </div>
		    </div>
		<?php } else { ?>
		    <div class="no-record">
		        <div class="no-record-icon">🔍</div>
		        <div class="no-record-text">Invalid category information</div>
		        <a href="list_shoes_cat.php" class="back-button">Back to Categories</a>
		    </div>
		<?php } ?>
	</div>
</body>
</html>
