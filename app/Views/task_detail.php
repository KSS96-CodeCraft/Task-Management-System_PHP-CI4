<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($task['title']) ?> | Task Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;600;700&family=Source+Sans+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --obsidian: #0f172a;
            --slate: #1e293b;
            --stone: #475569;
            --ash: #64748b;
            --mist: #cbd5e1;
            --pearl: #f1f5f9;
            --white: #ffffff;
            --amber: #f59e0b;
            --ruby: #ef4444;
            --emerald: #10b981;
            --sapphire: #3b82f6;
            --violet: #8b5cf6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--obsidian);
            line-height: 1.6;
        }

        /* Navigation Bar */
        .navbar {
            background: var(--white);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-btn {
            color: var(--stone);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .back-btn:hover {
            color: var(--obsidian);
            transform: translateX(-4px);
        }

        .nav-actions {
            display: flex;
            gap: 0.8rem;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* Task Header */
        .task-header {
            background: var(--white);
            border-radius: 16px;
            padding: 3rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .task-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .task-badges {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .badge {
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-priority {
            background: var(--bg);
            color: var(--text);
        }

        .badge-priority.low {
            --bg: #d1fae5;
            --text: #065f46;
        }

        .badge-priority.medium {
            --bg: #fef3c7;
            --text: #92400e;
        }

        .badge-priority.high {
            --bg: #fee2e2;
            --text: #991b1b;
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

        .task-title {
            font-family: 'EB Garamond', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--obsidian);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .task-description {
            font-size: 1.1rem;
            color: var(--stone);
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .task-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            padding-top: 2rem;
            border-top: 2px solid var(--pearl);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ash);
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--obsidian);
        }

        .detail-value.overdue {
            color: var(--ruby);
        }

        /* Team Section */
        .team-section {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-family: 'EB Garamond', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--obsidian);
            margin-bottom: 1.5rem;
        }

        .team-list {
            display: grid;
            gap: 1rem;
        }

        .team-member {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem;
            background: var(--pearl);
            border-radius: 12px;
            transition: all 0.2s;
        }

        .team-member:hover {
            background: var(--mist);
        }

        .member-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sapphire) 0%, var(--violet) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .member-info {
            flex: 1;
        }

        .member-name {
            font-weight: 700;
            color: var(--obsidian);
            margin-bottom: 0.2rem;
        }

        .member-role {
            font-size: 0.85rem;
            color: var(--ash);
        }

        .member-responsibility {
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .member-responsibility.owner {
            background: linear-gradient(135deg, var(--violet) 0%, #a78bfa 100%);
            color: white;
        }

        .member-responsibility.contributor {
            background: var(--mist);
            color: var(--stone);
        }

        /* Actions Section */
        .actions-section {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .btn {
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--sapphire) 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--emerald) 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--amber) 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--ruby) 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-secondary {
            background: var(--pearl);
            color: var(--stone);
            border: 2px solid var(--mist);
        }

        .btn-secondary:hover {
            background: var(--mist);
            color: var(--obsidian);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Activity Timeline */
        .activity-section {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--mist);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 0.3rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--sapphire);
            border: 3px solid white;
            box-shadow: 0 0 0 2px var(--sapphire);
        }

        .timeline-date {
            font-size: 0.85rem;
            color: var(--ash);
            margin-bottom: 0.3rem;
        }

        .timeline-content {
            background: var(--pearl);
            padding: 1rem 1.2rem;
            border-radius: 10px;
            color: var(--stone);
            line-height: 1.6;
        }

        .timeline-content strong {
            color: var(--obsidian);
        }

        /* Alert Box */
        .alert {
            padding: 1.2rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-size: 0.95rem;
            line-height: 1.6;
            display: flex;
            align-items: start;
            gap: 1rem;
        }

        .alert-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .alert-info {
            background: #e0f2fe;
            color: #0c4a6e;
            border-left: 4px solid var(--sapphire);
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid var(--amber);
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--ruby);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 2rem 1rem;
            }

            .task-header {
                padding: 2rem;
            }

            .task-title {
                font-size: 2rem;
            }

            .task-details-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: 1fr;
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

        .task-header,
        .team-section,
        .actions-section,
        .activity-section {
            animation: fadeInUp 0.5s ease forwards;
        }

        .team-section {
            animation-delay: 0.1s;
        }

        .actions-section {
            animation-delay: 0.2s;
        }

        .activity-section {
            animation-delay: 0.3s;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-content">
            <a href="<?= base_url(($user['role'] === 'admin' || $user['role'] === 'head') ? 'admin/dashboard' : 'dashboard') ?>" class="back-btn">
                <span>←</span>
                <span>Back to Dashboard</span>
            </a>
            <div class="nav-actions">
                <?php if ($task['can_edit']): ?>
                    <a href="<?= base_url('tasks/' . $task['id'] . '/edit') ?>" class="btn btn-secondary">Edit Task</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Alerts -->
        <?php if (strtotime($task['due_date']) < time() && $task['status'] !== 'completed'): ?>
            <div class="alert alert-danger">
                <span class="alert-icon">⚠️</span>
                <div>
                    <strong>Task Overdue!</strong><br>
                    This task is past its due date. Please update the status or extend the deadline.
                </div>
            </div>
        <?php endif; ?>

        <?php if ($task['status'] === 'completed' && strtotime($task['due_date']) > time()): ?>
            <div class="alert alert-info">
                <span class="alert-icon">ℹ️</span>
                <div>
                    <strong>Early Completion</strong><br>
                    This task was completed before the due date. Great work!
                </div>
            </div>
        <?php endif; ?>

        <?php if (count($task['users']) > 1 && !$task['can_delete']): ?>
            <div class="alert alert-warning">
                <span class="alert-icon">🔒</span>
                <div>
                    <strong>Deletion Restricted</strong><br>
                    This task has multiple assigned users and cannot be deleted. Consider archiving instead.
                </div>
            </div>
        <?php endif; ?>

        <!-- Task Header -->
        <div class="task-header">
            <div class="task-meta-row">
                <div class="task-badges">
                    <span class="badge badge-priority <?= $task['priority'] ?>">
                        <?= ucfirst($task['priority']) ?> Priority
                    </span>
                    <span class="badge badge-status <?= $task['status'] ?>">
                        <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                    </span>
                    <?php if ($task['archived_at']): ?>
                        <span class="badge" style="background: var(--ash); color: white;">Archived</span>
                    <?php endif; ?>
                </div>
            </div>

            <h1 class="task-title"><?= htmlspecialchars($task['title']) ?></h1>

            <?php if ($task['description']): ?>
                <div class="task-description">
                    <?= nl2br(htmlspecialchars($task['description'])) ?>
                </div>
            <?php endif; ?>

            <div class="task-details-grid">
                <div class="detail-item">
                    <div class="detail-label">Due Date</div>
                    <div class="detail-value <?= strtotime($task['due_date']) < time() && $task['status'] !== 'completed' ? 'overdue' : '' ?>">
                        <?= date('F j, Y', strtotime($task['due_date'])) ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Created</div>
                    <div class="detail-value">
                        <?= date('F j, Y', strtotime($task['created_at'])) ?>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label">Last Updated</div>
                    <div class="detail-value">
                        <?= date('F j, Y', strtotime($task['updated_at'])) ?>
                    </div>
                </div>

                <?php if ($task['completed_at']): ?>
                    <div class="detail-item">
                        <div class="detail-label">Completed</div>
                        <div class="detail-value">
                            <?= date('F j, Y', strtotime($task['completed_at'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Team Section -->
        <div class="team-section">
            <h2 class="section-title">Team Members</h2>
            <div class="team-list">
                <?php foreach ($task['users'] as $member): ?>
                    <div class="team-member">
                        <div class="member-avatar">
                            <?= strtoupper(substr($member['name'], 0, 1)) ?>
                        </div>
                        <div class="member-info">
                            <div class="member-name"><?= esc($member['name']) ?></div>
                            <div class="member-role"><?= ucfirst($member['role']) ?></div>
                        </div>
                        <div class="member-responsibility <?= esc($member['responsibility']) ?>">
                            <?= ucfirst($member['responsibility']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Actions Section -->
        <div class="actions-section">
            <h2 class="section-title">Available Actions</h2>
            <div class="actions-grid">
                <?php if ($task['can_update_status']): ?>
                    <?php if ($task['status'] === 'pending'): ?>
                        <button class="btn btn-primary" onclick="updateStatus('in_progress')">
                            Start Working
                        </button>
                    <?php endif; ?>

                    <?php if ($task['status'] === 'in_progress'): ?>
                        <button class="btn btn-success" onclick="updateStatus('completed')"
                            <?= strtotime($task['due_date']) > time() && !$task['has_elevated_permissions'] ? 'disabled title="Cannot complete before due date"' : '' ?>>
                            Mark as Complete
                        </button>
                    <?php endif; ?>

                    <?php if ($task['status'] === 'completed'): ?>
                        <button class="btn btn-warning" onclick="updateStatus('in_progress')">
                            Reopen Task
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($task['can_edit']): ?>
                    <a href="<?= base_url('tasks/' . $task['id'] . '/edit') ?>" class="btn btn-primary">
                        Edit Details
                    </a>
                <?php endif; ?>

                <?php if (($user['role'] === 'admin' || $user['role'] === 'head') && $task['is_owner']): ?>
                    <a href="<?= base_url('admin/tasks/' . $task['id'] . '/assign') ?>" class="btn btn-secondary">
                        Manage Assignments
                    </a>
                <?php endif; ?>

                <?php if ($task['can_archive'] && !$task['archived_at']): ?>
                    <button class="btn btn-secondary" onclick="archiveTask()">
                        Archive Task
                    </button>
                <?php endif; ?>

                <?php if ($task['archived_at'] && $task['can_restore']): ?>
                    <button class="btn btn-primary" onclick="restoreTask()">
                        Restore Task
                    </button>
                <?php endif; ?>

                <?php if ($task['can_delete']): ?>
                    <button class="btn btn-danger" onclick="deleteTask()" <?= count($task['users']) > 1 ? 'disabled title="Cannot delete task with multiple users"' : '' ?>>
                        Delete Task
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="activity-section">
            <h2 class="section-title">Activity History</h2>
            <div class="timeline">
                <?php if (isset($task->activity_log) && !empty($task->activity_log)): ?>
                    <?php foreach ($task->activity_log as $activity): ?>
                        <div class="timeline-item">
                            <div class="timeline-date"><?= date('F j, Y g:i A', strtotime($activity->created_at)) ?></div>
                            <div class="timeline-content">
                                <strong><?= htmlspecialchars($activity->user['name']) ?></strong>
                                <?= $activity->description ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="timeline-item">
                        <div class="timeline-date"><?= date('F j, Y g:i A', strtotime($task['created_at'])) ?></div>
                        <div class="timeline-content">
                            <strong><?= htmlspecialchars($task['owner']['name']) ?></strong> created this task
                        </div>
                    </div>

                    <?php if ($task['status'] === 'in_progress'): ?>
                        <div class="timeline-item">
                            <div class="timeline-date"><?= date('F j, Y g:i A', strtotime($task['updated_at'])) ?></div>
                            <div class="timeline-content">
                                Task status changed to <strong>In Progress</strong>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($task['status'] === 'completed' && $task['completed_at']): ?>
                        <div class="timeline-item">
                            <div class="timeline-date"><?= date('F j, Y g:i A', strtotime($task['completed_at'])) ?></div>
                            <div class="timeline-content">
                                Task marked as <strong>Completed</strong>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Update task status
        function updateStatus(newStatus) {
            const canComplete = <?= json_encode(strtotime($task['due_date']) <= time() || $task['has_elevated_permissions']) ?>;

            if (newStatus === 'completed' && !canComplete) {
                alert('This task cannot be marked as completed until the due date has passed.');
                return;
            }

            if (confirm(`Change task status to ${newStatus.replace('_', ' ')}?`)) {
                fetch('<?= base_url('tasks/' . $task['id'] . '/status') ?>', {
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
                            alert(data.message || 'Failed to update status');
                        }
                    })
                    .catch(error => {
                        alert('An error occurred. Please try again.');
                    });
            }
        }

        // Archive task
        function archiveTask() {
            if (confirm('Archive this task? You can restore it later from the archived tasks section.')) {
                fetch('<?= base_url('tasks/' . $task['id'] . '/archive') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '<?= base_url('dashboard') ?>';
                        } else {
                            alert(data.message || 'Failed to archive task');
                        }
                    });
            }
        }

        // Restore task
        function restoreTask() {
            if (confirm('Restore this task?')) {
                fetch('<?= base_url('tasks/' . $task['id'] . '/restore') ?>', {
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
                            alert(data.message || 'Failed to restore task');
                        }
                    });
            }
        }

        // Delete task
        function deleteTask() {
            const userCount = <?= count($task['users']) ?>;

            if (userCount > 1) {
                alert('This task has multiple assigned users and cannot be deleted. Consider archiving instead.');
                return;
            }

            if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
                fetch('<?= base_url('tasks/' . $task['id']) ?>', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '<?= base_url('dashboard') ?>';
                        } else {
                            alert(data.message || 'Failed to delete task');
                        }
                    });
            }
        }
    </script>
</body>

</html>