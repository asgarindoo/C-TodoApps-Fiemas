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
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg: #0f0f11;
    --surface: #17171a;
    --surface2: #1e1e22;
    --border: #2a2a30;
    --text: #e8e8ed;
    --muted: #6b6b78;
    --accent: #a78bfa;
    --accent2: #7c3aed;
    --danger: #f87171;
    --success: #34d399;
    --warn: #fbbf24;
}

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

body::before {
    content: '';
    position: fixed;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(124,58,237,.15) 0%, transparent 70%);
    top: -100px; left: -100px;
    pointer-events: none;
}
body::after {
    content: '';
    position: fixed;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(167,139,250,.08) 0%, transparent 70%);
    bottom: -80px; right: -80px;
    pointer-events: none;
}

.auth-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2.5rem;
    width: 100%;
    max-width: 420px;
    position: relative;
    z-index: 1;
    animation: slideUp .4s ease;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.logo {
    display: flex;
    align-items: center;
    gap: .6rem;
    margin-bottom: 2rem;
}
.logo-icon {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, var(--accent2), var(--accent));
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.logo-text {
    font-size: 1.25rem;
    font-weight: 600;
    letter-spacing: -.02em;
}
.logo-sub { color: var(--muted); font-size: .8rem; margin-top: 1px; }

h1 { font-size: 1.4rem; font-weight: 600; margin-bottom: .4rem; }
.subtitle { color: var(--muted); font-size: .88rem; margin-bottom: 1.8rem; }

.tabs {
    display: flex;
    background: var(--surface2);
    border-radius: 10px;
    padding: 4px;
    margin-bottom: 1.8rem;
    gap: 4px;
}
.tab {
    flex: 1;
    padding: .5rem;
    text-align: center;
    border-radius: 7px;
    font-size: .875rem;
    font-weight: 500;
    cursor: pointer;
    color: var(--muted);
    text-decoration: none;
    transition: all .2s;
}
.tab.active {
    background: var(--surface);
    color: var(--text);
    box-shadow: 0 1px 3px rgba(0,0,0,.4);
}

.form-group { margin-bottom: 1.1rem; }
label { display: block; font-size: .82rem; color: var(--muted); margin-bottom: .4rem; letter-spacing: .02em; }
input {
    width: 100%;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: .7rem 1rem;
    color: var(--text);
    font-family: inherit;
    font-size: .9rem;
    transition: border-color .2s;
    outline: none;
}
input:focus { border-color: var(--accent); }
input::placeholder { color: var(--muted); }

.btn {
    width: 100%;
    padding: .75rem;
    background: linear-gradient(135deg, var(--accent2), var(--accent));
    border: none;
    border-radius: 10px;
    color: #fff;
    font-family: inherit;
    font-size: .95rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: .5rem;
    transition: opacity .2s, transform .1s;
}
.btn:hover { opacity: .9; }
.btn:active { transform: scale(.99); }

.alert {
    padding: .7rem 1rem;
    border-radius: 10px;
    font-size: .85rem;
    margin-bottom: 1.2rem;
}
.alert-error { background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.3); color: var(--danger); }
.alert-success { background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.3); color: var(--success); }
</style>
</head>
<body>
<div class="auth-card">
    <div class="logo">
        <div class="logo-icon">✅</div>
        <div>
            <div class="logo-text">TaskFlow</div>
            <div class="logo-sub">Manajemen Tugas</div>
        </div>
    </div>

    <div class="tabs">
        <a href="?mode=login" class="tab <?= $mode === 'login' ? 'active' : '' ?>">Masuk</a>
        <a href="?mode=register" class="tab <?= $mode === 'register' ? 'active' : '' ?>">Daftar</a>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($mode === 'login'): ?>
    <h1>Selamat datang</h1>
    <p class="subtitle">Masuk untuk melanjutkan ke TaskFlow</p>
    <form method="POST">
        <input type="hidden" name="mode" value="login">
        <div class="form-group">
            <label>Username atau Email</label>
            <input type="text" name="username" placeholder="username / email" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button class="btn" type="submit">Masuk →</button>
    </form>

    <?php else: ?>
    <h1>Buat akun baru</h1>
    <p class="subtitle">Bergabung dan mulai kelola tugasmu</p>
    <form method="POST">
        <input type="hidden" name="mode" value="register">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="pilih username" required autofocus>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="email@contoh.com" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="min. 6 karakter" required>
        </div>
        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm" placeholder="ulangi password" required>
        </div>
        <button class="btn" type="submit">Daftar Sekarang →</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
