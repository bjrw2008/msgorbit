<?php
function sendTelegramMessage($botToken, $chatId, $message) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => false
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    if ($result === false) {
        return false;
    }
    
    $response = json_decode($result, true);
    return isset($response['ok']) && $response['ok'] === true;
}

function validateBotToken($botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/getMe";
    $result = file_get_contents($url);
    
    if ($result === false) {
        return false;
    }
    
    $response = json_decode($result, true);
    return isset($response['ok']) && $response['ok'] === true;
}

function getBotInfo($botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/getMe";
    $result = file_get_contents($url);
    
    if ($result === false) {
        return null;
    }
    
    $response = json_decode($result, true);
    if (isset($response['ok']) && $response['ok'] === true) {
        return $response['result'];
    }
    
    return null;
}
?>