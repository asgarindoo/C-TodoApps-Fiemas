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
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ── RESET & VARS ─────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg:       #0d0d10;
    --surface:  #14141a;
    --surface2: #1a1a22;
    --surface3: #20202a;
    --border:   #252530;
    --border2:  #2e2e3a;
    --text:     #e2e2ea;
    --text2:    #9999aa;
    --muted:    #555566;
    --accent:   #a78bfa;
    --accent2:  #7c3aed;
    --high:     #f87171;
    --high-bg:  rgba(248,113,113,.1);
    --high-bd:  rgba(248,113,113,.25);
    --medium:   #fbbf24;
    --medium-bg:rgba(251,191,36,.1);
    --medium-bd:rgba(251,191,36,.25);
    --low:      #34d399;
    --low-bg:   rgba(52,211,153,.1);
    --low-bd:   rgba(52,211,153,.25);
    --overdue:  #7f1d1d;
    --overdue-bg:rgba(239,68,68,.08);
    --success:  #34d399;
    --sidebar:  240px;
    --radius:   12px;
    --radius-sm:8px;
}

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    font-size: 14px;
    line-height: 1.5;
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
}

.logo {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: 0 .5rem;
    margin-bottom: 2rem;
}
.logo-icon {
    width: 34px; height: 34px;
    background: linear-gradient(135deg, var(--accent2), var(--accent));
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.logo-name { font-size: 1.1rem; font-weight: 600; letter-spacing: -.02em; }

.nav-section { margin-bottom: 1.5rem; }
.nav-label {
    font-size: .7rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--muted);
    padding: 0 .5rem;
    margin-bottom: .5rem;
}
.nav-item {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .5rem .7rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    color: var(--text2);
    font-size: .875rem;
    font-weight: 450;
    transition: all .15s;
    border: 1px solid transparent;
    user-select: none;
}
.nav-item:hover { background: var(--surface2); color: var(--text); }
.nav-item.active { background: rgba(167,139,250,.12); color: var(--accent); border-color: rgba(167,139,250,.2); }
.nav-item .icon { font-size: 1rem; width: 20px; text-align: center; }
.nav-item .badge {
    margin-left: auto;
    background: var(--surface3);
    color: var(--text2);
    font-size: .68rem;
    font-family: 'DM Mono', monospace;
    padding: .15rem .45rem;
    border-radius: 20px;
}
.nav-item.active .badge { background: rgba(167,139,250,.2); color: var(--accent); }

.cat-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-left: -2px;
}

.sidebar-footer {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}
.user-chip {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .5rem .7rem;
    border-radius: var(--radius-sm);
}
.user-avatar {
    width: 28px; height: 28px;
    background: linear-gradient(135deg, var(--accent2), var(--accent));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem;
    font-weight: 600;
    flex-shrink: 0;
    color: #fff;
}
.user-name { font-size: .82rem; font-weight: 500; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.logout-btn {
    background: none;
    border: none;
    color: var(--muted);
    cursor: pointer;
    font-size: 1rem;
    padding: .2rem;
    border-radius: 6px;
    transition: color .15s;
}
.logout-btn:hover { color: var(--high); }

/* ── MAIN ─────────────────────────────────────────────── */
.main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
}

.topbar {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: sticky;
    top: 0;
    z-index: 40;
}
.topbar-title { font-size: 1rem; font-weight: 600; flex: 1; }
.topbar-title span { color: var(--text2); font-weight: 400; font-size: .875rem; margin-left: .4rem; }

/* ── STATS ────────────────────────────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
}
.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: .9rem 1rem;
}
.stat-label { font-size: .75rem; color: var(--text2); margin-bottom: .3rem; }
.stat-value { font-size: 1.6rem; font-weight: 600; font-family: 'DM Mono', monospace; line-height: 1; }
.stat-value.danger { color: var(--high); }
.stat-value.warn   { color: var(--medium); }
.stat-value.ok     { color: var(--success); }

/* ── CONTENT ──────────────────────────────────────────── */
.content {
    padding: 1.25rem 1.5rem;
    flex: 1;
    overflow-y: auto;
}

