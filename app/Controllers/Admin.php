<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TaskModel;

class Admin extends ResourceController
{
    protected $taskModel;
    protected $session;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Admin/Head Dashboard
     */

    public function dashboard()
    {
        // 1. Security Check
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        $userName = $this->session->get('name');

        if (!in_array($userRole, ['admin', 'head'])) {
            return redirect()->to('/dashboard');
        }

        // 2. Fetch Statistics
        $stats = $this->calculateStats($userId, $userRole);

        // 3. Handle Filters
        $filter = $this->request->getGet('filter') ?? 'all';
        $tasks = $this->fetchFilteredTasks($filter, $userId, $userRole);

        // 4. Enhance Tasks (Users and Archive Permissions)
        foreach ($tasks as &$task) {
            // Get users assigned to this task
            $task['users'] = $this->taskModel->getJoinedDataPagination(
                'task_user',
                ['users' => 'task_user.user_id = users.id'],
                'users.id, users.name, users.role, task_user.responsibility',
                ['task_user.task_id' => $task['id']]
            );

            // can_archive logic
            $countData = $this->taskModel->get_specific_columns(
                'task_user',
                'COUNT(*) as total',
                ['task_id' => $task['id']]
            );
            $task['can_archive'] = (($countData[0]['total'] ?? 0) <= 1);
        }

        // 5. Get Archived Tasks
        $archJoins = ($userRole !== 'admin') ? ['task_user' => 'tasks.id = task_user.task_id'] : [];
        $archConds = ['tasks.archived_at IS NOT NULL' => null];
        if ($userRole !== 'admin') {
            $archConds['task_user.user_id'] = $userId;
        }

        $archivedTasks = $this->taskModel->getJoinedDataPagination(
            'tasks',
            $archJoins,
            'tasks.*',
            $archConds,
            'array',
            'tasks.id',
            ['tasks.archived_at' => 'DESC']
        );

        $data = [
            'user' => ['id' => $userId, 'name' => $userName, 'role' => $userRole],
            'stats' => $stats,
            'tasks' => $tasks,
            'archived_tasks' => $archivedTasks,
            'current_filter' => $filter
        ];

        return view('admin_dashboard', $data);
    }

