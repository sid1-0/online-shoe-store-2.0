<?php include('header.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact Us - My Shoe Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
        }

        .contact-hero {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.9), rgba(155, 89, 182, 0.9)), url('/placeholder.svg?height=400&width=1200');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 80px 20px;
            position: relative;
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .contact-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .contact-hero p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: -50px auto 80px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }

        .contact-info-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .contact-form-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 15px;
        }

        .card-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #9b59b6);
            border-radius: 2px;
        }

        .contact-info {
            margin-top: 20px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .contact-item:hover {
            background: #e9ecef;
            border-left-color: #3498db;
            transform: translateX(5px);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }

        .contact-details h3 {
            margin: 0 0 8px 0;
            font-size: 1.2rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .contact-details p {
            margin: 0;
            color: #7f8c8d;
            font-size: 1rem;
        }

        .contact-details a {
            color: #3498db;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-details a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        input, textarea {
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: inherit;
        }

        input:focus, textarea:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(41, 128, 185, 0.3);
        }

        .social-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .social-link {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .social-link.facebook {
            background: linear-gradient(45deg, #3b5998, #4267B2);
        }

        .social-link.instagram {
            background: linear-gradient(45deg, #E4405F, #C13584);
        }

        .social-link.email {
            background: linear-gradient(45deg, #34495e, #2c3e50);
        }

        .social-link.phone {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
        }

        .social-link:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .map-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 40px;
        }

        .map-placeholder {
            width: 100%;
            height: 300px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
            font-size: 1.2rem;
            margin-top: 20px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .contact-hero h1 {
                font-size: 2.5rem;
            }
            
            .contact-hero p {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 768px) {
            .contact-hero {
                padding: 60px 20px;
            }
            
            .contact-hero h1 {
                font-size: 2rem;
            }
            
            .container {
                margin: -30px auto 60px;
                padding: 0 15px;
            }
            
            .contact-info-card,
            .contact-form-card,
            .social-section,
            .map-section {
                padding: 30px 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .social-links {
                flex-wrap: wrap;
                gap: 15px;
            }
            
            .contact-item {
                flex-direction: column;
                text-align: center;
                padding: 20px 15px;
            }
            
            .contact-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }

        @media (max-width: 480px) {
            .contact-hero h1 {
                font-size: 1.8rem;
            }
            
            .card-title {
                font-size: 1.5rem;
            }
            
            .social-link {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="contact-hero">
        <div class="contact-hero-content">
            <h1>Get In Touch</h1>
            <p>We're here to help and answer any questions you might have. We look forward to hearing from you!</p>
        </div>
    </div>

    <div class="container">
        <div class="contact-grid">
            <div class="contact-info-card">
                <h2 class="card-title">Contact Information</h2>
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Phone Number</h3>
                            <p><a href="tel:+977-1234567890">+977-1234567890</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="far fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Email Address</h3>
                            <p><a href="mailto:support@myfamilystore.com">support@myfamilystore.com</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Store Location</h3>
                            <p>123 Fashion Street, Kathmandu, Nepal</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Business Hours</h3>
                            <p>Mon - Sat: 9:00 AM - 8:00 PM<br>Sunday: 10:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-card">
                <h2 class="card-title">Send us a Message</h2>
                <form class="contact-form" action="#" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Tell us how we can help you..." required></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Send Message</button>
                </form>
            </div>
        </div>
        
        <div class="social-section">
            <h2 class="card-title">Follow Us</h2>
            <p>Stay connected with us on social media for the latest updates, offers, and new arrivals!</p>
            <div class="social-links">
                <a href="https://www.facebook.com/myfamilystorepage" class="social-link facebook" target="_blank" rel="noopener">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/myfamilystore_official" class="social-link instagram" target="_blank" rel="noopener">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="mailto:support@myfamilystore.com" class="social-link email">
                    <i class="far fa-envelope"></i>
                </a>
                <a href="tel:+977-1234567890" class="social-link phone">
                    <i class="fas fa-phone"></i>
                </a>
            </div>
        </div>
        
        <div class="map-section">
            <h2 class="card-title">Find Our Store</h2>
            <p>Visit us at our physical location for a hands-on shopping experience.</p>
            <div class="map-placeholder">
                <i class="fas fa-map-marked-alt" style="font-size: 3rem; margin-right: 15px;"></i>
                Interactive Map Coming Soon
            </div>
        </div>
    </div>
</body>
</html>
<?php include('footer.php')?>
