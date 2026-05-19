<?php
require_once __DIR__ . '/db.php';

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUser() {
    startSession();
    if (!isset($_SESSION['user_id'])) return null;
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function registerUser($username, $email, $password) {
    $pdo = getDB();
    // Check existing
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Username atau email sudah digunakan.'];
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hash]);
    $userId = $pdo->lastInsertId();

    // Insert default categories
    $defaults = [
        ['Kerja', '#3b82f6', '💼'],
        ['Kuliah', '#8b5cf6', '📚'],
        ['Belanja', '#10b981', '🛒'],
        ['Pribadi', '#f59e0b', '🏠'],
    ];
    $catStmt = $pdo->prepare("INSERT INTO categories (user_id, name, color, icon) VALUES (?, ?, ?, ?)");
    foreach ($defaults as $cat) {
        $catStmt->execute([$userId, $cat[0], $cat[1], $cat[2]]);
    }
    return ['success' => true, 'user_id' => $userId];
}

function loginUser($username, $password) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Username/email atau password salah.'];
    }
    startSession();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    return ['success' => true];
}

function logoutUser() {
    startSession();
    session_destroy();
}
