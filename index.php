<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();
$user  = getCurrentUser();
$stats = getTaskStats($_SESSION['user_id']);
$cats  = getCategories($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TaskFlow — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
/* ── RESET & VARS ─────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg:       #0A0A0C;
    --surface:  #121214;
    --surface2: #1A1A1E;
    --surface3: #26262B;
    --border:   rgba(255,255,255,0.05);
    --border-hover: rgba(255,255,255,0.12);
    --text:     #FAFAFA;
    --text2:    #A1A1AA;
    --muted:    #52525B;
    --accent:   #FACC15;
    --accent-hover: #EAB308;
    --accent-light: #FEF08A;
    --high:     #EF4444;
    --high-bg:  rgba(239,68,68,0.08);
    --high-bd:  rgba(239,68,68,0.15);
    --medium:   #F59E0B;
    --medium-bg:rgba(245,158,11,0.08);
    --medium-bd:rgba(245,158,11,0.15);
    --low:      #10B981;
    --low-bg:   rgba(16,185,129,0.08);
    --low-bd:   rgba(16,185,129,0.15);
    --overdue:  #EF4444;
    --overdue-bg:rgba(239,68,68,0.08);
    --success:  #10B981;
    --sidebar:  260px;
    --radius:   8px;
    --radius-sm:6px;
    --shadow-soft: 0 4px 12px rgba(0,0,0,0.5);
    --shadow-hover: 0 8px 24px rgba(0,0,0,0.75);
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    font-size: 14px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

/* ── SIDEBAR ──────────────────────────────────────────── */
.sidebar {
    width: var(--sidebar);
    min-height: 100vh;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    padding: 1.5rem 1rem;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    flex-shrink: 0;
    transition: all 0.3s ease;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.sidebar::-webkit-scrollbar {
    display: none;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0 0.5rem;
    margin-bottom: 2.5rem;
}
.logo-dot {
    width: 8px; height: 8px;
    background: var(--accent);
    border-radius: 50%;
    flex-shrink: 0;
}
.logo-name { font-size: 1.125rem; font-weight: 700; letter-spacing: -0.03em; }

.nav-section { margin-bottom: 1.5rem; }
.nav-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--muted);
    padding: 0 0.5rem;
    margin-bottom: 0.5rem;
}
.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0.75rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    color: var(--text2);
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    user-select: none;
    margin-bottom: 2px;
}
.nav-item:hover { background: var(--surface2); color: var(--text); }
.nav-item.active { background: rgba(250,204,21,0.1); color: var(--accent); }
.nav-item .icon { width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; }
.nav-item .badge {
    margin-left: auto;
    background: var(--surface3);
    color: var(--text2);
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
    font-weight: 600;
}
.nav-item.active .badge { background: var(--accent); color: #000; }

.sidebar-footer {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}
.user-chip {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0.75rem;
    border-radius: var(--radius-sm);
    background: var(--surface2);
    border: 1px solid var(--border);
}
.user-avatar {
    width: 32px; height: 32px;
    background: var(--surface3);
    border: 1px solid var(--border);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem;
    font-weight: 600;
    flex-shrink: 0;
    color: var(--text);
}
.user-info { flex: 1; overflow: hidden; }
.user-name { font-size: 0.875rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text); }
.user-status { font-size: 0.7rem; color: var(--success); display: flex; align-items: center; gap: 4px; }
.user-status::before { content: ''; width: 6px; height: 6px; background: var(--success); border-radius: 50%; display: inline-block; }
.logout-btn {
    background: none; border: none; color: var(--muted); cursor: pointer;
    padding: 0.4rem; border-radius: 6px; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center;
}
.logout-btn:hover { color: var(--high); background: var(--high-bg); }

/* ── MAIN ─────────────────────────────────────────────── */
.main { flex: 1; display: flex; flex-direction: column; min-width: 0; overflow: hidden; }

.topbar {
    background: rgba(23, 23, 29, 0.8);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border);
    padding: 1rem 2rem;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 40;
}
.topbar-left { display: flex; align-items: center; gap: 1.5rem; }
.topbar-title { font-size: 1.25rem; font-weight: 600; letter-spacing: -0.01em; display: flex; align-items: center; gap: 0.5rem; }
.topbar-title span { color: var(--text2); font-weight: 500; font-size: 0.9rem; }

.search-bar {
    display: flex; align-items: center; gap: 0.5rem;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.4rem 0.8rem;
    width: 250px;
    transition: all 0.2s;
}
.search-bar:focus-within { border-color: var(--accent); width: 280px; }
.search-bar i { color: var(--muted); width: 16px; height: 16px; }
.search-bar input {
    background: none; border: none; outline: none; color: var(--text);
    font-family: inherit; font-size: 0.875rem; width: 100%; padding: 0; box-shadow: none;
}

.topbar-right { display: flex; align-items: center; gap: 1rem; }
.icon-btn {
    width: 36px; height: 36px; border-radius: 8px; background: var(--surface2);
    border: 1px solid var(--border); display: flex; align-items: center; justify-content: center;
    color: var(--text2); cursor: pointer; transition: all 0.2s;
}
.icon-btn:hover { color: var(--text); border-color: var(--border-hover); }

