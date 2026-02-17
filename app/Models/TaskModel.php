<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'archived_at',
        'completed_at',
        'created_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Base CRUD methods from your template
    public function insert_to_tb($tableName, $data)
    {
        $this->db->table($tableName)->insert($data);
        return $this->db->insertID();
    }

    public function get_specific_columns($table_name, $columns, $where_condition = null, $orderBy = null, $limit = null)
    {
        $builder = $this->db->table($table_name)->select($columns);

        if ($where_condition) {
            $builder->where($where_condition);
        }

        if ($orderBy) {
            foreach ($orderBy as $col => $dir) {
                $builder->orderBy($col, $dir);
            }
        }

        if ($limit) {
            $builder->limit($limit);
        }

        $query = $builder->get();

        if ($query === false) {
            return [];
        }

        return $query->getResultArray();
    }

    public function updateRecord($table, $data, $conditions)
    {
        $updated = $this->db->table($table)
            ->set($data)
            ->where($conditions)
            ->update();

        if ($updated) {
            return "updated";
        }

        return "not updated";
    }

    public function deleteRecord(string $table, array $conditions): int
    {
        $this->db->table($table)->where($conditions)->delete();
        return $this->db->affectedRows();
    }

    public function getJoinedDataPagination(
        $mainTable,
        $joins = [],
        $columns = '*',
        $conditions = [],
        $returnType = 'array',
        $groupBy = '',
        $orderBy = [],
        $limit = 0,
        $start = 0
    ) {
        $builder = $this->db->table($mainTable)->select($columns);

        if (!empty($joins)) {
            foreach ($joins as $table => $condition) {
                $builder->join($table, $condition, 'left');
            }
        }

        if (!empty($conditions)) {
            $builder->where($conditions);
        }

        if (!empty($groupBy)) {
            if (is_array($groupBy)) {
                $builder->groupBy(implode(', ', $groupBy));
            } else {
                $builder->groupBy($groupBy);
            }
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $column => $direction) {
                $builder->orderBy($column, $direction);
            }
        }

        if ($limit > 0) {
            $builder->limit($limit, $start);
        }

        $query = $builder->get();

        if ($returnType === 'row') {
            return $query->getRowArray();
        } else {
            return $query->getResultArray();
        }
    }

    // ==================== TASK-SPECIFIC METHODS ====================

    /**
     * Create a new task with owner assignment
     */
    public function createTask($taskData, $userId)
    {
        // Insert task
        $taskData['created_by'] = $userId;
        $taskId = $this->insert_to_tb('tasks', $taskData);

        if ($taskId) {
            // Assign creator as owner
            $this->insert_to_tb('task_user', [
                'task_id' => $taskId,
                'user_id' => $userId,
                'responsibility' => 'owner',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Log activity
            $this->logActivity($taskId, $userId, 'created', 'Task created');
        }

        return $taskId;
    }

    /**
     * Get task with all assigned users
     */
    public function getTaskWithUsers($taskId)
    {
        $task = $this->getJoinedDataPagination(
            'tasks',
            [],
            'tasks.*',
            ['tasks.id' => $taskId],
            'row'
        );

        if ($task) {
            // Get assigned users
            $task['users'] = $this->getJoinedDataPagination(
                'task_user',
                ['users' => 'task_user.user_id = users.id'],
                'users.id, users.name, users.email, users.role, task_user.responsibility',
                ['task_user.task_id' => $taskId]
            );

            // Get owner specifically
            $owner = $this->getJoinedDataPagination(
                'task_user',
                ['users' => 'task_user.user_id = users.id'],
                'users.id, users.name, users.email',
                [
                    'task_user.task_id' => $taskId,
                    'task_user.responsibility' => 'owner'
                ],
                'row'
            );
            $task['owner'] = $owner;
        }

        return $task;
    }

    /**
     * Get all active tasks (not archived) for a user based on role
     */
    public function getTasksForUser($userId, $userRole, $filters = [])
    {
        $mainTable = 'tasks';
        $joins = [];
        $conditions = ['tasks.archived_at' => null]; // Only active tasks

        // Role-based filtering
        if ($userRole === 'admin') {
            // Admin sees all tasks
        } elseif ($userRole === 'head') {
            // Head sees tasks they created
            $joins['task_user'] = 'tasks.id = task_user.task_id';
            $conditions['task_user.user_id'] = $userId;
            $conditions['task_user.responsibility'] = 'owner';
        } else {
            // Regular user sees tasks assigned to them
            $joins['task_user'] = 'tasks.id = task_user.task_id';
            $conditions['task_user.user_id'] = $userId;
        }

        // Apply additional filters
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $conditions['tasks.status'] = $filters['status'];
        }

        if (isset($filters['priority'])) {
            $conditions['tasks.priority'] = $filters['priority'];
        }

        // Get tasks
        $tasks = $this->getJoinedDataPagination(
            $mainTable,
            $joins,
            'tasks.*',
            $conditions,
            'array',
            'tasks.id', // 👈 group by primary key instead of DISTINCT
            ['tasks.created_at' => 'DESC']
        );

        // Attach users to each task
        foreach ($tasks as &$task) {
            $task['users'] = $this->getTaskUsers($task['id']);
        }

        return $tasks;
    }

    /**
     * Get tasks assigned to a specific user
     */
    public function getAssignedTasks($userId)
    {
        return $this->getJoinedDataPagination(
            'tasks',
            ['task_user' => 'tasks.id = task_user.task_id'],
            'tasks.*, task_user.responsibility',
            [
                'task_user.user_id' => $userId,
                'tasks.archived_at' => null
            ],
            'array',
            '',
            ['tasks.due_date' => 'ASC']
        );
    }

    /**
     * Get tasks created by a specific user
     */
    public function getCreatedTasks($userId)
    {
        return $this->getJoinedDataPagination(
            'tasks',
            ['task_user' => 'tasks.id = task_user.task_id'],
            'tasks.*',
            [
                'task_user.user_id' => $userId,
                'task_user.responsibility' => 'owner',
                'tasks.archived_at' => null
            ],
            'array',
            '',
            ['tasks.created_at' => 'DESC']
        );
    }

    /**
     * Get users assigned to a task
     */
    public function getTaskUsers($taskId)
    {
        return $this->getJoinedDataPagination(
            'task_user',
            ['users' => 'task_user.user_id = users.id'],
            'users.id, users.name, users.email, users.role, task_user.responsibility',
            ['task_user.task_id' => $taskId]
        );
    }

    /**
     * Assign user to task
     */
    public function assignUserToTask($taskId, $userId, $responsibility = 'contributor')
    {
        // Check if already assigned
        $existing = $this->get_specific_columns(
            'task_user',
            '*',
            ['task_id' => $taskId, 'user_id' => $userId],
            null,
            1
        );

        if (empty($existing)) {
            return $this->insert_to_tb('task_user', [
                'task_id' => $taskId,
                'user_id' => $userId,
                'responsibility' => $responsibility,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return false; // Already assigned
    }

    /**
     * Remove user from task (except owner)
     */
    public function removeUserFromTask($taskId, $userId)
    {
        // Check if user is owner
        $user = $this->get_specific_columns(
            'task_user',
            'responsibility',
            ['task_id' => $taskId, 'user_id' => $userId],
            null,
            1
        );

        if (!empty($user) && $user[0]['responsibility'] === 'owner') {
            return false; // Cannot remove owner
        }

        return $this->deleteRecord('task_user', [
            'task_id' => $taskId,
            'user_id' => $userId
        ]);
    }

    /**
     * Update task status
     */
    public function updateTaskStatus($taskId, $status, $userId)
    {
        $data = ['status' => $status];

        if ($status === 'completed') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        $result = $this->updateRecord('tasks', $data, ['id' => $taskId]);

        if ($result === 'updated') {
            $this->logActivity($taskId, $userId, 'status_changed', "Status changed to {$status}");
        }

        return $result;
    }

    /**
     * Archive task
     */
    public function archiveTask($taskId, $userId)
    {
        $result = $this->updateRecord('tasks', [
            'archived_at' => date('Y-m-d H:i:s')
        ], ['id' => $taskId]);

        if ($result === 'updated') {
            $this->logActivity($taskId, $userId, 'archived', 'Task archived');
        }

        return $result;
    }

    /**
     * Restore archived task
     */
    public function restoreTask($taskId, $userId)
    {
        $result = $this->updateRecord('tasks', [
            'archived_at' => null
        ], ['id' => $taskId]);

        if ($result === 'updated') {
            $this->logActivity($taskId, $userId, 'restored', 'Task restored from archive');
        }

        return $result;
    }

    /**
     * Get archived tasks
     */
    public function getArchivedTasks($userId, $userRole)
    {
        $joins = [];
        $conditions = ['tasks.archived_at IS NOT NULL' => null];

        if ($userRole !== 'admin') {
            $joins['task_user'] = 'tasks.id = task_user.task_id';
            $conditions['task_user.user_id'] = $userId;
        }

        return $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'DISTINCT tasks.*',
            $conditions,
            'array',
            '',
            ['tasks.archived_at' => 'DESC']
        );
    }

    /**
     * Check if user is task owner
     */
    public function isTaskOwner($taskId, $userId)
    {
        $result = $this->get_specific_columns(
            'task_user',
            'id',
            [
                'task_id' => $taskId,
                'user_id' => $userId,
                'responsibility' => 'owner'
            ],
            null,
            1
        );

        return !empty($result);
    }

    /**
     * Check if user is assigned to task
     */
    public function isUserAssigned($taskId, $userId)
    {
        $result = $this->get_specific_columns(
            'task_user',
            'id',
            [
                'task_id' => $taskId,
                'user_id' => $userId
            ],
            null,
            1
        );

        return !empty($result);
    }

    /**
     * Count assigned users for a task
     */
    public function countTaskUsers($taskId)
    {
        $users = $this->get_specific_columns(
            'task_user',
            'COUNT(*) as total',
            ['task_id' => $taskId]
        );

        return $users[0]['total'] ?? 0;
    }

    /**
     * Check if task can be deleted (only if single user)
     */
    public function canDeleteTask($taskId)
    {
        return $this->countTaskUsers($taskId) <= 1;
    }

    /**
     * Delete task completely
     */
    public function deleteTask($taskId)
    {
        // Delete task-user relationships first
        $this->deleteRecord('task_user', ['task_id' => $taskId]);

        // Delete activity logs
        $this->deleteRecord('task_activities', ['task_id' => $taskId]);

        // Delete task
        return $this->deleteRecord('tasks', ['id' => $taskId]);
    }

    /**
     * Get task statistics for dashboard
     */
    public function getTaskStats($userId, $userRole)
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'archived' => 0,
            'overdue' => 0,
            'assigned_to_me' => 0,
            'my_tasks' => 0
        ];

        // Base conditions
        $baseConditions = [];
        $joins = [];

        if ($userRole !== 'admin') {
            $joins['task_user'] = 'tasks.id = task_user.task_id';
            $baseConditions['task_user.user_id'] = $userId;
        }

        // Total tasks (active)
        $conditions = array_merge($baseConditions, ['tasks.archived_at' => null]);
        $total = $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'COUNT(DISTINCT tasks.id) as count',
            $conditions,
            'row'
        );
        $stats['total'] = $total['count'] ?? 0;

        // Pending tasks
        $conditions['tasks.status'] = 'pending';
        $pending = $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'COUNT(DISTINCT tasks.id) as count',
            $conditions,
            'row'
        );
        $stats['pending'] = $pending['count'] ?? 0;

        // In Progress tasks
        $conditions['tasks.status'] = 'in_progress';
        $inProgress = $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'COUNT(DISTINCT tasks.id) as count',
            $conditions,
            'row'
        );
        $stats['in_progress'] = $inProgress['count'] ?? 0;

        // Completed tasks
        $conditions['tasks.status'] = 'completed';
        $completed = $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'COUNT(DISTINCT tasks.id) as count',
            $conditions,
            'row'
        );
        $stats['completed'] = $completed['count'] ?? 0;

        // Archived tasks
        unset($conditions['tasks.status']);
        $conditions['tasks.archived_at IS NOT NULL'] = null;
        unset($conditions['tasks.archived_at']);
        $archived = $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'COUNT(DISTINCT tasks.id) as count',
            $conditions,
            'row'
        );
        $stats['archived'] = $archived['count'] ?? 0;

        // Overdue tasks
        $overdueConditions = array_merge($baseConditions, [
            'tasks.archived_at' => null,
            'tasks.status !=' => 'completed',
            'tasks.due_date <' => date('Y-m-d')
        ]);
        $overdue = $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'COUNT(DISTINCT tasks.id) as count',
            $overdueConditions,
            'row'
        );
        $stats['overdue'] = $overdue['count'] ?? 0;

        // User-specific stats (for regular users)
        if ($userRole === 'user') {
            // Assigned to me
            $assignedConditions = [
                'task_user.user_id' => $userId,
                'tasks.archived_at' => null
            ];
            $assigned = $this->getJoinedDataPagination(
                'tasks',
                ['task_user' => 'tasks.id = task_user.task_id'],
                'COUNT(DISTINCT tasks.id) as count',
                $assignedConditions,
                'row'
            );
            $stats['assigned_to_me'] = $assigned['count'] ?? 0;

            // My tasks (created by me)
            $myConditions = [
                'task_user.user_id' => $userId,
                'task_user.responsibility' => 'owner',
                'tasks.archived_at' => null
            ];
            $myTasks = $this->getJoinedDataPagination(
                'tasks',
                ['task_user' => 'tasks.id = task_user.task_id'],
                'COUNT(DISTINCT tasks.id) as count',
                $myConditions,
                'row'
            );
            $stats['my_tasks'] = $myTasks['count'] ?? 0;
        }

        return $stats;
    }

    /**
     * Get overdue tasks
     */
    public function getOverdueTasks($userId, $userRole)
    {
        $joins = [];
        $conditions = [
            'tasks.archived_at' => null,
            'tasks.status !=' => 'completed',
            'tasks.due_date <' => date('Y-m-d')
        ];

        if ($userRole !== 'admin') {
            $joins['task_user'] = 'tasks.id = task_user.task_id';
            $conditions['task_user.user_id'] = $userId;
        }

        return $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'DISTINCT tasks.*',
            $conditions,
            'array',
            '',
            ['tasks.due_date' => 'ASC']
        );
    }

    /**
     * Get tasks due today
     */
    public function getTasksDueToday($userId, $userRole)
    {
        $joins = [];
        $conditions = [
            'tasks.archived_at' => null,
            'tasks.due_date' => date('Y-m-d')
        ];

        if ($userRole !== 'admin') {
            $joins['task_user'] = 'tasks.id = task_user.task_id';
            $conditions['task_user.user_id'] = $userId;
        }

        return $this->getJoinedDataPagination(
            'tasks',
            $joins,
            'DISTINCT tasks.*',
            $conditions,
            'array',
            '',
            ['tasks.priority' => 'DESC']
        );
    }

    /**
     * Log task activity
     */
    public function logActivity($taskId, $userId, $action, $description)
    {
        return $this->insert_to_tb('task_activities', [
            'task_id' => $taskId,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get task activity log
     */
    public function getTaskActivities($taskId)
    {
        return $this->getJoinedDataPagination(
            'task_activities',
            ['users' => 'task_activities.user_id = users.id'],
            'task_activities.*, users.name as user_name',
            ['task_activities.task_id' => $taskId],
            'array',
            '',
            ['task_activities.created_at' => 'DESC']
        );
    }

    /**
     * Search tasks by title or description
     */
    public function searchTasks($searchTerm, $userId, $userRole)
    {
        $builder = $this->db->table('tasks');

        if ($userRole !== 'admin') {
            $builder->join('task_user', 'tasks.id = task_user.task_id', 'left');
            $builder->where('task_user.user_id', $userId);
        }

        $builder->where('tasks.archived_at', null);
        $builder->groupStart()
            ->like('tasks.title', $searchTerm)
            ->orLike('tasks.description', $searchTerm)
            ->groupEnd();

        return $builder->get()->getResultArray();
    }

    /**
     * Get available users for assignment (exclude already assigned)
     */
    public function getAvailableUsersForTask($taskId)
    {
        $assignedUserIds = array_column($this->getTaskUsers($taskId), 'id');

        $builder = $this->db->table('users');
        $builder->select('id, name, email, role');

        if (!empty($assignedUserIds)) {
            $builder->whereNotIn('id', $assignedUserIds);
        }

        $builder->orderBy('name', 'ASC');

        return $builder->get()->getResultArray();
    }
}
