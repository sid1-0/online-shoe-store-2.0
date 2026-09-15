<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

//after button click
if(isset($_POST['btnLogin'])){
    //assign error to array
    $err=[];

    if(isset($_POST['username']) && !empty($_POST['username']) && trim($_POST['username'])){
        $username=trim($_POST['username']);
    }else{
        $err['username']='Please enter username';
    }

    if(isset($_POST['password']) && !empty($_POST['password'])){
        $password=$_POST['password'];

    }else{
        $err['password']='Please enter password';
    }
    if (count($err)==0){
        require_once 'connection.php';

        // Use prepared statement to prevent SQL injection
        $stmt = $connection->prepare("SELECT id, name, username, password FROM admins WHERE username = ? AND status = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 1){
            $row = $result->fetch_assoc();

            // Verify password using password_hash (secure)
            // For backward compatibility: if the stored password is not yet hashed,
            // check plain-text and upgrade it to a hash automatically.
            $passwordValid = false;
            if (password_verify($password, $row['password'])) {
                $passwordValid = true;
            } elseif ($row['password'] === $password) {
                // Plain-text match — upgrade to hashed password automatically
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $connection->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $updateStmt->bind_param("si", $hashed, $row['id']);
                $updateStmt->execute();
                $updateStmt->close();
                $passwordValid = true;
            }

            if ($passwordValid) {
                $_SESSION['admin_id']=$row['id'];
                $_SESSION['admin_name']=$row['name'];
                $_SESSION['admin_username']=$row['username'];

                //redirect to next page
                header('Location: dashboard.php');
                exit();
            } else {
                $msg='Username and password do not match';
            }
        }else{
            $msg='Username and password do not match';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login | My Shoe Store</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .login-container {
            width: 400px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            position: relative;
        }

        .login-header {
            background: linear-gradient(90deg, #ff6f61, #de6262);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .login-header p {
            margin-top: 5px;
            font-size: 14px;
            opacity: 0.9;
        }

        .login-form {
            padding: 30px;
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

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 15px;
            color: #495057;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            border-color: #ff6f61;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 111, 97, 0.1);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
            accent-color: #ff6f61;
        }

        .remember-me label {
            font-size: 14px;
            color: #6c757d;
            cursor: pointer;
        }

        .error-msg {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }

        .btn-container {
            display: flex;
            gap: 15px;
        }

        .btn-container input[type="submit"],
        .btn-container input[type="reset"] {
            padding: 12px 0;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            flex: 1;
            font-family: 'Poppins', sans-serif;
        }

        .btn-container input[type="submit"] {
            background-color: #ff6f61;
            color: white;
        }

        .btn-container input[type="submit"]:hover {
            background-color: #e8635b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-container input[type="reset"] {
            background-color: #e9ecef;
            color: #495057;
        }

        .btn-container input[type="reset"]:hover {
            background-color: #dee2e6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-footer {
            text-align: center;
            padding: 15px 30px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .login-footer p {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                max-width: 350px;
            }
            
            .login-header {
                padding: 20px;
            }
            
            .login-form {
                padding: 20px;
            }
            
            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Enter your credentials to access the dashboard</p>
        </div>
        <div class="login-form">
            <form action="" method="POST">
                <?php if (isset($msg)) : ?>
                    <div class="error-msg"><?php echo $msg; ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['err']) && $_GET['err'] == 1) : ?>
                    <div class="error-msg">Please login to continue</div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter your username" value="<?php echo isset($username) ? $username : ''; ?>" />
                    <?php if (isset($err['username'])) : ?>
                        <p class="error-msg"><?php echo $err['username']; ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" />
                    <?php if (isset($err['password'])) : ?>
                        <p class="error-msg"><?php echo $err['password']; ?></p>
                    <?php endif; ?>
                </div>
                <div class="btn-container">
                    <input type="submit" name="btnLogin" value="Login">
                    <input type="reset" name="btnClear" value="Clear">
                </div>
            </form>
        </div>
        <div class="login-footer">
            <p>My Shoe Store Admin Panel &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
</body>
</html>
