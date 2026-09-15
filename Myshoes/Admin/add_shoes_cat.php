<?php
session_start();

//validation
if(isset($_POST['btnSave'])){
	//assing error to array
	$err=[];

	if(isset($_POST['name']) && !empty($_POST['name']) && trim($_POST['name'])){
		$name=$_POST['name'];
	}else{
		$err['name']='Please enter name';
	}

	$status = $_POST['status'];
	$created_at=date('Y-m-d H:i:s');
	$created_by = $_SESSION['admin_id'];
    

	if (count($err)==0){
			require_once'connection.php';
			$sql ="INSERT INTO shoe_categories (name,status,created_at,created_by) VALUES('$name','$status','$created_at','$created_by')";
			$connection->query($sql);
			if($connection->affected_rows ==1 && $connection->insert_id > 0){
				$success = 'Category insert success';
			}else{
				$error='Category insert failed';
			}

	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Shoe Category - Admin Panel</title>
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

        .wrapper {
            max-width: 800px;
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

        .form-group {
            margin-bottom: 25px;
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
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .wrapper {
                padding: 0 15px;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .form-content {
                padding: 30px 20px;
            }

            .radio-group {
                flex-direction: column;
                gap: 10px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'admin_menu.php'?>
    
    <div class="wrapper">
        <a href="dashboard.php" class="back-link">
            ← Back to Dashboard
        </a>
        
        <div class="page-header">
            <h1>Category Management</h1>
            <p>Create and organize shoe categories for better product management</p>
        </div>

        <div class="form-container">
            <div class="form-header">
                <h2>Create New Category</h2>
                <p>Add a new shoe category to organize your inventory</p>
            </div>

            <div class="form-content">
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['err']) && $_GET['err']==1): ?>
                    <div class="alert alert-error">
                        Please login to continue
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="form-group">
                        <label for="name">Category Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control"
                            placeholder="Enter category name (e.g., Sports, Casual, Formal)" 
                            value="<?php echo isset($name) ? $name : ''; ?>"
                        />
                        <?php if (isset($err['name'])): ?>
                            <div class="error-message"><?php echo $err['name']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Category Status</label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="active" name="status" value="1">
                                <label for="active">Active</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="deactive" name="status" value="0" checked>
                                <label for="deactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" name="btnSave" class="btn btn-primary">
                            Create Category
                        </button>
                        <button type="reset" name="btnClear" class="btn btn-secondary">
                            Clear Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
