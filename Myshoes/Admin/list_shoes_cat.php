<?php
require_once 'connection.php';

//query to select data from table
$sql="SELECT id,name,status from shoe_categories order by name";

//execute query 
$result=$connection->query($sql);
$data=[];

//checking if the databse is empty for fetching the data
if($result->num_rows > 0){
	//fetching data from result object using while loop
	while($row = $result->fetch_assoc()){
		array_push($data, $row);

	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Shoe Categories | My Shoe Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style type="text/css">
		/* Global styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Wrapper styles */
        .wrapper {
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

        /* Message styles */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
        }

        .error_msg {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .done_msg {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Table styles */
        .list_shoes_cat {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .list_shoes_cat th,
        .list_shoes_cat td {
            padding: 15px;
            text-align: left;
        }

        .list_shoes_cat th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
        }

        .list_shoes_cat tr {
            transition: background-color 0.3s ease;
        }

        .list_shoes_cat tr:hover {
            background-color: #f8f9fa;
        }

        .list_shoes_cat td {
            border-bottom: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }

        .list_shoes_cat tr:last-child td {
            border-bottom: none;
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Action column styles */
        .action_col {
            display: flex;
            gap: 10px;
        }

        .action_col a {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .edit {
            background-color: #e9ecef;
            color: #495057;
        }

        .edit:hover {
            background-color: #dee2e6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .view {
            background-color: #cce5ff;
            color: #0d6efd;
        }

        .view:hover {
            background-color: #b8daff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .delete {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .delete:hover {
            background-color: #f5c2c7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        /* No record styles */
        .no_record td {
            text-align: center;
            padding: 30px;
            font-style: italic;
            color: #6c757d;
        }

        /* Add category button */
        .add-category {
            display: inline-block;
            padding: 12px 25px;
            background-color: #ff6f61;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .add-category:hover {
            background-color: #e8635b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .wrapper {
                padding: 0 15px;
                margin: 20px auto;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .action_col {
                flex-direction: column;
                gap: 5px;
            }
            
            .action_col a {
                text-align: center;
            }
            
            .list_shoes_cat th,
            .list_shoes_cat td {
                padding: 10px;
            }
        }

        @media (max-width: 576px) {
            .list_shoes_cat {
                display: block;
                overflow-x: auto;
            }
        }
	</style>
	 <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<?php require_once'admin_menu.php';?>
	<div class="wrapper">
		<h2>Shoe Categories</h2>
		<div>
			<?php if(isset($_GET['msg']) && $_GET['msg'] == 1 ){?>
				<div class="message error_msg">Invalid Request</div>
			<?php }?>

			<?php if(isset($_GET['msg']) && $_GET['msg'] == 2 ){?>
				<div class="message done_msg">Category deleted successfully</div>
			<?php }?>

			<?php if(isset($_GET['msg']) && $_GET['msg'] == 3 ){?>
				<div class="message error_msg">Request failed</div>
			<?php }?>

            <a href="add_shoes_cat.php" class="add-category">+ Add New Category</a>
            
			<table class="list_shoes_cat">
				<thead>
				<tr>
					<th>SN</th>
					<th>Name</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				</thead>
				<tbody>
					<?php if (count($data) > 0) {?>
						<?php foreach($data as $key => $record){?>
				<tr>
					<td><?php echo $key + 1 ?></td>		
					<td><?php echo $record['name'] ?></td>
					<td>
                        <?php if($record['status'] == 1) { ?>
                            <span class="status-badge status-active">Active</span>
                        <?php } else { ?>
                            <span class="status-badge status-inactive">Inactive</span>
                        <?php } ?>
                    </td>
					<td class="action_col">
						<a href="edit_shoes_cat.php?id=<?php echo $record['id'] ?>" class="edit">Edit</a>
						<a href="view_shoes_cat.php?id=<?php echo $record['id'] ?>" class="view" target="_blank">View</a>
						<a href="delete_shoe_catagory.php?id=<?php echo $record['id'];?>" class="delete" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
					</td>
				</tr>
			<?php } ?>
			<?php } else { ?>
				<tr class="no_record">
					<td colspan="4">No categories found in the database. Add a new category to get started.</td>
				</tr>
			<?php } ?>
				</tbody>
			</table>	
		</div>
	</div>
</body>
</html>
