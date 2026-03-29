<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function getBotCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bots");
    $result = $stmt->fetch();
    return $result['count'];
}

function getChannelCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM channels");
    $result = $stmt->fetch();
    return $result['count'];
}

function getPendingPostsCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM posts WHERE status = 'pending' AND scheduled_time <= NOW()");
    $result = $stmt->fetch();
    return $result['count'];
}

function getTotalPostsCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM posts");
    $result = $stmt->fetch();
    return $result['count'];
}

function formatDateTime($datetime) {
    return date('Y-m-d H:i:s', strtotime($datetime));
}

function getStatusBadge($status) {
    switch($status) {
        case 'pending':
            return '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>';
        case 'sent':
            return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Sent</span>';
        case 'failed':
            return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Failed</span>';
        default:
            return '<span class="badge badge-secondary">Unknown</span>';
    }
}
?>
