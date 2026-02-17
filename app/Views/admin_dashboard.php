<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Command Center | Task Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0a0f1a;
            --slate: #1e293b;
            --stone: #334155;
            --smoke: #64748b;
            --frost: #f1f5f9;
            --ice: #f8fafc;
            --amber: #f59e0b;
            --ruby: #dc2626;
            --emerald: #10b981;
            --sapphire: #3b82f6;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: var(--ice);
            color: var(--ink);
            line-height: 1.6;
        }

        /* Top Navigation */
        .top-nav {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 0;
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

        .nav-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

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
            display: inline-block;
        }

        .btn-logout:hover {
            background: var(--ink);
            transform: translateY(-1px);
        }

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* Hero Section */
        .hero {
            margin-bottom: 3rem;
        }

        .hero h1 {
            font-family: 'Crimson Pro', serif;
            font-size: 3rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 0.5rem;
            letter-spacing: -0.03em;
        }

        .hero-subtitle {
            color: var(--smoke);
            font-size: 1.1rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent);
            transition: width 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card:hover::before {
            width: 100%;
            opacity: 0.05;
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--smoke);
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-family: 'Crimson Pro', serif;
            font-size: 3rem;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }

        .stat-card.total {
            --accent: var(--sapphire);
        }

        .stat-card.pending {
            --accent: var(--amber);
        }

        .stat-card.in-progress {
            --accent: var(--ruby);
        }

        .stat-card.completed {
            --accent: var(--emerald);
        }

        .stat-card.archived {
            --accent: var(--smoke);
        }

        /* Archived stat card active state */
        .stat-card.archived.active-filter {
            border-color: var(--smoke);
            box-shadow: 0 0 0 3px rgba(100, 116, 139, 0.15);
        }

        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filters {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--stone);
        }

        .filter-btn:hover {
            border-color: var(--sapphire);
            color: var(--sapphire);
        }

        .filter-btn.active {
            background: var(--sapphire);
            color: white;
            border-color: var(--sapphire);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--sapphire) 0%, #2563eb 100%);
            color: white;
            border: none;
            padding: 0.8rem 1.8rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        /* Tasks Section */
        .tasks-section {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--frost);
        }

        .section-title {
            font-family: 'Crimson Pro', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--ink);
        }

        .view-toggle {
            display: flex;
            gap: 0.5rem;
        }

        .toggle-btn {
            background: var(--frost);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
            color: var(--stone);
        }

        .toggle-btn.active {
            background: var(--slate);
            color: white;
        }

        .task-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .task-table thead th {
            text-align: left;
            padding: 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--smoke);
            border-bottom: 2px solid var(--frost);
        }

        .task-table tbody tr {
            transition: all 0.2s;
            border-bottom: 1px solid var(--frost);
        }

        .task-table tbody tr:hover {
            background: var(--ice);
        }

        .task-table tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
        }

        .task-title {
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.25rem;
        }

        .task-desc {
            font-size: 0.85rem;
            color: var(--smoke);
            line-height: 1.4;
        }

        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.9rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-in_progress {
            background: #fecaca;
            color: #991b1b;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        /* legacy hyphen class kept for safety */
        .status-in-progress {
            background: #fecaca;
            color: #991b1b;
        }

        .priority-badge {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .priority-low {
            background: var(--emerald);
        }

        .priority-medium {
            background: var(--amber);
        }

        .priority-high {
            background: var(--ruby);
        }

        .assignee-list {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .assignee-chip {
            background: var(--frost);
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--stone);
        }

        .assignee-chip.owner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .task-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            background: var(--frost);
            border: none;
            padding: 0.5rem 0.9rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--stone);
            text-decoration: none;
            display: inline-block;
        }

        .btn-action:hover {
            background: var(--stone);
            color: white;
            transform: translateY(-1px);
        }

        .btn-edit:hover {
            background: var(--sapphire);
            color: white;
        }

        .btn-assign:hover {
            background: var(--amber);
            color: white;
        }

        .btn-archive:hover {
            background: var(--smoke);
            color: white;
        }

        .btn-restore:hover {
            background: var(--emerald);
            color: white;
        }

        .date-cell {
            font-size: 0.85rem;
            color: var(--smoke);
        }

        .date-overdue {
            color: var(--ruby);
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--smoke);
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* ── Archived Toggle ── */
        .archived-toggle {
            background: var(--frost);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
            user-select: none;
        }

        .archived-toggle:hover {
            background: white;
            box-shadow: var(--shadow-md);
        }

        .archived-toggle span {
            font-weight: 600;
            color: var(--stone);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .archived-toggle .arrow {
            display: inline-block;
            transition: transform 0.3s ease;
            font-style: normal;
        }

        .archived-toggle.open .arrow {
            transform: rotate(180deg);
        }

        /* ── Archived Section ── */
        .archived-section {
            display: none;
            overflow: hidden;
        }

        .archived-section.visible {
            display: block;
            animation: slideDown 0.35s ease forwards;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .task-table {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container>* {
            animation: fadeInUp 0.6s ease forwards;
        }

        .stats-grid .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stats-grid .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stats-grid .stat-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stats-grid .stat-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .stats-grid .stat-card:nth-child(5) {
            animation-delay: 0.5s;
        }

        /* Highlight the archived section when auto-opened */
        .archived-section.highlight .tasks-section {
            border-color: var(--smoke);
            box-shadow: 0 0 0 3px rgba(100, 116, 139, 0.12);
        }
    </style>
</head>

<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-container">
            <a href="<?= base_url('admin/dashboard') ?>" class="brand">TaskFlow</a>
            <div class="nav-actions">
                <div class="user-badge">
                    <?= $user['name'] ?>
                    <span class="role-tag"><?= ucfirst($user['role']) ?></span>
                </div>
                <a href="<?= base_url('logout') ?>" class="btn-logout">Sign Out</a>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">

        <!-- Hero Section -->
        <div class="hero">
            <h1>Command Center</h1>
            <p class="hero-subtitle">Monitor, assign, and orchestrate tasks across your organization</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Each stat card now uses JS to handle navigation, keeping archived as a scroll+open toggle -->
            <div class="stat-card total" onclick="goToFilter('all')">
                <div class="stat-label">Total Tasks</div>
                <div class="stat-value"><?= $stats['total'] ?></div>
            </div>

            <div class="stat-card pending" onclick="goToFilter('pending')">
                <div class="stat-label">Pending</div>
                <div class="stat-value"><?= $stats['pending'] ?></div>
            </div>

            <div class="stat-card in-progress" onclick="goToFilter('in_progress')">
                <div class="stat-label">In Progress</div>
                <div class="stat-value"><?= $stats['in_progress'] ?></div>
            </div>

            <div class="stat-card completed" onclick="goToFilter('completed')">
                <div class="stat-label">Completed</div>
                <div class="stat-value"><?= $stats['completed'] ?></div>
            </div>

            <!-- Archived stat → scrolls to + opens archived section -->
            <div class="stat-card archived" id="archivedStatCard" onclick="openArchived()">
                <div class="stat-label">Archived</div>
                <div class="stat-value"><?= $stats['archived'] ?></div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="filters">
                <button class="filter-btn <?= ($current_filter === 'all')          ? 'active' : '' ?>" data-filter="all">All Tasks</button>
                <button class="filter-btn <?= ($current_filter === 'my_tasks')     ? 'active' : '' ?>" data-filter="my_tasks">My Tasks</button>
                <button class="filter-btn <?= ($current_filter === 'overdue')      ? 'active' : '' ?>" data-filter="overdue">Overdue</button>
                <button class="filter-btn <?= ($current_filter === 'due_today')    ? 'active' : '' ?>" data-filter="due_today">Due Today</button>
            </div>
            <a href="<?= base_url('tasks/create') ?>" class="btn-primary">+ Create Task</a>
        </div>

        <!-- Active Tasks Section -->
        <div class="tasks-section">
            <div class="section-header">
                <h2 class="section-title">Active Tasks</h2>
                <div class="view-toggle">
                    <button class="toggle-btn active" data-view="table">Table View</button>
                </div>
            </div>

            <table class="task-table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tasks)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-state-icon">📋</div>
                                    <p>No tasks found. Create your first task to get started.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td>
                                    <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                                    <div class="task-desc"><?= htmlspecialchars(substr($task['description'] ?? '', 0, 80)) ?><?= strlen($task['description'] ?? '') > 80 ? '...' : '' ?></div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $task['status'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="priority-badge priority-<?= $task['priority'] ?>"></span>
                                    <?= ucfirst($task['priority']) ?>
                                </td>
                                <td>
                                    <div class="assignee-list">
                                        <?php if (!empty($task['users'])): ?>
                                            <?php foreach ($task['users'] as $member): ?>
                                                <span class="assignee-chip <?= $member['responsibility'] === 'owner' ? 'owner' : '' ?>">
                                                    <?= htmlspecialchars($member['name']) ?>
                                                    <?= $member['responsibility'] === 'owner' ? ' ★' : '' ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="assignee-chip">No assignees</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="date-cell <?= (!empty($task['due_date']) && strtotime($task['due_date']) < time() && $task['status'] !== 'completed') ? 'date-overdue' : '' ?>">
                                        <?= !empty($task['due_date']) ? date('M d, Y', strtotime($task['due_date'])) : '—' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="task-actions">
                                        <a href="<?= base_url('tasks/' . $task['id'] . '/edit') ?>" class="btn-action btn-edit">Edit</a>
                                        <?php if (in_array($user['role'], ['admin', 'head'])): ?>
                                            <a href="<?= base_url('admin/tasks/' . $task['id'] . '/assign') ?>" class="btn-action btn-assign">Assign</a>
                                        <?php endif; ?>
                                        <?php if (!empty($task['can_archive'])): ?>
                                            <button onclick="archiveTask(<?= $task['id'] ?>)" class="btn-action btn-archive">Archive</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ── Archived Toggle ── -->
        <div class="archived-toggle" id="archivedToggle" onclick="toggleArchived()">
            <span>
                <i class="arrow">▼</i>
                View Archived Tasks (<?= $stats['archived'] ?>)
            </span>
        </div>

        <!-- ── Archived Section ── -->
        <div class="archived-section" id="archivedSection">
            <div class="tasks-section">
                <div class="section-header">
                    <h2 class="section-title">Archived Tasks</h2>
                </div>

                <table class="task-table">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Archived Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($archived_tasks)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📦</div>
                                        <p>No archived tasks.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($archived_tasks as $task): ?>
                                <tr>
                                    <td>
                                        <div class="task-title"><?= htmlspecialchars($task['title']) ?></div>
                                        <div class="task-desc"><?= htmlspecialchars(substr($task['description'] ?? '', 0, 80)) ?><?= strlen($task['description'] ?? '') > 80 ? '...' : '' ?></div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $task['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="priority-badge priority-<?= $task['priority'] ?>"></span>
                                        <?= ucfirst($task['priority']) ?>
                                    </td>
                                    <td>
                                        <span class="date-cell">
                                            <?= !empty($task['archived_at']) ? date('M d, Y', strtotime($task['archived_at'])) : '—' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="task-actions">
                                            <button onclick="restoreTask(<?= $task['id'] ?>)" class="btn-action btn-restore">Restore</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /container -->

    <script>
        /* ─────────────────────────────────────────
           1. Archived open/close
        ───────────────────────────────────────── */
        const archivedSection = document.getElementById('archivedSection');
        const archivedToggle = document.getElementById('archivedToggle');
        const archivedStatCard = document.getElementById('archivedStatCard');

        let archivedOpen = false;

        function toggleArchived() {
            archivedOpen = !archivedOpen;

            if (archivedOpen) {
                archivedSection.classList.add('visible');
                archivedToggle.classList.add('open');
                archivedToggle.querySelector('span').childNodes[2].textContent = ' Hide Archived Tasks (<?= $stats['archived'] ?>)';
            } else {
                archivedSection.classList.remove('visible', 'highlight');
                archivedToggle.classList.remove('open');
                archivedToggle.querySelector('span').childNodes[2].textContent = ' View Archived Tasks (<?= $stats['archived'] ?>)';
            }
        }

        /* Opens the archived section, highlights it, scrolls to it */
        function openArchived() {
            if (!archivedOpen) {
                toggleArchived();
            }
            // Brief highlight pulse
            archivedSection.classList.add('highlight');

            // Scroll to toggle smoothly
            setTimeout(() => {
                archivedToggle.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 80);
        }

        /* ─────────────────────────────────────────
           2. Filter buttons (status filters)
        ───────────────────────────────────────── */
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                window.location.href = '<?= base_url('admin/dashboard') ?>?filter=' + this.dataset.filter;
            });
        });

        /* ─────────────────────────────────────────
           3. Stat card navigation (non-archived)
        ───────────────────────────────────────── */
        function goToFilter(filter) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            const matchBtn = document.querySelector(`.filter-btn[data-filter="${filter}"]`);
            if (matchBtn) matchBtn.classList.add('active');
            window.location.href = '<?= base_url('admin/tasks') ?>?status=' + filter;
        }

        /* ─────────────────────────────────────────
           4. View toggle
        ───────────────────────────────────────── */
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                if (this.dataset.view === 'board') {
                    window.location.href = '<?= base_url('/tasks/board') ?>';
                }
            });
        });

        /* ─────────────────────────────────────────
           5. Auto-open archived if URL has ?filter=archived
              (handles browser back/forward or direct link)
        ───────────────────────────────────────── */
        (function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('filter') === 'archived') {
                // Give the DOM a moment to settle, then open
                requestAnimationFrame(() => openArchived());
            }
        })();

        /* ─────────────────────────────────────────
           6. Archive task (AJAX)
        ───────────────────────────────────────── */
        function archiveTask(taskId) {
            if (confirm('Archive this task? It can be restored later.')) {
                fetch('<?= base_url('tasks') ?>/' + taskId + '/archive', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Cannot archive task');
                        }
                    })
                    .catch(() => alert('An error occurred. Please try again.'));
            }
        }

        /* ─────────────────────────────────────────
           7. Restore task (AJAX)
        ───────────────────────────────────────── */
        function restoreTask(taskId) {
            if (confirm('Restore this task?')) {
                fetch('<?= base_url('tasks') ?>/' + taskId + '/restore', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Cannot restore task');
                        }
                    })
                    .catch(() => alert('An error occurred. Please try again.'));
            }
        }
    </script>
</body>

</html>