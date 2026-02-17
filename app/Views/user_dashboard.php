<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Workspace | Task Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --midnight: #1a1d29;
            --charcoal: #2d3142;
            --steel: #4f5d75;
            --fog: #bfc0c0;
            --cloud: #f0f0f0;
            --paper: #ffffff;
            --coral: #ef8354;
            --teal: #06bcc1;
            --lime: #a8dadc;
            --gold: #ffd166;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Work Sans', sans-serif;
            background: linear-gradient(to bottom, #f7f9fb, #e8ecf1);
            color: var(--midnight);
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: var(--paper);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1300px;
            margin: 0 auto;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Libre Baskerville', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--midnight);
            text-decoration: none;
        }

        .header-right {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--midnight);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--steel);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-secondary {
            background: var(--cloud);
            color: var(--charcoal);
            border: none;
            padding: 0.7rem 1.3rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: var(--steel);
            color: white;
        }

        /* Main Layout */
        .main-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* Welcome Section */
        .welcome-section {
            margin-bottom: 3rem;
        }

        .greeting {
            font-family: 'Libre Baskerville', serif;
            font-size: 2.5rem;
            color: var(--midnight);
            margin-bottom: 0.5rem;
        }

        .date-info {
            color: var(--steel);
            font-size: 1rem;
        }

        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-box {
            background: var(--paper);
            padding: 1.8rem;
            border-radius: 12px;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--accent);
            transition: transform 0.3s;
        }

        .stat-box:hover {
            transform: translateY(-4px);
        }

        .stat-box.assigned {
            --accent: var(--teal);
        }

        .stat-box.my-tasks {
            --accent: var(--coral);
        }

        .stat-box.completed {
            --accent: var(--lime);
        }

        .stat-box.overdue {
            --accent: var(--gold);
        }

        .stat-number {
            font-family: 'Libre Baskerville', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--midnight);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--steel);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Task Sections */
        .task-section {
            background: var(--paper);
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }

        .section-title {
            font-family: 'Libre Baskerville', serif;
            font-size: 1.8rem;
            color: var(--midnight);
            margin-bottom: 0.5rem;
        }

        .section-subtitle {
            color: var(--steel);
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            border-bottom: 2px solid var(--cloud);
            margin-bottom: 2rem;
        }

        .tab {
            background: none;
            border: none;
            padding: 0.8rem 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--steel);
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        .tab.active {
            color: var(--midnight);
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--midnight);
        }

        /* Task Cards */
        .task-list {
            display: grid;
            gap: 1.5rem;
        }

        .task-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.8rem;
            transition: all 0.3s;
            position: relative;
        }

        .task-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .task-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--priority-color);
            border-radius: 12px 0 0 12px;
        }

        .task-card.priority-high {
            --priority-color: var(--coral);
        }

        .task-card.priority-medium {
            --priority-color: var(--gold);
        }

        .task-card.priority-low {
            --priority-color: var(--lime);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .task-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--midnight);
            margin-bottom: 0.5rem;
        }

        .task-badges {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-status {
            background: var(--bg);
            color: var(--text);
        }

        .badge-status.pending {
            --bg: #fef3c7;
            --text: #92400e;
        }

        .badge-status.in-progress {
            --bg: #dbeafe;
            --text: #1e40af;
        }

        .badge-status.completed {
            --bg: #d1fae5;
            --text: #065f46;
        }

        .badge-owner {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .task-description {
            color: var(--steel);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.2rem;
        }

        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.2rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--steel);
        }

        .meta-icon {
            font-size: 1.1rem;
        }

        .due-date {
            font-weight: 600;
        }

        .due-date.overdue {
            color: var(--coral);
        }

        .task-contributors {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.2rem;
            flex-wrap: wrap;
        }

        .contributor {
            background: var(--cloud);
            padding: 0.4rem 0.9rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--charcoal);
        }

        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--cloud);
        }

        .status-update {
            display: flex;
            gap: 0.5rem;
        }

        .btn-status {
            background: var(--bg);
            color: var(--text);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-status:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-status.start {
            --bg: var(--teal);
            --text: white;
        }

        .btn-status.complete {
            --bg: var(--lime);
            --text: var(--midnight);
        }

        .btn-view {
            background: none;
            border: 1px solid var(--cloud);
            color: var(--steel);
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view:hover {
            border-color: var(--midnight);
            color: var(--midnight);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .empty-text {
            color: var(--steel);
            font-size: 1.1rem;
        }

        /* Create Task Button */
        .fab {
            position: fixed;
            bottom: 2.5rem;
            right: 2.5rem;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--coral) 0%, #e76f51 100%);
            color: white;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(239, 131, 84, 0.4);
            transition: all 0.3s;
            z-index: 90;
        }

        .fab:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 12px 32px rgba(239, 131, 84, 0.6);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .greeting {
                font-size: 2rem;
            }

            .quick-stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .task-meta {
                flex-direction: column;
                gap: 0.8rem;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .task-card {
            animation: fadeIn 0.5s ease forwards;
        }

        .task-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .task-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .task-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .task-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        .task-card:nth-child(5) {
            animation-delay: 0.5s;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="<?= base_url('dashboard') ?>" class="logo">TaskFlow</a>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-name"><?= $user['name'] ?></div>
                    <div class="user-role"><?= ucfirst($user['role']) ?></div>
                </div>
                <a href="<?= base_url('logout') ?>" class="btn-secondary">Sign Out</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-container">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <h1 class="greeting">Welcome back, <?= explode(' ', $user['name'])[0] ?></h1>
            <p class="date-info"><?= date('l, F j, Y') ?></p>
        </section>

        <!-- Quick Stats -->
        <section class="quick-stats">
            <div class="stat-box assigned">
                <div class="stat-number"><?= $stats['assigned_to_me'] ?></div>
                <div class="stat-label">Assigned to Me</div>
            </div>

            <div class="stat-box my-tasks">
                <div class="stat-number"><?= $stats['my_tasks'] ?></div>
                <div class="stat-label">My Tasks</div>
            </div>

            <div class="stat-box completed">
                <div class="stat-number"><?= $stats['completed'] ?></div>
                <div class="stat-label">Completed</div>
            </div>

            <div class="stat-box overdue">
                <div class="stat-number"><?= $stats['overdue'] ?></div>
                <div class="stat-label">Overdue</div>
            </div>
        </section>

        <!-- Task Sections -->
        <section class="task-section">
            <h2 class="section-title">My Tasks</h2>
            <p class="section-subtitle">Tasks you created and tasks assigned to you</p>

            <div class="tabs">
                <button class="tab active" data-tab="assigned">Assigned to Me</button>
                <button class="tab" data-tab="created">Created by Me</button>
                <button class="tab" data-tab="all">All My Tasks</button>
            </div>

            <div class="tab-content" data-content="assigned">
                <div class="task-list">
                    <?php if (empty($assigned_tasks)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📬</div>
                            <div class="empty-text">No tasks assigned to you yet.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($assigned_tasks as $task): ?>
                            <div class="task-card priority-<?= $task['priority'] ?>">
                                <div class="task-header">
                                    <div>
                                        <h3 class="task-title"><?= htmlspecialchars($task['title']) ?></h3>
                                    </div>
                                    <div class="task-badges">
                                        <span class="badge badge-status <?= $task['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                        </span>
                                        <?php if ($task['is_owner']): ?>
                                            <span class="badge badge-owner">Owner</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($task['description']): ?>
                                    <div class="task-description">
                                        <?= htmlspecialchars($task['description']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="task-meta">
                                    <div class="meta-item">
                                        <span class="meta-icon">📅</span>
                                        <span class="due-date <?= strtotime($task['due_date']) < time() && $task['status'] !== 'completed' ? 'overdue' : '' ?>">
                                            Due: <?= date('M d, Y', strtotime($task['due_date'])) ?>
                                        </span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-icon">⚡</span>
                                        <span><?= ucfirst($task['priority']) ?> Priority</span>
                                    </div>
                                </div>

                                <?php if (count($task['users']) > 1): ?>
                                    <div class="task-contributors">
                                        <?php foreach ($task['users'] as $user): ?>
                                            <span class="contributor">
                                                <?= htmlspecialchars($user['name']) ?>
                                                <?= $user['responsibility'] === 'owner' ? '★' : '' ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="task-footer">
                                    <div class="status-update">
                                        <?php if ($task['status'] === 'pending' && $task['can_update_status']): ?>
                                            <button class="btn-status start" onclick="updateStatus(<?= $task['id'] ?>, 'in_progress')">
                                                Start Task
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($task['status'] === 'in_progress' && $task['can_update_status']): ?>
                                            <button class="btn-status complete" onclick="updateStatus(<?= $task['id'] ?>, 'completed')">
                                                Mark Complete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url('tasks/' . $task['id']) ?>" class="btn-view">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-content" data-content="created" style="display: none;">
                <div class="task-list">
                    <?php if (empty($created_tasks)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📝</div>
                            <div class="empty-text">You haven't created any tasks yet.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($created_tasks as $task): ?>
                            <div class="task-card priority-<?= $task['priority'] ?>">
                                <div class="task-header">
                                    <div>
                                        <h3 class="task-title"><?= htmlspecialchars($task['title']) ?></h3>
                                    </div>
                                    <div class="task-badges">
                                        <span class="badge badge-status <?= $task['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                        </span>
                                        <span class="badge badge-owner">Owner</span>
                                    </div>
                                </div>

                                <?php if ($task['description']): ?>
                                    <div class="task-description">
                                        <?= htmlspecialchars($task['description']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="task-meta">
                                    <div class="meta-item">
                                        <span class="meta-icon">📅</span>
                                        <span class="due-date <?= strtotime($task['due_date']) < time() && $task['status'] !== 'completed' ? 'overdue' : '' ?>">
                                            Due: <?= date('M d, Y', strtotime($task['due_date'])) ?>
                                        </span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-icon">⚡</span>
                                        <span><?= ucfirst($task['priority']) ?> Priority</span>
                                    </div>
                                </div>

                                <?php if (count($task['users']) > 1): ?>
                                    <div class="task-contributors">
                                        <?php foreach ($task['users'] as $user): ?>
                                            <span class="contributor">
                                                <?= htmlspecialchars($user['name']) ?>
                                                <?= $user['responsibility'] === 'owner' ? '★' : '' ?>
                                            </span>
                                        <?php endforeach; ?>

                                    </div>
                                <?php endif; ?>

                                <div class="task-footer">
                                    <div class="status-update">
                                        <?php if ($task['can_archive']): ?>
                                            <button class="btn-status" style="--bg: var(--fog); --text: var(--midnight);" onclick="archiveTask(<?= $task['id'] ?>)">
                                                Archive
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url('tasks/' . $task['id'] . '/edit') ?>" class="btn-view">Edit Task</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tab-content" data-content="all" style="display: none;">
                <div class="task-list">
                    <?php if (empty($all_tasks)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📋</div>
                            <div class="empty-text">No tasks to display.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($all_tasks as $task): ?>
                            <div class="task-card priority-<?= $task['priority'] ?>">
                                <div class="task-header">
                                    <div>
                                        <h3 class="task-title"><?= htmlspecialchars($task['title']) ?></h3>
                                    </div>
                                    <div class="task-badges">
                                        <span class="badge badge-status <?= $task['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                        </span>
                                        <?php if ($task['is_owner']): ?>
                                            <span class="badge badge-owner">Owner</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($task['description']): ?>
                                    <div class="task-description">
                                        <?= htmlspecialchars($task['description']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="task-meta">
                                    <div class="meta-item">
                                        <span class="meta-icon">📅</span>
                                        <span class="due-date <?= strtotime($task['due_date']) < time() && $task['status'] !== 'completed' ? 'overdue' : '' ?>">
                                            Due: <?= date('M d, Y', strtotime($task['due_date'])) ?>
                                        </span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-icon">⚡</span>
                                        <span><?= ucfirst($task['priority']) ?> Priority</span>
                                    </div>
                                </div>

                                <div class="task-footer">
                                    <div class="status-update">
                                        <?php if ($task['status'] === 'pending' && $task['can_update_status']): ?>
                                            <button class="btn-status start" onclick="updateStatus(<?= $task['id'] ?>, 'in_progress')">
                                                Start Task
                                            </button>
                                        <?php endif; ?>

                                        <?php if ($task['status'] === 'in_progress' && $task['can_update_status']): ?>
                                            <button class="btn-status complete" onclick="updateStatus(<?= $task['id'] ?>, 'completed')">
                                                Mark Complete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url('tasks/' . $task['id']) ?>" class="btn-view">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Floating Action Button -->
    <button class="fab" onclick="window.location.href='<?= base_url('tasks/create') ?>'" title="Create New Task">+</button>

    <script>
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const tabName = this.dataset.tab;
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.style.display = 'none';
                });
                document.querySelector(`[data-content="${tabName}"]`).style.display = 'block';
            });
        });

        // Update task status
        function updateStatus(taskId, newStatus) {
            if (confirm(`Change status to ${newStatus.replace('_', ' ')}?`)) {
                fetch(`<?= base_url('tasks') ?>/${taskId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Cannot update status');
                        }
                    });
            }
        }

        // Archive task
        function archiveTask(taskId) {
            if (confirm('Archive this task? You can restore it later.')) {
                fetch(`<?= base_url('tasks') ?>/${taskId}/archive`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Cannot archive task');
                        }
                    });
            }
        }
    </script>
</body>

</html>