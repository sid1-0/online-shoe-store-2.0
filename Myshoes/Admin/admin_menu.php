<header class="header">
    <div class="logo">
      <a href="dashboard.php">MY SHOE STORE</a>
    </div>
    <div class="header-icons">
      <div class="account">
      </div>
    </div>
</header>
<ul class="admin_menu">
    <?php
        $sn = explode('/', $_SERVER['SCRIPT_NAME']);
        $page = $sn[count($sn) - 1];
        $list_cat = isset($_GET['category']) ? $_GET['category'] : '';
        $is_list = ($page == 'list_shoes.php');
        if ($is_list && $list_cat === '') {
            $list_cat = 'shoes';
        }
        $is_add = ($page == 'add_shoes.php');
    ?>
    <li class="<?php echo ($page == 'dashboard.php') ? 'active_link' : ''; ?>"><a href="dashboard.php">Dashboard</a></li>
    <li class="<?php echo ($is_list && $list_cat === 'shoes') ? 'active_link' : ''; ?>"><a href="list_shoes.php?category=shoes">Shoes</a></li>
    <li class="<?php echo ($is_list && $list_cat === 'socks') ? 'active_link' : ''; ?>"><a href="list_shoes.php?category=socks">Socks</a></li>
    <li class="<?php echo ($is_list && $list_cat === 'shoe_care') ? 'active_link' : ''; ?>"><a href="list_shoes.php?category=shoe_care">Shoe Care</a></li>
    <li class="<?php echo $is_add ? 'active_link' : ''; ?>"><a href="add_shoes.php">Add Product</a></li>
    <li class="<?php echo ($page == 'coupons.php') ? 'active_link' : ''; ?>"><a href="coupons.php">Coupons</a></li>
    <li class="<?php echo ($page == 'orders.php') ? 'active_link' : ''; ?>"><a href="orders.php">Orders</a></li>
    <li class="<?php echo ($page == 'logout.php') ? 'active_link' : ''; ?>"><a href="logout.php">Logout</a></li>
</ul>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
    }

    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 70px;
        padding: 0 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .logo a {
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        text-decoration: none;
        letter-spacing: 1px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .logo a:hover {
        transform: scale(1.05);
    }

    .admin_menu {
        list-style-type: none;
        padding: 0;
        margin: 20px 30px;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-wrap: wrap;
        animation: slideDown 0.6s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .admin_menu li {
        flex: 1;
        position: relative;
    }

    .admin_menu li a {
        display: block;
        padding: 20px 25px;
        text-decoration: none;
        color: #495057;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 1rem;
        font-weight: 600;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .admin_menu li a::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        transition: all 0.4s ease;
        z-index: -1;
    }

    .admin_menu li a:hover::before {
        left: 0;
    }

    .admin_menu li a:hover {
        color: white;
        transform: translateY(-2px);
    }

    .admin_menu .active_link a {
        color: white;
        background: linear-gradient(135deg, #667eea, #764ba2);
        font-weight: 700;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .admin_menu .active_link a::before {
        left: 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header {
            padding: 0 20px;
            height: 60px;
        }

        .logo a {
            font-size: 1.3rem;
        }

        .admin_menu {
            margin: 15px 20px;
            flex-direction: column;
        }

        .admin_menu li a {
            padding: 15px 20px;
            font-size: 0.95rem;
        }
    }

    @media (max-width: 480px) {
        .header {
            padding: 0 15px;
        }

        .logo a {
            font-size: 1.2rem;
        }

        .admin_menu {
            margin: 10px 15px;
        }

        .admin_menu li a {
            padding: 12px 15px;
            font-size: 0.9rem;
        }
    }
</style>