/* Filters bar */
.filters-bar {
    display: flex;
    align-items: center;
    gap: .6rem;
    margin-bottom: 1.1rem;
    flex-wrap: wrap;
}
.filter-tabs {
    display: flex;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 3px;
    gap: 2px;
}
.filter-tab {
    padding: .35rem .75rem;
    border-radius: 6px;
    font-size: .8rem;
    font-weight: 500;
    cursor: pointer;
    color: var(--text2);
    transition: all .15s;
    border: none;
    background: none;
    font-family: inherit;
}
.filter-tab.active { background: var(--surface3); color: var(--text); }

.filter-select {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-family: inherit;
    font-size: .8rem;
    padding: .38rem .7rem;
    cursor: pointer;
    outline: none;
    transition: border-color .15s;
}
.filter-select:focus { border-color: var(--accent); }
.filter-select option { background: var(--surface2); }

.spacer { flex: 1; }

.btn-primary {
    background: linear-gradient(135deg, var(--accent2), var(--accent));
    border: none;
    border-radius: var(--radius-sm);
    color: #fff;
    font-family: inherit;
    font-size: .82rem;
    font-weight: 600;
    padding: .45rem 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: .4rem;
    transition: opacity .15s;
    white-space: nowrap;
}
.btn-primary:hover { opacity: .88; }

/* ── TASK LIST ────────────────────────────────────────── */
.tasks-header {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .7rem;
    font-size: .75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--muted);
}

.task-list { display: flex; flex-direction: column; gap: .5rem; }

.task-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: .85rem 1rem;
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    transition: border-color .15s, box-shadow .15s;
    position: relative;
    overflow: hidden;
}
.task-item::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
}
.task-item.priority-high::before   { background: var(--high); }
.task-item.priority-medium::before { background: var(--medium); }
.task-item.priority-low::before    { background: var(--low); }
.task-item:hover { border-color: var(--border2); box-shadow: 0 2px 12px rgba(0,0,0,.2); }
.task-item.overdue { background: var(--overdue-bg); border-color: rgba(239,68,68,.2); }
.task-item.completed-item { opacity: .6; }
.task-item.completed-item .task-title { text-decoration: line-through; color: var(--muted); }

.task-check {
    width: 18px; height: 18px;
    border: 1.5px solid var(--border2);
    border-radius: 5px;
    flex-shrink: 0;
    cursor: pointer;
    margin-top: 1px;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
    background: none;
}
.task-check:hover { border-color: var(--accent); background: rgba(167,139,250,.1); }
.task-check.checked { background: var(--accent2); border-color: var(--accent2); }
.task-check.checked::after { content: '✓'; color: #fff; font-size: .65rem; font-weight: 700; }

.task-body { flex: 1; min-width: 0; }
.task-title {
    font-size: .9rem;
    font-weight: 500;
    margin-bottom: .3rem;
    line-height: 1.35;
}
.task-desc { font-size: .8rem; color: var(--text2); margin-bottom: .4rem; line-height: 1.4; }
.task-meta {
    display: flex;
    align-items: center;
    gap: .5rem;
    flex-wrap: wrap;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    padding: .18rem .5rem;
    border-radius: 20px;
    font-size: .72rem;
    font-weight: 500;
    border: 1px solid transparent;
}
.badge-high    { background: var(--high-bg);   border-color: var(--high-bd);   color: var(--high); }
.badge-medium  { background: var(--medium-bg); border-color: var(--medium-bd); color: var(--medium); }
.badge-low     { background: var(--low-bg);    border-color: var(--low-bd);    color: var(--low); }
.badge-cat     { background: var(--surface2);  border-color: var(--border);    color: var(--text2); }
.badge-overdue { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.3); color: var(--high); }

.deadline-text {
    font-size: .75rem;
    color: var(--muted);
    font-family: 'DM Mono', monospace;
}
.deadline-text.overdue { color: var(--high); }
.deadline-text.soon    { color: var(--medium); }

