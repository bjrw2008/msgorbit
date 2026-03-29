<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';
require_once 'includes/telegram.php';
requireLogin();

$message = '';
$error = '';

// Handle channel addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_channel'])) {
    $bot_id = sanitizeInput($_POST['bot_id']);
    $channel_name = sanitizeInput($_POST['channel_name']);
    $channel_username = sanitizeInput($_POST['channel_username']);
    $chat_id = sanitizeInput($_POST['chat_id']);
    
    $stmt = $pdo->prepare("INSERT INTO channels (bot_id, channel_name, channel_username, chat_id) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$bot_id, $channel_name, $channel_username, $chat_id])) {
        $message = "Channel added successfully!";
    } else {
        $error = "Failed to add channel!";
    }
}

// Handle channel deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM channels WHERE id = ?");
    if ($stmt->execute([$_GET['delete']])) {
        $message = "Channel deleted successfully!";
    }
}

// Fetch all channels with bot info
$channels = $pdo->query("
    SELECT c.*, b.bot_username 
    FROM channels c 
    JOIN bots b ON c.bot_id = b.id 
    ORDER BY c.created_at DESC
")->fetchAll();

$bots = $pdo->query("SELECT id, bot_username FROM bots WHERE is_active = 1")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Channels Management - BJ AUTO MESSAGES</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h2><i class="fas fa-telegram"></i> Channel Management</h2>
                    <p>Add and manage Telegram channels/groups</p>
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
                <h3><i class="fas fa-plus-circle"></i> Add New Channel/Group</h3>
                <form method="POST" action="">
                    <div class="form-row">
                        <label><i class="fas fa-robot"></i> Select Bot</label>
                        <select name="bot_id" required>
                            <option value="">Choose a bot...</option>
                            <?php foreach($bots as $bot): ?>
                            <option value="<?php echo $bot['id']; ?>">@<?php echo htmlspecialchars($bot['bot_username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <label><i class="fas fa-hashtag"></i> Channel Name</label>
                        <input type="text" name="channel_name" placeholder="e.g., My Awesome Channel" required>
                    </div>
                    
                    <div class="form-row">
                        <label><i class="fab fa-telegram"></i> Channel Username</label>
                        <input type="text" name="channel_username" placeholder="@mychannel (optional)">
                    </div>
                    
                    <div class="form-row">
                        <label><i class="fas fa-id-card"></i> Chat ID</label>
                        <input type="text" name="chat_id" placeholder="-1001234567890 or @username" required>
                    </div>
                    
                    <button type="submit" name="add_channel" class="btn-submit">
                        <i class="fas fa-save"></i> Add Channel
                    </button>
                </form>
            </div>
            
            <div class="table-container" style="margin-top: 30px;">
                <h3><i class="fas fa-list"></i> Your Channels/Groups</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Channel Name</th>
                            <th>Bot</th>
                            <th>Chat ID</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($channels as $channel): ?>
                        <tr>
                            <td><?php echo $channel['id']; ?></td>
                            <td><i class="fab fa-telegram"></i> <?php echo htmlspecialchars($channel['channel_name']); ?></td>
                            <td>@<?php echo htmlspecialchars($channel['bot_username']); ?></td>
                            <td><code><?php echo htmlspecialchars($channel['chat_id']); ?></code></td>
                            <td><?php echo $channel['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?></td>
                            <td><?php echo $channel['created_at']; ?></td>
                            <td>
                                <a href="?delete=<?php echo $channel['id']; ?>" onclick="return confirm('Are you sure?')" style="color: #dc3545;">
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