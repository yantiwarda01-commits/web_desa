<?php
/**
 * Health Check Endpoint
 * Website Desa Sejahtera
 * 
 * Used by Docker healthcheck and monitoring systems
 */

header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'service' => 'Website Desa Sejahtera',
    'version' => '1.0.0'
];

// Check database connection
try {
    require_once __DIR__ . '/config/database.php';
    
    $dbStatus = testDatabaseConnection();
    
    if ($dbStatus['status']) {
        $health['database'] = 'connected';
        $health['database_host'] = $dbStatus['host'];
        $health['database_name'] = $dbStatus['database'] ?? 'N/A';
    } else {
        $health['status'] = 'warning';
        $health['database'] = 'disconnected';
        $health['database_error'] = $dbStatus['message'];
        http_response_code(503); // Service Unavailable
    }
} catch (Exception $e) {
    $health['status'] = 'error';
    $health['database'] = 'error';
    $health['error'] = $e->getMessage();
    http_response_code(503);
}

// Check writable directories
$uploadDir = __DIR__ . '/assets/uploads';
if (is_writable($uploadDir)) {
    $health['uploads_writable'] = true;
} else {
    $health['status'] = 'warning';
    $health['uploads_writable'] = false;
}

// PHP info
$health['php_version'] = PHP_VERSION;
$health['memory_usage'] = round(memory_get_usage() / 1024 / 1024, 2) . ' MB';

// Return JSON
echo json_encode($health, JSON_PRETTY_PRINT);
exit();
