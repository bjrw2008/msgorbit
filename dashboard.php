<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';
requireLogin();

$admin = getCurrentAdmin($pdo);
$botCount = getBotCount($pdo);
$channelCount = getChannelCount($pdo);
$pendingPosts = getPendingPostsCount($pdo);
$totalPosts = getTotalPostsCount($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BJ AUTO MESSAGES</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
                    <p>Welcome back, <?php echo htmlspecialchars($admin['full_name']); ?>!</p>
                </div>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($admin['phone']); ?></span>
                    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><i class="fas fa-robot"></i> Total Bots</h3>
                        <div class="number"><?php echo $botCount; ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><i class="fas fa-telegram"></i> Channels/Groups</h3>
                        <div class="number"><?php echo $channelCount; ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fab fa-telegram"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><i class="fas fa-clock"></i> Pending Posts</h3>
                        <div class="number"><?php echo $pendingPosts; ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><i class="fas fa-envelope"></i> Total Posts</h3>
                        <div class="number"><?php echo $totalPosts; ?></div>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>
            </div>
            
            <div class="table-container">
                <h3><i class="fas fa-calendar-alt"></i> Recent Scheduled Messages</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Channel</th>
                            <th>Message</th>
                            <th>Scheduled Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT p.*, c.channel_name FROM posts p JOIN channels c ON p.channel_id = c.id ORDER BY p.scheduled_time DESC LIMIT 10");
                        while($post = $stmt->fetch()):
                        ?>
                        <tr>
                            <td><?php echo $post['id']; ?></td>
                            <td><?php echo htmlspecialchars($post['channel_name']); ?></td>
                            <td><?php echo substr(htmlspecialchars($post['message']), 0, 50) . '...'; ?></td>
                            <td><?php echo $post['scheduled_time']; ?></td>
                            <td><?php echo getStatusBadge($post['status']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>