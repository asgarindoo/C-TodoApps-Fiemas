<?php
require_once __DIR__ . '/db.php';

// ── TASKS ──────────────────────────────────────────────────

function getTasks($userId, $status = null, $priority = null, $categoryId = null, $sort = 'deadline') {
    $pdo = getDB();
    $where = ["t.user_id = ?"];
    $params = [$userId];

    if ($status)     { $where[] = "t.status = ?";      $params[] = $status; }
    if ($priority)   { $where[] = "t.priority = ?";    $params[] = $priority; }
    if ($categoryId) { $where[] = "t.category_id = ?"; $params[] = $categoryId; }

    $orderMap = [
        'deadline'    => "CASE WHEN t.deadline IS NULL THEN 1 ELSE 0 END, t.deadline ASC",
        'priority'    => "FIELD(t.priority,'high','medium','low')",
        'created'     => "t.created_at DESC",
    ];
    $order = $orderMap[$sort] ?? $orderMap['deadline'];

    $sql = "SELECT t.*, c.name AS category_name, c.color AS category_color, c.icon AS category_icon
            FROM tasks t
            LEFT JOIN categories c ON t.category_id = c.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY $order";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getTask($id, $userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    return $stmt->fetch();
}

function createTask($userId, $data) {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, category_id, title, description, priority, deadline) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $userId,
        $data['category_id'] ?: null,
        $data['title'],
        $data['description'] ?? null,
        $data['priority'] ?? 'medium',
        $data['deadline'] ?: null,
    ]);
    return $pdo->lastInsertId();
}

function updateTask($id, $userId, $data) {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE tasks SET title=?, description=?, priority=?, deadline=?, category_id=?, updated_at=NOW() WHERE id=? AND user_id=?");
    return $stmt->execute([
        $data['title'],
        $data['description'] ?? null,
        $data['priority'],
        $data['deadline'] ?: null,
        $data['category_id'] ?: null,
        $id,
        $userId
    ]);
}

function toggleTaskStatus($id, $userId) {
    $pdo = getDB();
    $task = getTask($id, $userId);
    if (!$task) return false;
    $newStatus = $task['status'] === 'pending' ? 'completed' : 'pending';
    $completedAt = $newStatus === 'completed' ? 'NOW()' : 'NULL';
    $stmt = $pdo->prepare("UPDATE tasks SET status=?, completed_at=" . ($newStatus === 'completed' ? 'NOW()' : 'NULL') . ", updated_at=NOW() WHERE id=? AND user_id=?");
    return $stmt->execute([$newStatus, $id, $userId]);
}

function deleteTask($id, $userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    return $stmt->execute([$id, $userId]);
}

function getTaskStats($userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT
        COUNT(*) AS total,
        SUM(status='pending') AS pending,
        SUM(status='completed') AS completed,
        SUM(status='pending' AND deadline < CURDATE()) AS overdue,
        SUM(status='pending' AND priority='high') AS high_pending
        FROM tasks WHERE user_id=?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

// ── CATEGORIES ─────────────────────────────────────────────

function getCategories($userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT c.*, COUNT(t.id) AS task_count FROM categories c LEFT JOIN tasks t ON t.category_id = c.id AND t.user_id = c.user_id WHERE c.user_id = ? GROUP BY c.id ORDER BY c.name");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function createCategory($userId, $name, $color, $icon) {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, color, icon) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $name, $color, $icon]);
    return $pdo->lastInsertId();
}

function deleteCategory($id, $userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
    return $stmt->execute([$id, $userId]);
}
