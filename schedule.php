<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';
requireLogin();

$message = '';
$error = '';

// Handle scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_post'])) {
    $channel_id = sanitizeInput($_POST['channel_id']);
    $message_text = sanitizeInput($_POST['message']);
    $scheduled_time = sanitizeInput($_POST['scheduled_time']);
    
    $stmt = $pdo->prepare("INSERT INTO posts (channel_id, message, scheduled_time) VALUES (?, ?, ?)");
    if ($stmt->execute([$channel_id, $message_text, $scheduled_time])) {
        $message = "Message scheduled successfully!";
    } else {
        $error = "Failed to schedule message!";
    }
}

// Handle post deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    if ($stmt->execute([$_GET['delete']])) {
        $message = "Post deleted successfully!";
    }
}

// Fetch scheduled posts
$posts = $pdo->query("
    SELECT p.*, c.channel_name, b.bot_username 
    FROM posts p 
    JOIN channels c ON p.channel_id = c.id 
    JOIN bots b ON c.bot_id = b.id 
    ORDER BY p.scheduled_time DESC
")->fetchAll();

$channels = $pdo->query("
    SELECT c.*, b.bot_username 
    FROM channels c 
    JOIN bots b ON c.bot_id = b.id 
    WHERE c.is_active = 1
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Messages - BJ AUTO MESSAGES</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h2><i class="fas fa-calendar-plus"></i> Schedule Messages</h2>
                    <p>Schedule automatic messages to your channels</p>
                </div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="form-card">
                <h3><i class="fas fa-clock"></i> Schedule New Message</h3>
                <form method="POST" action="">
                    <div class="form-row">
                        <label><i class="fab fa-telegram"></i> Select Channel/Group</label>
                        <select name="channel_id" required>
                            <option value="">Choose a channel...</option>
                            <?php foreach($channels as $channel): ?>
                            <option value="<?php echo $channel['id']; ?>">
                                <?php echo htmlspecialchars($channel['channel_name']); ?> (@<?php echo htmlspecialchars($channel['bot_username']); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label><i class="fas fa-envelope"></i> Message Content</label>
                        <textarea name="message" rows="5" placeholder="Enter your message here... Supports HTML formatting" required></textarea>
                        <small><i class="fas fa-info-circle"></i> You can use HTML tags: &lt;b&gt;, &lt;i&gt;, &lt;a href="..."&gt;, etc.</small>
                    </div>
                    
                    <div class="form-row">
                        <label><i class="fas fa-calendar-alt"></i> Schedule Date & Time</label>
                        <input type="datetime-local" name="scheduled_time" required>
                    </div>
                    
                    <button type="submit" name="schedule_post" class="btn-submit">
                        <i class="fas fa-save"></i> Schedule Message
                    </button>
                </form>
            </div>
            
            <div class="table-container" style="margin-top: 30px;">
                <h3><i class="fas fa-list"></i> Scheduled Messages</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Channel</th>
                            <th>Bot</th>
                            <th>Message</th>
                            <th>Scheduled Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($posts as $post): ?>
                        <tr>
                            <td><?php echo $post['id']; ?></td>
                            <td><?php echo htmlspecialchars($post['channel_name']); ?></td>
                            <td>@<?php echo htmlspecialchars($post['bot_username']); ?></td>
                            <td><?php echo substr(htmlspecialchars($post['message']), 0, 50) . '...'; ?></td>
                            <td><?php echo $post['scheduled_time']; ?></td>
                            <td><?php echo getStatusBadge($post['status']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure?')" style="color: #dc3545;">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>