    public function tasks()
    {
        log_message('debug', '===== admin_tasks() START =====');

        // ── 1. Auth ───────────────────────────────────────────────────────────
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId   = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        $userName = $this->session->get('name');

        if (!in_array($userRole, ['admin', 'head'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // ── 2. Resolve filter ─────────────────────────────────────────────────
        // Accept both ?filter= (filter buttons) and ?status= (stat card links)
        $filter = $this->request->getGet('filter')
            ?? $this->request->getGet('status')
            ?? 'all';

        // Normalise — only allow known values
        $allowed = ['all', 'pending', 'in_progress', 'completed', 'overdue', 'my_tasks', 'due_today'];
        if (!in_array($filter, $allowed)) {
            $filter = 'all';
        }

        log_message('debug', 'Active filter: ' . $filter);

        // ── 3. Build query conditions ─────────────────────────────────────────
        $joins      = [];
        $conditions = ['tasks.archived_at' => null]; // active tasks only

        // Role-based scope
        if ($userRole !== 'admin') {
            // head / user only sees tasks they are linked to
            $joins['task_user'] = 'tasks.id = task_user.task_id';
            $conditions['task_user.user_id'] = $userId;
        }

        // Filter-specific conditions
        switch ($filter) {
            case 'pending':
                $conditions['tasks.status'] = 'pending';
                break;

            case 'in_progress':
                $conditions['tasks.status'] = 'in_progress';
                break;

            case 'completed':
                $conditions['tasks.status'] = 'completed';
                break;

            case 'overdue':
                $conditions['tasks.status !='] = 'completed';
                $conditions['tasks.due_date <'] = date('Y-m-d');
                break;

            case 'my_tasks':
                // If admin hits my_tasks, we still need the join
                if ($userRole === 'admin') {
                    $joins['task_user'] = 'tasks.id = task_user.task_id';
                    $conditions['task_user.user_id'] = $userId;
                }
                $conditions['task_user.responsibility'] = 'owner';
                break;

            case 'due_today':
                $conditions['tasks.due_date'] = date('Y-m-d');
                break;

                // 'all' — no extra conditions
        }

        log_message('debug', 'Query conditions: ' . json_encode($conditions));

        // ── 4. Fetch tasks ────────────────────────────────────────────────────
        $tasks = $this->taskModel->getJoinedDataPagination(
            'tasks',
            $joins,
            'tasks.*',
            $conditions,
            'array',
            'tasks.id',                        // GROUP BY to deduplicate after joins
            ['tasks.created_at' => 'DESC']
        );

        log_message('debug', 'Tasks fetched: ' . count($tasks));

        // ── 5. Attach users + can_archive flag to every task ──────────────────
        foreach ($tasks as &$task) {

            // Assigned users with responsibility
            $task['users'] = $this->taskModel->getJoinedDataPagination(
                'task_user',
                ['users' => 'task_user.user_id = users.id'],
                'users.id, users.name, users.role, task_user.responsibility',
                ['task_user.task_id' => $task['id']]
            );

            // Can archive only if single user assigned
            $countData = $this->taskModel->get_specific_columns(
                'task_user',
                'COUNT(*) as total',
                ['task_id' => $task['id']]
            );
            $task['can_archive'] = (($countData[0]['total'] ?? 0) <= 1);

            // Is current user the owner of this task?
            $task['is_owner'] = false;
            foreach ($task['users'] as $u) {
                if ($u['id'] == $userId && $u['responsibility'] === 'owner') {
                    $task['is_owner'] = true;
                    break;
                }
            }
        }
        unset($task); // break reference

        // ── 6. Stats (reuse same calculateStats pattern) ─────────────────────
        $statsJoins   = ($userRole !== 'admin') ? ['task_user' => 'tasks.id = task_user.task_id'] : [];
        $statsBase    = ($userRole !== 'admin') ? ['task_user.user_id' => $userId] : [];

        $runCount = function ($extraCond) use ($statsJoins, $statsBase) {
            $res = $this->taskModel->getJoinedDataPagination(
                'tasks',
                $statsJoins,
                'COUNT(DISTINCT tasks.id) as total',
                array_merge($statsBase, $extraCond),
                'row'
            );
            return $res['total'] ?? 0;
        };

        $stats = [
            'total'       => $runCount(['tasks.archived_at' => null]),
            'pending'     => $runCount(['tasks.archived_at' => null, 'tasks.status' => 'pending']),
            'in_progress' => $runCount(['tasks.archived_at' => null, 'tasks.status' => 'in_progress']),
            'completed'   => $runCount(['tasks.archived_at' => null, 'tasks.status' => 'completed']),
            'overdue'     => $runCount(['tasks.archived_at' => null, 'tasks.status !=' => 'completed', 'tasks.due_date <' => date('Y-m-d')]),
            'archived'    => $runCount(['tasks.archived_at IS NOT NULL' => null]),
        ];

        log_message('debug', 'Stats: ' . json_encode($stats));
        log_message('debug', '===== admin_tasks() END =====');

        // ── 7. Pass to view ───────────────────────────────────────────────────
        $data = [
            'user' => [
                'id'   => $userId,
                'name' => $userName,
                'role' => $userRole
            ],
            'tasks'          => $tasks,
            'stats'          => $stats,
            'current_filter' => $filter,
        ];

        return view('admin_tasks', $data);
    }
    // private function fetchFilteredTasks($filter, $userId, $userRole, $limit = 0, $start = 0)
    // {
    //     $joins = [];
    //     $conditions = ['tasks.archived_at' => null];

    //     // Only join task_user for non-admins or for filters that require it
    //     if ($userRole !== 'admin') {
    //         $joins['task_user'] = 'tasks.id = task_user.task_id';
    //         $conditions['task_user.user_id'] = $userId;
    //     }

    //     // Apply filter-specific conditions
    //     switch ($filter) {
    //         case 'my_tasks':
    //             // Ensure task_user is joined
    //             if (!isset($joins['task_user'])) {
    //                 $joins['task_user'] = 'tasks.id = task_user.task_id';
    //             }
    //             $conditions['task_user.responsibility'] = 'owner';
    //             break;

    //         case 'overdue':
    //             $conditions['tasks.status !='] = 'completed';
    //             $conditions['tasks.due_date <'] = date('Y-m-d');
    //             break;

    //         case 'due_today':
    //             $conditions['tasks.due_date'] = date('Y-m-d');
    //             break;

    //         case 'all':
    //         default:
    //             // No extra conditions
    //             break;
    //     }

    //     // Always join users to get assigned names
    //     if (!isset($joins['users'])) {
    //         $joins['users'] = 'users.id = task_user.user_id';
    //     }

    //     // GROUP BY tasks.id to avoid duplicates if multiple users assigned
    //     $groupBy = 'tasks.id';

    //     // Fetch tasks using the common model function
    //     return $this->taskModel->getJoinedDataPagination(
    //         'tasks',
    //         $joins,
    //         'tasks.*, GROUP_CONCAT(users.name) as assigned_users',
    //         $conditions,
    //         'array',
    //         $groupBy,
    //         ['tasks.created_at' => 'DESC'],
    //         $limit,
    //         $start
    //     );
    // }
    private function fetchFilteredTasks($filter, $userId, $userRole, $limit = 0, $start = 0)
    {
        $joins = [];
        $conditions = ['tasks.archived_at' => null];

        // Join task_user if needed
        $joinTaskUser = false;

        if ($userRole !== 'admin' || $filter === 'my_tasks') {
            $joins['task_user'] = 'tasks.id = task_user.task_id';
            $conditions['task_user.user_id'] = $userId;
            $joinTaskUser = true;
        }

        // Filter-specific conditions
        switch ($filter) {
            case 'my_tasks':
                if (!$joinTaskUser) {
                    $joins['task_user'] = 'tasks.id = task_user.task_id';
                    $conditions['task_user.user_id'] = $userId;
                    $joinTaskUser = true;
                }
                $conditions['task_user.responsibility'] = 'owner';
                break;

            case 'overdue':
                $conditions['tasks.status !='] = 'completed';
                $conditions['tasks.due_date <'] = date('Y-m-d');
                break;

            case 'due_today':
                $conditions['tasks.due_date'] = date('Y-m-d');
                break;
        }

        // Only join users if task_user is joined
        if ($joinTaskUser) {
            $joins['users'] = 'users.id = task_user.user_id';
        }

        // GROUP BY tasks.id to avoid duplicates
        $groupBy = 'tasks.id';

        return $this->taskModel->getJoinedDataPagination(
            'tasks',
            $joins,
            $joinTaskUser ? 'tasks.*, GROUP_CONCAT(users.name) as assigned_users' : 'tasks.*',
            $conditions,
            'array',
            $groupBy,
            ['tasks.created_at' => 'DESC'],
            $limit,
            $start
        );
    }

    private function calculateStats($userId, $userRole)
    {
        $joins = ($userRole !== 'admin') ? ['task_user' => 'tasks.id = task_user.task_id'] : [];
        $baseCond = ($userRole !== 'admin') ? ['task_user.user_id' => $userId] : [];

        $runCount = function ($extraCond) use ($joins, $baseCond) {
            $res = $this->taskModel->getJoinedDataPagination(
                'tasks',
                $joins,
                'COUNT(DISTINCT tasks.id) as total',
                array_merge($baseCond, $extraCond),
                'row'
            );
            return $res['total'] ?? 0;
        };

        return [
            'total'       => $runCount(['tasks.archived_at' => null]),
            'pending'     => $runCount(['tasks.archived_at' => null, 'tasks.status' => 'pending']),
            'in_progress' => $runCount(['tasks.archived_at' => null, 'tasks.status' => 'in_progress']),
            'completed'   => $runCount(['tasks.archived_at' => null, 'tasks.status' => 'completed']),
            'overdue'     => $runCount(['tasks.archived_at' => null, 'tasks.status !=' => 'completed', 'tasks.due_date <' => date('Y-m-d')]),
            'archived'    => $runCount(['tasks.archived_at IS NOT NULL' => null])
        ];
    }

    /**
     * Task Assignment Page
     */
    // public function assign_users($taskId)
    // {
    //     if (!$this->session->get('isLoggedIn')) {
    //         return redirect()->to('/login');
    //     }

    //     $userId = $this->session->get('user_id');
    //     $userRole = $this->session->get('role');

    //     // Only admin and head can assign
    //     if (!in_array($userRole, ['admin', 'head'])) {
    //         return redirect()->to('/tasks/' . $taskId)->with('error', 'Access denied');
    //     }

    //     // Get task
    //     $task = $this->taskModel->getTaskWithUsers($taskId);

    //     if (!$task) {
    //         return redirect()->to('/admin/dashboard')->with('error', 'Task not found');
    //     }

    //     // Check if user is owner (for head role)
    //     if ($userRole === 'head') {
    //         $isOwner = $this->taskModel->isTaskOwner($taskId, $userId);
    //         if (!$isOwner) {
    //             return redirect()->to('/tasks/' . $taskId)->with('error', 'Only task owner can manage assignments');
    //         }
    //     }

    //     // Get available users (not yet assigned)
    //     $availableUsers = $this->taskModel->getAvailableUsersForTask($taskId);

    //     $data = [
    //         'user' => [
    //             'id' => $userId,
    //             'name' => $this->session->get('name'),
    //             'role' => $userRole
    //         ],
    //         'task' => $task,
    //         'available_users' => $availableUsers
    //     ];

    //     return view('task_assign', $data);
    // }
    public function assign_users($taskId)
    {
        log_message('debug', '===== assign_users() START =====');
        log_message('debug', 'Task ID: ' . $taskId);

        if (!$this->session->get('isLoggedIn')) {
            log_message('debug', 'User not logged in');
            return redirect()->to('/login');
        }

        $userId   = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        log_message('debug', 'User ID: ' . $userId);
        log_message('debug', 'User Role: ' . $userRole);

        // Only admin and head can assign
        if (!in_array($userRole, ['admin', 'head'])) {
            log_message('debug', 'Access denied for role: ' . $userRole);
            return redirect()->to('/tasks/' . $taskId)
                ->with('error', 'Access denied');
        }

        // ✅ 1. Get Task with Assigned Users
        log_message('debug', 'Fetching task with assigned users...');

        $task = $this->taskModel->getJoinedDataPagination(
            'tasks t',
            [
                'task_user tu' => 'tu.task_id = t.id',
                'users u'       => 'u.id = tu.user_id'
            ],
            't.*, GROUP_CONCAT(u.name) as assigned_users',
            ['t.id' => $taskId],
            'row',
            't.id'
        );

        log_message('debug', 'Task Query Result: ' . json_encode($task));

        if (!$task) {
            log_message('error', 'Task not found for ID: ' . $taskId);
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Task not found');
        }

        // ✅ 2. If role is head → check ownership
        if ($userRole === 'head') {

            log_message('debug', 'Checking ownership for head user...');

            $ownerCheck = $this->taskModel->get_specific_columns(
                'tasks',
                'id',
                [
                    'id' => $taskId,
                    'created_by' => $userId
                ],
                null,
                1
            );

            log_message('debug', 'Owner Check Result: ' . json_encode($ownerCheck));

            if (empty($ownerCheck)) {
                log_message('error', 'Head user is not owner of task');
                return redirect()->to('/tasks/' . $taskId)
                    ->with('error', 'Only task owner can manage assignments');
            }
        }

        // ✅ 3. Get Assigned User IDs
        log_message('debug', 'Fetching assigned user IDs...');

        $assignedUsers = $this->taskModel->get_specific_columns(
            'task_user',
            'user_id',
            ['task_id' => $taskId]
        );

        log_message('debug', 'Assigned Users Raw: ' . json_encode($assignedUsers));

        $assignedUserIds = array_column($assignedUsers, 'user_id');
        // After fetching assigned user IDs and task info
        $assigned_users_data = [];

        // Fetch full assigned user details
        if (!empty($assignedUserIds)) {
            $assigned_users_data = $this->taskModel->getJoinedDataPagination(
                'task_user tu',
                ['users u' => 'u.id = tu.user_id'],
                'u.id, u.name, u.email, u.role, tu.responsibility',
                ['tu.task_id' => $taskId],
                'array'
            );
        }

        log_message('debug', 'Assigned User IDs: ' . json_encode($assignedUserIds));

        // ✅ 4. Get Available Users (NOT assigned)
        if (!empty($assignedUserIds)) {

            log_message('debug', 'Fetching all users for filtering...');

            $availableUsers = $this->taskModel->getJoinedDataPagination(
                'users',
                [],
                'id, name, email',
                [],
                'array',
                '',
                ['name' => 'ASC'],
                0,
                0
            );

            log_message('debug', 'All Users Before Filter: ' . json_encode($availableUsers));

            $availableUsers = array_filter($availableUsers, function ($user) use ($assignedUserIds) {
                return !in_array($user['id'], $assignedUserIds);
            });

            log_message('debug', 'Available Users After Filter: ' . json_encode($availableUsers));
        } else {

            log_message('debug', 'No assigned users. Fetching all users directly...');

            $availableUsers = $this->taskModel->get_specific_columns(
                'users',
                'id, name, email',
                null,
                ['name' => 'ASC']
            );

            log_message('debug', 'Available Users: ' . json_encode($availableUsers));
        }

        log_message('debug', '===== assign_users() END SUCCESS =====');

        $data = [
            'user' => [
                'id'   => $userId,
                'name' => $this->session->get('name'),
                'role' => $userRole
            ],
            'task' => $task,
            'available_users' => $availableUsers,
            'assigned_users_data' => $assigned_users_data  // ✅ Pass this to the view
        ];


        return view('task_assign', $data);
    }


    /**
     * Assign User to Task (AJAX)
     */
    public function assign_user($taskId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Check permission
        if (!in_array($userRole, ['admin', 'head'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // For head, check if owner
        if ($userRole === 'head' && !$this->taskModel->isTaskOwner($taskId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only task owner can assign users']);
        }

        $data = $this->request->getJSON();
        $assignUserId = $data->user_id;
        $responsibility = $data->responsibility ?? 'contributor';

        $result = $this->taskModel->assignUserToTask($taskId, $assignUserId, $responsibility);

        if ($result) {
            // Log activity
            $assignedUser = $this->taskModel->get_specific_columns('users', 'name', ['id' => $assignUserId], null, 1);
            $userName = $assignedUser[0]['name'] ?? 'User';
            $this->taskModel->logActivity($taskId, $userId, 'user_assigned', "Assigned {$userName} to task");

            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'User already assigned or failed']);
    }

    /**
     * Remove User from Task (AJAX)
     */
    public function remove_user($taskId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Check permission
        if (!in_array($userRole, ['admin', 'head'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // For head, check if owner
        if ($userRole === 'head' && !$this->taskModel->isTaskOwner($taskId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only task owner can remove users']);
        }

        $data = $this->request->getJSON();
        $removeUserId = $data->user_id;

        $result = $this->taskModel->removeUserFromTask($taskId, $removeUserId);

        if ($result === false) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot remove task owner']);
        }

        if ($result > 0) {
            // Log activity
            $removedUser = $this->taskModel->get_specific_columns('users', 'name', ['id' => $removeUserId], null, 1);
            $userName = $removedUser[0]['name'] ?? 'User';
            $this->taskModel->logActivity($taskId, $userId, 'user_removed', "Removed {$userName} from task");

            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove user']);
    }

    /**
     * Create Task (Admin/Head version)
     */
    public function create_task()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Only admin and head
        if (!in_array($userRole, ['admin', 'head'])) {
            return redirect()->to('/dashboard');
        }

        // Get all users for assignment
        $availableUsers = $this->taskModel->get_specific_columns(
            'users',
            'id, name, email, role',
            null,
            ['name' => 'ASC']
        );

        $data = [
            'user' => [
                'id' => $userId,
                'name' => $this->session->get('name'),
                'role' => $userRole
            ],
            'available_users' => $availableUsers
        ];

        return view('task_form', $data);
    }

    /**
     * Save Task (Admin/Head version)
     */
    public function save_task()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        if (!in_array($userRole, ['admin', 'head'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Validate
        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'due_date' => 'required|valid_date',
            'priority' => 'required|in_list[low,medium,high]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Prepare task data
        $taskData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'status' => 'pending',
            'priority' => $this->request->getPost('priority'),
            'due_date' => $this->request->getPost('due_date'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Create task
        $taskId = $this->taskModel->createTask($taskData, $userId);

        if ($taskId) {
            // Assign additional users
            $assignedUsers = $this->request->getPost('assigned_users');
            if (!empty($assignedUsers) && is_array($assignedUsers)) {
                foreach ($assignedUsers as $assignedUserId) {
                    if ($assignedUserId != $userId) {
                        $this->taskModel->assignUserToTask($taskId, $assignedUserId, 'contributor');

                        // Log assignment
                        $assignedUser = $this->taskModel->get_specific_columns('users', 'name', ['id' => $assignedUserId], null, 1);
                        $userName = $assignedUser[0]['name'] ?? 'User';
                        $this->taskModel->logActivity($taskId, $userId, 'user_assigned', "Assigned {$userName} to task");
                    }
                }
            }

            return redirect()->to('/tasks/' . $taskId)->with('success', 'Task created and assigned successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create task');
    }

    /**
     * Archive Task (AJAX)
     */
    public function archive_task($taskId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Check permission
        if ($userRole === 'head' && !$this->taskModel->isTaskOwner($taskId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only owner can archive']);
        }

        $result = $this->taskModel->archiveTask($taskId, $userId);

        if ($result === 'updated') {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to archive']);
    }

    /**
     * Restore Task (AJAX)
     */
    public function restore_task($taskId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Check permission
        if ($userRole === 'head' && !$this->taskModel->isTaskOwner($taskId, $userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only owner can restore']);
        }

        $result = $this->taskModel->restoreTask($taskId, $userId);

        if ($result === 'updated') {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to restore']);
    }

    /**
     * Get Task Statistics (AJAX)
     */
    public function get_stats()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        $stats = $this->taskModel->getTaskStats($userId, $userRole);

        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Search Tasks (AJAX)
     */
    public function search_tasks()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        $searchTerm = $this->request->getGet('q');

        if (empty($searchTerm)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Search term required']);
        }

        $tasks = $this->taskModel->searchTasks($searchTerm, $userId, $userRole);

        return $this->response->setJSON([
            'success' => true,
            'tasks' => $tasks
        ]);
    }

    /**
     * Bulk Update Status (AJAX) - Admin only
     */
    public function bulk_update_status()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        if ($userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Admin only']);
        }

        $data = $this->request->getJSON();
        $taskIds = $data->task_ids ?? [];
        $newStatus = $data->status ?? '';

        if (empty($taskIds) || empty($newStatus)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        $updated = 0;
        foreach ($taskIds as $taskId) {
            $result = $this->taskModel->updateTaskStatus($taskId, $newStatus, $userId);
            if ($result === 'updated') {
                $updated++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Updated {$updated} tasks"
        ]);
    }

    /**
     * Export Tasks (CSV) - Admin/Head
     */
    public function export_tasks()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        if (!in_array($userRole, ['admin', 'head'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }

        // Get all tasks
        $tasks = $this->taskModel->getTasksForUser($userId, $userRole);

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="tasks_export_' . date('Y-m-d') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write headers
        fputcsv($output, ['ID', 'Title', 'Description', 'Status', 'Priority', 'Due Date', 'Created At', 'Owner']);

        // Write data
        foreach ($tasks as $task) {
            $taskWithUsers = $this->taskModel->getTaskWithUsers($task['id']);
            $ownerName = $taskWithUsers['owner']['name'] ?? 'N/A';

            fputcsv($output, [
                $task['id'],
                $task['title'],
                $task['description'] ?? '',
                $task['status'],
                $task['priority'],
                $task['due_date'],
                $task['created_at'],
                $ownerName
            ]);
        }

        fclose($output);
        exit;
    }
}
