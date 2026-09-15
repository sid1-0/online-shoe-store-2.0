<?php
session_start();
if(!isset($_SESSION['admin_id']))
{
	header('location:index.php?err=1');
    exit();
}

require_once 'connection.php';
date_default_timezone_set('Asia/Kathmandu');

$res = $connection->query("SELECT COUNT(*) AS total FROM shoes");
$total_shoes = $res->fetch_assoc()['total'] ?? 0;

$res = $connection->query("SELECT COUNT(DISTINCT COALESCE(transaction_uuid, id)) AS total FROM purchase_request");
$total_orders = $res->fetch_assoc()['total'] ?? 0;

$res = $connection->query("SELECT COUNT(*) AS total FROM users");
$total_users = $res->fetch_assoc()['total'] ?? 0;

$res = $connection->query("SELECT SUM(COALESCE(pr.amount, s.price)) AS total FROM purchase_request pr LEFT JOIN shoes s ON pr.shoe_id = s.shoe_id WHERE pr.status = 'Accepted'");
$total_revenue = $res->fetch_assoc()['total'] ?? 0;

$activities = [];

$res = $connection->query("SELECT name, created_at FROM shoes ORDER BY created_at DESC LIMIT 2");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $activities[] = [
            'title' => 'New shoe "' . $row['name'] . '" added',
            'time' => strtotime($row['created_at']),
            'icon' => '👟'
        ];
    }
}

$res = $connection->query("SELECT MIN(id) AS order_id, MAX(created_at) AS created_at FROM purchase_request WHERE status = 'Accepted' GROUP BY COALESCE(transaction_uuid, id) ORDER BY MAX(created_at) DESC LIMIT 2");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $activities[] = [
            'title' => 'Order #' . $row['order_id'] . ' completed',
            'time' => strtotime($row['created_at']),
            'icon' => '📦'
        ];
    }
}

$res = $connection->query("SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 2");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $activities[] = [
            'title' => 'New user "' . $row['username'] . '" registered',
            'time' => strtotime($row['created_at']),
            'icon' => '👥'
        ];
    }
}

$res = $connection->query("SELECT category, updated_at FROM shoes WHERE updated_at IS NOT NULL GROUP BY category ORDER BY MAX(updated_at) DESC LIMIT 2");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $activities[] = [
            'title' => 'Category "' . ucfirst($row['category']) . '" updated',
            'time' => strtotime($row['updated_at']),
            'icon' => '🏷️'
        ];
    }
}

usort($activities, function($a, $b) {
    return $b['time'] - $a['time'];
});
$activities = array_slice($activities, 0, 4);

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime();
    $ago->setTimestamp($datetime);
    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    
    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - My Shoe Store</title>
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
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .welcome-section p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .stat-icon.shoes {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .stat-icon.orders {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        .stat-icon.users {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }

        .stat-icon.revenue {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 1rem;
            color: #7f8c8d;
            font-weight: 500;
        }

        .quick-actions {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .quick-actions h2 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: none;
            border-radius: 12px;
            text-decoration: none;
            color: #495057;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            font-size: 1.2rem;
        }

        .action-btn:hover .action-icon {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .recent-activity {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .recent-activity h2 {
            font-size: 1.8rem;
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .activity-time {
            font-size: 0.875rem;
            color: #7f8c8d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px 15px;
            }

            .welcome-section {
                padding: 30px 20px;
            }

            .welcome-section h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }

            .stat-card {
                padding: 25px 20px;
            }

            .action-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'admin_menu.php'; ?>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>Welcome to Admin Dashboard</h1>
            <p>Manage your shoe store efficiently with our comprehensive admin panel</p>
            <div style="margin-top: 20px; font-size: 0.9rem; opacity: 0.8;">
                Last login: <?php echo date('F j, Y \a\t g:i A'); ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon shoes">👟</div>
                <div class="stat-number"><?php echo htmlspecialchars($total_shoes); ?></div>
                <div class="stat-label">Total Shoes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orders">📦</div>
                <div class="stat-number"><?php echo htmlspecialchars($total_orders); ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon users">👥</div>
                <div class="stat-number"><?php echo htmlspecialchars($total_users); ?></div>
                <div class="stat-label">Registered Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon revenue">💰</div>
                <div class="stat-number">Rs <?php echo number_format((float)$total_revenue, 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-grid">
                <a href="add_shoes.php" class="action-btn">
                    <div class="action-icon">➕</div>
                    <span>Add Product</span>
                </a>
                <a href="list_shoes.php?category=shoes" class="action-btn">
                    <div class="action-icon">👟</div>
                    <span>Manage Shoes</span>
                </a>
                <a href="list_shoes.php?category=socks" class="action-btn">
                    <div class="action-icon">🧦</div>
                    <span>Manage Socks</span>
                </a>
                <a href="list_shoes.php?category=shoe_care" class="action-btn">
                    <div class="action-icon">🧹</div>
                    <span>Manage Cleaner</span>
                </a>
                <a href="orders.php" class="action-btn">
                    <div class="action-icon">📊</div>
                    <span>View Orders</span>
                </a>
            </div>
        </div>

        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <?php if (empty($activities)): ?>
                <p>No recent activity found.</p>
            <?php else: ?>
                <?php foreach ($activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon"><?php echo $activity['icon']; ?></div>
                        <div class="activity-content">
                            <div class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></div>
                            <div class="activity-time"><?php echo time_elapsed_string($activity['time']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
