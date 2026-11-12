<?php
/**
 * Admin Logout
 * Website Desa Sejahtera
 */

require_once '../includes/session.php';

// Destroy session
session_unset();
session_destroy();

// Redirect ke login
header("Location: login.php");
exit();
