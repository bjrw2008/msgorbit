#!/usr/bin/env php
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/telegram.php';

// This file should be run every minute via cron job
// crontab -e
// * * * * * /usr/bin/php /path/to/bj-auto-messages/cron/send_messages.php

$stmt = $pdo->prepare("
    SELECT p.*, c.chat_id, c.channel_name, b.bot_token, b.bot_username 
    FROM posts p 
    JOIN channels c ON p.channel_id = c.id 
    JOIN bots b ON c.bot_id = b.id 
    WHERE p.status = 'pending' 
    AND p.scheduled_time <= NOW()
    LIMIT 10
");
$stmt->execute();
$pendingPosts = $stmt->fetchAll();

foreach ($pendingPosts as $post) {
    $success = sendTelegramMessage($post['bot_token'], $post['chat_id'], $post['message']);
    
    $status = $success ? 'sent' : 'failed';
    $sentAt = $success ? date('Y-m-d H:i:s') : null;
    
    $updateStmt = $pdo->prepare("UPDATE posts SET status = ?, sent_at = ? WHERE id = ?");
    $updateStmt->execute([$status, $sentAt, $post['id']]);
    
    // Log the action
    $logMessage = date('Y-m-d H:i:s') . " - Post ID: {$post['id']} - Status: {$status}\n";
    file_put_contents(__DIR__ . '/../logs/cron.log', $logMessage, FILE_APPEND);
}

echo "Cron job completed at " . date('Y-m-d H:i:s') . "\n";
?>