<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Tasks | Task Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink:      #0a0f1a;
            --slate:    #1e293b;
            --stone:    #334155;
            --smoke:    #64748b;
            --frost:    #f1f5f9;
            --ice:      #f8fafc;
            --amber:    #f59e0b;
            --ruby:     #dc2626;
            --emerald:  #10b981;
            --sapphire: #3b82f6;
            --violet:   #7c3aed;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.06);
            --shadow-lg: 0 20px 40px rgba(0,0,0,0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--ice);
            color: var(--ink);
            line-height: 1.6;
        }

        /* ── Top Nav ── */
        .top-nav {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-family: 'Crimson Pro', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ink);
            text-decoration: none;
            letter-spacing: -0.02em;
        }

        .nav-actions { display: flex; gap: 1rem; align-items: center; }

        .user-badge {
            background: var(--frost);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--stone);
        }

        .role-tag {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 0.5rem;
        }

        .btn-logout {
            background: var(--slate);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-logout:hover { background: var(--ink); transform: translateY(-1px); }

        /* ── Container ── */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2.5rem 2rem;
        }

        /* ── Page Header ── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--smoke);
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 600;
            margin-bottom: 0.7rem;
            transition: all 0.2s;
        }

        .back-link:hover { color: var(--ink); transform: translateX(-3px); }

        .page-title {
            font-family: 'Crimson Pro', serif;
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -0.03em;
            margin-bottom: 0.25rem;
            line-height: 1.1;
        }

        .page-subtitle { color: var(--smoke); font-size: 0.95rem; }

        .btn-create {
            background: linear-gradient(135deg, var(--sapphire) 0%, #2563eb 100%);
            color: white;
            border: none;
            padding: 0.8rem 1.6rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(59,130,246,0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
            align-self: flex-start;
            margin-top: 1.6rem;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59,130,246,0.4);
        }

        /* ── Filter Tab Pills ── */
        .filter-tabs {
            display: flex;
            gap: 0.6rem;
            margin-bottom: 1.8rem;
            flex-wrap: wrap;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0.6rem;
        }

        .filter-tab {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.1rem;
            border-radius: 9px;
            font-size: 0.83rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: var(--smoke);
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .filter-tab:hover {
            background: var(--frost);
            color: var(--stone);
        }

        .filter-tab .tab-count {
            background: #e2e8f0;
            color: var(--stone);
            padding: 0.1rem 0.5rem;
            border-radius: 10px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        /* Active states per filter */
        .filter-tab.active-all        { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .filter-tab.active-all        .tab-count { background: #bfdbfe; color: #1d4ed8; }

        .filter-tab.active-pending    { background: #fffbeb; color: #92400e; border-color: #fde68a; }
        .filter-tab.active-pending    .tab-count { background: #fde68a; color: #92400e; }

        .filter-tab.active-in_progress { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
        .filter-tab.active-in_progress .tab-count { background: #fecaca; color: #991b1b; }

        .filter-tab.active-completed  { background: #f0fdf4; color: #065f46; border-color: #a7f3d0; }
        .filter-tab.active-completed  .tab-count { background: #a7f3d0; color: #065f46; }

        .filter-tab.active-overdue    { background: #faf5ff; color: #5b21b6; border-color: #ddd6fe; }
        .filter-tab.active-overdue    .tab-count { background: #ddd6fe; color: #5b21b6; }

        .filter-tab.active-my_tasks   { background: var(--frost); color: var(--slate); border-color: #cbd5e1; }
        .filter-tab.active-my_tasks   .tab-count { background: #cbd5e1; color: var(--slate); }

        .filter-tab.active-due_today  { background: #ecfeff; color: #0e7490; border-color: #a5f3fc; }
        .filter-tab.active-due_today  .tab-count { background: #a5f3fc; color: #0e7490; }

        /* ── Controls Bar ── */
        .controls-bar {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1.2rem;
            flex-wrap: wrap;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--smoke);
            font-size: 0.9rem;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.88rem;
            background: white;
            color: var(--ink);
            transition: all 0.25s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--sapphire);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        .filter-select {
            padding: 0.8rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.85rem;
            background: white;
            color: var(--stone);
            cursor: pointer;
            transition: all 0.2s;
            min-width: 145px;
        }

        .filter-select:focus { outline: none; border-color: var(--sapphire); }

        .results-count {
            font-size: 0.82rem;
            color: var(--smoke);
            font-weight: 600;
            white-space: nowrap;
        }

        /* ── Table Section ── */
        .table-section {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.4rem 1.8rem;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .table-title-wrap {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .table-title {
            font-family: 'Crimson Pro', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--ink);
        }

        .table-badge {
            background: var(--slate);
            color: white;
            padding: 0.25rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* ── Task Table ── */
        .task-table {
            width: 100%;
            border-collapse: collapse;
        }

        .task-table thead th {
            text-align: left;
            padding: 0.85rem 1.2rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--smoke);
            background: var(--frost);
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .task-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }

        .task-table tbody tr:hover    { background: #fafbff; }
        .task-table tbody tr:last-child { border-bottom: none; }
        .task-table tbody tr.row-hidden { display: none; }

        .task-table tbody td {
            padding: 1rem 1.2rem;
            vertical-align: middle;
        }

        /* Task cell */
        .task-name {
            font-weight: 600;
            color: var(--ink);
            font-size: 0.92rem;
            margin-bottom: 0.18rem;
            line-height: 1.3;
        }

        .task-snippet {
            font-size: 0.78rem;
            color: var(--smoke);
            line-height: 1.4;
        }

        /* Status badges */
        .s-badge {
            display: inline-block;
            padding: 0.28rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        .s-pending     { background: #fef3c7; color: #92400e; }
        .s-in_progress { background: #fecaca; color: #991b1b; }
        .s-completed   { background: #d1fae5; color: #065f46; }

        /* Priority */
        .p-cell { display: flex; align-items: center; gap: 0.45rem; font-size: 0.85rem; font-weight: 500; color: var(--stone); }
        .p-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .p-high   .p-dot { background: var(--ruby); }
        .p-medium .p-dot { background: var(--amber); }
        .p-low    .p-dot { background: var(--emerald); }

        /* Assignee avatars */
        .av-stack { display: flex; align-items: center; }

        .av {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--stone) 0%, var(--smoke) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.62rem;
            border: 2px solid white;
            margin-left: -6px;
            flex-shrink: 0;
            cursor: default;
            transition: transform 0.15s;
        }

        .av:first-child { margin-left: 0; }
        .av:hover { transform: scale(1.15); z-index: 1; }
        .av.av-owner { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .av-more { background: var(--frost); color: var(--stone); font-size: 0.6rem; }

        .no-assign { font-size: 0.78rem; color: var(--smoke); font-style: italic; }

        /* Due date */
        .due-cell     { font-size: 0.82rem; color: var(--smoke); white-space: nowrap; }
        .due-overdue  { color: var(--ruby);  font-weight: 700; }
        .due-today    { color: var(--amber); font-weight: 700; }

        /* Actions */
        .act-cell { display: flex; gap: 0.35rem; flex-wrap: nowrap; }

        .btn-act {
            padding: 0.38rem 0.75rem;
            border-radius: 6px;
            font-size: 0.73rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s;
            border: 1px solid #e2e8f0;
            background: var(--frost);
            color: var(--stone);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            white-space: nowrap;
        }

        .btn-act:hover            { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
        .btn-view:hover           { background: var(--ink);      color: white; border-color: var(--ink); }
        .btn-edit:hover           { background: var(--sapphire); color: white; border-color: var(--sapphire); }
        .btn-assign:hover         { background: var(--amber);    color: var(--ink); border-color: var(--amber); }
        .btn-archive:hover        { background: var(--smoke);    color: white; border-color: var(--smoke); }

        /* ── Empty State ── */
        .empty-state {
            padding: 5rem 2rem;
            text-align: center;
            color: var(--smoke);
        }

        .empty-icon  { font-size: 3.5rem; opacity: 0.2; margin-bottom: 1rem; }
        .empty-title { font-family: 'Crimson Pro', serif; font-size: 1.5rem; color: var(--stone); margin-bottom: 0.5rem; }
        .empty-text  { font-size: 0.92rem; line-height: 1.6; }

        .empty-cta {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.8rem 1.8rem;
            background: linear-gradient(135deg, var(--sapphire) 0%, #2563eb 100%);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(59,130,246,0.3);
        }

        .empty-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(59,130,246,0.4); }

        /* ── No Search Results ── */
        .no-results {
            display: none;
            padding: 3rem;
            text-align: center;
            color: var(--smoke);
            font-size: 0.92rem;
        }

        /* ── Pagination ── */
        .pagination-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.8rem;
            border-top: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 0.7rem;
        }

        .pag-info  { font-size: 0.8rem; color: var(--smoke); }
        .pag-btns  { display: flex; gap: 0.35rem; }

        .pag-btn {
            padding: 0.42rem 0.8rem;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            background: white;
            color: var(--stone);
            transition: all 0.15s;
        }

        .pag-btn:hover   { background: var(--frost); }
        .pag-btn.active  { background: var(--sapphire); color: white; border-color: var(--sapphire); }
        .pag-btn:disabled { opacity: 0.38; cursor: not-allowed; }

        /* ── Toast ── */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--ink);
            color: white;
            padding: 0.9rem 1.4rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.85rem;
            z-index: 9999;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            transform: translateY(80px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
        }

        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.ok   { border-left: 4px solid var(--emerald); }
        .toast.err  { border-left: 4px solid var(--ruby); }

        /* ── Flash messages ── */
        .flash {
            padding: 0.9rem 1.2rem;
            border-radius: 10px;
            margin-bottom: 1.2rem;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .flash-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .flash-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* ── Responsive ── */
        @media (max-width: 1100px) {
            .task-table th:nth-child(4),
            .task-table td:nth-child(4) { display: none; }
        }

        @media (max-width: 860px) {
            .task-table th:nth-child(3),
            .task-table td:nth-child(3) { display: none; }
        }

        @media (max-width: 680px) {
            .task-table th:nth-child(5),
            .task-table td:nth-child(5) { display: none; }
            .page-title  { font-size: 1.9rem; }
            .filter-tabs { gap: 0.3rem; padding: 0.4rem; }
            .filter-tab  { padding: 0.5rem 0.8rem; font-size: 0.78rem; }
        }

        /* ── Animations ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .container > * { animation: fadeInUp 0.45s ease forwards; opacity: 0; }
        .container > *:nth-child(1) { animation-delay: 0.05s; }
        .container > *:nth-child(2) { animation-delay: 0.10s; }
        .container > *:nth-child(3) { animation-delay: 0.15s; }
        .container > *:nth-child(4) { animation-delay: 0.20s; }
        .container > *:nth-child(5) { animation-delay: 0.25s; }
        .container > *:nth-child(6) { animation-delay: 0.30s; }
    </style>
</head>
<body>

<!-- ── Top Nav ── -->
<nav class="top-nav">
    <div class="nav-container">
        <a href="<?= base_url('admin/dashboard') ?>" class="brand">TaskFlow</a>
        <div class="nav-actions">
            <div class="user-badge">
                <?= htmlspecialchars($user['name']) ?>
                <span class="role-tag"><?= ucfirst($user['role']) ?></span>
            </div>
            <a href="<?= base_url('logout') ?>" class="btn-logout">Sign Out</a>
        </div>
    </div>
</nav>

<!-- ── Container ── -->
<div class="container">

    <?php
    /* ─────────────────────────────────────────────────────────────────────
       PHP helpers — labels, icons, active filter
    ───────────────────────────────────────────────────────────────────── */
    $filterLabels = [
        'all'         => 'All Tasks',
        'pending'     => 'Pending',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'overdue'     => 'Overdue',
        'my_tasks'    => 'My Tasks',
        'due_today'   => 'Due Today',
    ];

    $filterIcons = [
        'all'         => '📋',
        'pending'     => '⏳',
        'in_progress' => '🔄',
        'completed'   => '✅',
        'overdue'     => '⚠️',
        'my_tasks'    => '👤',
        'due_today'   => '📅',
    ];

    $af = $current_filter ?? 'all'; // active filter shorthand
    ?>

    <!-- ── Flash messages ── -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success">✅ <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">❌ <?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- ── Page Header ── -->
    <div class="page-header">
        <div>
            <a href="<?= base_url('admin/dashboard') ?>" class="back-link">← Back to Dashboard</a>
            <h1 class="page-title">
                <?= $filterIcons[$af] ?? '📋' ?>
                <?= $filterLabels[$af] ?? 'All Tasks' ?>
            </h1>
            <p class="page-subtitle">
                <?= count($tasks) ?> task<?= count($tasks) !== 1 ? 's' : '' ?> found
                <?php if ($af !== 'all'): ?>
                    &middot; filtered by <strong><?= $filterLabels[$af] ?? $af ?></strong>
                    &middot; <a href="<?= base_url('admin/tasks?filter=all') ?>" style="color:var(--sapphire);font-weight:600;">Clear filter</a>
                <?php endif; ?>
            </p>
        </div>
        <a href="<?= base_url('tasks/create') ?>" class="btn-create">+ New Task</a>
    </div>

    <!-- ── Filter Tabs (all 7 filters, stat counts from controller) ── -->
    <div class="filter-tabs">

        <a href="<?= base_url('admin/tasks?filter=all') ?>"
           class="filter-tab <?= $af === 'all' ? 'active-all' : '' ?>">
            📋 All
            <span class="tab-count"><?= $stats['total'] ?></span>
        </a>

        <a href="<?= base_url('admin/tasks?filter=pending') ?>"
           class="filter-tab <?= $af === 'pending' ? 'active-pending' : '' ?>">
            ⏳ Pending
            <span class="tab-count"><?= $stats['pending'] ?></span>
        </a>

        <a href="<?= base_url('admin/tasks?filter=in_progress') ?>"
           class="filter-tab <?= $af === 'in_progress' ? 'active-in_progress' : '' ?>">
            🔄 In Progress
            <span class="tab-count"><?= $stats['in_progress'] ?></span>
        </a>

        <a href="<?= base_url('admin/tasks?filter=completed') ?>"
           class="filter-tab <?= $af === 'completed' ? 'active-completed' : '' ?>">
            ✅ Completed
            <span class="tab-count"><?= $stats['completed'] ?></span>
        </a>

        <a href="<?= base_url('admin/tasks?filter=overdue') ?>"
           class="filter-tab <?= $af === 'overdue' ? 'active-overdue' : '' ?>">
            ⚠️ Overdue
            <span class="tab-count"><?= $stats['overdue'] ?></span>
        </a>

        <a href="<?= base_url('admin/tasks?filter=my_tasks') ?>"
           class="filter-tab <?= $af === 'my_tasks' ? 'active-my_tasks' : '' ?>">
            👤 My Tasks
        </a>

        <a href="<?= base_url('admin/tasks?filter=due_today') ?>"
           class="filter-tab <?= $af === 'due_today' ? 'active-due_today' : '' ?>">
            📅 Due Today
        </a>

    </div>

    <!-- ── Controls Bar ── -->
    <div class="controls-bar">
        <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" id="searchInput"
                   placeholder="Search by title or description...">
        </div>
        <select class="filter-select" id="priorityFilter">
            <option value="all">All Priorities</option>
            <option value="high">🔴 High</option>
            <option value="medium">🟡 Medium</option>
            <option value="low">🟢 Low</option>
        </select>
        <span class="results-count" id="resultsCount">
            <?= count($tasks) ?> task<?= count($tasks) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <!-- ── Table Section ── -->
    <div class="table-section">

        <div class="table-top">
            <div class="table-title-wrap">
                <span class="table-title"><?= $filterLabels[$af] ?? 'Tasks' ?></span>
                <span class="table-badge" id="tableBadge"><?= count($tasks) ?></span>
            </div>
        </div>

        <?php if (empty($tasks)): ?>

            <div class="empty-state">
                <div class="empty-icon"><?= $filterIcons[$af] ?? '📋' ?></div>
                <div class="empty-title">No tasks found</div>
                <p class="empty-text">
                    <?php if ($af !== 'all'): ?>
                        There are no <strong><?= strtolower($filterLabels[$af] ?? $af) ?></strong> tasks right now.
                    <?php else: ?>
                        No tasks exist yet. Create your first task to get started.
                    <?php endif; ?>
                </p>
                <?php if ($af !== 'all'): ?>
                    <a href="<?= base_url('admin/tasks?filter=all') ?>" class="empty-cta">View All Tasks</a>
                <?php else: ?>
                    <a href="<?= base_url('tasks/create') ?>" class="empty-cta">+ Create Task</a>
                <?php endif; ?>
            </div>

        <?php else: ?>

            <table class="task-table">
                <thead>
                    <tr>
                        <th style="width:30%">Task</th>
                        <th style="width:11%">Status</th>
                        <th style="width:10%">Priority</th>
                        <th style="width:16%">Assigned To</th>
                        <th style="width:11%">Due Date</th>
                        <th style="width:22%">Actions</th>
                    </tr>
                </thead>
                <tbody id="taskBody">

                <?php
                $today = date('Y-m-d');
                foreach ($tasks as $task):

                    // ── Due date CSS class ──
                    $dueCls = '';
                    $dueStr = '—';
                    if (!empty($task['due_date'])) {
                        $dueStr = date('M d, Y', strtotime($task['due_date']));
                        if ($task['status'] !== 'completed') {
                            if ($task['due_date'] < $today)      $dueCls = 'due-overdue';
                            elseif ($task['due_date'] === $today) $dueCls = 'due-today';
                        }
                    }
                ?>

                    <tr data-title="<?= strtolower(htmlspecialchars($task['title'])) ?>"
                        data-desc="<?= strtolower(htmlspecialchars($task['description'] ?? '')) ?>"
                        data-priority="<?= $task['priority'] ?>">

                        <!-- Task -->
                        <td>
                            <div class="task-name"><?= htmlspecialchars($task['title']) ?></div>
                            <?php if (!empty($task['description'])): ?>
                            <div class="task-snippet">
                                <?= htmlspecialchars(substr($task['description'], 0, 90)) ?><?= strlen($task['description']) > 90 ? '…' : '' ?>
                            </div>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td>
                            <span class="s-badge s-<?= $task['status'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                            </span>
                        </td>

                        <!-- Priority -->
                        <td>
                            <div class="p-cell p-<?= $task['priority'] ?>">
                                <div class="p-dot"></div>
                                <?= ucfirst($task['priority']) ?>
                            </div>
                        </td>

                        <!-- Assignees -->
                        <td>
                            <?php if (!empty($task['users'])): ?>
                                <div class="av-stack">
                                    <?php
                                    $shown     = array_slice($task['users'], 0, 4);
                                    $overflow  = count($task['users']) - count($shown);
                                    foreach ($shown as $member):
                                    ?>
                                        <div class="av <?= $member['responsibility'] === 'owner' ? 'av-owner' : '' ?>"
                                             title="<?= htmlspecialchars($member['name']) ?> · <?= ucfirst($member['responsibility']) ?>">
                                            <?= strtoupper(substr($member['name'], 0, 1)) ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if ($overflow > 0): ?>
                                        <div class="av av-more" title="<?= $overflow ?> more">+<?= $overflow ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="no-assign">Unassigned</span>
                            <?php endif; ?>
                        </td>

                        <!-- Due Date -->
                        <td>
                            <span class="due-cell <?= $dueCls ?>">
                                <?= $dueStr ?>
                                <?php if ($dueCls === 'due-overdue'): ?> ⚠️
                                <?php elseif ($dueCls === 'due-today'): ?> 📅
                                <?php endif; ?>
                            </span>
                        </td>

                        <!-- Actions -->
                        <td>
                            <div class="act-cell">

                                <a href="<?= base_url('tasks/' . $task['id']) ?>"
                                   class="btn-act btn-view">👁 View</a>

                                <a href="<?= base_url('tasks/' . $task['id'] . '/edit') ?>"
                                   class="btn-act btn-edit">✏️ Edit</a>

                                <?php if (in_array($user['role'], ['admin', 'head'])): ?>
                                <a href="<?= base_url('tasks/' . $task['id'] . '/assign') ?>"
                                   class="btn-act btn-assign">👥</a>
                                <?php endif; ?>

                                <?php if (!empty($task['can_archive'])): ?>
                                <button class="btn-act btn-archive"
                                        onclick="archiveTask(<?= $task['id'] ?>, this)"
                                        title="Archive Task">🗄️</button>
                                <?php endif; ?>

                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>

            <!-- No search results row -->
            <div class="no-results" id="noResults">
                🔍 No tasks match your search or filter.
            </div>

            <!-- Pagination -->
            <div class="pagination-bar">
                <span class="pag-info" id="pagInfo"></span>
                <div class="pag-btns" id="pagBtns"></div>
            </div>

        <?php endif; ?>
    </div><!-- /table-section -->

</div><!-- /container -->

<!-- ── Toast ── -->
<div class="toast" id="toast"></div>

<script>
/* ─────────────────────────────────────────────────────────────────────
   1. Live Search + Priority Filter
───────────────────────────────────────────────────────────────────── */
const searchEl   = document.getElementById('searchInput');
const priorityEl = document.getElementById('priorityFilter');
const tbody      = document.getElementById('taskBody');
const noResults  = document.getElementById('noResults');
const countEl    = document.getElementById('resultsCount');
const badgeEl    = document.getElementById('tableBadge');

function applyFilters() {
    if (!tbody) return;

    const term     = (searchEl.value || '').toLowerCase().trim();
    const priority = priorityEl.value;
    const rows     = Array.from(tbody.querySelectorAll('tr'));
    let   visible  = 0;

    rows.forEach(row => {
        const title    = row.dataset.title    || '';
        const desc     = row.dataset.desc     || '';
        const rowPri   = row.dataset.priority || '';

        const okSearch   = !term    || title.includes(term) || desc.includes(term);
        const okPriority = priority === 'all' || rowPri === priority;

        if (okSearch && okPriority) {
            row.classList.remove('row-hidden');
            visible++;
        } else {
            row.classList.add('row-hidden');
        }
    });

    const label = visible + ' task' + (visible !== 1 ? 's' : '');
    if (countEl) countEl.textContent = label;
    if (badgeEl) badgeEl.textContent = visible;
    if (noResults) noResults.style.display = (visible === 0 && rows.length > 0) ? 'block' : 'none';

    paginate(visible);
}

if (searchEl)   searchEl.addEventListener('input', applyFilters);
if (priorityEl) priorityEl.addEventListener('change', applyFilters);

/* ─────────────────────────────────────────────────────────────────────
   2. Client-side Pagination (10 per page)
───────────────────────────────────────────────────────────────────── */
const PER_PAGE = 10;
let   curPage  = 1;

function visibleRows() {
    if (!tbody) return [];
    return Array.from(tbody.querySelectorAll('tr:not(.row-hidden)'));
}

function showPage(page) {
    curPage = page;
    const rows  = visibleRows();
    const start = (page - 1) * PER_PAGE;
    const end   = start + PER_PAGE;

    // Reset display on all non-hidden rows
    Array.from(tbody.querySelectorAll('tr')).forEach(r => {
        if (!r.classList.contains('row-hidden')) r.style.display = 'none';
    });

    rows.slice(start, end).forEach(r => r.style.display = '');

    renderPagination(rows.length, page);
}

function paginate(total) { showPage(1); }

function renderPagination(total, page) {
    const infoEl   = document.getElementById('pagInfo');
    const btnsEl   = document.getElementById('pagBtns');
    if (!infoEl || !btnsEl) return;

    const pages = Math.ceil(total / PER_PAGE);
    const s     = total === 0 ? 0 : (page - 1) * PER_PAGE + 1;
    const e     = Math.min(page * PER_PAGE, total);

    infoEl.textContent = total === 0
        ? 'No tasks'
        : `Showing ${s}–${e} of ${total} task${total !== 1 ? 's' : ''}`;

    btnsEl.innerHTML = '';
    if (pages <= 1) return;

    const mk = (label, pg, disabled, active) => {
        const b = document.createElement('button');
        b.className   = 'pag-btn' + (active ? ' active' : '');
        b.textContent = label;
        b.disabled    = disabled;
        b.onclick     = () => showPage(pg);
        btnsEl.appendChild(b);
    };

    mk('←', page - 1, page === 1, false);

    for (let i = 1; i <= pages; i++) {
        if (i === 1 || i === pages || (i >= page - 1 && i <= page + 1)) {
            mk(i, i, false, i === page);
        } else if (i === page - 2 || i === page + 2) {
            const d = document.createElement('button');
            d.className   = 'pag-btn';
            d.textContent = '…';
            d.disabled    = true;
            btnsEl.appendChild(d);
        }
    }

    mk('→', page + 1, page === pages, false);
}

// Init on load
document.addEventListener('DOMContentLoaded', () => {
    if (tbody) paginate(visibleRows().length);
});

/* ─────────────────────────────────────────────────────────────────────
   3. Toast
───────────────────────────────────────────────────────────────────── */
function showToast(msg, type = 'ok') {
    const t   = document.getElementById('toast');
    t.textContent = (type === 'ok' ? '✅ ' : '❌ ') + msg;
    t.className   = 'toast show ' + type;
    setTimeout(() => t.classList.remove('show'), 3000);
}

/* ─────────────────────────────────────────────────────────────────────
   4. Archive Task (AJAX)
───────────────────────────────────────────────────────────────────── */
function archiveTask(taskId, btn) {
    if (!confirm('Archive this task? It can be restored from the archived tasks page.')) return;

    const orig  = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = '⏳';

    fetch(`<?= base_url('tasks/') ?>${taskId}/archive`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Task archived successfully!');
            const row = btn.closest('tr');
            if (row) {
                row.style.transition = 'opacity 0.3s, transform 0.3s';
                row.style.opacity    = '0';
                row.style.transform  = 'translateX(20px)';
                setTimeout(() => {
                    row.remove();
                    applyFilters(); // refresh counts + pagination
                }, 300);
            }
        } else {
            showToast(data.message || 'Failed to archive task', 'err');
            btn.disabled  = false;
            btn.innerHTML = orig;
        }
    })
    .catch(() => {
        showToast('An error occurred. Please try again.', 'err');
        btn.disabled  = false;
        btn.innerHTML = orig;
    });
}
</script>

</body>
</html>