.task-actions {
    display: flex;
    gap: .3rem;
    opacity: 0;
    transition: opacity .15s;
    flex-shrink: 0;
}
.task-item:hover .task-actions { opacity: 1; }
.action-btn {
    width: 28px; height: 28px;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: var(--surface2);
    color: var(--text2);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem;
    transition: all .15s;
}
.action-btn:hover { border-color: var(--border2); color: var(--text); background: var(--surface3); }
.action-btn.delete:hover { border-color: rgba(248,113,113,.4); color: var(--high); background: rgba(248,113,113,.08); }

/* ── EMPTY STATE ──────────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--muted);
}
.empty-icon { font-size: 2.5rem; margin-bottom: .75rem; }
.empty-title { font-size: .95rem; font-weight: 500; margin-bottom: .3rem; color: var(--text2); }
.empty-sub { font-size: .82rem; }

/* ── LOADING ──────────────────────────────────────────── */
.spinner {
    width: 18px; height: 18px;
    border: 2px solid var(--border2);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin .6s linear infinite;
    margin: 2rem auto;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── MODAL ────────────────────────────────────────────── */
.overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.6);
    backdrop-filter: blur(4px);
    z-index: 100;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.overlay.open { display: flex; }
.modal {
    background: var(--surface);
    border: 1px solid var(--border2);
    border-radius: 18px;
    padding: 1.75rem;
    width: 100%;
    max-width: 480px;
    animation: modalIn .25s ease;
    max-height: 90vh;
    overflow-y: auto;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(.96) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-header {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 1.4rem;
}
.modal-title { font-size: 1.05rem; font-weight: 600; }
.modal-close {
    background: none; border: none;
    color: var(--muted); cursor: pointer;
    font-size: 1.2rem; line-height: 1;
    padding: .2rem;
    transition: color .15s;
}
.modal-close:hover { color: var(--text); }

.form-group { margin-bottom: 1rem; }
.form-label {
    display: block;
    font-size: .78rem;
    color: var(--text2);
    margin-bottom: .4rem;
    letter-spacing: .02em;
    font-weight: 500;
}
.form-control {
    width: 100%;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .65rem .9rem;
    color: var(--text);
    font-family: inherit;
    font-size: .875rem;
    transition: border-color .15s;
    outline: none;
    resize: vertical;
}
.form-control:focus { border-color: var(--accent); }
.form-control::placeholder { color: var(--muted); }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }

.priority-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .5rem;
}
.priority-opt {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .5rem;
    text-align: center;
    cursor: pointer;
    font-size: .8rem;
    font-weight: 500;
    transition: all .15s;
    color: var(--text2);
}
.priority-opt:hover { border-color: var(--border2); }
.priority-opt.sel-high   { border-color: var(--high);   background: var(--high-bg);   color: var(--high); }
.priority-opt.sel-medium { border-color: var(--medium); background: var(--medium-bg); color: var(--medium); }
.priority-opt.sel-low    { border-color: var(--low);    background: var(--low-bg);    color: var(--low); }

.modal-footer {
    display: flex;
    gap: .6rem;
    margin-top: 1.4rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}
.btn-ghost {
    flex: 1;
    padding: .6rem;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text2);
    font-family: inherit;
    font-size: .875rem;
    cursor: pointer;
    transition: all .15s;
}
.btn-ghost:hover { border-color: var(--border2); color: var(--text); }
.btn-save {
    flex: 2;
    padding: .6rem;
    background: linear-gradient(135deg, var(--accent2), var(--accent));
    border: none;
    border-radius: var(--radius-sm);
    color: #fff;
    font-family: inherit;
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity .15s;
}
.btn-save:hover { opacity: .88; }

