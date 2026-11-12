<?php
/**
 * Admin Login Page
 * Website Desa Sejahtera
 */

require_once '../includes/session.php';
require_once '../includes/functions.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi';
    } else {
        // Cek ke database
        $admin = fetchOne(
            "SELECT * FROM admin WHERE username = ? LIMIT 1",
            [$username],
            's'
        );
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Login berhasil
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];
            $_SESSION['admin_level'] = $admin['level'];
            
            // Update last login
            executeQuery(
                "UPDATE admin SET terakhir_login = NOW() WHERE id = ?",
                [$admin['id']],
                'i'
            );
            
            setFlashMessage('success', 'Selamat datang, ' . $admin['nama_lengkap'] . '!');
            redirect('index.php');
        } else {
            $error = 'Username atau password salah';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Website Desa</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #16a34a 0%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-container {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #16a34a, #2563eb);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-header .logo {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
        .login-header h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
        .login-header p { font-size: 0.85rem; opacity: 0.9; }
        .login-body { padding: 2rem; }
        .alert {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-group i {
            position: absolute;
            left: 1rem;
            color: #9ca3af;
            font-size: 0.95rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 1.5px solid #d1d5db;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #15803d, #14532d);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.4);
        }
        .login-footer {
            text-align: center;
            padding: 1.5rem 2rem 2rem;
            border-top: 1px solid #e5e7eb;
        }
        .login-footer a {
            color: #16a34a;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .login-footer a:hover { text-decoration: underline; }
        .demo-credentials {
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #1e40af;
        }
        .demo-credentials strong { display: block; margin-bottom: 0.5rem; color: #1e3a8a; }
    </style>
</head>
<body>
    
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-landmark"></i>
            </div>
            <h1>Admin Panel</h1>
            <p>Website Desa Sejahtera</p>
        </div>
        
        <div class="login-body">
            <?php if (!empty($error)): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $error; ?></span>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required autofocus />
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required />
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </button>
            </form>
            
            <div class="demo-credentials">
                <strong><i class="fas fa-info-circle"></i> Akun Demo:</strong>
                Username: <code>admin</code> | Password: <code>admin123</code><br>
                <small style="opacity: 0.8;">Ganti password setelah login pertama kali</small>
            </div>
        </div>
        
        <div class="login-footer">
            <a href="../index-landing.php">
                <i class="fas fa-arrow-left"></i> Kembali ke Website
            </a>
        </div>
    </div>
    
</body>
</html>
