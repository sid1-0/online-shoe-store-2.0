<?php include('header.php'); ?>

<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #fff;
    margin: 0;
    padding: 0;
    color: #2c3e50;
    line-height: 1.7;
    font-size: 18px;
  }

  .container {
    max-width: 850px;
    margin: 60px auto 80px;
    padding: 0 30px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    animation: fadeInUp 1s ease forwards;
    opacity: 0;
    position: relative;
    overflow: hidden;
  }


  .about-header {
    text-align: center;
    padding: 60px 30px 40px;
    border-bottom: 1px solid #e1e8f0;
    position: relative;
  }

  .about-header::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(90deg, #3498db, #9b59b6);
  }

  .about-header h2 {
    font-size: 3rem;
    font-weight: 800;
    color: #34495e;
    text-transform: uppercase;
    margin-bottom: 15px;
    letter-spacing: 2px;
    position: relative;
    display: inline-block;
  }

  
  .about-header p {
    font-style: italic;
    font-size: 1.3rem;
    color: #7f8c8d;
    font-weight: 500;
  }

  section p {
    background-color: #fafafa;
    padding: 26px 36px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(44, 62, 80, 0.05);
    margin: 30px auto;
    font-size: 1.1rem;
    color: #34495e;
    line-height: 1.8;
    max-width: 760px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  section p:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(44, 62, 80, 0.1);
  }

  section p strong {
    color: #2980b9;
    font-weight: 700;
  }

  .button {
    text-align: center;
    margin: 40px 0 60px;
    animation: fadeInUp 1.2s ease forwards;
    opacity: 0;
  }

  .button a {
    display: inline-block;
    background: linear-gradient(45deg, #3498db, #2980b9);
    color: #fff;
    padding: 16px 44px;
    border-radius: 32px;
    font-size: 1.2rem;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 6px 20px rgba(41, 128, 185, 0.4);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
    z-index: 1;
  }

  .button a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.7s;
    z-index: -1;
  }

  .button a:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(41, 128, 185, 0.6);
  }

  .button a:hover::before {
    left: 100%;
  }

  @keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(25px); }
    100% { opacity: 1; transform: translateY(0); }
  }

  .values-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin: 40px auto;
    max-width: 760px;
  }

  .value-item {
    background-color: #fafafa;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(44, 62, 80, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 4px solid #3498db;
  }

  .value-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(44, 62, 80, 0.1);
  }

  .value-item h3 {
    color: #2980b9;
    margin-top: 0;
    font-size: 1.2rem;
  }

  .value-item p {
    margin: 0;
    padding: 0;
    background: none;
    box-shadow: none;
    border-left: none;
  }

  .value-item:hover {
    transform: translateY(-5px);
  }

  @media (max-width: 700px) {
    .container { padding: 0 20px; margin: 40px auto 60px; }
    section p { font-size: 1rem; padding: 20px 24px; }
    .button a { font-size: 1rem; padding: 14px 36px; }
    .about-header h2 { font-size: 2.4rem; }
    .about-header p { font-size: 1.1rem; }
    .values-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="container">
  <section class="about-header">
    <h2>KNOW US</h2>
    <p>My Shoe Store</p>
  </section>

  <section>
    <p>
      Where shoes meet fashion and comfort! Established with a passion for footwear and a commitment to exceptional customer service, we strive to offer the latest trends while ensuring quality and affordability. At <strong>My Shoe Store</strong>, shoes aren't just necessities—they're statements of style.
    </p>
    <p>
      <strong>Quality Craftsmanship:</strong> We partner with reputable manufacturers known for attention to detail.<br>
      <strong>Wide Selection:</strong> Sneakers, sandals, boots— we've got you covered.<br>
      <strong>Exceptional Customer Service:</strong> Friendly team ready to help.<br>
      <strong>Our Commitment to Sustainability:</strong> Eco-friendly materials and ethical manufacturing.
    </p>
    <p>
      Visit us today and experience the difference at <strong>My Shoe Store</strong> — where style, comfort, and quality come together!
    </p>
  </section>

  <div class="button">
    <a href="index.php">Shop Now</a>
  </div>
</div>

<?php include('footer.php'); ?>
