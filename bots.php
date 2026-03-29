<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions.php';
require_once 'includes/telegram.php';
requireLogin();

$message = '';
$error = '';

// Handle bot addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_bot'])) {
    $botToken = sanitizeInput($_POST['bot_token']);
    
    if (validateBotToken($botToken)) {
        $botInfo = getBotInfo($botToken);
        $botUsername = $botInfo['username'];
        
        $stmt = $pdo->prepare("INSERT INTO bots (bot_token, bot_username) VALUES (?, ?)");
        if ($stmt->execute([$botToken, $botUsername])) {
            $message = "Bot added successfully!";
        } else {
            $error = "Failed to add bot!";
        }
    } else {
        $error = "Invalid bot token!";
    }
}

// Handle bot deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM bots WHERE id = ?");
    if ($stmt->execute([$_GET['delete']])) {
        $message = "Bot deleted successfully!";
    }
}

// Fetch all bots
$bots = $pdo->query("SELECT * FROM bots ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bots Management - BJ AUTO MESSAGES</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="top-bar">
                <div class="page-title">
                    <h2><i class="fas fa-robot"></i> Bot Management</h2>
                    <p>Add and manage your Telegram bots</p>
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
                <h3><i class="fas fa-plus-circle"></i> Add New Bot</h3>
                <form method="POST" action="">
                    <div class="form-row">
                        <label><i class="fas fa-key"></i> Bot Token</label>
                        <input type="text" name="bot_token" placeholder="Enter your bot token from @BotFather" required>
                    </div>
                    <button type="submit" name="add_bot" class="btn-submit">
                        <i class="fas fa-save"></i> Add Bot
                    </button>
                </form>
            </div>
            
            <div class="table-container" style="margin-top: 30px;">
                <h3><i class="fas fa-list"></i> Your Bots</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Bot Username</th>
                            <th>Token</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($bots as $bot): ?>
                        <tr>
                            <td><?php echo $bot['id']; ?></td>
                            <td><i class="fab fa-telegram"></i> @<?php echo htmlspecialchars($bot['bot_username']); ?></td>
                            <td><code><?php echo substr($bot['bot_token'], 0, 20) . '...'; ?></code></td>
                            <td><?php echo $bot['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'; ?></td>
                            <td><?php echo $bot['created_at']; ?></td>
                            <td>
                                <a href="?delete=<?php echo $bot['id']; ?>" onclick="return confirm('Are you sure?')" style="color: #dc3545;">
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