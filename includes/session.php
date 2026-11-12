<?php
/**
 * Session Management
 * Website Desa Sejahtera
 */

// Set session configuration untuk keamanan
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID secara periodik untuk keamanan
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) {
    // Session sudah berjalan lebih dari 30 menit, regenerate ID
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}

// Fungsi untuk check session timeout (30 menit tidak aktif)
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // Last activity lebih dari 30 menit, destroy session
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time();
