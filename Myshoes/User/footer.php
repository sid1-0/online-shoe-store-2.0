<!-- footer.php -->
<footer class="footer" role="contentinfo">
  <div class="container">
    <section class="footer-col" aria-labelledby="footer-info">
      <h4 id="footer-info">Information</h4>
      <ul>
        <li><a href="aboutus.php">About Us</a></li>
        <li><a href="privacy_policy.php">Privacy Policy</a></li>
        <li><a href="all_shoes.php">All Products</a></li>
      </ul>
    </section>

    <section class="footer-col" aria-labelledby="footer-help">
      <h4 id="footer-help">Get Help</h4>
      <ul>
        <li><a href="contactus.php">Contact Us</a></li>
      </ul>
    </section>

    <section class="footer-col" aria-labelledby="footer-follow">
      <h4 id="footer-follow">Follow Us</h4>
      <div class="social-links">
        <a href="#" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
        <a href="#" aria-label="Twitter"><i class="fa fa-twitter"></i></a>
        <a href="#" aria-label="Instagram"><i class="fa fa-instagram"></i></a>
      </div>
    </section>
  </div>
  <div class="trademark">&copy; 2024 My Shoe Store. All rights reserved.</div>

  <style>
    .footer {
      background: linear-gradient(to right, #1a1a1a, #2c2c2c);
      color: #ddd;
      padding: 60px 20px 30px;
      width: 100%;
      position: relative;
      overflow: hidden;
    }

    .footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #3498db, #9b59b6, #e74c3c, #f1c40f);
      z-index: 1;
    }

    .footer .container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      gap: 60px;
      justify-content: space-between;
      position: relative;
      z-index: 2;
    }

    .footer-col {
      flex: 1 1 200px;
      min-width: 180px;
      position: relative;
    }

    .footer h4 {
      font-size: 1.4rem;
      margin-bottom: 25px;
      color: #fff;
      position: relative;
      display: inline-block;
      padding-bottom: 12px;
    }

    .footer h4::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 40px;
      height: 3px;
      background: #3498db;
      transition: width 0.3s ease;
    }

    .footer-col:hover h4::after {
      width: 100%;
    }

    .footer ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer ul li {
      margin-bottom: 15px;
      position: relative;
      padding-left: 0;
      transition: padding-left 0.3s ease;
    }

    .footer ul li:hover {
      padding-left: 8px;
    }

    .footer ul li a {
      color: #bbb;
      text-decoration: none;
      font-size: 1.05rem;
      transition: color 0.3s ease;
      display: inline-block;
      position: relative;
    }

    .footer ul li a::after {
      content: '';
      position: absolute;
      bottom: -3px;
      left: 0;
      width: 0;
      height: 1px;
      background: #3498db;
      transition: width 0.3s ease;
    }

    .footer ul li a:hover {
      color: #fff;
    }

    .footer ul li a:hover::after {
      width: 100%;
    }

    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 15px;
    }

    .social-links a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 45px;
      height: 45px;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 50%;
      color: #bbb;
      font-size: 20px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .social-links a::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(45deg, #3498db, #2980b9);
      z-index: -1;
      transform: scale(0);
      transition: transform 0.5s ease;
      border-radius: 50%;
    }

    .social-links a:hover {
      color: #fff;
      transform: translateY(-5px);
    }

    .social-links a:hover::before {
      transform: scale(1);
    }

    .trademark {
      text-align: center;
      color: #666;
      font-size: 0.95rem;
      margin-top: 50px;
      padding-top: 25px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-pattern {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: radial-gradient(rgba(255, 255, 255, 0.03) 2px, transparent 2px);
      background-size: 30px 30px;
      pointer-events: none;
      opacity: 0.5;
    }

    @media (max-width: 768px) {
      .footer .container {
        flex-direction: column;
        gap: 40px;
      }

      .footer-col {
        min-width: 100%;
      }
      
      .footer h4 {
        font-size: 1.3rem;
        margin-bottom: 20px;
      }
    }
  </style>
  <div class="footer-pattern"></div>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</footer>
