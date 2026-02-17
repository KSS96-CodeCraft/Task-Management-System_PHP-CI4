<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= isset($task) ? 'Edit Task' : 'Create Task' ?> | Task Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Lato:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #1e3a5f;
            --denim: #2c5f8d;
            --sky: #4a90e2;
            --cream: #fef9f3;
            --tan: #e8dcc4;
            --rust: #d35400;
            --forest: #27ae60;
            --wine: #8e44ad;
            --shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Lato', sans-serif;
            background: linear-gradient(to bottom right, var(--cream), var(--tan));
            color: var(--navy);
            min-height: 100vh;
            padding: 2rem;
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--denim) 100%);
            color: white;
            padding: 3rem 3rem 2rem;
        }

        .back-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 1rem;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: white;
            transform: translateX(-4px);
        }

        .form-title {
            font-family: 'Merriweather', serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .form-body {
            padding: 3rem;
        }

        .form-section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-family: 'Merriweather', serif;
            font-size: 1.3rem;
            color: var(--navy);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--tan);
        }

        .form-group {
            margin-bottom: 1.8rem;
        }

        label {
            display: block;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--navy);
            margin-bottom: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .label-optional {
            font-weight: 400;
            color: #666;
            text-transform: none;
            letter-spacing: 0;
            font-size: 0.8rem;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s;
            background: #fafafa;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--sky);
            background: white;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .help-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.4rem;
            line-height: 1.4;
        }

        .help-text.warning {
            color: var(--rust);
            font-weight: 600;
        }

        /* User Assignment Section */
        .user-assignment {
            background: var(--cream);
            border-radius: 12px;
            padding: 2rem;
        }

        .user-search {
            margin-bottom: 1.5rem;
        }

        .search-input {
            position: relative;
        }

        .search-input input {
            padding-right: 3rem;
        }

        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.2rem;
        }

        .user-list {
            max-height: 300px;
            overflow-y: auto;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background: white;
        }

        .user-item {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: background 0.2s;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-item:hover {
            background: #f8f8f8;
        }

        .user-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 0.2rem;
        }

        .user-role {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .selected-users {
            margin-top: 1.5rem;
        }

        .selected-users-title {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--navy);
            margin-bottom: 0.8rem;
        }

        .selected-user-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .user-chip {
            background: var(--navy);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .user-chip.owner {
            background: linear-gradient(135deg, var(--wine) 0%, #9b59b6 100%);
        }

        .remove-user {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2rem;
            line-height: 1;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .remove-user:hover {
            opacity: 1;
        }

        /* Priority Radio Buttons */
        .priority-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .priority-option {
            position: relative;
        }

        .priority-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .priority-label {
            display: block;
            padding: 1.2rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .priority-option input[type="radio"]:checked + .priority-label {
            border-color: var(--priority-color);
            background: var(--priority-color);
            color: white;
            font-weight: 700;
        }

        .priority-option.low { --priority-color: var(--forest); }
        .priority-option.medium { --priority-color: #f39c12; }
        .priority-option.high { --priority-color: var(--rust); }

        .priority-label .priority-icon {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 0.5rem;
        }

        /* Status Select (for edit mode) */
        .status-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .status-option {
            position: relative;
        }

        .status-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .status-label {
            display: block;
            padding: 1.2rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .status-option input[type="radio"]:checked + .status-label {
            border-color: var(--status-color);
            background: var(--status-color);
            color: white;
            font-weight: 700;
        }

        .status-option.pending { --status-color: #f39c12; }
        .status-option.in-progress { --status-color: var(--sky); }
        .status-option.completed { --status-color: var(--forest); }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 1rem;
            padding-top: 2rem;
            border-top: 2px solid var(--tan);
        }

        .btn {
            flex: 1;
            padding: 1.2rem;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--navy) 0%, var(--denim) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(30, 58, 95, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 58, 95, 0.4);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: var(--navy);
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .btn-danger {
            background: var(--rust);
            color: white;
            flex: 0 0 auto;
            padding: 1.2rem 2rem;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        /* Alert Box */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border-left: 4px solid #1565c0;
        }

        .alert-warning {
            background: #fff3e0;
            color: #e65100;
            border-left: 4px solid #e65100;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .form-header,
            .form-body {
                padding: 2rem;
            }

            .form-title {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .priority-options,
            .status-options {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }
        }

        /* Animation */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-container {
            animation: slideInUp 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <a href="<?= base_url(($user['role'] === 'admin' || $user['role'] === 'head') ? 'admin/dashboard' : 'dashboard') ?>" class="back-link">← Back to Dashboard</a>
            <h1 class="form-title"><?= isset($task) ? 'Edit Task' : 'Create New Task' ?></h1>
            <p class="form-subtitle"><?= isset($task) ? 'Update task details and manage assignments' : 'Define your task and assign team members' ?></p>
        </div>

        <form method="POST" action="<?= isset($task) ? base_url('tasks/'.$task['id'].'/update') : base_url('home/save_task') ?>" id="taskForm">
            <?php if (isset($task)): ?>
                <input type="hidden" name="_method" value="POST">
            <?php endif; ?>

            <div class="form-body">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <h2 class="section-title">Basic Information</h2>

                    <div class="form-group">
                        <label for="title">Task Title *</label>
                        <input type="text" id="title" name="title" value="<?= isset($task) ? htmlspecialchars($task['title']) : '' ?>" placeholder="Enter a clear, descriptive title" required>
                    </div>

                    <div class="form-group">
                        <label for="description">
                            Description 
                            <span class="label-optional">(Optional)</span>
                        </label>
                        <textarea id="description" name="description" placeholder="Provide additional context, requirements, or notes about this task"><?= isset($task) ? htmlspecialchars($task['description']) : '' ?></textarea>
                        <p class="help-text">A clear description helps team members understand expectations and deliverables.</p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="due_date">Due Date *</label>
                            <input type="date" id="due_date" name="due_date" value="<?= isset($task) ? $task['due_date'] : date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
                            <p class="help-text">Only the task owner can change the due date after creation.</p>
                        </div>

                        <div class="form-group">
                            <label>Priority Level *</label>
                            <div class="priority-options">
                                <div class="priority-option low">
                                    <input type="radio" id="priority-low" name="priority" value="low" <?= (isset($task) && $task['priority'] === 'low') || !isset($task) ? '' : '' ?>>
                                    <label for="priority-low" class="priority-label">
                                        <span class="priority-icon">🟢</span>
                                        Low
                                    </label>
                                </div>
                                <div class="priority-option medium">
                                    <input type="radio" id="priority-medium" name="priority" value="medium" <?= !isset($task) || (isset($task) && $task['priority'] === 'medium') ? 'checked' : '' ?>>
                                    <label for="priority-medium" class="priority-label">
                                        <span class="priority-icon">🟡</span>
                                        Medium
                                    </label>
                                </div>
                                <div class="priority-option high">
                                    <input type="radio" id="priority-high" name="priority" value="high" <?= isset($task) && $task['priority'] === 'high' ? 'checked' : '' ?>>
                                    <label for="priority-high" class="priority-label">
                                        <span class="priority-icon">🔴</span>
                                        High
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($task) && ($user['role'] === 'admin' || $task['is_owner'] || $task['is_creator'])): ?>
                        <div class="form-group">
                            <label>Task Status</label>
                            <div class="status-options">
                                <div class="status-option pending">
                                    <input type="radio" id="status-pending" name="status" value="pending" <?= $task['status'] === 'pending' ? 'checked' : '' ?>>
                                    <label for="status-pending" class="status-label">Pending</label>
                                </div>
                                <div class="status-option in-progress">
                                    <input type="radio" id="status-in-progress" name="status" value="in_progress" <?= $task['status'] === 'in_progress' ? 'checked' : '' ?>>
                                    <label for="status-in-progress" class="status-label">In Progress</label>
                                </div>
                                <div class="status-option completed">
                                    <input type="radio" id="status-completed" name="status" value="completed" <?= $task['status'] === 'completed' ? 'checked' : '' ?>>
                                    <label for="status-completed" class="status-label">Completed</label>
                                </div>
                            </div>
                            <?php if (strtotime($task['due_date']) > time()): ?>
                                <p class="help-text warning">⚠️ This task cannot be marked as completed until the due date has passed (unless you have elevated permissions).</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- User Assignment Section -->
                <?php if ($user['role'] === 'admin' || $user['role'] === 'head' || !isset($task)): ?>
                    <div class="form-section">
                        <h2 class="section-title">Team Assignment</h2>

                        <div class="alert alert-info">
                            ℹ️ You will automatically be assigned as the <strong>Owner</strong> of this task. Select additional contributors below.
                        </div>

                        <div class="user-assignment">
                            <div class="user-search">
                                <label for="user-search">Search Team Members</label>
                                <div class="search-input">
                                    <input type="text" id="user-search" placeholder="Search by name or role..." autocomplete="off">
                                    <span class="search-icon">🔍</span>
                                </div>
                            </div>

                            <div class="user-list" id="userList">
                                <?php foreach ($available_users as $available_user): ?>
                                    <?php if ($available_user['id'] !== $user['id']): ?>
                                        <div class="user-item" data-user-name="<?= strtolower($available_user['name']) ?>" data-user-role="<?= strtolower($available_user['role']) ?>">
                                            <input type="checkbox" class="user-checkbox" name="assigned_users[]" value="<?= $available_user['id'] ?>" id="user-<?= $available_user['id'] ?>" 
                                                <?= isset($task) && in_array($available_user['id'], array_column($task['users'], 'id')) ? 'checked' : '' ?>>
                                            <label for="user-<?= $available_user['id'] ?>" class="user-info" style="cursor: pointer;">
                                                <div class="user-name"><?= htmlspecialchars($available_user['name']) ?></div>
                                                <div class="user-role"><?= ucfirst($available_user['role']) ?></div>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div class="selected-users">
                                <div class="selected-users-title">Selected Team Members</div>
                                <div class="selected-user-chips" id="selectedChips">
                                    <div class="user-chip owner">
                                        <span><?= htmlspecialchars($user['name']) ?> (You - Owner)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="help-text">Tasks can be assigned to multiple users. The creator is always the Owner. All other assigned users are Contributors.</p>
                    </div>
                <?php endif; ?>

                <!-- Business Rules Info -->
                <?php if (isset($task)): ?>
                    <div class="form-section">
                        <h2 class="section-title">Important Notes</h2>

                        <div class="alert alert-warning">
                            <strong>Task Management Rules:</strong><br>
                            • Only the owner can change the due date<br>
                            • Tasks cannot be marked completed before the due date (unless you have admin permissions)<br>
                            • Tasks with multiple assigned users cannot be deleted<br>
                            • Only owners can archive tasks
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="<?= base_url(($user['role'] === 'admin' || $user['role'] === 'head') ? 'admin/dashboard' : 'dashboard') ?>" class="btn btn-secondary">Cancel</a>
                    
                    <?php if (isset($task) && $task['can_delete'] && count($task['users']) <= 1): ?>
                        <button type="button" class="btn btn-danger" onclick="deleteTask(<?= $task['id'] ?>)">Delete Task</button>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn-primary">
                        <?= isset($task) ? 'Update Task' : 'Create Task' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // User search functionality
        const searchInput = document.getElementById('user-search');
        const userItems = document.querySelectorAll('.user-item');
        const selectedChips = document.getElementById('selectedChips');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');

        searchInput?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            userItems.forEach(item => {
                const userName = item.dataset.userName;
                const userRole = item.dataset.userRole;
                
                if (userName.includes(searchTerm) || userRole.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Update selected chips
        function updateSelectedChips() {
            // Clear existing chips except owner
            const chips = selectedChips.querySelectorAll('.user-chip:not(.owner)');
            chips.forEach(chip => chip.remove());

            // Add chips for checked users
            userCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const userItem = checkbox.closest('.user-item');
                    const userName = userItem.querySelector('.user-name').textContent;
                    
                    const chip = document.createElement('div');
                    chip.className = 'user-chip';
                    chip.innerHTML = `
                        <span>${userName}</span>
                        <button type="button" class="remove-user" onclick="removeUser('${checkbox.id}')">×</button>
                    `;
                    selectedChips.appendChild(chip);
                }
            });
        }

        // Remove user
        function removeUser(checkboxId) {
            const checkbox = document.getElementById(checkboxId);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedChips();
            }
        }

        // Listen to checkbox changes
        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedChips);
        });

        // Initialize on page load
        if (selectedChips) {
            updateSelectedChips();
        }

        // Form validation
        const form = document.getElementById('taskForm');
        form?.addEventListener('submit', function(e) {
            const dueDate = new Date(document.getElementById('due_date').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (dueDate < today) {
                e.preventDefault();
                alert('Due date cannot be in the past. Please select a valid date.');
                return false;
            }

            <?php if (isset($task) && !$task['is_owner']): ?>
                const dueDateInput = document.getElementById('due_date');
                const originalDueDate = '<?= $task['due_date'] ?>';
                if (dueDateInput.value !== originalDueDate) {
                    e.preventDefault();
                    alert('Only the task owner can change the due date.');
                    dueDateInput.value = originalDueDate;
                    return false;
                }
            <?php endif; ?>
        });

        // Delete task
        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
                fetch(`<?= base_url('tasks') ?>/${taskId}`, {
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
                        alert(data.message || 'Cannot delete task');
                    }
                });
            }
        }
    </script>
</body>
</html>
