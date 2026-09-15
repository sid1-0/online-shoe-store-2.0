<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user_wishlist = [];
if (isset($_SESSION['user_id'])) {
    require_once 'connection.php';
    $stmt = $connection->prepare("SELECT shoe_id FROM wishlist WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $user_wishlist[] = $r['shoe_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Header</title>
  <style>
    *, *::before, *::after {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f2f2f2;
      color: #fff;
    }

    header {
      background: linear-gradient(135deg, rgba(40, 40, 40, 0.97) 0%, rgba(20, 20, 20, 0.97) 100%);
      padding: 20px 60px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: all 0.3s ease;
    }

    header.scrolled {
      padding: 15px 60px;
      background: rgba(30, 30, 30, 0.98);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    @keyframes fadeInHeader {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1400px;
      margin: 0 auto;
    }

    nav ul {
      margin: 0;
      padding: 0;
      list-style: none;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      flex-grow: 1;
      justify-content: space-evenly;
      align-items: center;
    }

    nav ul li {
      position: relative;
    }

    nav ul li a, 
    nav ul li .brand,
    .welcome,
    .logout {
      display: inline-block;
      text-decoration: none;
      color: #fff;
      font-weight: 600;
      font-size: 18px;
      text-transform: uppercase;
      padding: 10px 16px;
      border-radius: 6px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    nav ul li a::before,
    nav ul li .brand::before,
    .welcome::before,
    .logout::before {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 0;
      background: rgba(255, 255, 255, 0.1);
      transition: height 0.3s ease;
      z-index: -1;
      border-radius: 6px;
    }

    nav ul li a:hover::before,
    nav ul li a:focus::before,
    nav ul li .brand:hover::before,
    nav ul li .brand:focus::before {
      height: 100%;
    }

    nav ul li a::after,
    nav ul li .brand::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 0;
      height: 3px;
      background: #3498db;
      transition: all 0.3s ease;
      transform: translateX(-50%);
    }

    nav ul li a:hover::after,
    nav ul li a:focus::after,
    nav ul li .brand:hover::after,
    nav ul li .brand:focus::after {
      width: 80%;
    }

    nav ul li a:hover,
    nav ul li a:focus,
    nav ul li .brand:hover,
    nav ul li .brand:focus {
      color: #ffffff;
      transform: translateY(-2px);
      outline: none;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 110%;
      left: 0;
      background: rgba(40, 40, 40, 0.98);
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      padding: 10px 0;
      border-radius: 8px;
      min-width: 200px;
      z-index: 200;
      opacity: 0;
      transform: translateY(10px);
      transition: all 0.3s ease;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dropdown:hover .dropdown-content,
    .dropdown:focus-within .dropdown-content {
      display: block;
      opacity: 1;
      transform: translateY(0);
    }

    .dropdown-content a {
      padding: 12px 22px;
      color: #ddd;
      font-size: 16px;
      display: block;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .dropdown-content a::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 0;
      height: 100%;
      background: linear-gradient(90deg, #3498db, #2980b9);
      transition: width 0.3s ease;
      z-index: -1;
    }

    .dropdown-content a:hover::before,
    .dropdown-content a:focus::before {
      width: 100%;
    }

    .dropdown-content a:hover,
    .dropdown-content a:focus {
      color: #fff;
      transform: translateX(8px);
      outline: none;
      font-weight: 700;
    }

    .welcome-link {
      color: #f1c40f !important;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .welcome-link i {
      font-size: 1.2rem;
    }

    .logout-link {
      color: #e74c3c !important;
      font-weight: bold;
    }
    
    .logout-link:hover {
      background: rgba(231, 76, 60, 0.1) !important;
    }

    .mobile-menu-toggle {
      display: none;
      background: none;
      border: none;
      color: #fff;
      font-size: 24px;
      cursor: pointer;
      padding: 10px;
      border-radius: 6px;
      transition: all 0.3s ease;
    }

    .mobile-menu-toggle:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .mobile-menu-toggle span {
      display: block;
      width: 25px;
      height: 3px;
      background: #fff;
      margin: 5px 0;
      transition: 0.3s;
    }

    .mobile-menu-toggle.active span:nth-child(1) {
      transform: rotate(-45deg) translate(-5px, 6px);
    }

    .mobile-menu-toggle.active span:nth-child(2) {
      opacity: 0;
    }

    .mobile-menu-toggle.active span:nth-child(3) {
      transform: rotate(45deg) translate(-5px, -6px);
    }

    @media (max-width: 992px) {
      header {
        padding: 15px 30px;
      }
      nav ul {
        gap: 20px;
        justify-content: space-around;
      }
      nav ul li a,
      nav ul li .brand {
        font-size: 16px;
        padding: 8px 14px;
      }
    }

    @media (max-width: 768px) {
      header {
        padding: 15px 20px;
      }
      
      .mobile-menu-toggle {
        display: block;
      }
      
      nav ul {
        position: fixed;
        top: 100%;
        left: 0;
        width: 100%;
        background: rgba(30, 30, 30, 0.98);
        backdrop-filter: blur(10px);
        flex-direction: column;
        gap: 0;
        padding: 20px 0;
        transform: translateY(-100vh);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      }
      
      nav ul.active {
        transform: translateY(0);
      }
      
      nav ul li {
        width: 100%;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      nav ul li:last-child {
        border-bottom: none;
      }
      
      nav ul li a,
      nav ul li .brand {
        width: 100%;
        font-size: 16px;
        padding: 15px 20px;
        text-align: center;
        border-radius: 0;
      }
      
      .dropdown-content {
        position: static;
        box-shadow: none;
        background: rgba(50, 50, 50, 0.9);
        padding: 0;
        border-radius: 0;
        opacity: 1;
        transform: none;
        border: none;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      .dropdown-content a {
        padding: 12px 40px;
        background: rgba(60, 60, 60, 0.5);
        margin: 2px 0;
        font-size: 14px;
      }
      
      .dropdown-content a:hover,
      .dropdown-content a:focus {
        background: rgba(52, 152, 219, 0.8);
        transform: none;
      }
    }

    @media (max-width: 480px) {
      header {
        padding: 12px 15px;
      }
      
      nav ul li a,
      nav ul li .brand {
        font-size: 14px;
        padding: 12px 15px;
      }
    }
  </style>
</head>
<body>
  <header id="header">
    <nav>
      <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <span></span>
        <span></span>
        <span></span>
      </button>
      
      <ul id="navMenu">
        <li><a href="index.php" class="home">Home</a></li>
        <li class="dropdown" tabindex="0">
          <a href="javascript:void(0)" class="brand" aria-haspopup="true" aria-expanded="false">Brand</a>
          <div class="dropdown-content" role="menu" aria-label="Brand submenu">
            <a href="addidas.php" role="menuitem">Adidas</a>
            <a href="nike.php" role="menuitem">Nike</a>
            <a href="puma.php" role="menuitem">Puma</a>
          </div>
        </li>
        <li><a href="socks.php">Socks</a></li>
        <li><a href="shoe_care.php">Shoe Care</a></li>
        <li><a href="aboutus.php" class="aboutus">About Us</a></li>
        <li><a href="contactus.php" class="contact">Contact Us</a></li>
        <li><a href="mycart.php" class="my-cart">My Cart</a></li>

        <?php 
          if (isset($_SESSION['username'])) {
            $username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
            echo '<li class="dropdown" tabindex="0">';
            echo '<a href="javascript:void(0)" class="welcome-link" aria-haspopup="true" aria-expanded="false">👤 ' . $username . '</a>';
            echo '<div class="dropdown-content" role="menu">';
            echo '<a href="order.php" role="menuitem">My Orders</a>';
            echo '<a href="wishlist.php" role="menuitem">My Wishlist</a>';
            echo '<a href="loggingout.php" class="logout-link" role="menuitem">Logout</a>';
            echo '</div>';
            echo '</li>';
          } else {
            echo '<li><a href="login.php" class="login">Login</a></li>';
          }
        ?>
      </ul>
    </nav>
  </header>

  <script>
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    
    mobileMenuToggle.addEventListener('click', function() {
      this.classList.toggle('active');
      navMenu.classList.toggle('active');
    });
    
    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('nav ul li a');
    navLinks.forEach(link => {
      link.addEventListener('click', function() {
        mobileMenuToggle.classList.remove('active');
        navMenu.classList.remove('active');
      });
    });
    
    // Header scroll effect
    const header = document.getElementById('header');
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      if (!event.target.closest('nav') && navMenu.classList.contains('active')) {
        mobileMenuToggle.classList.remove('active');
        navMenu.classList.remove('active');
      }
    });

    // Global Wishlist Toggle Function
    function toggleWishlist(shoeId, btnElement) {
        fetch('toggle_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'shoe_id=' + shoeId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'error' && data.message === 'not_logged_in') {
                window.location.href = 'login.php';
            } else if (data.status === 'success') {
                if (data.action === 'added') {
                    btnElement.innerHTML = '♥ Added';
                    btnElement.style.color = '#e74c3c';
                    btnElement.style.borderColor = '#e74c3c';
                } else if (data.action === 'removed') {
                    btnElement.innerHTML = '♡ Wishlist';
                    btnElement.style.color = '';
                    btnElement.style.borderColor = '';
                }
            }
        })
        .catch(error => {
            console.error('Error toggling wishlist:', error);
        });
    }
  </script>
</body>
</html>
