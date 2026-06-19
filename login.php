<?php
require_once 'includes/auth.php';
startSession();
if (isLoggedIn()) { header('Location: index.php'); exit; }

$error = '';
$success = '';
$mode = $_GET['mode'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'login';
    if ($mode === 'register') {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        if (!$username || !$email || !$password) {
            $error = 'Semua field wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
        } elseif ($password !== $confirm) {
            $error = 'Konfirmasi password tidak cocok.';
        } else {
            $result = registerUser($username, $email, $password);
            if ($result['success']) {
                $success = 'Registrasi berhasil! Silakan login.';
                $mode = 'login';
            } else {
                $error = $result['message'];
            }
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$username || !$password) {
            $error = 'Username dan password wajib diisi.';
        } else {
            $result = loginUser($username, $password);
            if ($result['success']) {
                header('Location: index.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TaskFlow — <?= $mode === 'login' ? 'Masuk' : 'Daftar' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg: #0A0A0C;
    --surface: #121214;
    --surface2: #1A1A1E;
    --border: rgba(255,255,255,0.05);
    --border-hover: rgba(255,255,255,0.12);
    --text: #FAFAFA;
    --muted: #52525B;
    --accent: #FACC15;
    --accent-hover: #EAB308;
    --accent-light: #FEF08A;
    --danger: #EF4444;
    --success: #10B981;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    position: relative;
    overflow-x: hidden;
}

.split-layout {
    display: flex;
    width: 100%;
    min-height: 100vh;
}

.left-panel {
    display: none;
    flex: 1;
    background: #0A0A0C;
    position: relative;
    overflow: hidden;
    padding: 3rem;
    flex-direction: column;
    justify-content: center;
    border-right: 1px solid var(--border);
}

@media (min-width: 1024px) {
    .left-panel {
        display: flex;
    }
}

.branding-content {
    position: relative;
    z-index: 10;
    max-width: 480px;
}

.branding-logo {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 2rem;
}

.logo-dot {
    width: 8px;
    height: 8px;
    background: var(--accent);
    border-radius: 50%;
    flex-shrink: 0;
}

.logo-name {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.03em;
    color: var(--text);
}

.branding-title {
    font-size: 3rem;
    font-weight: 700;
    letter-spacing: -0.03em;
    margin-bottom: 1rem;
    color: #fff;
}

.branding-desc {
    font-size: 1.125rem;
    color: var(--text2);
    line-height: 1.6;
}

.right-panel {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
}

.auth-container {
    width: 100%;
    max-width: 400px;
    animation: fadeUp 0.5s ease-out;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.mobile-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    margin-bottom: 2rem;
}

@media (min-width: 1024px) {
    .mobile-logo {
        display: none;
    }
}

.mobile-logo-text {
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-title {
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}

.auth-subtitle {
    color: var(--muted);
    font-size: 0.95rem;
}

.tabs {
    display: flex;
    background: var(--surface2);
    border-radius: 8px;
    padding: 4px;
    margin-bottom: 2rem;
    border: 1px solid var(--border);
}

.tab {
    flex: 1;
    padding: 0.6rem;
    text-align: center;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    color: var(--muted);
    text-decoration: none;
    transition: all 0.2s ease;
}

.tab:hover {
    color: var(--text);
}

.tab.active {
    background: var(--surface);
    color: var(--text);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    border: 1px solid var(--border);
}

.form-group {
    margin-bottom: 1.25rem;
}

label {
    display: block;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text);
    margin-bottom: 0.5rem;
}

input {
    width: 100%;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    color: var(--text);
    font-family: inherit;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    outline: none;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

input:hover {
    border-color: var(--border-hover);
}

input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(250, 204, 21, 0.15);
}

input::placeholder {
    color: var(--muted);
}

.btn {
    width: 100%;
    padding: 0.8rem;
    background: var(--accent);
    border: none;
    border-radius: 8px;
    color: #000;
    font-family: inherit;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 0.5rem;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
}

.btn:active {
    transform: translateY(0);
}

.alert {
    padding: 0.8rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert i {
    flex-shrink: 0;
    width: 18px;
    height: 18px;
}

.alert-error {
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.15);
    color: #FCA5A5;
}

.alert-success {
    background: rgba(16, 185, 129, 0.08);
    border: 1px solid rgba(16, 185, 129, 0.15);
    color: #6EE7B7;
}
</style>
</head>
<body>

<div class="split-layout">
    <!-- Left Panel: Branding -->
    <div class="left-panel">
        <div class="branding-content">
            <div class="branding-logo">
                <div class="logo-dot"></div>
                <div class="logo-name">TaskFlow</div>
            </div>
            <p class="branding-desc">Tingkatkan produktivitas Anda dengan manajemen tugas yang modern, cepat, dan terorganisir.</p>
        </div>
    </div>

    <!-- Right Panel: Auth Form -->
    <div class="right-panel">
        <div class="auth-container">
            
            <div class="mobile-logo">
                <div class="logo-dot"></div>
                <div class="mobile-logo-text">TaskFlow</div>
            </div>

            <div class="auth-header">
                <h1 class="auth-title"><?= $mode === 'login' ? 'Selamat Datang Kembali' : 'Buat Akun Baru' ?></h1>
                <p class="auth-subtitle"><?= $mode === 'login' ? 'Masuk ke akun Anda untuk melanjutkan' : 'Bergabung dan kelola tugas Anda dengan mudah' ?></p>
            </div>

            <div class="tabs">
                <a href="?mode=login" class="tab <?= $mode === 'login' ? 'active' : '' ?>">Masuk</a>
                <a href="?mode=register" class="tab <?= $mode === 'register' ? 'active' : '' ?>">Daftar</a>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i data-lucide="alert-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                <i data-lucide="check-circle"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
            <?php endif; ?>

            <?php if ($mode === 'login'): ?>
            <form method="POST">
                <input type="hidden" name="mode" value="login">
                <div class="form-group">
                    <label>Username atau Email</label>
                    <input type="text" name="username" placeholder="Masukkan username / email" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button class="btn" type="submit">
                    Masuk
                    <i data-lucide="arrow-right" style="width:16px;height:16px"></i>
                </button>
            </form>

            <?php else: ?>
            <form method="POST">
                <input type="hidden" name="mode" value="register">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Pilih username Anda" required autofocus>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="nama@email.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="confirm" placeholder="Ulangi password" required>
                </div>
                <button class="btn" type="submit">
                    Daftar Sekarang
                    <i data-lucide="arrow-right" style="width:16px;height:16px"></i>
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
</body>
</html>