/* Toast */
.toast-wrap {
    position: fixed;
    bottom: 1.5rem; right: 1.5rem;
    z-index: 200;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    pointer-events: none;
}
.toast {
    background: var(--surface2);
    border: 1px solid var(--border2);
    border-radius: var(--radius-sm);
    padding: .6rem 1rem;
    font-size: .82rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    animation: toastIn .25s ease;
    pointer-events: all;
    min-width: 220px;
    box-shadow: 0 4px 20px rgba(0,0,0,.4);
}
.toast.success { border-color: rgba(52,211,153,.3); }
.toast.error   { border-color: rgba(248,113,113,.3); }
@keyframes toastIn {
    from { opacity: 0; transform: translateX(20px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* Categories tab */
.cat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: .75rem;
    margin-top: .75rem;
}
.cat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem;
    cursor: pointer;
    transition: all .15s;
    position: relative;
}
.cat-card:hover { border-color: var(--border2); }
.cat-card.active { border-color: var(--accent); background: rgba(167,139,250,.06); }
.cat-icon { font-size: 1.4rem; margin-bottom: .5rem; }
.cat-name { font-size: .875rem; font-weight: 500; margin-bottom: .2rem; }
.cat-count { font-size: .75rem; color: var(--muted); }
.cat-del {
    position: absolute; top: .5rem; right: .5rem;
    background: none; border: none;
    color: var(--muted); cursor: pointer;
    font-size: .9rem; opacity: 0;
    transition: opacity .15s, color .15s;
    padding: .2rem;
    line-height: 1;
}
.cat-card:hover .cat-del { opacity: 1; }
.cat-del:hover { color: var(--high); }

.add-cat-btn {
    background: var(--surface);
    border: 1.5px dashed var(--border2);
    border-radius: var(--radius);
    padding: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .4rem;
    cursor: pointer;
    color: var(--muted);
    font-size: .82rem;
    transition: all .15s;
    min-height: 90px;
}
.add-cat-btn:hover { border-color: var(--accent); color: var(--accent); background: rgba(167,139,250,.04); }

/* Responsive */
@media (max-width: 768px) {
    .sidebar { display: none; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .form-row { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon">✅</div>
        <div class="logo-name">TaskFlow</div>
    </div>

    <div class="nav-section">
        <div class="nav-label">Tampilan</div>
        <div class="nav-item active" data-view="all" onclick="setView('all')">
            <span class="icon">📋</span> Semua Tugas
            <span class="badge" id="badge-all"><?= $stats['total'] ?></span>
        </div>
        <div class="nav-item" data-view="pending" onclick="setView('pending')">
            <span class="icon">⏳</span> Belum Selesai
            <span class="badge" id="badge-pending"><?= $stats['pending'] ?></span>
        </div>
        <div class="nav-item" data-view="completed" onclick="setView('completed')">
            <span class="icon">✅</span> Selesai
            <span class="badge" id="badge-completed"><?= $stats['completed'] ?></span>
        </div>
        <div class="nav-item" data-view="overdue" onclick="setView('overdue')">
            <span class="icon">⚠️</span> Lewat Deadline
            <span class="badge" id="badge-overdue" style="<?= $stats['overdue'] > 0 ? 'background:rgba(248,113,113,.2);color:var(--high)' : '' ?>"><?= $stats['overdue'] ?></span>
        </div>
    </div>

    <div class="nav-section">
        <div class="nav-label">Prioritas</div>
        <div class="nav-item" data-view="high" onclick="setView('high')">
            <span class="icon">🔴</span> High
        </div>
        <div class="nav-item" data-view="medium" onclick="setView('medium')">
            <span class="icon">🟡</span> Medium
        </div>
        <div class="nav-item" data-view="low" onclick="setView('low')">
            <span class="icon">🟢</span> Low
        </div>
    </div>

    <div class="nav-section">
        <div class="nav-label">Kategori</div>
        <div id="sidebar-cats">
        <?php foreach ($cats as $cat): ?>
        <div class="nav-item" data-view="cat-<?= $cat['id'] ?>" onclick="setView('cat-<?= $cat['id'] ?>')">
            <span class="icon"><?= htmlspecialchars($cat['icon']) ?></span>
            <?= htmlspecialchars($cat['name']) ?>
            <span class="badge"><?= $cat['task_count'] ?></span>
        </div>
        <?php endforeach; ?>
        </div>
        <div class="nav-item" onclick="setView('categories')">
            <span class="icon">⚙️</span> Kelola Kategori
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar"><?= strtoupper(substr($user['username'],0,1)) ?></div>
            <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
            <button class="logout-btn" onclick="location.href='logout.php'" title="Keluar">⎋</button>
        </div>
    </div>
</aside>

<!-- MAIN -->
<main class="main">
    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-title" id="topbar-title">
            Semua Tugas <span id="topbar-sub"></span>
        </div>
        <button class="btn-primary" onclick="openModal()">
            + Tugas Baru
        </button>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Tugas</div>
            <div class="stat-value" id="stat-total"><?= $stats['total'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Belum Selesai</div>
            <div class="stat-value warn" id="stat-pending"><?= $stats['pending'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Lewat Deadline</div>
            <div class="stat-value danger" id="stat-overdue"><?= $stats['overdue'] ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Selesai</div>
            <div class="stat-value ok" id="stat-completed"><?= $stats['completed'] ?></div>
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
                <option value="high">🔴 High</option>
                <option value="medium">🟡 Medium</option>
                <option value="low">🟢 Low</option>
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
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="font-size:.9rem;font-weight:500;">Kategori Kamu</div>
                <button class="btn-primary" onclick="openCatModal()" style="font-size:.78rem;padding:.38rem .8rem">+ Kategori</button>
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
        <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <form id="task-form" onsubmit="submitTask(event)">
        <input type="hidden" id="task-id">
        <div class="form-group">
            <label class="form-label">Judul Tugas *</label>
            <input class="form-control" id="task-title" placeholder="Apa yang perlu dikerjakan?" required>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" id="task-desc" rows="2" placeholder="Detail tambahan (opsional)..."></textarea>
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
                <div class="priority-opt sel-high" data-p="high" onclick="selectPriority('high')">🔴 High</div>
                <div class="priority-opt" data-p="medium" onclick="selectPriority('medium')">🟡 Medium</div>
                <div class="priority-opt" data-p="low" onclick="selectPriority('low')">🟢 Low</div>
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
<div class="modal" style="max-width:360px">
    <div class="modal-header">
        <div class="modal-title">Kategori Baru</div>
        <button class="modal-close" onclick="document.getElementById('cat-overlay').classList.remove('open')">✕</button>
    </div>
    <div class="form-group">
        <label class="form-label">Nama Kategori *</label>
        <input class="form-control" id="cat-name" placeholder="mis. Kerja, Pribadi...">
    </div>
    <div class="form-group">
        <label class="form-label">Ikon</label>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap" id="icon-picker">
            <?php foreach(['📁','💼','📚','🛒','🏠','💪','🎯','🎨','🎵','🚀','❤️','⚡'] as $ic): ?>
            <div class="icon-opt" data-icon="<?= $ic ?>" onclick="selectIcon('<?= $ic ?>')" style="font-size:1.3rem;cursor:pointer;padding:.35rem;border:1.5px solid var(--border);border-radius:8px;transition:border-color .15s"><?= $ic ?></div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="cat-icon" value="📁">
    </div>
    <div class="form-group">
        <label class="form-label">Warna</label>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap" id="color-picker">
            <?php foreach(['#6366f1','#3b82f6','#8b5cf6','#ec4899','#f59e0b','#10b981','#f87171','#06b6d4'] as $col): ?>
            <div class="color-opt" data-color="<?= $col ?>" onclick="selectColor('<?= $col ?>')" style="width:24px;height:24px;border-radius:50%;background:<?= $col ?>;cursor:pointer;border:2px solid transparent;transition:border-color .15s"></div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" id="cat-color" value="#6366f1">
    </div>
    <div class="modal-footer">
        <button class="btn-ghost" onclick="document.getElementById('cat-overlay').classList.remove('open')">Batal</button>
        <button class="btn-save" onclick="submitCategory()">Simpan</button>
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
        document.getElementById('topbar-title').innerHTML = 'Kategori <span></span>';
        renderCategories();
        return;
    }

    catCont.style.display = 'none';
    taskCont.style.display = 'block';
    filterBar.style.display = 'flex';

    // Map view to filter
    const viewMap = {
        'all':       { title: 'Semua Tugas' },
        'pending':   { title: 'Belum Selesai', status: 'pending' },
        'completed': { title: 'Selesai', status: 'completed' },
        'overdue':   { title: 'Lewat Deadline', status: 'pending' },
        'high':      { title: 'Prioritas High', priority: 'high', status: 'pending' },
        'medium':    { title: 'Prioritas Medium', priority: 'medium', status: 'pending' },
        'low':       { title: 'Prioritas Low', priority: 'low', status: 'pending' },
    };

    if (v.startsWith('cat-')) {
        const catId = v.replace('cat-', '');
        state.categoryId = catId;
        const cat = state.categories.find(c => c.id == catId);
        document.getElementById('topbar-title').innerHTML = `${cat?.icon || '📁'} ${escHtml(cat?.name || 'Kategori')} <span></span>`;
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
            <div class="empty-icon">🎉</div>
            <div class="empty-title">Tidak ada tugas</div>
            <div class="empty-sub">Tambah tugas baru dengan tombol di atas</div>
        </div>`;
        return;
    }

    cont.innerHTML = `<div class="tasks-header">${tasks.length} tugas</div><div class="task-list" id="task-list"></div>`;
    const list = document.getElementById('task-list');
    tasks.forEach(t => list.appendChild(buildTaskEl(t)));

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
        deadlineHtml = `<span class="deadline-text ${cls}">${isOverdue ? '⚠️ ' : '📅 '}${label}</span>`;
    }

    let catHtml = '';
    if (t.category_name) {
        catHtml = `<span class="badge badge-cat">${escHtml(t.category_icon || '📁')} ${escHtml(t.category_name)}</span>`;
    }

    el.innerHTML = `
        <div class="task-check${t.status==='completed'?' checked':''}" onclick="toggleTask(${t.id})"></div>
        <div class="task-body">
            <div class="task-title">${escHtml(t.title)}</div>
            ${t.description ? `<div class="task-desc">${escHtml(t.description)}</div>` : ''}
            <div class="task-meta">
                <span class="badge badge-${t.priority}">${priorityLabel(t.priority)}</span>
                ${catHtml}
                ${deadlineHtml}
                ${isOverdue ? '<span class="badge badge-overdue">Lewat Deadline</span>' : ''}
            </div>
        </div>
        <div class="task-actions">
            <button class="action-btn" onclick="editTask(${t.id})" title="Edit">✏️</button>
            <button class="action-btn delete" onclick="deleteTask(${t.id})" title="Hapus">🗑️</button>
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
        sel.innerHTML += `<option value="${c.id}">${escHtml(c.icon)} ${escHtml(c.name)}</option>`;
    });
}

function updateSidebarCats() {
    const cont = document.getElementById('sidebar-cats');
    cont.innerHTML = state.categories.map(c => `
        <div class="nav-item" data-view="cat-${c.id}" onclick="setView('cat-${c.id}')">
            <span class="icon">${escHtml(c.icon)}</span>
            ${escHtml(c.name)}
            <span class="badge">${c.task_count}</span>
        </div>`).join('');
}

function renderCategories() {
    const grid = document.getElementById('cat-grid');
    grid.innerHTML = state.categories.map(c => `
        <div class="cat-card" onclick="setView('cat-${c.id}')">
            <button class="cat-del" onclick="event.stopPropagation();deleteCat(${c.id})">✕</button>
            <div class="cat-icon">${escHtml(c.icon)}</div>
            <div class="cat-name">${escHtml(c.name)}</div>
            <div class="cat-count">${c.task_count} tugas</div>
        </div>
    `).join('') + `<div class="add-cat-btn" onclick="openCatModal()">
        <div style="font-size:1.4rem">＋</div>
        <div>Tambah Kategori</div>
    </div>`;
}

function openCatModal() {
    document.getElementById('cat-overlay').classList.add('open');
    document.getElementById('cat-name').value = '';
    selectIcon('📁');
    selectColor('#6366f1');
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
    });
}
function selectColor(col) {
    document.getElementById('cat-color').value = col;
    document.querySelectorAll('.color-opt').forEach(el => {
        el.style.borderColor = el.dataset.color === col ? '#fff' : 'transparent';
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
    el.innerHTML = `<span>${type==='success'?'✅':'❌'}</span> ${escHtml(msg)}`;
    wrap.appendChild(el);
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
    return { high: '🔴 High', medium: '🟡 Medium', low: '🟢 Low' }[p] || p;
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
