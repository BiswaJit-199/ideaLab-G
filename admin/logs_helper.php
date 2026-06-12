<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function addLog($action, $details = '') {
    $logDir = dirname(__DIR__) . '/data';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    $logFile = $logDir . '/logs.json';
    $logs = [];
    if (file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true) ?? [];
    }
    
    $newLog = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $_SESSION['username'] ?? 'admin',
        'action' => $action,
        'details' => $details,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
    ];
    
    array_unshift($logs, $newLog);
    
    // Keep max 300 logs to stay efficient
    if (count($logs) > 300) {
        $logs = array_slice($logs, 0, 300);
    }
    
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
}
?>