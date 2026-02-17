<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Assignments - <?= htmlspecialchars($task['title']) ?> | Task Management</title>
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

        /* Header */
        .page-header {
            background: linear-gradient(135deg, var(--midnight) 0%, var(--ocean) 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .header-content {
            max-width: 1100px;
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

        .page-title {
            font-family: 'Spectral', serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            opacity: 0.9;
            font-size: 1rem;
        }

        .task-title-ref {
            background: rgba(255,255,255,0.1);
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        /* Container */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        /* Current Team Section */
        .section {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--cloud);
        }

        .section-title {
            font-family: 'Spectral', serif;
            font-size: 1.8rem;
            color: var(--midnight);
        }

        .team-count {
            background: var(--midnight);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        /* Team Member Cards */
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .member-card {
            background: var(--cream);
            border: 2px solid var(--cloud);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s;
            position: relative;
        }

        .member-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border-color: var(--steel);
        }

        .member-card.owner {
            border-color: var(--violet);
            background: linear-gradient(135deg, rgba(131, 56, 236, 0.05) 0%, rgba(131, 56, 236, 0.02) 100%);
        }

        .member-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .member-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--violet) 0%, #a855f7 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .member-card.owner .member-avatar {
            background: linear-gradient(135deg, var(--violet) 0%, var(--coral) 100%);
            box-shadow: 0 4px 12px rgba(131, 56, 236, 0.4);
        }

        .member-info {
            flex: 1;
            min-width: 0;
        }

        .member-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--midnight);
            margin-bottom: 0.3rem;
            word-wrap: break-word;
        }

        .member-role {
            font-size: 0.8rem;
            color: var(--silver);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .member-responsibility {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .member-responsibility.owner {
            background: linear-gradient(135deg, var(--violet) 0%, #a855f7 100%);
            color: white;
        }

        .member-responsibility.contributor {
            background: var(--cloud);
            color: var(--steel);
        }

        .remove-btn {
            width: 100%;
            padding: 0.8rem;
            background: transparent;
            border: 2px solid var(--coral);
            color: var(--coral);
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .remove-btn:hover:not(:disabled) {
            background: var(--coral);
            color: white;
        }

        .remove-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        /* Add Users Section */
        .search-box {
            margin-bottom: 2rem;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1.2rem 1rem 3rem;
            border: 2px solid var(--cloud);
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s;
            background: var(--cream);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--steel);
            background: white;
            box-shadow: 0 0 0 4px rgba(65, 90, 119, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--silver);
            font-size: 1.2rem;
        }

        .available-users-grid {
            display: grid;
            gap: 1rem;
            max-height: 500px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .available-users-grid::-webkit-scrollbar {
            width: 8px;
        }

        .available-users-grid::-webkit-scrollbar-track {
            background: var(--cream);
            border-radius: 4px;
        }

        .available-users-grid::-webkit-scrollbar-thumb {
            background: var(--cloud);
            border-radius: 4px;
        }

        .available-users-grid::-webkit-scrollbar-thumb:hover {
            background: var(--silver);
        }

        .available-user-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem;
            background: var(--cream);
            border: 2px solid var(--cloud);
            border-radius: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .available-user-card:hover {
            background: white;
            border-color: var(--steel);
            transform: translateX(4px);
        }

        .available-user-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .available-user-card.disabled:hover {
            transform: none;
            border-color: var(--cloud);
            background: var(--cream);
        }

        .user-avatar-small {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--steel) 0%, var(--silver) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            color: var(--midnight);
            margin-bottom: 0.2rem;
        }

        .user-role-tag {
            font-size: 0.75rem;
            color: var(--silver);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .add-btn {
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, var(--mint) 0%, #00d48a 100%);
            color: var(--midnight);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6, 255, 165, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--silver);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .empty-text {
            font-size: 1.1rem;
        }

        /* Action Buttons */
        .actions-bar {
            display: flex;
            gap: 1rem;
            padding: 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--midnight) 0%, var(--ocean) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(13, 27, 42, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 27, 42, 0.4);
        }

        .btn-secondary {
            background: var(--cloud);
            color: var(--midnight);
        }

        .btn-secondary:hover {
            background: var(--silver);
            color: white;
        }

        /* Alert */
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
            border-left: 4px solid #0ea5e9;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid var(--gold);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .team-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 2rem;
            }

            .section {
                padding: 1.5rem;
            }

            .actions-bar {
                flex-direction: column;
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

        .section {
            animation: fadeIn 0.5s ease forwards;
        }

        .section:nth-child(2) { animation-delay: 0.1s; }
        .section:nth-child(3) { animation-delay: 0.2s; }
    </style>
</head>
<body>
    <!-- Page Header -->
    <header class="page-header">
        <div class="header-content">
            <a href="<?= base_url('tasks/'.$task['id']) ?>" class="back-link">
                <span>←</span>
                <span>Back to Task</span>
            </a>
            <h1 class="page-title">Manage Team Assignments</h1>
            <p class="page-subtitle">Add or remove team members from this task</p>
            <div class="task-title-ref">
                📋 <strong><?= htmlspecialchars($task['title']) ?></strong>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Alert -->
        <div class="alert alert-info">
            <span class="alert-icon">ℹ️</span>
            <div>
                <strong>Assignment Rules:</strong><br>
                • The task owner cannot be removed<br>
                • All assigned users become Contributors (except the owner)<br>
                • Tasks with multiple users cannot be deleted<br>
                • Only Admins and Heads can manage assignments
            </div>
        </div>

        <!-- Current Team Section -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Current Team</h2>
                <div class="team-count">
                    <?= count($assigned_users_data) ?> Members
                </div>
            </div>

            <div class="team-grid">
                <?php foreach ($assigned_users_data as $member): ?>
                    <div class="member-card <?= $member['responsibility'] === 'owner' ? 'owner' : '' ?>">
                        <div class="member-header">
                            <div class="member-avatar">
                                <?= strtoupper(substr($member['name'], 0, 1)) ?>
                            </div>
                            <div class="member-info">
                                <div class="member-name"><?= htmlspecialchars($member['name']) ?></div>
                                <div class="member-role"><?= ucfirst($member['role']) ?></div>
                            </div>
                        </div>

                        <div class="member-responsibility <?= $member['responsibility'] ?>">
                            <?= $member['responsibility'] === 'owner' ? '⭐ ' : '' ?>
                            <?= ucfirst($member['responsibility']) ?>
                        </div>

                        <button class="remove-btn" 
                            onclick="removeUser(<?= $member['id'] ?>)"
                            <?= $member['responsibility'] === 'owner' ? 'disabled title="Owner cannot be removed"' : '' ?>>
                            <?= $member['responsibility'] === 'owner' ? 'Owner (Cannot Remove)' : 'Remove from Task' ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add Users Section -->
        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Add Team Members</h2>
            </div>

            <div class="search-box">
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-input" id="searchUsers" placeholder="Search by name or email...">
                </div>
            </div>

            <div class="available-users-grid" id="availableUsers">
                <?php 
                if (!empty($available_users) && is_array($available_users)): 
                    foreach ($available_users as $available_user): 
                ?>
                    <div class="available-user-card" 
                        data-user-name="<?= strtolower($available_user['name']) ?>" 
                        data-user-email="<?= strtolower($available_user['email']) ?>">
                        <div class="user-avatar-small">
                            <?= strtoupper(substr($available_user['name'], 0, 1)) ?>
                        </div>
                        <div class="user-details">
                            <div class="user-name"><?= htmlspecialchars($available_user['name']) ?></div>
                            <div class="user-role-tag"><?= htmlspecialchars($available_user['email']) ?></div>
                        </div>
                        <button class="add-btn" onclick="addUser(<?= $available_user['id'] ?>, '<?= htmlspecialchars($available_user['name'], ENT_QUOTES) ?>')">
                            + Add
                        </button>
                    </div>
                <?php 
                    endforeach;
                else:
                ?>
                    <div class="empty-state">
                        <div class="empty-icon">👥</div>
                        <div class="empty-text">All available users are already assigned to this task.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="actions-bar">
            <a href="<?= base_url('tasks/'.$task['id']) ?>" class="btn btn-secondary">Cancel</a>
            <a href="<?= base_url('tasks/'.$task['id']) ?>" class="btn btn-primary">Done</a>
        </div>
    </div>

    <script>
        // Search functionality
        const searchInput = document.getElementById('searchUsers');
        const userCards = document.querySelectorAll('.available-user-card');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            userCards.forEach(card => {
                const userName = card.dataset.userName;
                const userEmail = card.dataset.userEmail;

                if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Add user to task
        function addUser(userId, userName) {
            if (confirm(`Add ${userName} to this task as a contributor?`)) {
                fetch('<?= base_url('admin/tasks/'.$task['id'].'/assign-user') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                    },
                    body: JSON.stringify({ 
                        user_id: userId,
                        responsibility: 'contributor'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to add user');
                    }
                })
                .catch(error => {
                    alert('An error occurred. Please try again.');
                });
            }
        }

        // Remove user from task
        function removeUser(userId) {
            if (confirm('Remove this user from the task?')) {
                fetch('<?= base_url('tasks/'.$task['id'].'/remove-user') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                    },
                    body: JSON.stringify({ user_id: userId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to remove user');
                    }
                })
                .catch(error => {
                    alert('An error occurred. Please try again.');
                });
            }
        }
    </script>
</body>
</html>