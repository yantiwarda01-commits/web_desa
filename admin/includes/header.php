<?php
if (!defined('ADMIN_PAGE')) {
    die('Direct access not permitted');
}

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check login
requireLogin();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$admin_name = $_SESSION['admin_nama'] ?? 'Admin';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> - Website Desa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/admin.css" />
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fas fa-landmark"></i>
        </div>
        <div class="sidebar-logo-text">
            <h3>Admin Panel</h3>
            <p>Desa Sejahtera</p>
        </div>
    </div>
    
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo $current_page === 'index' ? 'active' : ''; ?>">
            <a href="index.php">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page === 'pengajuan' ? 'active' : ''; ?>">
            <a href="pengajuan.php">
                <i class="fas fa-file-alt"></i>
                <span>Pengajuan Surat</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page === 'berita' ? 'active' : ''; ?>">
            <a href="berita.php">
                <i class="fas fa-newspaper"></i>
                <span>Berita & Pengumuman</span>
            </a>
        </li>
        <li class="menu-item <?php echo $current_page === 'profil' ? 'active' : ''; ?>">
            <a href="profil.php">
                <i class="fas fa-building"></i>
                <span>Profil Desa</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="../index-landing.php" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>Lihat Website</span>
            </a>
        </li>
        <li class="menu-item">
            <a href="logout.php" onclick="return confirm('Yakin ingin logout?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-left">
        <h1><?php echo $page_title ?? 'Dashboard'; ?></h1>
    </div>
    <div class="topbar-right">
        <div class="user-menu">
            <div class="user-avatar"><?php echo $admin_initial; ?></div>
            <div class="user-info">
                <strong><?php echo sanitize($admin_name); ?></strong>
                <small><?php echo ucfirst($_SESSION['admin_level'] ?? 'admin'); ?></small>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="main-content">
    <div class="content-wrapper">
        <?php
        $flash = getFlashMessage();
        if ($flash):
        ?>
        <div class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'danger'; ?>">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <span><?php echo $flash['message']; ?></span>
        </div>
        <?php endif; ?>
