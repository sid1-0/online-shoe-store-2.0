<?php
session_start();

require_once __DIR__ . '/../classes/User.php';

$username = $password = '';
$errUsername = $errPassword = '';
$userModel = new User();

if (isset($_POST['btnlogin'])) {
    if (isset($_POST['username']) && !empty($_POST['username']) && trim($_POST['username'])) {
        $username = trim($_POST['username']);
    } else {
        $errUsername = 'Enter your username';
    }

    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $errPassword = 'Enter password';
    }

    if (empty($errUsername) && empty($errPassword)) {
        $result = $userModel->login($username, $password);
        if ($result === true) {
            header('Location: index.php');
            exit();
        }
        if ($result === 'User not found') {
            $errUsername = $result;
        } else {
            $errPassword = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
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

    .login-container {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 50px 40px;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 450px;
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

    .login-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #3498db, #2980b9, #8e44ad, #9b59b6);
      animation: gradientShift 4s ease infinite;
    }

    @keyframes gradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .login-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .login-header h2 {
      font-size: 2.5rem;
      font-weight: 800;
      color: #2c3e50;
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 2px;
      position: relative;
    }

    .login-header h2::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background: linear-gradient(90deg, #3498db, #2980b9);
      border-radius: 2px;
    }

    .login-header p {
      color: #7f8c8d;
      font-size: 1.1rem;
      margin-top: 15px;
    }

    .input-container {
      margin-bottom: 25px;
      position: relative;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #34495e;
      font-size: 1rem;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 15px 20px;
      border: 2px solid #e1e8ed;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background-color: #f8f9fa;
      position: relative;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #3498db;
      background-color: #fff;
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
      transform: translateY(-2px);
    }

    .error-message {
      color: #e74c3c;
      font-size: 0.9rem;
      margin-top: 5px;
      display: block;
      font-weight: 500;
      animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }

    button {
      width: 100%;
      background: linear-gradient(45deg, #3498db, #2980b9);
      color: #fff;
      border: none;
      border-radius: 12px;
      padding: 15px;
      font-size: 1.1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      position: relative;
      overflow: hidden;
      margin-top: 10px;
    }

    button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: left 0.5s;
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(52, 152, 219, 0.4);
    }

    button:hover::before {
      left: 100%;
    }

    button:active {
      transform: translateY(-1px);
    }

    .register-link {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #e1e8ed;
    }

    .register-link p {
      color: #7f8c8d;
      margin-bottom: 0;
    }

    .register-link a {
      color: #3498db;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      position: relative;
    }

    .register-link a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: #3498db;
      transition: width 0.3s ease;
    }

    .register-link a:hover {
      color: #2980b9;
    }

    .register-link a:hover::after {
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
      width: 100px;
      height: 100px;
      background: #3498db;
      top: 20%;
      left: 10%;
      animation-delay: 0s;
    }

    .floating-circle:nth-child(2) {
      width: 150px;
      height: 150px;
      background: #2980b9;
      top: 60%;
      right: 10%;
      animation-delay: 3s;
    }

    .floating-circle:nth-child(3) {
      width: 80px;
      height: 80px;
      background: #8e44ad;
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

    @media (max-width: 480px) {
      .login-container {
        padding: 40px 25px;
        margin: 0 15px;
      }

      .login-header h2 {
        font-size: 2rem;
      }

      input[type="text"],
      input[type="password"],
      button {
        padding: 12px 15px;
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

  <div class="login-container">
    <div class="login-header">
      <h2>Login</h2>
      <p>Welcome back! Please sign in to your account</p>
    </div>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" novalidate>
      <div class="input-container">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo isset($username) ? $username : ''; ?>"/>
        <span class="error-message"><?php echo isset($errUsername) ? $errUsername : ''; ?></span>
      </div>
      <div class="input-container">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" />
        <span class="error-message"><?php echo isset($errPassword) ? $errPassword : ''; ?></span>
      </div>

      <button type="submit" name="btnlogin">Login</button>
    </form>

    <div class="register-link">
      <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>
</body>
</html>
