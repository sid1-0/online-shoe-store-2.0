<?php include('header.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - My Shoe Store</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.7;
        }

        .privacy-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .privacy-header {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .privacy-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
            animation: shimmer 4s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .privacy-header h1 {
            font-size: 3.5rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
        }

        .privacy-header h1::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 3px;
        }

        .privacy-header p {
            font-size: 1.3rem;
            color: #7f8c8d;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .privacy-content {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.8s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .privacy-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f8f9fa;
            position: relative;
        }

        .privacy-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .privacy-section h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 25px;
            position: relative;
            padding-left: 25px;
        }

        .privacy-section h2::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 40px;
            background: linear-gradient(180deg, #667eea, #764ba2);
            border-radius: 3px;
        }

        .privacy-section p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.8;
            text-align: justify;
            padding-left: 25px;
        }

        .privacy-section ul {
            padding-left: 50px;
            margin-bottom: 20px;
        }

        .privacy-section li {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 12px;
            line-height: 1.7;
            position: relative;
        }

        .privacy-section li::before {
            content: '✓';
            position: absolute;
            left: -25px;
            color: #667eea;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .highlight-box {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border: 2px solid #667eea30;
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
            position: relative;
        }

        .highlight-box::before {
            content: '💡';
            position: absolute;
            top: -15px;
            left: 25px;
            background: white;
            padding: 5px 10px;
            border-radius: 50%;
            font-size: 1.5rem;
        }

        .highlight-box h3 {
            color: #667eea;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 15px;
            margin-top: 10px;
        }

        .highlight-box p {
            color: #2c3e50;
            font-weight: 600;
            padding-left: 0;
            margin-bottom: 0;
        }

        .contact-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-top: 40px;
            position: relative;
            overflow: hidden;
        }

        .contact-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .contact-section h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .contact-section p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .contact-email {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 2;
        }

        .contact-email:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .last-updated {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            color: #7f8c8d;
            font-style: italic;
            border-left: 4px solid #667eea;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .privacy-container {
                padding: 0 15px;
                margin: 20px auto;
            }

            .privacy-header,
            .privacy-content {
                padding: 30px 25px;
            }

            .privacy-header h1 {
                font-size: 2.5rem;
                letter-spacing: 2px;
            }

            .privacy-header p {
                font-size: 1.1rem;
            }

            .privacy-section h2 {
                font-size: 1.8rem;
            }

            .privacy-section p,
            .privacy-section li {
                font-size: 1rem;
            }

            .contact-section {
                padding: 30px 20px;
            }

            .contact-section h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .privacy-header h1 {
                font-size: 2rem;
            }

            .privacy-section h2 {
                font-size: 1.5rem;
            }

            .privacy-section p {
                padding-left: 15px;
            }

            .privacy-section ul {
                padding-left: 35px;
            }

            .highlight-box {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="privacy-container">
        <div class="privacy-header">
            <h1>Privacy Policy</h1>
            <p>Your privacy is important to us. This policy explains how we collect, use, and protect your personal information when you use our services.</p>
        </div>

        <div class="privacy-content">
            <div class="privacy-section">
                <h2>Information We Collect</h2>
                <p>
                    At My Shoe Store, we may collect personal information from you such as your name, email address, postal address, phone number, and payment information when you voluntarily submit this information to us. This occurs when you:
                </p>
                <ul>
                    <li>Create an account on our website</li>
                    <li>Make a purchase or place an order</li>
                    <li>Sign up for our newsletter or promotional emails</li>
                    <li>Contact us through our customer service channels</li>
                    <li>Participate in surveys, contests, or promotional activities</li>
                </ul>
                
                <div class="highlight-box">
                    <h3>Automatic Information Collection</h3>
                    <p>We may also automatically collect certain information about your device and how you interact with our website, including IP address, browser type, operating system, and browsing behavior.</p>
                </div>
            </div>

            <div class="privacy-section">
                <h2>How We Use Your Information</h2>
                <p>
                    We use the information you provide about yourself to fulfill your requests, process your orders, and communicate with you about your purchases. Specifically, we use your information to:
                </p>
                <ul>
                    <li>Process and fulfill your orders</li>
                    <li>Send order confirmations and shipping notifications</li>
                    <li>Provide customer support and respond to inquiries</li>
                    <li>Send promotional emails about our products and services (with your consent)</li>
                    <li>Improve our website and customer experience</li>
                    <li>Prevent fraud and ensure security</li>
                    <li>Comply with legal obligations</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h2>Information Sharing and Disclosure</h2>
                <p>
                    We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except in the following circumstances:
                </p>
                <ul>
                    <li>Service providers who assist us in operating our website and conducting business</li>
                    <li>Payment processors for secure transaction processing</li>
                    <li>Shipping companies for order delivery</li>
                    <li>Legal authorities when required by law or to protect our rights</li>
                </ul>
                
                <div class="highlight-box">
                    <h3>Third-Party Services</h3>
                    <p>We may use third-party services for analytics, advertising, and other business purposes. These services may collect information about your use of our website.</p>
                </div>
            </div>

            <div class="privacy-section">
                <h2>Data Security</h2>
                <p>
                    We are committed to ensuring that your information is secure. We have implemented suitable physical, electronic, and managerial procedures to safeguard and secure the information we collect online. These measures include:
                </p>
                <ul>
                    <li>SSL encryption for data transmission</li>
                    <li>Secure servers and databases</li>
                    <li>Regular security audits and updates</li>
                    <li>Access controls and employee training</li>
                    <li>Secure payment processing systems</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h2>Your Rights and Choices</h2>
                <p>
                    You have certain rights regarding your personal information, including:
                </p>
                <ul>
                    <li>Access to your personal information</li>
                    <li>Correction of inaccurate information</li>
                    <li>Deletion of your personal information</li>
                    <li>Opt-out of marketing communications</li>
                    <li>Data portability</li>
                </ul>
                <p>
                    To exercise these rights or if you have any questions about your personal information, please contact us using the information provided below.
                </p>
            </div>

            <div class="privacy-section">
                <h2>Cookies and Tracking Technologies</h2>
                <p>
                    Our website uses cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, and personalize content. You can control cookie settings through your browser preferences.
                </p>
                
                <div class="highlight-box">
                    <h3>Cookie Types</h3>
                    <p>We use essential cookies for website functionality, analytics cookies for performance monitoring, and marketing cookies for personalized advertising (with your consent).</p>
                </div>
            </div>

            <div class="privacy-section">
                <h2>Children's Privacy</h2>
                <p>
                    Our website is not intended for children under the age of 13. We do not knowingly collect personal information from children under 13. If we become aware that we have collected personal information from a child under 13, we will take steps to delete such information.
                </p>
            </div>

            <div class="privacy-section">
                <h2>Changes to This Privacy Policy</h2>
                <p>
                    We reserve the right to update or change our Privacy Policy at any time. Any changes will be posted on this page with an updated revision date. Your continued use of our website after any changes constitutes your acceptance of the new Privacy Policy.
                </p>
                <p>
                    We encourage you to review this Privacy Policy periodically to stay informed about how we are protecting your information.
                </p>
            </div>
        </div>

        <div class="contact-section">
            <h2>Contact Us</h2>
            <p>If you have any questions about this Privacy Policy or our data practices, please don't hesitate to contact us.</p>
            <a href="mailto:privacy@myfamilystore.com" class="contact-email">privacy@myfamilystore.com</a>
        </div>

        <div class="last-updated">
            <p><strong>Last Updated:</strong> December 2024</p>
        </div>
    </div>
</body>
</html>
<?php include('footer.php') ?>
