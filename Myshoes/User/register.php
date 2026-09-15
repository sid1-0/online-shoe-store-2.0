<?php
require_once __DIR__ . '/../classes/User.php';

$fullname = $username = $email = $password = '';
$errfullName = $errUsername = $errEmail = $errPassword = '';
$userModel = new User();

if (isset($_POST['btnRegister'])) {
    $err = [];
    if (isset($_POST['fullname']) && !empty($_POST['fullname']) && trim($_POST['fullname'])) {
        $fullname = trim($_POST['fullname']);
        if (!preg_match('/^[A-Za-z\s]+$/', $fullname)) {
            $errfullName = 'Name must include valid characters A-Z or a-z';
        }
    } else {
        $errfullName = 'Enter your full name';
    }

    if (isset($_POST['username']) && !empty($_POST['username']) && trim($_POST['username'])) {
        $username = trim($_POST['username']);
        if (!preg_match('/^[A-Za-z0-9_]+$/', $username)) {
            $errUsername = 'Username must include valid characters A-Z, a-z, 0-9, or underscore';
        }
    } else {
        $errUsername = 'Enter your username';
    }

    if (isset($_POST['email']) && !empty($_POST['email']) && trim($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errEmail = 'Enter valid email';
        }
    } else {
        $errEmail = 'Enter your email';
    }

    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
        if (strlen($password) < 8) {
            $errPassword = 'Password must be at least 8 characters long';
        }
    } else {
        $errPassword = 'Enter password';
    }

    if (empty($err) && empty($errfullName) && empty($errUsername) && empty($errEmail) && empty($errPassword)) {
        $result = $userModel->register($username, $email, $password);
        if ($result === true) {
            echo "<script>alert('Registration successful'); window.location.href='login.php';</script>";
            exit;
        }
        if ($result === 'Email already exists') {
            $errEmail = $result;
        } elseif ($result === 'Username already exists') {
            $errUsername = $result;
        } else {
            echo "<script>alert(" . json_encode($result) . ")</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - My Shoe Store</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="10" cy="60" r="1" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .registration-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.8s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .registration-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            animation: gradientShift 4s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .registration-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .registration-header h2 {
            font-size: 2.8rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
        }

        .registration-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .registration-header p {
            color: #7f8c8d;
            font-size: 1.1rem;
            margin-top: 20px;
        }

        .input-container {
            margin-bottom: 25px;
            position: relative;
        }

        .input-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #2c3e50;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-container input[type="text"],
        .input-container input[type="email"],
        .input-container input[type="password"] {
            width: 100%;
            padding: 18px 25px;
            border: 2px solid #e1e8ed;
            border-radius: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            position: relative;
        }

        .input-container input:focus {
            outline: none;
            border-color: #667eea;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 8px;
            display: block;
            font-weight: 600;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        button[type="submit"] {
            width: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            border-radius: 15px;
            padding: 18px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }

        button[type="submit"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        button[type="submit"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        button[type="submit"]:hover::before {
            left: 100%;
        }

        button[type="submit"]:active {
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #e1e8ed;
        }

        .login-link p {
            color: #7f8c8d;
            margin-bottom: 0;
            font-size: 1rem;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #667eea;
            transition: width 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .floating-circle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 8s ease-in-out infinite;
        }

        .floating-circle:nth-child(1) {
            width: 120px;
            height: 120px;
            background: #667eea;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-circle:nth-child(2) {
            width: 180px;
            height: 180px;
            background: #764ba2;
            top: 60%;
            right: 10%;
            animation-delay: 3s;
        }

        .floating-circle:nth-child(3) {
            width: 100px;
            height: 100px;
            background: #f093fb;
            bottom: 20%;
            left: 20%;
            animation-delay: 6s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            33% {
                transform: translateY(-30px) rotate(120deg);
            }
            66% {
                transform: translateY(30px) rotate(240deg);
            }
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.85rem;
        }

        .strength-weak { color: #e74c3c; }
        .strength-medium { color: #f39c12; }
        .strength-strong { color: #27ae60; }

        /* Responsive Design */
        @media (max-width: 480px) {
            .registration-container {
                padding: 40px 25px;
                margin: 0 15px;
            }

            .registration-header h2 {
                font-size: 2.2rem;
                letter-spacing: 2px;
            }

            .input-container input[type="text"],
            .input-container input[type="email"],
            .input-container input[type="password"],
            button[type="submit"] {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
        <div class="floating-circle"></div>
    </div>

    <div class="registration-container">
        <div class="registration-header">
            <h2>Register</h2>
            <p>Create your account to start shopping</p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
            <div class="input-container">
                <label for="fullname"><b>Full Name</b></label>
                <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" value="<?php echo htmlspecialchars($fullname); ?>">
                <span class="error-message"><?php echo $errfullName; ?></span>
            </div>

            <div class="input-container">
                <label for="username"><b>Username</b></label>
                <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($username); ?>">
                <span class="error-message"><?php echo $errUsername; ?></span>
            </div>

            <div class="input-container">
                <label for="email"><b>Email</b></label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
                <span class="error-message"><?php echo $errEmail; ?></span>
            </div>

            <div class="input-container">
                <label for="password"><b>Password</b></label>
                <input type="password" id="password" name="password" placeholder="Enter your password">
                <span class="error-message"><?php echo $errPassword; ?></span>
                <div class="password-strength" id="password-strength"></div>
            </div>

            <button type="submit" name="btnRegister"><b>Create Account</b></button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php"><b><i>Login here</i></b></a></p>
        </div>
    </div>
</body>
</html>