/* ── STATS ────────────────────────────────────────────── */
.stats-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem;
    padding: 1.5rem 2rem; border-bottom: 1px solid var(--border);
}
.stat-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 1.25rem;
    transition: all 0.2s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-soft); border-color: var(--border-hover); }
.stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; }
.stat-label { font-size: 0.85rem; font-weight: 500; color: var(--text2); }
.stat-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.stat-icon.blue { background: rgba(59,130,246,0.1); color: #3B82F6; }
.stat-icon.orange { background: rgba(245,158,11,0.1); color: #F59E0B; }
.stat-icon.red { background: rgba(239,68,68,0.1); color: #EF4444; }
.stat-icon.green { background: rgba(16,185,129,0.1); color: #10B981; }
.stat-value { font-size: 2rem; font-weight: 700; line-height: 1; letter-spacing: -0.03em; }

/* ── CONTENT ──────────────────────────────────────────── */
.content { padding: 1.5rem 2rem; flex: 1; overflow-y: auto; }

/* Filters bar */
.filters-bar { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.filter-tabs {
    display: flex; background: var(--surface2); border: 1px solid var(--border);
    border-radius: 8px; padding: 4px; gap: 4px;
}
.filter-tab {
    padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 500;
    cursor: pointer; color: var(--text2); transition: all 0.2s; border: none; background: none; font-family: inherit;
}
.filter-tab:hover { color: var(--text); }
.filter-tab.active { background: var(--surface); color: var(--text); box-shadow: 0 1px 3px rgba(0,0,0,0.2); }

.filter-select {
    background: var(--surface2); border: 1px solid var(--border); border-radius: 8px;
    color: var(--text); font-family: inherit; font-size: 0.85rem; padding: 0.55rem 1rem;
    cursor: pointer; outline: none; transition: all 0.2s;
}
.filter-select:hover { border-color: var(--border-hover); }
.filter-select:focus { border-color: var(--accent); }
.filter-select option { background: var(--surface); }

.spacer { flex: 1; }

.btn-primary {
    background: var(--accent); border: none; border-radius: var(--radius-sm);
    color: #000; font-family: inherit; font-size: 0.875rem; font-weight: 600;
    padding: 0.6rem 1.25rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;
    transition: all 0.2s; white-space: nowrap;
}
.btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); }

/* ── TASK LIST ────────────────────────────────────────── */
.tasks-header {
    display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;
    font-size: 0.85rem; font-weight: 600; color: var(--text);
}

.task-list { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }

.task-item {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 1.25rem;
    display: flex; flex-direction: column; gap: 1rem;
    transition: all 0.2s ease; position: relative;
}
.task-item:hover {
    border-color: var(--border-hover); box-shadow: var(--shadow-soft);
    transform: translateY(-2px);
}
.task-item.overdue { background: linear-gradient(180deg, rgba(239,68,68,0.05) 0%, var(--surface) 100%); border-color: rgba(239,68,68,0.2); }
.task-item.completed-item { opacity: 0.6; }
.task-item.completed-item .task-title { text-decoration: line-through; color: var(--muted); }

.task-header-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; }

.task-check {
    width: 20px; height: 20px; border: 2px solid var(--muted);
    border-radius: 6px; flex-shrink: 0; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s; background: none; margin-top: 2px;
}
.task-check:hover { border-color: var(--accent); background: rgba(250,204,21,0.15); }
.task-check.checked { background: var(--accent); border-color: var(--accent); }
.task-check.checked::after { content: ''; width: 10px; height: 10px; background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>'); background-size: cover; }

.task-title-group { flex: 1; min-width: 0; }
.task-title { font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; line-height: 1.4; color: var(--text); }
.task-desc { font-size: 0.85rem; color: var(--text2); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

.task-actions {
    position: relative;
    opacity: 0; transition: opacity 0.2s;
}
.task-item:hover .task-actions { opacity: 1; }
.action-menu-btn {
    background: none; border: none; color: var(--muted); cursor: pointer;
    padding: 0.2rem; border-radius: 4px; display: flex; align-items: center; justify-content: center;
}
.action-menu-btn:hover { color: var(--text); background: var(--surface2); }
.action-dropdown {
    position: absolute; right: 0; top: 100%; margin-top: 0.25rem;
    background: var(--surface2); border: 1px solid var(--border); border-radius: 8px;
    padding: 0.25rem; display: none; z-index: 10; box-shadow: var(--shadow-soft);
    min-width: 120px;
}
.task-actions:hover .action-dropdown { display: block; }
.action-item {
    display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem;
    font-size: 0.85rem; color: var(--text); cursor: pointer; border-radius: 4px;
    background: none; border: none; width: 100%; text-align: left;
}
.action-item:hover { background: var(--surface); }
.action-item.delete { color: var(--high); }
.action-item.delete:hover { background: var(--high-bg); }

.task-meta { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-top: auto; }

.badge {
    display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.6rem;
    border-radius: 6px; font-size: 0.75rem; font-weight: 500; border: 1px solid transparent;
}
.badge-high    { background: var(--high-bg);   border-color: var(--high-bd);   color: var(--high); }
.badge-medium  { background: var(--medium-bg); border-color: var(--medium-bd); color: var(--medium); }
.badge-low     { background: var(--low-bg);    border-color: var(--low-bd);    color: var(--low); }
.badge-cat     { background: var(--surface2);  border-color: var(--border);    color: var(--text2); }
.badge-overdue { background: var(--high-bg); border-color: var(--high-bd); color: var(--high); }

.deadline-text { font-size: 0.75rem; color: var(--muted); display: flex; align-items: center; gap: 0.35rem; }
.deadline-text.overdue { color: var(--high); }
.deadline-text.soon    { color: var(--medium); }

/* ── EMPTY STATE ──────────────────────────────────────── */
.empty-state { text-align: center; padding: 4rem 1rem; color: var(--muted); display: flex; flex-direction: column; align-items: center; }
.empty-icon { width: 64px; height: 64px; background: var(--surface2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; color: var(--text2); }
.empty-title { font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text); }
.empty-sub { font-size: 0.9rem; max-width: 300px; margin: 0 auto; }

/* ── LOADING ──────────────────────────────────────────── */
.spinner { width: 24px; height: 24px; border: 2px solid var(--border-hover); border-top-color: var(--accent); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 2rem auto; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── MODAL ────────────────────────────────────────────── */
.overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
    z-index: 100; display: none; align-items: center; justify-content: center; padding: 1rem;
}
.overlay.open { display: flex; }
.modal {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
    padding: 2rem; width: 100%; max-width: 500px; animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    max-height: 90vh; overflow-y: auto; box-shadow: var(--shadow-hover);
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.modal-title { font-size: 1.25rem; font-weight: 600; }
.modal-close { background: var(--surface2); border: none; color: var(--muted); cursor: pointer; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.modal-close:hover { color: var(--text); background: var(--border); }

.form-group { margin-bottom: 1.25rem; }
.form-label { display: block; font-size: 0.85rem; color: var(--text); margin-bottom: 0.5rem; font-weight: 500; }
.form-control {
    width: 100%; background: var(--surface2); border: 1px solid var(--border);
    border-radius: 10px; padding: 0.75rem 1rem; color: var(--text); font-family: inherit; font-size: 0.9rem;
    transition: all 0.2s; outline: none; resize: vertical;
}
.form-control:hover { border-color: var(--border-hover); }
.form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(250,204,21,0.15); }
.form-control::placeholder { color: var(--muted); }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.priority-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
.priority-opt {
    border: 1px solid var(--border); border-radius: 10px; padding: 0.75rem;
    text-align: center; cursor: pointer; font-size: 0.85rem; font-weight: 500;
    transition: all 0.2s; color: var(--text2); background: var(--surface2);
}
.priority-opt:hover { border-color: var(--border-hover); }
.priority-opt.sel-high   { border-color: var(--high);   background: var(--high-bg);   color: var(--high); }
.priority-opt.sel-medium { border-color: var(--medium); background: var(--medium-bg); color: var(--medium); }
.priority-opt.sel-low    { border-color: var(--low);    background: var(--low-bg);    color: var(--low); }

.modal-footer { display: flex; gap: 1rem; margin-top: 2rem; }
.btn-ghost {
    flex: 1; padding: 0.8rem; background: var(--surface2); border: 1px solid var(--border);
    border-radius: 10px; color: var(--text); font-family: inherit; font-size: 0.9rem; font-weight: 500;
    cursor: pointer; transition: all 0.2s;
}
.btn-ghost:hover { border-color: var(--border-hover); background: var(--surface3); }
.btn-save {
    flex: 2; padding: 0.8rem; background: var(--accent); border: none;
    border-radius: var(--radius-sm); color: #000; font-family: inherit; font-size: 0.9rem; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.btn-save:hover { background: var(--accent-hover); }

/* Toast */
.toast-wrap { position: fixed; bottom: 2rem; right: 2rem; z-index: 200; display: flex; flex-direction: column; gap: 0.75rem; pointer-events: none; }
.toast {
    background: var(--surface2); border: 1px solid var(--border); border-radius: 12px;
    padding: 0.8rem 1.25rem; font-size: 0.9rem; display: flex; align-items: center; gap: 0.75rem;
    animation: toastIn 0.3s cubic-bezier(0.16, 1, 0.3, 1); pointer-events: all; min-width: 250px;
    box-shadow: var(--shadow-hover); font-weight: 500; color: var(--text);
}
.toast i { width: 20px; height: 20px; }
.toast.success i { color: var(--success); }
.toast.error i { color: var(--high); }
@keyframes toastIn {
    from { opacity: 0; transform: translateX(30px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* Categories tab */
.cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
.cat-card {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
    padding: 1.25rem; cursor: pointer; transition: all 0.2s; position: relative;
    display: flex; flex-direction: column; gap: 0.75rem;
}
.cat-card:hover { border-color: var(--border-hover); transform: translateY(-2px); box-shadow: var(--shadow-soft); }
.cat-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--surface2); display: flex; align-items: center; justify-content: center; }
.cat-icon i { width: 20px; height: 20px; }
.cat-name { font-size: 1rem; font-weight: 600; color: var(--text); }
.cat-count { font-size: 0.85rem; color: var(--muted); }
.cat-del {
    position: absolute; top: 1rem; right: 1rem; background: var(--surface2); border: 1px solid var(--border);
    color: var(--muted); cursor: pointer; opacity: 0; transition: all 0.2s; padding: 0.35rem; border-radius: 6px;
}
.cat-card:hover .cat-del { opacity: 1; }
.cat-del:hover { color: var(--high); border-color: rgba(239,68,68,0.3); background: var(--high-bg); }

.add-cat-btn {
    background: transparent; border: 2px dashed var(--border); border-radius: var(--radius);
    padding: 1.25rem; display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 0.75rem; cursor: pointer; color: var(--muted); font-size: 0.9rem; font-weight: 500;
    transition: all 0.2s; min-height: 120px;
}
.add-cat-btn:hover { border-color: var(--accent); color: var(--accent); background: rgba(250,204,21,0.05); }

/* Responsive */
@media (max-width: 768px) {
    .sidebar { display: none; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); padding: 1rem; }
    .form-row { grid-template-columns: 1fr; }
    .topbar { padding: 1rem; }
    .search-bar { width: 200px; }
    .content { padding: 1rem; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="logo">
        <div class="logo-dot"></div>
        <div class="logo-name">TaskFlow</div>
    </div>

    <div class="nav-section">
        <div class="nav-label">Tampilan</div>
        <div class="nav-item active" data-view="all" onclick="setView('all')">
            <span class="icon"><i data-lucide="layout-dashboard"></i></span> Semua Tugas
            <span class="badge" id="badge-all"><?= $stats['total'] ?></span>
        </div>
        <div class="nav-item" data-view="pending" onclick="setView('pending')">
            <span class="icon"><i data-lucide="clock"></i></span> Belum Selesai
            <span class="badge" id="badge-pending"><?= $stats['pending'] ?></span>
        </div>
        <div class="nav-item" data-view="completed" onclick="setView('completed')">
            <span class="icon"><i data-lucide="check-circle-2"></i></span> Selesai
            <span class="badge" id="badge-completed"><?= $stats['completed'] ?></span>
        </div>
        <div class="nav-item" data-view="overdue" onclick="setView('overdue')">
            <span class="icon"><i data-lucide="alert-triangle"></i></span> Lewat Deadline
            <span class="badge" id="badge-overdue" style="<?= $stats['overdue'] > 0 ? 'background:var(--high);color:#fff' : '' ?>"><?= $stats['overdue'] ?></span>
        </div>
    </div>

    <div class="nav-section">
        <div class="nav-label">Prioritas</div>
        <div class="nav-item" data-view="high" onclick="setView('high')">
            <span class="icon"><i data-lucide="flag" style="color:var(--high)"></i></span> High
        </div>
        <div class="nav-item" data-view="medium" onclick="setView('medium')">
            <span class="icon"><i data-lucide="flag" style="color:var(--medium)"></i></span> Medium
        </div>
        <div class="nav-item" data-view="low" onclick="setView('low')">
            <span class="icon"><i data-lucide="flag" style="color:var(--low)"></i></span> Low
        </div>
    </div>

    <div class="nav-section">
        <div class="nav-label">Kategori</div>
        <div id="sidebar-cats">
        <?php foreach ($cats as $cat): ?>
        <div class="nav-item" data-view="cat-<?= $cat['id'] ?>" onclick="setView('cat-<?= $cat['id'] ?>')">
            <span class="icon"><i data-lucide="<?= htmlspecialchars($cat['icon']) ?>" style="color: <?= htmlspecialchars($cat['color']) ?>"></i></span>
            <?= htmlspecialchars($cat['name']) ?>
            <span class="badge"><?= $cat['task_count'] ?></span>
        </div>
        <?php endforeach; ?>
        </div>
        <div class="nav-item" onclick="setView('categories')">
            <span class="icon"><i data-lucide="settings"></i></span> Kelola Kategori
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar"><?= strtoupper(substr($user['username'],0,1)) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
                <div class="user-status">Online</div>
            </div>
            <button class="logout-btn" onclick="location.href='logout.php'" title="Keluar"><i data-lucide="log-out"></i></button>
        </div>
    </div>
</aside>

<!-- MAIN -->
<main class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-title" id="topbar-title">
                <i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Semua Tugas <span id="topbar-sub"></span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="search-bar">
                <i data-lucide="search"></i>
                <input type="text" placeholder="Cari tugas...">
            </div>
            <button class="icon-btn"><i data-lucide="bell"></i></button>
            <button class="btn-primary" onclick="openModal()">
                <i data-lucide="plus" style="width:18px;height:18px"></i> Tugas Baru
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Total Tugas</div>
                <div class="stat-icon blue"><i data-lucide="layers"></i></div>
            </div>
            <div class="stat-value" id="stat-total"><?= $stats['total'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Belum Selesai</div>
                <div class="stat-icon orange"><i data-lucide="clock"></i></div>
            </div>
            <div class="stat-value" id="stat-pending" style="color:var(--medium)"><?= $stats['pending'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Lewat Deadline</div>
                <div class="stat-icon red"><i data-lucide="alert-triangle"></i></div>
            </div>
            <div class="stat-value" id="stat-overdue" style="color:var(--high)"><?= $stats['overdue'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-label">Selesai</div>
                <div class="stat-icon green"><i data-lucide="check-circle-2"></i></div>
            </div>
            <div class="stat-value" id="stat-completed" style="color:var(--success)"><?= $stats['completed'] ?></div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Filters -->
        <div class="filters-bar" id="filters-bar">
            <div class="filter-tabs">
                <button class="filter-tab active" data-status="" onclick="setFilter('status','')">Semua</button>
                <button class="filter-tab" data-status="pending" onclick="setFilter('status','pending')">Aktif</button>
                <button class="filter-tab" data-status="completed" onclick="setFilter('status','completed')">Selesai</button>
            </div>

            <select class="filter-select" id="filter-priority" onchange="setFilter('priority',this.value)">
                <option value="">Semua Prioritas</option>
                <option value="high">High Priority</option>
                <option value="medium">Medium Priority</option>
                <option value="low">Low Priority</option>
            </select>

            <select class="filter-select" id="filter-sort" onchange="setFilter('sort',this.value)">
                <option value="deadline">Urut: Deadline</option>
                <option value="priority">Urut: Prioritas</option>
                <option value="created">Urut: Terbaru</option>
            </select>

            <div class="spacer"></div>
        </div>

        <!-- Task list container -->
        <div id="tasks-container">
            <div class="spinner"></div>
        </div>

        <!-- Categories view -->
        <div id="categories-container" style="display:none">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
                <div style="font-size:1.125rem;font-weight:600;">Kategori Kamu</div>
                <button class="btn-primary" onclick="openCatModal()">
                    <i data-lucide="plus" style="width:16px;height:16px"></i> Kategori
                </button>
            </div>
            <div class="cat-grid" id="cat-grid"></div>
        </div>
    </div>
</main>

<!-- TASK MODAL -->
<div class="overlay" id="task-overlay">
<div class="modal">
    <div class="modal-header">
        <div class="modal-title" id="modal-title">Tugas Baru</div>
        <button class="modal-close" type="button" onclick="closeModal()"><i data-lucide="x"></i></button>
    </div>
    <form id="task-form" onsubmit="submitTask(event)">
        <input type="hidden" id="task-id">
        <div class="form-group">
            <label class="form-label">Judul Tugas *</label>
            <input class="form-control" id="task-title" placeholder="Apa yang perlu dikerjakan?" required>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" id="task-desc" rows="3" placeholder="Detail tambahan (opsional)..."></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Deadline</label>
                <input class="form-control" type="date" id="task-deadline">
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select class="form-control" id="task-category">
                    <option value="">— Tanpa Kategori —</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Prioritas</label>
            <div class="priority-grid">
                <div class="priority-opt sel-high" data-p="high" onclick="selectPriority('high')">High</div>
                <div class="priority-opt" data-p="medium" onclick="selectPriority('medium')">Medium</div>
                <div class="priority-opt" data-p="low" onclick="selectPriority('low')">Low</div>
            </div>
            <input type="hidden" id="task-priority" value="high">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-ghost" onclick="closeModal()">Batal</button>
            <button type="submit" class="btn-save">Simpan Tugas</button>
        </div>
    </form>
</div>
</div>

<!-- CATEGORY MODAL -->
<div class="overlay" id="cat-overlay">
<div class="modal" style="max-width:400px">
    <div class="modal-header">
        <div class="modal-title">Kategori Baru</div>
        <button class="modal-close" type="button" onclick="document.getElementById('cat-overlay').classList.remove('open')"><i data-lucide="x"></i></button>
    </div>
    <div class="form-group">
        <label class="form-label">Nama Kategori *</label>
        <input class="form-control" id="cat-name" placeholder="mis. Kerja, Pribadi...">
    </div>
    <div class="form-group">
        <label class="form-label">Ikon</label>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap" id="icon-picker">
            <?php foreach(['folder','briefcase','book','shopping-cart','home','dumbbell','target','palette','music','rocket','heart','zap'] as $ic): ?>
            <div class="icon-opt" data-icon="<?= $ic ?>" onclick="selectIcon('<?= $ic ?>')" style="cursor:pointer;padding:0.5rem;border:1px solid var(--border);border-radius:10px;transition:all 0.2s;background:var(--surface2)">
                <i data-lucide="<?= $ic ?>" style="width:20px;height:20px;color:var(--text2)"></i>
            </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="cat-icon" value="folder">
    </div>
    <div class="form-group">
        <label class="form-label">Warna</label>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap" id="color-picker">
            <?php foreach(['#6366f1','#3b82f6','#8b5cf6','#ec4899','#f59e0b','#10b981','#ef4444','#06b6d4'] as $col): ?>
            <div class="color-opt" data-color="<?= $col ?>" onclick="selectColor('<?= $col ?>')" style="width:28px;height:28px;border-radius:50%;background:<?= $col ?>;cursor:pointer;border:2px solid transparent;transition:all 0.2s;box-shadow:0 2px 5px rgba(0,0,0,0.2)"></div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="cat-color" value="#6366f1">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn-ghost" onclick="document.getElementById('cat-overlay').classList.remove('open')">Batal</button>
        <button type="button" class="btn-save" onclick="submitCategory()">Simpan</button>
    </div>
</div>
</div>

<!-- TOAST -->
<div class="toast-wrap" id="toast-wrap"></div>

<script>
// ── STATE ─────────────────────────────────────────────────
const state = {
    view: 'all',
    status: '',
    priority: '',
    categoryId: '',
    sort: 'deadline',
    categories: [],
};

// ── INIT ──────────────────────────────────────────────────
(async () => {
    await loadCategories();
    loadTasks();
    lucide.createIcons();
})();

// ── VIEW MANAGEMENT ───────────────────────────────────────
function setView(v) {
    state.view = v;
    // Reset filters
    state.status = '';
    state.priority = '';
    state.categoryId = '';

    document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
    document.querySelector(`[data-view="${v}"]`)?.classList.add('active');

    const catCont = document.getElementById('categories-container');
    const taskCont = document.getElementById('tasks-container');
    const filterBar = document.getElementById('filters-bar');

    if (v === 'categories') {
        catCont.style.display = 'block';
        taskCont.style.display = 'none';
        filterBar.style.display = 'none';
        document.getElementById('topbar-title').innerHTML = '<i data-lucide="settings" style="width:20px;height:20px"></i> Kategori <span></span>';
        renderCategories();
        lucide.createIcons();
        return;
    }

    catCont.style.display = 'none';
    taskCont.style.display = 'block';
    filterBar.style.display = 'flex';

    // Map view to filter
    const viewMap = {
        'all':       { title: '<i data-lucide="layout-dashboard" style="width:20px;height:20px"></i> Semua Tugas' },
        'pending':   { title: '<i data-lucide="clock" style="width:20px;height:20px"></i> Belum Selesai', status: 'pending' },
        'completed': { title: '<i data-lucide="check-circle-2" style="width:20px;height:20px"></i> Selesai', status: 'completed' },
        'overdue':   { title: '<i data-lucide="alert-triangle" style="width:20px;height:20px"></i> Lewat Deadline', status: 'pending' },
        'high':      { title: '<i data-lucide="flag" style="width:20px;height:20px;color:var(--high)"></i> Prioritas High', priority: 'high', status: 'pending' },
        'medium':    { title: '<i data-lucide="flag" style="width:20px;height:20px;color:var(--medium)"></i> Prioritas Medium', priority: 'medium', status: 'pending' },
        'low':       { title: '<i data-lucide="flag" style="width:20px;height:20px;color:var(--low)"></i> Prioritas Low', priority: 'low', status: 'pending' },
    };

    if (v.startsWith('cat-')) {
        const catId = v.replace('cat-', '');
        state.categoryId = catId;
        const cat = state.categories.find(c => c.id == catId);
        document.getElementById('topbar-title').innerHTML = `<i data-lucide="${cat?.icon || 'folder'}" style="width:20px;height:20px;color:${cat?.color || 'currentColor'}"></i> ${escHtml(cat?.name || 'Kategori')} <span></span>`;
    } else {
        const def = viewMap[v] || { title: v };
        if (def.status)   state.status   = def.status;
        if (def.priority) state.priority = def.priority;
        document.getElementById('topbar-title').innerHTML = `${def.title} <span id="topbar-sub"></span>`;
    }

    // Sync filter UI
    document.querySelectorAll('.filter-tab').forEach(t => {
        t.classList.toggle('active', t.dataset.status === state.status);
    });
    document.getElementById('filter-priority').value = state.priority;

    loadTasks();
}

function setFilter(key, val) {
    state[key] = val;
    if (key === 'status') {
        document.querySelectorAll('.filter-tab').forEach(t =>
            t.classList.toggle('active', t.dataset.status === val));
    }
    loadTasks();
}

// ── LOAD TASKS ────────────────────────────────────────────
async function loadTasks() {
    const cont = document.getElementById('tasks-container');
    cont.innerHTML = '<div class="spinner"></div>';

    const params = new URLSearchParams({
        action: 'get_tasks',
        sort: state.sort,
    });
    if (state.status)     params.set('status', state.status);
    if (state.priority)   params.set('priority', state.priority);
    if (state.categoryId) params.set('category_id', state.categoryId);

    // Overdue = pending + deadline < today
    const isOverdue = state.view === 'overdue';

    const res  = await fetch(`api.php?${params}`);
    const data = await res.json();
    if (!data.success) { cont.innerHTML = '<p style="color:var(--muted);padding:1rem">Gagal memuat</p>'; return; }

    let tasks = data.tasks;
    if (isOverdue) {
        const today = new Date(); today.setHours(0,0,0,0);
        tasks = tasks.filter(t => t.status === 'pending' && t.deadline && new Date(t.deadline) < today);
    }

    document.getElementById('topbar-sub').textContent = `(${tasks.length})`;

    if (!tasks.length) {
        cont.innerHTML = `<div class="empty-state">
            <div class="empty-icon"><i data-lucide="inbox" style="width:32px;height:32px"></i></div>
            <div class="empty-title">Tidak ada tugas</div>
            <div class="empty-sub">Tambah tugas baru menggunakan tombol "Tugas Baru" di atas.</div>
        </div>`;
        lucide.createIcons();
        return;
    }

    cont.innerHTML = `<div class="tasks-header">${tasks.length} tugas ditemukan</div><div class="task-list" id="task-list"></div>`;
    const list = document.getElementById('task-list');
    tasks.forEach(t => list.appendChild(buildTaskEl(t)));

    // Re-initialize icons inside new elements
    lucide.createIcons();

    // Update stats
    loadStats();
}

function buildTaskEl(t) {
    const today = new Date(); today.setHours(0,0,0,0);
    const deadline = t.deadline ? new Date(t.deadline) : null;
    const isOverdue = deadline && deadline < today && t.status === 'pending';
    const isSoon    = deadline && !isOverdue && (deadline - today) / 86400000 <= 3 && t.status === 'pending';

    const el = document.createElement('div');
    el.className = `task-item priority-${t.priority}${isOverdue ? ' overdue' : ''}${t.status === 'completed' ? ' completed-item' : ''}`;
    el.dataset.id = t.id;

    let deadlineHtml = '';
    if (t.deadline) {
        const cls = isOverdue ? 'overdue' : isSoon ? 'soon' : '';
        const label = formatDate(t.deadline);
        deadlineHtml = `<span class="deadline-text ${cls}">${isOverdue ? '<i data-lucide="alert-circle" style="width:14px;height:14px"></i>' : '<i data-lucide="calendar" style="width:14px;height:14px"></i>'} ${label}</span>`;
    }

    let catHtml = '';
    if (t.category_name) {
        catHtml = `<span class="badge badge-cat"><i data-lucide="${t.category_icon || 'folder'}" style="width:12px;height:12px;color:${t.category_color || 'currentColor'}"></i> ${escHtml(t.category_name)}</span>`;
    }

    el.innerHTML = `
        <div class="task-header-row">
            <div class="task-check${t.status==='completed'?' checked':''}" onclick="toggleTask(${t.id})"></div>
            <div class="task-title-group">
                <div class="task-title">${escHtml(t.title)}</div>
                ${t.description ? `<div class="task-desc">${escHtml(t.description)}</div>` : ''}
            </div>
            <div class="task-actions">
                <button class="action-menu-btn"><i data-lucide="more-vertical" style="width:18px;height:18px"></i></button>
                <div class="action-dropdown">
                    <button class="action-item" onclick="editTask(${t.id})"><i data-lucide="edit" style="width:14px;height:14px"></i> Edit</button>
                    <button class="action-item delete" onclick="deleteTask(${t.id})"><i data-lucide="trash-2" style="width:14px;height:14px"></i> Hapus</button>
                </div>
            </div>
        </div>
        <div class="task-meta">
            <span class="badge badge-${t.priority}">${priorityLabel(t.priority)}</span>
            ${catHtml}
            ${deadlineHtml}
            ${isOverdue ? '<span class="badge badge-overdue">Lewat Deadline</span>' : ''}
        </div>`;
    return el;
}

// ── TOGGLE TASK ───────────────────────────────────────────
async function toggleTask(id) {
    const res  = await fetch('api.php', { method:'POST', body: new URLSearchParams({ action:'toggle_task', id }) });
    const data = await res.json();
    if (data.success) loadTasks();
}

// ── DELETE TASK ───────────────────────────────────────────
async function deleteTask(id) {
    if (!confirm('Hapus tugas ini?')) return;
    const res  = await fetch('api.php', { method:'POST', body: new URLSearchParams({ action:'delete_task', id }) });
    const data = await res.json();
    if (data.success) { toast('Tugas dihapus','success'); loadTasks(); }
}

// ── EDIT TASK ─────────────────────────────────────────────
async function editTask(id) {
    const res  = await fetch(`api.php?action=get_task&id=${id}`);
    const data = await res.json();
    if (!data.success) return;
    const t = data.task;
    openModal(t);
}

// ── MODAL ─────────────────────────────────────────────────
function openModal(task = null) {
    document.getElementById('modal-title').textContent = task ? 'Edit Tugas' : 'Tugas Baru';
    document.getElementById('task-id').value       = task?.id   || '';
    document.getElementById('task-title').value    = task?.title || '';
    document.getElementById('task-desc').value     = task?.description || '';
    document.getElementById('task-deadline').value = task?.deadline || '';
    document.getElementById('task-category').value = task?.category_id || '';
    selectPriority(task?.priority || 'high');
    document.getElementById('task-overlay').classList.add('open');
    setTimeout(() => document.getElementById('task-title').focus(), 100);
}
function closeModal() { document.getElementById('task-overlay').classList.remove('open'); }

// ── SUBMIT TASK ───────────────────────────────────────────
async function submitTask(e) {
    e.preventDefault();
    const id = document.getElementById('task-id').value;
    const body = new URLSearchParams({
        action:      id ? 'update_task' : 'create_task',
        id,
        title:       document.getElementById('task-title').value,
        description: document.getElementById('task-desc').value,
        priority:    document.getElementById('task-priority').value,
        deadline:    document.getElementById('task-deadline').value,
        category_id: document.getElementById('task-category').value,
    });
    const res  = await fetch('api.php', { method:'POST', body });
    const data = await res.json();
    if (data.success) {
        closeModal();
        toast(id ? 'Tugas diperbarui' : 'Tugas ditambahkan', 'success');
        loadTasks();
    } else {
        toast(data.message || 'Gagal menyimpan', 'error');
    }
}

// ── PRIORITY ──────────────────────────────────────────────
function selectPriority(p) {
    document.getElementById('task-priority').value = p;
    document.querySelectorAll('.priority-opt').forEach(el => {
        el.className = 'priority-opt';
        if (el.dataset.p === p) el.classList.add(`sel-${p}`);
    });
}

// ── CATEGORIES ────────────────────────────────────────────
async function loadCategories() {
    const res  = await fetch('api.php?action=get_categories');
    const data = await res.json();
    if (!data.success) return;
    state.categories = data.categories;
    populateCategorySelect();
    updateSidebarCats();
}

function populateCategorySelect() {
    const sel = document.getElementById('task-category');
    sel.innerHTML = '<option value="">— Tanpa Kategori —</option>';
    state.categories.forEach(c => {
        sel.innerHTML += `<option value="${c.id}">${escHtml(c.name)}</option>`;
    });
}

function updateSidebarCats() {
    const cont = document.getElementById('sidebar-cats');
    cont.innerHTML = state.categories.map(c => `
        <div class="nav-item" data-view="cat-${c.id}" onclick="setView('cat-${c.id}')">
            <span class="icon"><i data-lucide="${escHtml(c.icon)}" style="color: ${escHtml(c.color)}"></i></span>
            ${escHtml(c.name)}
            <span class="badge">${c.task_count}</span>
        </div>`).join('');
    lucide.createIcons();
}

function renderCategories() {
    const grid = document.getElementById('cat-grid');
    grid.innerHTML = state.categories.map(c => `
        <div class="cat-card" onclick="setView('cat-${c.id}')">
            <button class="cat-del" onclick="event.stopPropagation();deleteCat(${c.id})"><i data-lucide="trash-2" style="width:16px;height:16px"></i></button>
            <div class="cat-icon"><i data-lucide="${escHtml(c.icon)}" style="color: ${escHtml(c.color)}; width:24px; height:24px;"></i></div>
            <div class="cat-name">${escHtml(c.name)}</div>
            <div class="cat-count">${c.task_count} tugas</div>
        </div>
    `).join('') + `<div class="add-cat-btn" onclick="openCatModal()">
        <i data-lucide="plus" style="width:28px;height:28px;margin-bottom:0.5rem;"></i>
        <div>Tambah Kategori</div>
    </div>`;
}

function openCatModal() {
    document.getElementById('cat-overlay').classList.add('open');
    document.getElementById('cat-name').value = '';
    selectIcon('folder');
    selectColor('#f59e0b');
}

async function submitCategory() {
    const name  = document.getElementById('cat-name').value.trim();
    const color = document.getElementById('cat-color').value;
    const icon  = document.getElementById('cat-icon').value;
    if (!name) { toast('Nama kategori wajib diisi', 'error'); return; }
    const res  = await fetch('api.php', { method:'POST', body: new URLSearchParams({ action:'create_category', name, color, icon }) });
    const data = await res.json();
    if (data.success) {
        document.getElementById('cat-overlay').classList.remove('open');
        toast('Kategori ditambahkan', 'success');
        await loadCategories();
        if (state.view === 'categories') renderCategories();
    }
}

async function deleteCat(id) {
    if (!confirm('Hapus kategori ini? Tugas terkait tidak akan terhapus.')) return;
    const res = await fetch('api.php', { method:'POST', body: new URLSearchParams({ action:'delete_category', id }) });
    const data = await res.json();
    if (data.success) { toast('Kategori dihapus','success'); await loadCategories(); renderCategories(); }
}

function selectIcon(ic) {
    document.getElementById('cat-icon').value = ic;
    document.querySelectorAll('.icon-opt').forEach(el => {
        el.style.borderColor = el.dataset.icon === ic ? 'var(--accent)' : 'var(--border)';
        el.style.background = el.dataset.icon === ic ? 'rgba(250,204,21,0.1)' : 'var(--surface2)';
    });
}
function selectColor(col) {
    document.getElementById('cat-color').value = col;
    document.querySelectorAll('.color-opt').forEach(el => {
        el.style.borderColor = el.dataset.color === col ? '#fff' : 'transparent';
        el.style.transform = el.dataset.color === col ? 'scale(1.15)' : 'scale(1)';
    });
}

// ── STATS ─────────────────────────────────────────────────
async function loadStats() {
    const res  = await fetch('api.php?action=get_stats');
    const data = await res.json();
    if (!data.success) return;
    const s = data.stats;
    document.getElementById('stat-total').textContent = s.total;
    document.getElementById('stat-pending').textContent = s.pending;
    document.getElementById('stat-overdue').textContent = s.overdue;
    document.getElementById('stat-completed').textContent = s.completed;
    document.getElementById('badge-all').textContent = s.total;
    document.getElementById('badge-pending').textContent = s.pending;
    document.getElementById('badge-completed').textContent = s.completed;
    document.getElementById('badge-overdue').textContent = s.overdue;
}

// ── TOAST ─────────────────────────────────────────────────
function toast(msg, type = 'success') {
    const wrap = document.getElementById('toast-wrap');
    const el   = document.createElement('div');
    el.className = `toast ${type}`;
    const icon = type === 'success' ? 'check-circle' : 'alert-circle';
    el.innerHTML = `<i data-lucide="${icon}"></i> <span>${escHtml(msg)}</span>`;
    wrap.appendChild(el);
    lucide.createIcons({ root: el });
    setTimeout(() => el.remove(), 3000);
}

// ── UTILS ─────────────────────────────────────────────────
function escHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function formatDate(d) {
    if (!d) return '';
    const dt = new Date(d + 'T00:00:00');
    return dt.toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });
}

function priorityLabel(p) {
    return { high: 'High', medium: 'Medium', low: 'Low' }[p] || p;
}

// Close overlay on outside click
document.getElementById('task-overlay').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeModal();
});
document.getElementById('cat-overlay').addEventListener('click', e => {
    if (e.target === e.currentTarget) e.currentTarget.classList.remove('open');
});
</script>
</body>
</html>
