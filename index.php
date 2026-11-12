<?php
/**
 * Main Entry Point
 * Website Desa Sejahtera
 * 
 * This file redirects to index-landing.php with error handling
 */

// Enable error logging but hide from display
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

// Try to include the main landing page
try {
    require_once __DIR__ . '/index-landing.php';
} catch (Exception $e) {
    // Log the error
    error_log("Error loading index-landing.php: " . $e->getMessage());
    
    // Show a user-friendly error page
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Website Desa - Sedang Maintenance</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-container {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 600px;
                text-align: center;
            }
            .error-icon {
                font-size: 80px;
                margin-bottom: 20px;
            }
            h1 {
                color: #333;
                font-size: 28px;
                margin-bottom: 15px;
            }
            p {
                color: #666;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .debug-links {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 2px solid #eee;
            }
            .debug-links a {
                display: inline-block;
                margin: 5px;
                padding: 10px 20px;
                background: #667eea;
                color: white;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 600;
            }
            .debug-links a:hover {
                background: #5568d3;
            }
            code {
                background: #f5f5f5;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">🔧</div>
            <h1>Website Sedang Dalam Perbaikan</h1>
            <p>Maaf, website sedang mengalami masalah teknis. Tim kami sedang bekerja untuk memperbaikinya.</p>
            <p>Kemungkinan penyebab:</p>
            <ul style="text-align: left; margin: 20px auto; max-width: 400px;">
                <li>Database belum siap (masih inisialisasi)</li>
                <li>Koneksi database bermasalah</li>
                <li>Konfigurasi belum lengkap</li>
            </ul>
            
            <div class="debug-links">
                <strong>Debug Tools (Untuk Administrator):</strong><br><br>
                <a href="debug-info.php">📊 Debug Info</a>
                <a href="test-connection.php">🔌 Test Connection</a>
                <a href="health.php">❤️ Health Check</a>
            </div>
            
            <p style="margin-top: 30px; font-size: 12px; color: #999;">
                Jika Anda administrator, jalankan: <code>docker-compose logs webserver</code>
            </p>
        </div>
    </body>
    </html>
    <?php
    exit();
}
