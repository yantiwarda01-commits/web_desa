<?php
/**
 * Konfigurasi Koneksi Database
 * Website Desa Sejahtera
 * 
 * Mode: Docker / Production with ProxySQL
 * 
 * Docker Environment:
 * - DB_HOST: proxysql (via ProxySQL load balancer)
 * - DB_PORT: 6033 (ProxySQL MySQL interface)
 * - DB_USER: desa_user
 * - DB_PASS: desa_pass_2024
 * 
 * Local Development (non-Docker):
 * - DB_HOST: localhost
 * - DB_PORT: 3306
 * - DB_USER: root
 * - DB_PASS: (empty or your local password)
 */

// ============================================
// DATABASE CONSTANTS
// ============================================

// Auto-detect environment (Docker vs Local)
$isDocker = file_exists('/.dockerenv');

if ($isDocker) {
    // Docker environment - Use environment variables or defaults
    define('DB_HOST', getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'mysql');
    define('DB_PORT', getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306');
    define('DB_USER', getenv('DB_USER') !== false ? getenv('DB_USER') : 'desa_user');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'desa_pass_2024');
    define('DB_NAME', getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'db_desa_sejahtera');
} else {
    // Local development environment
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'db_desa_sejahtera');
}

define('DB_CHARSET', 'utf8mb4');

// Connection timeout in seconds
define('DB_TIMEOUT', 10);

// ============================================
// CONNECTION FUNCTION
// ============================================

/**
 * Fungsi untuk membuat koneksi database
 * Mendukung koneksi via ProxySQL (Docker) atau MySQL langsung (Local)
 * 
 * @return mysqli|false
 */
function getConnection() {
    static $conn = null;
    
    if ($conn === null) {
        // Disable mysqli error reporting to handle errors manually
        mysqli_report(MYSQLI_REPORT_OFF);
        
        // Create connection with custom port
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);
        
        // Check connection
        if ($conn->connect_error) {
            // Log error
            error_log("Database connection failed: " . $conn->connect_error);
            
            // Return false instead of die for graceful degradation
            return false;
        }
        
        // Set charset
        if (!$conn->set_charset(DB_CHARSET)) {
            error_log("Error setting charset: " . $conn->error);
        }
        
        // Set connection timeout
        $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, DB_TIMEOUT);
        
        // Automatic reconnect is unavailable in this PHP mysqli build.
        // Set timezone to Asia/Jakarta
        $conn->query("SET time_zone = '+07:00'");
    }
    
    return $conn;
}

/**
 * Fungsi untuk menutup koneksi database
 */
function closeConnection() {
    $conn = getConnection();
    if ($conn && !$conn->connect_error) {
        $conn->close();
    }
}

/**
 * Fungsi helper untuk prepared statement
 * @param string $query
 * @param array $params
 * @param string $types
 * @return mysqli_stmt|false
 */
function prepareStatement($query, $params = [], $types = '') {
    $conn = getConnection();
    
    // Check if connection is valid
    if ($conn === false || $conn->connect_error) {
        error_log("Database connection not available for prepare statement");
        return false;
    }
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("Prepare statement gagal: " . $conn->error . " | Query: " . $query);
        return false;
    }
    
    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    
    return $stmt;
}

/**
 * Fungsi untuk execute query dengan prepared statement
 * @param string $query
 * @param array $params
 * @param string $types
 * @return bool
 */
function executeQuery($query, $params = [], $types = '') {
    $stmt = prepareStatement($query, $params, $types);
    
    if ($stmt === false) {
        return false;
    }
    
    $result = $stmt->execute();
    
    if ($result === false) {
        error_log("Execute query gagal: " . $stmt->error);
    }
    
    $stmt->close();
    
    return $result;
}

/**
 * Fungsi untuk mengambil satu baris data
 * @param string $query
 * @param array $params
 * @param string $types
 * @return array|null
 */
function fetchOne($query, $params = [], $types = '') {
    $stmt = prepareStatement($query, $params, $types);
    
    if ($stmt === false) {
        return null;
    }
    
    if (!$stmt->execute()) {
        error_log("Execute query failed in fetchOne: " . $stmt->error);
        $stmt->close();
        return null;
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        error_log("Get result failed in fetchOne");
        $stmt->close();
        return null;
    }
    
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}

/**
 * Fungsi untuk mengambil banyak baris data
 * @param string $query
 * @param array $params
 * @param string $types
 * @return array
 */
function fetchAll($query, $params = [], $types = '') {
    $stmt = prepareStatement($query, $params, $types);
    
    if ($stmt === false) {
        return [];
    }
    
    if (!$stmt->execute()) {
        error_log("Execute query failed in fetchAll: " . $stmt->error);
        $stmt->close();
        return [];
    }
    
    $result = $stmt->get_result();
    if ($result === false) {
        error_log("Get result failed in fetchAll");
        $stmt->close();
        return [];
    }
    
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    
    $stmt->close();
    
    return $rows;
}

/**
 * Fungsi untuk mendapatkan ID terakhir yang diinsert
 * @return int
 */
function getLastInsertId() {
    $conn = getConnection();
    return $conn->insert_id;
}

/**
 * Fungsi untuk escape string (hindari SQL injection)
 * @param string $str
 * @return string
 */
function escapeString($str) {
    $conn = getConnection();
    return $conn->real_escape_string($str);
}

/**
 * Fungsi untuk test koneksi database
 * Berguna untuk health check dan debugging
 * 
 * @return array ['status' => bool, 'message' => string, 'host' => string]
 */
function testDatabaseConnection() {
    try {
        $conn = getConnection();
        
        if ($conn->connect_error) {
            return [
                'status' => false,
                'message' => 'Connection failed: ' . $conn->connect_error,
                'host' => DB_HOST . ':' . DB_PORT
            ];
        }
        
        // Test query
        $result = $conn->query("SELECT 1 as test");
        if ($result === false) {
            return [
                'status' => false,
                'message' => 'Query test failed: ' . $conn->error,
                'host' => DB_HOST . ':' . DB_PORT
            ];
        }
        
        return [
            'status' => true,
            'message' => 'Connection successful',
            'host' => DB_HOST . ':' . DB_PORT,
            'database' => DB_NAME,
            'user' => DB_USER,
            'server_version' => $conn->server_info,
            'charset' => DB_CHARSET
        ];
        
    } catch (Exception $e) {
        return [
            'status' => false,
            'message' => 'Exception: ' . $e->getMessage(),
            'host' => DB_HOST . ':' . DB_PORT
        ];
    }
}
