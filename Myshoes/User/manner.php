<?php
// Database connection
$servername = "localhost";
$username = "your_username";
$password = " ";
$dbname = "airwalker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$username = $_POST['username'];
$password = $_POST['password'];

// SQL query to insert data into the database
$sql = "INSERT INTO Users (username, password) VALUES ('$username', '$password')";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
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
    }

    .registration-container {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 450px;
      position: relative;
      overflow: hidden;
    }

    .registration-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
      animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }

    .form-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .form-header h2 {
      font-size: 2.5rem;
      font-weight: 800;
      color: #2c3e50;
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .form-header p {
      color: #7f8c8d;
      font-size: 1.1rem;
    }

    .form-group {
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
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #667eea;
      background-color: #fff;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      transform: translateY(-2px);
    }

    input[type="submit"] {
      width: 100%;
      padding: 15px;
      background: linear-gradient(45deg, #667eea, #764ba2);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      position: relative;
      overflow: hidden;
    }

    input[type="submit"]::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      transition: left 0.5s;
    }

    input[type="submit"]:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
    }

    input[type="submit"]:hover::before {
      left: 100%;
    }

    input[type="submit"]:active {
      transform: translateY(-1px);
    }

    .login-link {
      text-align: center;
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #e1e8ed;
    }

    .login-link a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .login-link a:hover {
      color: #764ba2;
      text-decoration: underline;
    }

    .success-message,
    .error-message {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 600;
      text-align: center;
    }

    .success-message {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .error-message {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .floating-shapes {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: -1;
    }

    .shape {
      position: absolute;
      opacity: 0.1;
      animation: float 6s ease-in-out infinite;
    }

    .shape:nth-child(1) {
      top: 20%;
      left: 10%;
      width: 80px;
      height: 80px;
      background: #667eea;
      border-radius: 50%;
      animation-delay: 0s;
    }

    .shape:nth-child(2) {
      top: 60%;
      right: 10%;
      width: 60px;
      height: 60px;
      background: #764ba2;
      border-radius: 50%;
      animation-delay: 2s;
    }

    .shape:nth-child(3) {
      bottom: 20%;
      left: 20%;
      width: 40px;
      height: 40px;
      background: #f093fb;
      border-radius: 50%;
      animation-delay: 4s;
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0px) rotate(0deg);
      }
      50% {
        transform: translateY(-20px) rotate(180deg);
      }
    }

    @media (max-width: 480px) {
      .registration-container {
        padding: 30px 20px;
        margin: 0 15px;
      }

      .form-header h2 {
        font-size: 2rem;
      }

      input[type="text"],
      input[type="password"],
      input[type="submit"] {
        padding: 12px 15px;
      }
    }
  </style>
</head>
<body>
  <div class="floating-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
  </div>

  <div class="registration-container">
    <div class="form-header">
      <h2>Register</h2>
      <p>Create your account to get started</p>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
      <?php if ($conn->query($sql) === TRUE): ?>
        <div class="success-message">
          Registration successful! You can now login.
        </div>
      <?php else: ?>
        <div class="error-message">
          Registration failed. Please try again.
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <form action="register.php" method="post">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>
      
      <input type="submit" value="Register">
    </form>

    <div class="login-link">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</body>
</html>
