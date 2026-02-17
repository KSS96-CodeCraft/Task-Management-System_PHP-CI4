<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Tasks | Task Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Spectral:wght@400;600;700&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --midnight: #0d1b2a;
            --ocean: #1b263b;
            --steel: #415a77;
            --silver: #778da9;
            --cloud: #e0e1dd;
            --cream: #f8f9fa;
            --white: #ffffff;
            --coral: #e63946;
            --mint: #06ffa5;
            --gold: #ffb703;
            --violet: #8338ec;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Rubik', sans-serif;
            background: var(--cream);
            color: var(--midnight);
            min-height: 100vh;
        }

        /* ── Header ── */
        .page-header {
            background: linear-gradient(135deg, var(--midnight) 0%, var(--ocean) 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .back-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: white;
            transform: translateX(-4px);
        }

        .header-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-family: 'Spectral', serif;
            font-size: 2.5rem;
            margin-bottom: 0.4rem;
        }

        .page-subtitle {
            opacity: 0.85;
            font-size: 1rem;
        }

        .archive-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,183,3,0.18);
            border: 1px solid rgba(255,183,3,0.5);
            color: var(--gold);
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.4px;
            margin-top: 0.3rem;
        }

        /* ── Container ── */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* ── Stats Row ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 1.4rem 1.6rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: fadeIn 0.5s ease forwards;
        }

        .stat-icon {
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .stat-info {}

        .stat-value {
            font-family: 'Spectral', serif;
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--midnight);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.78rem;
            color: var(--silver);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.25rem;
        }

        /* ── Controls Bar ── */
        .controls-bar {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--silver);
            font-size: 1rem;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 0.9rem 1.2rem 0.9rem 2.8rem;
            border: 2px solid var(--cloud);
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            background: white;
            transition: all 0.3s;
            color: var(--midnight);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--steel);
            box-shadow: 0 0 0 4px rgba(65,90,119,0.1);
        }

        .filter-select {
            padding: 0.9rem 1.2rem;
            border: 2px solid var(--cloud);
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.9rem;
            background: white;
            color: var(--midnight);
            cursor: pointer;
            transition: all 0.3s;
            min-width: 160px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--steel);
        }

        .view-toggle {
            display: flex;
            background: white;
            border-radius: 12px;
            border: 2px solid var(--cloud);
            overflow: hidden;
        }

        .view-btn {
            padding: 0.9rem 1rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.2s;
            color: var(--silver);
        }

        .view-btn.active {
            background: var(--midnight);
            color: white;
        }

        /* ── Alert ── */
        .alert {
            padding: 1.1rem 1.4rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-size: 0.92rem;
            line-height: 1.6;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .alert-icon { font-size: 1.3rem; flex-shrink: 0; margin-top: 0.1rem; }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid var(--gold);
        }

        /* ── Section ── */
        .section {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            animation: fadeIn 0.5s ease forwards;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--cloud);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-title {
            font-family: 'Spectral', serif;
            font-size: 1.8rem;
            color: var(--midnight);
        }

        .task-count {
            background: var(--midnight);
            color: white;
            padding: 0.45rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.82rem;
        }

        /* ── GRID VIEW ── */
        .tasks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .task-card {
            background: var(--cream);
            border: 2px solid var(--cloud);
            border-radius: 14px;
            padding: 1.6rem;
            transition: all 0.3s;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .task-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0,0,0,0.09);
            border-color: var(--steel);
        }

        .task-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.8rem;
        }

        .task-title {
            font-family: 'Spectral', serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--midnight);
            line-height: 1.35;
            flex: 1;
        }

        .task-description {
            font-size: 0.88rem;
            color: var(--silver);
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.75rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .badge-priority-high   { background: rgba(230,57,70,0.12); color: var(--coral); }
        .badge-priority-medium { background: rgba(255,183,3,0.15); color: #b07d00; }
        .badge-priority-low    { background: rgba(6,255,165,0.15); color: #007a4d; }

        .badge-status-pending     { background: rgba(119,141,169,0.15); color: var(--steel); }
        .badge-status-in_progress { background: rgba(131,56,236,0.12); color: var(--violet); }
        .badge-status-completed   { background: rgba(6,255,165,0.15); color: #007a4d; }

        /* Meta row */
        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            align-items: center;
        }

        /* Team avatars */
        .team-avatars {
            display: flex;
            align-items: center;
        }

        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--steel) 0%, var(--silver) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.72rem;
            border: 2px solid white;
            margin-left: -8px;
            flex-shrink: 0;
        }

        .avatar:first-child { margin-left: 0; }

        .avatar.owner-av {
            background: linear-gradient(135deg, var(--violet) 0%, #a855f7 100%);
        }

        .avatar-more {
            background: var(--cloud);
            color: var(--steel);
            font-size: 0.65rem;
        }

        /* Dates */
        .task-dates {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            font-size: 0.8rem;
            color: var(--silver);
        }

        .task-dates span { display: flex; align-items: center; gap: 0.4rem; }

        .archived-date {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            color: #b07d00;
            background: rgba(255,183,3,0.1);
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Card actions */
        .card-actions {
            display: flex;
            gap: 0.7rem;
            margin-top: auto;
            padding-top: 0.5rem;
            border-top: 1px solid var(--cloud);
        }

        .btn-restore {
            flex: 1;
            padding: 0.7rem;
            background: linear-gradient(135deg, var(--mint) 0%, #00d48a 100%);
            color: var(--midnight);
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .btn-restore:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6,255,165,0.3);
        }

        .btn-delete {
            padding: 0.7rem 1rem;
            background: transparent;
            border: 2px solid var(--coral);
            color: var(--coral);
            border-radius: 8px;
            font-family: inherit;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .btn-delete:hover {
            background: var(--coral);
            color: white;
        }

        /* ── LIST VIEW ── */
        .tasks-list { display: none; flex-direction: column; gap: 0.8rem; }

        .list-item {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            background: var(--cream);
            border: 2px solid var(--cloud);
            border-radius: 12px;
            padding: 1.2rem 1.4rem;
            transition: all 0.2s;
        }

        .list-item:hover {
            background: white;
            border-color: var(--steel);
            transform: translateX(4px);
        }

        .list-main { flex: 1; min-width: 0; }

        .list-title {
            font-weight: 700;
            font-size: 1rem;
            color: var(--midnight);
            margin-bottom: 0.3rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .list-sub {
            font-size: 0.8rem;
            color: var(--silver);
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .list-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .list-actions {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .btn-sm {
            padding: 0.5rem 0.9rem;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-sm-restore {
            background: linear-gradient(135deg, var(--mint) 0%, #00d48a 100%);
            color: var(--midnight);
        }

        .btn-sm-restore:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(6,255,165,0.3);
        }

        .btn-sm-delete {
            background: transparent;
            border: 2px solid var(--coral) !important;
            color: var(--coral);
        }

        .btn-sm-delete:hover {
            background: var(--coral);
            color: white;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
            color: var(--silver);
        }

        .empty-icon { font-size: 5rem; margin-bottom: 1.2rem; opacity: 0.25; }
        .empty-title {
            font-family: 'Spectral', serif;
            font-size: 1.6rem;
            color: var(--steel);
            margin-bottom: 0.6rem;
        }
        .empty-text { font-size: 1rem; line-height: 1.6; }

        .empty-link {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.9rem 2rem;
            background: linear-gradient(135deg, var(--midnight) 0%, var(--ocean) 100%);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .empty-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13,27,42,0.3);
        }

        /* ── No results (search) ── */
        .no-results {
            display: none;
            text-align: center;
            padding: 3rem 2rem;
            color: var(--silver);
        }
        .no-results-icon { font-size: 3rem; margin-bottom: 0.8rem; opacity: 0.3; }
        .no-results-text { font-size: 1rem; }

        /* ── Actions bar ── */
        .actions-bar {
            display: flex;
            gap: 1rem;
            padding: 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1.1rem;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--midnight) 0%, var(--ocean) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(13,27,42,0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13,27,42,0.4);
        }

        .btn-secondary {
            background: var(--cloud);
            color: var(--midnight);
        }

        .btn-secondary:hover {
            background: var(--silver);
            color: white;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .tasks-grid { grid-template-columns: 1fr; }
            .page-title { font-size: 2rem; }
            .section { padding: 1.5rem; }
            .actions-bar { flex-direction: column; }
            .controls-bar { flex-direction: column; align-items: stretch; }
            .header-top { flex-direction: column; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .list-sub { display: none; }
            .list-badges { display: none; }
        }

        /* ── Animation ── */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.10s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.20s; }
        .section { animation-delay: 0.1s; }

        /* ── Toast ── */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--midnight);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            z-index: 9999;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast.success { border-left: 4px solid var(--mint); }
        .toast.error   { border-left: 4px solid var(--coral); }
    </style>
</head>
<body>

    <!-- ── Page Header ── -->
    <header class="page-header">
        <div class="header-content">
            <a href="<?= base_url('admin/dashboard') ?>" class="back-link">
                <span>←</span>
                <span>Back to Dashboard</span>
            </a>
            <div class="header-top">
                <div>
                    <h1 class="page-title">Archived Tasks</h1>
                    <p class="page-subtitle">Manage and restore previously archived tasks</p>
                </div>
                <div class="archive-badge">
                    🗄️ Archive Vault
                </div>
            </div>
        </div>
    </header>

    <!-- ── Main Container ── -->
    <div class="container">

        <?php
        // ── Pre-compute stats from archived_tasks array ──
        $totalArchived   = count($archived_tasks);
        $completedCount  = 0;
        $pendingCount    = 0;
        $inProgressCount = 0;
        foreach ($archived_tasks as $t) {
            if ($t['status'] === 'completed')   $completedCount++;
            elseif ($t['status'] === 'in_progress') $inProgressCount++;
            else $pendingCount++;
        }
        ?>

        <!-- ── Stats Row ── -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon">🗄️</div>
                <div class="stat-info">
                    <div class="stat-value"><?= $totalArchived ?></div>
                    <div class="stat-label">Total Archived</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <div class="stat-value"><?= $completedCount ?></div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-info">
                    <div class="stat-value"><?= $pendingCount ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔄</div>
                <div class="stat-info">
                    <div class="stat-value"><?= $inProgressCount ?></div>
                    <div class="stat-label">In Progress</div>
                </div>
            </div>
        </div>

        <!-- ── Alert ── -->
        <div class="alert alert-warning">
            <span class="alert-icon">⚠️</span>
            <div>
                <strong>Archive Rules:</strong><br>
                • Archived tasks are hidden from active dashboards<br>
                • Restoring a task will make it visible again to all assigned users<br>
                • Permanent deletion cannot be undone — only tasks with a single user can be deleted
            </div>
        </div>

        <!-- ── Controls Bar ── -->
        <div class="controls-bar">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" class="search-input" id="searchInput" placeholder="Search archived tasks by title or description...">
            </div>
            <select class="filter-select" id="statusFilter">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
            <select class="filter-select" id="priorityFilter">
                <option value="all">All Priorities</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
            <div class="view-toggle">
                <button class="view-btn active" id="gridViewBtn" onclick="setView('grid')" title="Grid View">⊞</button>
                <button class="view-btn" id="listViewBtn" onclick="setView('list')" title="List View">≡</button>
            </div>
        </div>

        <!-- ── Main Section ── -->
        <?php if (!empty($archived_tasks)): ?>

        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Archived Tasks</h2>
                <div class="task-count" id="visibleCount"><?= $totalArchived ?> Tasks</div>
            </div>

            <!-- ─ GRID VIEW ─ -->
            <div class="tasks-grid" id="gridView">
                <?php foreach ($archived_tasks as $task): ?>
                    <?php
                    // Fetch assigned users for this task
                    $taskUsers = $this->taskModel->getJoinedDataPagination(
                        'task_user tu',
                        ['users u' => 'u.id = tu.user_id'],
                        'u.id, u.name, tu.responsibility',
                        ['tu.task_id' => $task['id']],
                        'array'
                    );

                    // Check if current user can delete (single user only)
                    $userCount = $this->taskModel->get_specific_columns(
                        'task_user',
                        'COUNT(*) as total',
                        ['task_id' => $task['id']]
                    );
                    $canDelete = ($userCount[0]['total'] ?? 0) <= 1;

                    // Format archived date
                    $archivedAgo = '';
                    if (!empty($task['archived_at'])) {
                        $diff = time() - strtotime($task['archived_at']);
                        if ($diff < 3600)       $archivedAgo = floor($diff/60) . 'm ago';
                        elseif ($diff < 86400)  $archivedAgo = floor($diff/3600) . 'h ago';
                        elseif ($diff < 604800) $archivedAgo = floor($diff/86400) . 'd ago';
                        else                     $archivedAgo = date('M j, Y', strtotime($task['archived_at']));
                    }
                    ?>
                    <div class="task-card"
                        data-title="<?= strtolower(htmlspecialchars($task['title'])) ?>"
                        data-description="<?= strtolower(htmlspecialchars($task['description'] ?? '')) ?>"
                        data-status="<?= $task['status'] ?>"
                        data-priority="<?= $task['priority'] ?>">

                        <!-- Top Row -->
                        <div class="task-card-top">
                            <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                            <span class="badge badge-priority-<?= $task['priority'] ?>">
                                <?= $task['priority'] === 'high' ? '🔴' : ($task['priority'] === 'medium' ? '🟡' : '🟢') ?>
                                <?= ucfirst($task['priority']) ?>
                            </span>
                        </div>

                        <!-- Description -->
                        <?php if (!empty($task['description'])): ?>
                        <p class="task-description"><?= htmlspecialchars($task['description']) ?></p>
                        <?php endif; ?>

                        <!-- Meta -->
                        <div class="task-meta">
                            <span class="badge badge-status-<?= $task['status'] ?>">
                                <?= $task['status'] === 'completed' ? '✅' : ($task['status'] === 'in_progress' ? '🔄' : '⏳') ?>
                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                            </span>

                            <span class="archived-date">
                                🗄️ <?= $archivedAgo ?>
                            </span>
                        </div>

                        <!-- Team Avatars -->
                        <?php if (!empty($taskUsers)): ?>
                        <div style="display:flex; align-items:center; gap:0.7rem;">
                            <div class="team-avatars">
                                <?php
                                $displayUsers  = array_slice($taskUsers, 0, 4);
                                $remainingCount = count($taskUsers) - count($displayUsers);
                                foreach ($displayUsers as $member):
                                ?>
                                    <div class="avatar <?= $member['responsibility'] === 'owner' ? 'owner-av' : '' ?>"
                                        title="<?= htmlspecialchars($member['name']) ?> (<?= ucfirst($member['responsibility']) ?>)">
                                        <?= strtoupper(substr($member['name'], 0, 1)) ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php if ($remainingCount > 0): ?>
                                    <div class="avatar avatar-more">+<?= $remainingCount ?></div>
                                <?php endif; ?>
                            </div>
                            <span style="font-size:0.78rem; color:var(--silver);">
                                <?= count($taskUsers) ?> member<?= count($taskUsers) !== 1 ? 's' : '' ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <!-- Dates -->
                        <div class="task-dates">
                            <?php if (!empty($task['due_date'])): ?>
                            <span>📅 Due: <?= date('M j, Y', strtotime($task['due_date'])) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($task['archived_at'])): ?>
                            <span>🗄️ Archived: <?= date('M j, Y', strtotime($task['archived_at'])) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Actions -->
                        <div class="card-actions">
                            <button class="btn-restore" onclick="restoreTask(<?= $task['id'] ?>, this)">
                                ♻️ Restore Task
                            </button>
                            <?php if ($canDelete || $user['role'] === 'admin'): ?>
                            <button class="btn-delete" onclick="deleteTask(<?= $task['id'] ?>, this)">
                                🗑️
                            </button>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ─ LIST VIEW ─ -->
            <div class="tasks-list" id="listView">
                <?php foreach ($archived_tasks as $task): ?>
                    <?php
                    // Reuse same computed values — use task-level user count
                    $taskUsersL = $this->taskModel->getJoinedDataPagination(
                        'task_user tu',
                        ['users u' => 'u.id = tu.user_id'],
                        'u.id, u.name, tu.responsibility',
                        ['tu.task_id' => $task['id']],
                        'array'
                    );
                    $userCountL = $this->taskModel->get_specific_columns(
                        'task_user',
                        'COUNT(*) as total',
                        ['task_id' => $task['id']]
                    );
                    $canDeleteL = ($userCountL[0]['total'] ?? 0) <= 1;

                    $archivedAgoL = '';
                    if (!empty($task['archived_at'])) {
                        $diff = time() - strtotime($task['archived_at']);
                        if ($diff < 3600)       $archivedAgoL = floor($diff/60) . 'm ago';
                        elseif ($diff < 86400)  $archivedAgoL = floor($diff/3600) . 'h ago';
                        elseif ($diff < 604800) $archivedAgoL = floor($diff/86400) . 'd ago';
                        else                     $archivedAgoL = date('M j, Y', strtotime($task['archived_at']));
                    }
                    ?>
                    <div class="list-item"
                        data-title="<?= strtolower(htmlspecialchars($task['title'])) ?>"
                        data-description="<?= strtolower(htmlspecialchars($task['description'] ?? '')) ?>"
                        data-status="<?= $task['status'] ?>"
                        data-priority="<?= $task['priority'] ?>">

                        <div class="list-main">
                            <div class="list-title"><?= htmlspecialchars($task['title']) ?></div>
                            <div class="list-sub">
                                <span>📅 <?= !empty($task['due_date']) ? date('M j, Y', strtotime($task['due_date'])) : 'No due date' ?></span>
                                <span>🗄️ <?= $archivedAgoL ?></span>
                                <span>👥 <?= count($taskUsersL) ?> member<?= count($taskUsersL) !== 1 ? 's' : '' ?></span>
                            </div>
                        </div>

                        <div class="list-badges">
                            <span class="badge badge-priority-<?= $task['priority'] ?>">
                                <?= ucfirst($task['priority']) ?>
                            </span>
                            <span class="badge badge-status-<?= $task['status'] ?>">
                                <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                            </span>
                        </div>

                        <div class="list-actions">
                            <button class="btn-sm btn-sm-restore" onclick="restoreTask(<?= $task['id'] ?>, this)">
                                ♻️ Restore
                            </button>
                            <?php if ($canDeleteL || $user['role'] === 'admin'): ?>
                            <button class="btn-sm btn-sm-delete" onclick="deleteTask(<?= $task['id'] ?>, this)">
                                🗑️
                            </button>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- No search results -->
            <div class="no-results" id="noResults">
                <div class="no-results-icon">🔍</div>
                <div class="no-results-text">No archived tasks match your search.</div>
            </div>

        </div><!-- /section -->

        <?php else: ?>
        <!-- Empty State -->
        <div class="section">
            <div class="empty-state">
                <div class="empty-icon">🗄️</div>
                <div class="empty-title">No Archived Tasks</div>
                <p class="empty-text">
                    Tasks you archive will appear here.<br>
                    You can restore them at any time.
                </p>
                <a href="<?= base_url('admin/dashboard') ?>" class="empty-link">← Back to Dashboard</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Actions Bar ── -->
        <div class="actions-bar">
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">← Back to Dashboard</a>
            <?php if (!empty($archived_tasks)): ?>
            <a href="<?= base_url('admin/tasks') ?>" class="btn btn-primary">View Active Tasks</a>
            <?php endif; ?>
        </div>

    </div><!-- /container -->

    <!-- ── Toast ── -->
    <div class="toast" id="toast"></div>

    <script>
        /* ── View Toggle ── */
        let currentView = 'grid';

        function setView(view) {
            currentView = view;
            const grid     = document.getElementById('gridView');
            const list     = document.getElementById('listView');
            const gridBtn  = document.getElementById('gridViewBtn');
            const listBtn  = document.getElementById('listViewBtn');

            if (view === 'grid') {
                grid.style.display = 'grid';
                list.style.display = 'none';
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
            } else {
                grid.style.display = 'none';
                list.style.display = 'flex';
                listBtn.classList.add('active');
                gridBtn.classList.remove('active');
            }
        }

        /* ── Search & Filter ── */
        const searchInput    = document.getElementById('searchInput');
        const statusFilter   = document.getElementById('statusFilter');
        const priorityFilter = document.getElementById('priorityFilter');
        const visibleCount   = document.getElementById('visibleCount');
        const noResults      = document.getElementById('noResults');

        function filterTasks() {
            const term     = searchInput.value.toLowerCase().trim();
            const status   = statusFilter.value;
            const priority = priorityFilter.value;

            const gridCards = document.querySelectorAll('#gridView .task-card');
            const listItems = document.querySelectorAll('#listView .list-item');

            let visible = 0;

            function matchItem(el) {
                const title       = el.dataset.title || '';
                const description = el.dataset.description || '';
                const elStatus    = el.dataset.status || '';
                const elPriority  = el.dataset.priority || '';

                const matchSearch   = !term || title.includes(term) || description.includes(term);
                const matchStatus   = status === 'all'   || elStatus === status;
                const matchPriority = priority === 'all' || elPriority === priority;

                return matchSearch && matchStatus && matchPriority;
            }

            gridCards.forEach(card => {
                const show = matchItem(card);
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            listItems.forEach(item => {
                item.style.display = matchItem(item) ? '' : 'none';
            });

            if (visibleCount) visibleCount.textContent = visible + ' Task' + (visible !== 1 ? 's' : '');
            if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', filterTasks);
        statusFilter.addEventListener('change', filterTasks);
        priorityFilter.addEventListener('change', filterTasks);

        /* ── Toast ── */
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = (type === 'success' ? '✅ ' : '❌ ') + message;
            toast.className = 'toast show ' + type;
            setTimeout(() => { toast.classList.remove('show'); }, 3500);
        }

        /* ── Restore Task ── */
        function restoreTask(taskId, btn) {
            if (!confirm('Restore this task? It will become active again for all assigned users.')) return;

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '⏳ Restoring...';

            fetch(`<?= base_url('tasks/') ?>${taskId}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Task restored successfully!', 'success');
                    // Remove the card/row from DOM
                    const card = btn.closest('.task-card') || btn.closest('.list-item');
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.transition = 'all 0.3s';
                        setTimeout(() => {
                            card.remove();
                            updateCount();
                        }, 300);
                    }
                } else {
                    showToast(data.message || 'Failed to restore task', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(() => {
                showToast('An error occurred. Please try again.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        /* ── Delete Task ── */
        function deleteTask(taskId, btn) {
            if (!confirm('Permanently delete this task? This action cannot be undone.')) return;

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '⏳';

            fetch(`<?= base_url('tasks/') ?>${taskId}/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Task permanently deleted.', 'success');
                    const card = btn.closest('.task-card') || btn.closest('.list-item');
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)';
                        card.style.transition = 'all 0.3s';
                        setTimeout(() => {
                            card.remove();
                            updateCount();
                        }, 300);
                    }
                } else {
                    showToast(data.message || 'Failed to delete task', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(() => {
                showToast('An error occurred. Please try again.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        /* ── Update visible count after DOM removal ── */
        function updateCount() {
            const visibleCards = document.querySelectorAll('#gridView .task-card:not([style*="display: none"])').length;
            if (visibleCount) visibleCount.textContent = visibleCards + ' Task' + (visibleCards !== 1 ? 's' : '');
            if (noResults) noResults.style.display = visibleCards === 0 ? 'block' : 'none';
        }
    </script>

</body>
</html>