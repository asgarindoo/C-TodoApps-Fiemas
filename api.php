<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

startSession();
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // ── TASKS ─────────────────────────────
    case 'create_task':
        $title = trim($_POST['title'] ?? '');
        if (!$title) { echo json_encode(['success'=>false,'message'=>'Judul wajib diisi']); exit; }
        $id = createTask($userId, [
            'title'       => $title,
            'description' => trim($_POST['description'] ?? ''),
            'priority'    => $_POST['priority'] ?? 'medium',
            'deadline'    => $_POST['deadline'] ?? '',
            'category_id' => $_POST['category_id'] ?? null,
        ]);
        echo json_encode(['success' => true, 'id' => $id]);
        break;

    case 'update_task':
        $id    = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        if (!$id || !$title) { echo json_encode(['success'=>false,'message'=>'Data tidak lengkap']); exit; }
        $ok = updateTask($id, $userId, [
            'title'       => $title,
            'description' => trim($_POST['description'] ?? ''),
            'priority'    => $_POST['priority'] ?? 'medium',
            'deadline'    => $_POST['deadline'] ?? '',
            'category_id' => $_POST['category_id'] ?? null,
        ]);
        echo json_encode(['success' => $ok]);
        break;

    case 'toggle_task':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success'=>false]); exit; }
        echo json_encode(['success' => toggleTaskStatus($id, $userId)]);
        break;

    case 'delete_task':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success'=>false]); exit; }
        echo json_encode(['success' => deleteTask($id, $userId)]);
        break;

    case 'get_task':
        $id   = (int)($_GET['id'] ?? 0);
        $task = getTask($id, $userId);
        if ($task) {
            echo json_encode(['success'=>true,'task'=>$task]);
        } else {
            echo json_encode(['success'=>false,'message'=>'Tidak ditemukan']);
        }
        break;

    case 'get_tasks':
        $tasks = getTasks(
            $userId,
            $_GET['status']      ?? null,
            $_GET['priority']    ?? null,
            $_GET['category_id'] ?? null,
            $_GET['sort']        ?? 'deadline'
        );
        echo json_encode(['success'=>true,'tasks'=>$tasks]);
        break;

    case 'get_stats':
        echo json_encode(['success'=>true,'stats'=>getTaskStats($userId)]);
        break;

    // ── CATEGORIES ────────────────────────
    case 'create_category':
        $name  = trim($_POST['name'] ?? '');
        $color = $_POST['color'] ?? '#6366f1';
        $icon  = $_POST['icon']  ?? '📁';
        if (!$name) { echo json_encode(['success'=>false,'message'=>'Nama wajib diisi']); exit; }
        $id = createCategory($userId, $name, $color, $icon);
        echo json_encode(['success'=>true,'id'=>$id]);
        break;

    case 'delete_category':
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['success'=>false]); exit; }
        echo json_encode(['success' => deleteCategory($id, $userId)]);
        break;

    case 'get_categories':
        echo json_encode(['success'=>true,'categories'=>getCategories($userId)]);
        break;

    default:
        echo json_encode(['success'=>false,'message'=>'Action tidak dikenal']);
}
