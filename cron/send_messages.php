#!/usr/bin/env php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/telegram.php';

if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
}

function writeLog($message) {
    $logMessage = date('Y-m-d H:i:s') . " - " . $message . "\n";
    file_put_contents(__DIR__ . '/../logs/cron.log', $logMessage, FILE_APPEND);
    echo $logMessage;
}

writeLog("=== CRON JOB STARTED ===");

try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.chat_id, c.channel_name, b.bot_token, b.bot_username 
        FROM posts p 
        JOIN channels c ON p.channel_id = c.id 
        JOIN bots b ON c.bot_id = b.id 
        WHERE p.status = 'pending' 
        AND p.scheduled_time <= NOW()
        ORDER BY p.scheduled_time ASC
        LIMIT 10
    ");
    $stmt->execute();
    $pendingPosts = $stmt->fetchAll();
    
    writeLog("Found " . count($pendingPosts) . " pending messages to send");
    
    foreach ($pendingPosts as $post) {
        writeLog("Processing Post ID: {$post['id']} - Channel: {$post['channel_name']}");
        
        $success = sendTelegramMessage($post['bot_token'], $post['chat_id'], $post['message']);
        
        if ($success) {
            $updateStmt = $pdo->prepare("UPDATE posts SET status = 'sent', sent_at = NOW() WHERE id = ?");
            $updateStmt->execute([$post['id']]);
            writeLog("✅ SUCCESS: Message sent to {$post['channel_name']}");
        } else {
            $updateStmt = $pdo->prepare("UPDATE posts SET status = 'failed' WHERE id = ?");
            $updateStmt->execute([$post['id']]);
            writeLog("❌ FAILED: Could not send to {$post['channel_name']}");
        }
    }
    
    writeLog("=== CRON JOB COMPLETED ===");
    
} catch (Exception $e) {
    writeLog("ERROR: " . $e->getMessage());
}

echo "Cron job completed at " . date('Y-m-d H:i:s') . "\n";
?>
