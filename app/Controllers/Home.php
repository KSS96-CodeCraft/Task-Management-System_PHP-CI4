<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TaskModel;

class Home extends ResourceController
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
     * User Dashboard
     */

    public function index()
    {
        // Check if user is logged in
        if (!$this->session->get('isLoggedIn')) {
            return redirect('auth/login');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        $userName = $this->session->get('name');

        // Redirect admin/head to their dashboard
        if (in_array($userRole, ['admin', 'head'])) {
            return redirect()->to('/admin/dashboard');
        }

        // Get user statistics
        $stats = $this->taskModel->getTaskStats($userId, $userRole);

        // Get tasks assigned to user
        $assignedTasks = $this->taskModel->getAssignedTasks($userId);

        // Get tasks created by user
        $createdTasks = $this->taskModel->getCreatedTasks($userId);

        // Get all tasks (assigned + created)
        $allTasks = $this->taskModel->getTasksForUser($userId, $userRole);

        // Enhance tasks with user info
        foreach ($assignedTasks as &$task) {
            $task['users'] = $this->taskModel->getTaskUsers($task['id']);
            $task['is_owner'] = $task['responsibility'] === 'owner';
            $task['can_update_status'] = true;
            $task['can_archive'] = $task['responsibility'] === 'owner';
        }

        foreach ($createdTasks as &$task) {
            $task['users'] = $this->taskModel->getTaskUsers($task['id']);
            $task['is_owner'] = true;
            $task['can_archive'] = $this->taskModel->countTaskUsers($task['id']) <= 1;
        }

        foreach ($allTasks as &$task) {
            $task['is_owner'] = $this->taskModel->isTaskOwner($task['id'], $userId);
            $task['can_update_status'] = $this->taskModel->isUserAssigned($task['id'], $userId);
        }

        $data = [
            'user' => [
                'id' => $userId,
                'name' => $userName,
                'role' => $userRole
            ],
            'stats' => $stats,
            'assigned_tasks' => $assignedTasks,
            'created_tasks' => $createdTasks,
            'all_tasks' => $allTasks
        ];

        return view('user_dashboard', $data);
    }

    /**
     * Task List Page
     */

    public function tasks()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId   = $this->session->get('user_id');
        $userRole = $this->session->get('role');
        $status   = $this->request->getGet('status') ?? 'all';

        // Get logged-in user details
        $userResult = $this->taskModel->get_specific_columns(
            'users',
            'id,name,email,role',
            ['id' => $userId],
            null,
            1
        );

        $user = $userResult[0] ?? null;

        // ✅ Get all statistics from model
        $stats = $this->taskModel->getTaskStats($userId, $userRole);

        // ==============================
        // Pagination Setup
        // ==============================
        $limit = 10;
        $page  = (int) ($this->request->getGet('page') ?? 1);
        $start = ($page - 1) * $limit;

        $conditions = [
            'task_user.user_id' => $userId
        ];

        if ($status !== 'all') {
            $conditions['tasks.status'] = $status;
        }

        $joins = [
            'task_user' => 'task_user.task_id = tasks.id',
            'users'     => 'users.id = task_user.user_id'
        ];

        $tasks = $this->taskModel->getJoinedDataPagination(
            'tasks',
            $joins,
            'tasks.*, users.name, task_user.responsibility',
            $conditions,
            'array',
            '',
            ['tasks.id' => 'DESC'],
            $limit,
            $start
        );

        return view('user_dashboard', [
            'tasks'          => $tasks,
            'current_filter' => $status,
            'user'           => $user,
            'stats'          => $stats
        ]);
    }

    /**
     * Create Task Page
     */
    public function create_task()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Get all users if admin/head
        $availableUsers = [];
        if (in_array($userRole, ['admin', 'head'])) {
            $availableUsers = $this->taskModel->get_specific_columns(
                'users',
                'id, name, email, role',
                null,
                ['name' => 'ASC']
            );
        }

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
     * Save Task (Create)
     */
    public function save_task()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');

        // Validate input
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

        // Validate due date is not in the past
        if (strtotime($taskData['due_date']) < strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Due date cannot be in the past');
        }

        // Create task
        $taskId = $this->taskModel->createTask($taskData, $userId);

        if ($taskId) {
            // Assign additional users if admin/head
            $assignedUsers = $this->request->getPost('assigned_users');
            if (!empty($assignedUsers) && is_array($assignedUsers)) {
                foreach ($assignedUsers as $assignedUserId) {
                    if ($assignedUserId != $userId) {
                        $this->taskModel->assignUserToTask($taskId, $assignedUserId, 'contributor');
                    }
                }
            }

            return redirect()->to('/tasks/' . $taskId)->with('success', 'Task created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create task');
    }

    /**
     * View Task Details
     */
    public function view_task($taskId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Get task with users
        $task = $this->taskModel->getTaskWithUsers($taskId);

        if (!$task) {
            return redirect()->to('/dashboard')->with('error', 'Task not found');
        }

        // Check access permission
        if ($userRole !== 'admin') {
            $isAssigned = $this->taskModel->isUserAssigned($taskId, $userId);
            if (!$isAssigned) {
                return redirect()->to('/dashboard')->with('error', 'Access denied');
            }
        }

        // Get activity log
        $task['activity_log'] = $this->taskModel->getTaskActivities($taskId);

        // Set permissions
        $task['is_owner'] = $this->taskModel->isTaskOwner($taskId, $userId);
        $task['can_edit'] = $userRole === 'admin' || $task['is_owner'];
        $task['can_update_status'] = $this->taskModel->isUserAssigned($taskId, $userId);
        $task['can_delete'] = ($userRole === 'admin' || $task['is_owner']) && $this->taskModel->canDeleteTask($taskId);
        $task['can_archive'] = $userRole === 'admin' || $task['is_owner'];
        $task['can_restore'] = $userRole === 'admin' || $task['is_owner'];
        $task['has_elevated_permissions'] = in_array($userRole, ['admin', 'head']) || $task['is_owner'];

        $data = [
            'user' => [
                'id' => $userId,
                'name' => $this->session->get('name'),
                'role' => $userRole
            ],
            'task' => $task
        ];

        return view('task_detail', $data);
    }

    /**
     * Edit Task Page
     */
    public function edit_task($taskId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Get task
        $task = $this->taskModel->getTaskWithUsers($taskId);

        if (!$task) {
            return redirect()->to('/dashboard')->with('error', 'Task not found');
        }

        // Check edit permission
        $isOwner = $this->taskModel->isTaskOwner($taskId, $userId);
        if ($userRole !== 'admin' && !$isOwner) {
            return redirect()->to('/tasks/' . $taskId)->with('error', 'Only task owner can edit');
        }

        // Get available users
        $availableUsers = [];
        if (in_array($userRole, ['admin', 'head'])) {
            $availableUsers = $this->taskModel->getAvailableUsersForTask($taskId);
        }

        $task['is_owner'] = $isOwner;
        $task['is_creator'] = $isOwner;
        $task['can_archive'] = $this->taskModel->canDeleteTask($taskId);
        $task['can_delete'] = $this->taskModel->canDeleteTask($taskId);

        $data = [
            'user' => [
                'id' => $userId,
                'name' => $this->session->get('name'),
                'role' => $userRole
            ],
            'task' => $task,
            'available_users' => $availableUsers
        ];

        return view('task_form', $data);
    }

    /**
     * Update Task
     */
    public function update_task($taskId)
    {
        log_message('debug', '==== update_task() STARTED ====');
        log_message('debug', 'Task ID: ' . $taskId);

        if (!$this->session->get('isLoggedIn')) {
            log_message('debug', 'User not logged in');
            return redirect()->to('/login');
        }

        $userId   = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        log_message('debug', 'User ID: ' . $userId);
        log_message('debug', 'User Role: ' . $userRole);

        // Check permission
        $isOwner = $this->taskModel->isTaskOwner($taskId, $userId);
        log_message('debug', 'Is Owner: ' . ($isOwner ? 'YES' : 'NO'));

        if ($userRole !== 'admin' && !$isOwner) {
            log_message('error', 'Access denied for user ' . $userId);
            return redirect()->to('/tasks/' . $taskId)
                ->with('error', 'Access denied');
        }

        // Get current task
        $currentTask = $this->taskModel->getTaskWithUsers($taskId);

        if (!$currentTask) {
            log_message('error', 'Task not found for ID: ' . $taskId);
            return redirect()->back()
                ->with('error', 'Task not found');
        }

        log_message('debug', 'Current Task: ' . json_encode($currentTask));

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'title'       => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[1000]',
            'due_date'    => 'required|valid_date',
            'priority'    => 'required|in_list[low,medium,high]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            log_message('error', 'Validation failed: ' . json_encode($validation->getErrors()));
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $newDueDate = $this->request->getPost('due_date');
        log_message('debug', 'New Due Date: ' . $newDueDate);

        // Check due date change permission
        if ($newDueDate !== $currentTask['due_date'] && !$isOwner && $userRole !== 'admin') {
            log_message('error', 'Due date change not allowed');
            return redirect()->back()
                ->with('error', 'Only task owner can change due date');
        }

        // Prepare update data
        $updateData = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'priority'    => $this->request->getPost('priority'),
            'due_date'    => $newDueDate,
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        log_message('debug', 'Update Data: ' . json_encode($updateData));

        // Update status if provided
        if ($this->request->getPost('status')) {
            $updateData['status'] = $this->request->getPost('status');

            if ($updateData['status'] === 'completed') {
                $updateData['completed_at'] = date('Y-m-d H:i:s');
            }

            log_message('debug', 'Status Updated To: ' . $updateData['status']);
        }

        // Update task
        $result = $this->taskModel->updateRecord('tasks', $updateData, ['id' => $taskId]);

        log_message('debug', 'Update Result: ' . print_r($result, true));

        if ($result === 'updated') {

            $assignedUsers = $this->request->getPost('assigned_users');
            log_message('debug', 'Assigned Users: ' . json_encode($assignedUsers));

            if (!empty($assignedUsers) && is_array($assignedUsers)) {
                foreach ($assignedUsers as $assignedUserId) {
                    if ($assignedUserId != $userId) {
                        log_message('debug', 'Assigning User: ' . $assignedUserId);
                        $this->taskModel->assignUserToTask($taskId, $assignedUserId, 'contributor');
                    }
                }
            }

            log_message('debug', 'Logging activity...');
            $this->taskModel->logActivity($taskId, $userId, 'updated', 'Task updated');

            log_message('debug', '==== update_task() SUCCESS ====');

            return redirect()->to('/tasks/' . $taskId)
                ->with('success', 'Task updated successfully');
        }

        log_message('error', 'Update failed for Task ID: ' . $taskId);

        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update task');
    }


    /**
     * Update Task Status (AJAX)
     */
    public function update_status($taskId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Check permission
        if (!$this->taskModel->isUserAssigned($taskId, $userId) && $userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $newStatus = $this->request->getJSON()->status;

        // Validate status
        if (!in_array($newStatus, ['pending', 'in_progress', 'completed'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status']);
        }

        // Check completion rules
        if ($newStatus === 'completed') {
            $task = $this->taskModel->getTaskWithUsers($taskId);
            $isOwner = $this->taskModel->isTaskOwner($taskId, $userId);
            $hasElevatedPermissions = in_array($userRole, ['admin', 'head']) || $isOwner;

            if (!$hasElevatedPermissions && strtotime($task['due_date']) > time()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot complete task before due date'
                ]);
            }
        }

        // Update status
        $result = $this->taskModel->updateTaskStatus($taskId, $newStatus, $userId);

        if ($result === 'updated') {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
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
        $isOwner = $this->taskModel->isTaskOwner($taskId, $userId);
        if ($userRole !== 'admin' && !$isOwner) {
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
        $isOwner = $this->taskModel->isTaskOwner($taskId, $userId);
        if ($userRole !== 'admin' && !$isOwner) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only owner can restore']);
        }

        $result = $this->taskModel->restoreTask($taskId, $userId);

        if ($result === 'updated') {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to restore']);
    }

    /**
     * Delete Task (AJAX)
     */
    public function delete_task($taskId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = $this->session->get('user_id');
        $userRole = $this->session->get('role');

        // Check permission
        $isOwner = $this->taskModel->isTaskOwner($taskId, $userId);
        if ($userRole !== 'admin' && !$isOwner) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        // Check if can delete
        if (!$this->taskModel->canDeleteTask($taskId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete task with multiple users'
            ]);
        }

        $result = $this->taskModel->deleteTask($taskId);

        if ($result > 0) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete']);
    }
}
