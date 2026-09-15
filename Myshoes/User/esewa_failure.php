<?php
session_start();
// We deliberately do NOT clear $_SESSION['cart'] or ['pending_esewa_order']
// here, so the user can retry payment without re-entering their details/cart.
include('header.php');
?>
<div style="max-width:600px;margin:80px auto;background:#fff;padding:40px;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.1);text-align:center;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <div style="font-size:60px;color:#e74c3c;margin-bottom:20px;">❌</div>
    <h1 style="color:#2c3e50;">Payment Not Completed</h1>
    <p style="color:#7f8c8d;line-height:1.6;">
        Your eSewa payment was cancelled or did not go through. No money was charged, and your cart is still saved.
    </p>
    <a href="mycart.php" style="display:inline-block;margin-top:20px;background:linear-gradient(45deg,#667eea,#764ba2);color:#fff;text-decoration:none;padding:14px 30px;border-radius:50px;font-weight:700;">Back to Cart</a>
</div>
<?php
include('footer.php');
