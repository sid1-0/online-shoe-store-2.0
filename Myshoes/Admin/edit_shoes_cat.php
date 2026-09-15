<?php
// Start session to access session variables
session_start();

// Check if form is submitted
if(isset($_POST['btnUpdate'])){
    // Assign errors to array
    $err = [];

    // Validate name field
    if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
        $name = $_POST['name'];
    }else{
        $err['name'] = 'Please enter name';
    }

    // Validate status field
    $status = $_POST['status'];
    if($status != '1' && $status != '0'){
        $err['status'] = 'Please select a valid status';
    }

    // Proceed if there are no errors
    if (count($err) == 0){
        require_once 'connection.php';
        $id = $_GET['id']; // Get category ID from URL
        $updated_at = date('Y-m-d H:i:s');
        $updated_by = $_SESSION['admin_id']; // Assuming admin ID is stored in session

        // Prepare and execute SQL statement to update category
        $sql = "UPDATE shoe_categories SET name='$name', status='$status', updated_by='$updated_by', updated_at='$updated_at' WHERE id=$id";
        $connection->query($sql);

        // Check if category was updated successfully
        if($connection->affected_rows == 1 ){
            $success = 'Category updated successfully';
        }else{
            $error = 'Category update failed';
        }
    }
}

// Get category ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    // Redirect if category ID is missing or invalid
    header('location:list_shoes_cat.php?msg=1');
    exit(); // Add exit to stop further execution
}

require_once 'connection.php';

// Query to select data from table
$sql = "SELECT id, name, status FROM shoe_categories WHERE id=$id";

// Execute query
$result = $connection->query($sql);
$row = [];

// Checking if the database returned exactly one row
if ($result->num_rows == 1) {
    // Fetching data from result object
    $row = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Shoe Category</title>
    <link rel="stylesheet" type="text/css" href="style.css">
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
            max-width: 800px;
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

        /* Form styles */
        .edit_shoes_form {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin: 0 auto;
            max-width: 500px;
        }

        fieldset {
            border: none;
            padding: 0;
            margin: 0;
        }

        legend {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
            width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #495057;
            font-size: 15px;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 15px;
            color: #495057;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input[type="text"]:focus {
            border-color: #ff6f61;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 111, 97, 0.1);
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .radio-option input[type="radio"] {
            margin-right: 8px;
            cursor: pointer;
            accent-color: #ff6f61;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .button-group input[type="submit"],
        .button-group input[type="reset"] {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
        }

        .button-group input[type="submit"] {
            background-color: #ff6f61;
            color: white;
            flex: 1;
        }

        .button-group input[type="submit"]:hover {
            background-color: #e8635b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .button-group input[type="reset"] {
            background-color: #e9ecef;
            color: #495057;
            flex: 1;
        }

        .button-group input[type="reset"]:hover {
            background-color: #dee2e6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Message styles */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
        }

        .error_message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success_message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: block;
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
            
            .edit_shoes_form {
                padding: 20px;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<?php require_once 'admin_menu.php'; ?>
<div class="wrapper">
    <h2>Edit Shoe Category</h2>
    <div>
        <form action="" method="POST" class="edit_shoes_form">
            <fieldset>
                <?php if (isset($error)) : ?>
                    <div class="message error_message"><?php echo $error ?></div>
                <?php endif; ?>
                <?php if (isset($success)) : ?>
                    <div class="message success_message"><?php echo $success ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['err']) && $_GET['err'] == 1) : ?>
                    <div class="message error_message">Please login to continue</div>
                <?php endif; ?>
                <legend>Category Details</legend>
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" name="name" id="name" value="<?php echo isset($row['name']) ? $row['name'] : ''; ?>" placeholder="Enter category name">
                    <?php if (isset($err['name'])) : ?>
                        <span class="form-error"><?php echo $err['name']; ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="status" value="1" <?php echo $row['status'] == 1 ? 'checked' : ''; ?>>
                            Active
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="status" value="0" <?php echo $row['status'] == 0 ? 'checked' : ''; ?>>
                            Inactive
                        </label>
                    </div>
                </div>
                <div class="button-group">
                    <input type="submit" name="btnUpdate" id="update" value="Update Category">
                    <input type="reset" name="btnClear" id="clear" value="Reset">
                </div>
            </fieldset>
        </form>
    </div>
</div>
</body>
</html>
