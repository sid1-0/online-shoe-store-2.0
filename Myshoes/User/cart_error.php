<?php include('header.php')?>

<style>
    .error-container {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .error-content {
        background: white;
        padding: 60px 40px;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 600px;
        width: 100%;
        position: relative;
        overflow: hidden;
    }

    .error-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .error-icon {
        font-size: 120px;
        color: #ff6b6b;
        margin-bottom: 30px;
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-20px);
        }
        60% {
            transform: translateY(-10px);
        }
    }

    .error-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .error-message {
        font-size: 1.2rem;
        color: #7f8c8d;
        margin-bottom: 40px;
        line-height: 1.6;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    .error-actions {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 40px;
    }

    .btn {
        display: inline-block;
        padding: 15px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
    }

    .btn-secondary {
        background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(240, 147, 251, 0.4);
    }

    .btn-secondary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(240, 147, 251, 0.6);
    }

    .error-suggestions {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 2px dashed #e1e8ed;
    }

    .suggestions-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .suggestions-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .suggestions-list li {
        padding: 12px 0;
        color: #5a6c7d;
        font-size: 1.1rem;
        position: relative;
        padding-left: 30px;
    }

    .suggestions-list li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #27ae60;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .floating-shapes {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
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
        background: #ff6b6b;
        border-radius: 50%;
        animation-delay: 0s;
    }

    .shape:nth-child(2) {
        top: 60%;
        right: 10%;
        width: 60px;
        height: 60px;
        background: #4ecdc4;
        border-radius: 50%;
        animation-delay: 2s;
    }

    .shape:nth-child(3) {
        bottom: 20%;
        left: 20%;
        width: 40px;
        height: 40px;
        background: #45b7d1;
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

    @media (max-width: 768px) {
        .error-content {
            padding: 40px 20px;
            margin: 20px;
        }

        .error-icon {
            font-size: 80px;
        }

        .error-title {
            font-size: 2rem;
        }

        .error-message {
            font-size: 1.1rem;
        }

        .error-actions {
            flex-direction: column;
            align-items: center;
        }

        .btn {
            width: 100%;
            max-width: 300px;
        }
    }

    @media (max-width: 480px) {
        .error-title {
            font-size: 1.5rem;
        }

        .error-message {
            font-size: 1rem;
        }

        .btn {
            padding: 12px 25px;
            font-size: 1rem;
        }
    }
</style>

<div class="error-container">
    <div class="error-content">
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>
        
        <div class="error-icon">🛒</div>
        
        <h1 class="error-title">Cart Error</h1>
        
        <p class="error-message">
            Oops! Something went wrong with your cart. Don't worry, we're here to help you get back on track!
        </p>
        
        <div class="error-actions">
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <a href="mycart.php" class="btn btn-secondary">View Cart</a>
        </div>
        
        <div class="error-suggestions">
            <h3 class="suggestions-title">What you can do:</h3>
            <ul class="suggestions-list">
                <li>Check your internet connection</li>
                <li>Clear your browser cache and cookies</li>
                <li>Try refreshing the page</li>
                <li>Contact our support team if the problem persists</li>
            </ul>
        </div>
    </div>
</div>

<?php include('footer.php')?>
