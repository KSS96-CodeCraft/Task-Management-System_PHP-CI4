<?php

/**
 * Task Management System - Routes Configuration
 * File: app/Config/Routes.php
 */

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// Authentication Routes
// ============================================

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::store');
$routes->get('logout', 'Auth::logout');

// ============================================
// Dashboard Routes
// ============================================



// User Dashboard (Regular users)

$routes->group('', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', 'Home::index');
    $routes->get('dashboard', 'Home::index');
});




// // $routes->get('/', 'Home::index', ['filter' => 'auth']);
// $routes->get('dashboard', 'Home::index', ['filter' => 'auth']);

// // Admin/Head Dashboard
$routes->get('admin/dashboard', 'Admin::dashboard', ['filter' => 'auth:admin,head']);

// // ============================================
// // Task Management Routes (User)
// // ============================================

$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Task List
    $routes->get('home/tasks', 'Home::tasks');
    $routes->get('admin/tasks', 'Admin::tasks');
    // Create Task
    $routes->get('tasks/create', 'Home::create_task');
    $routes->post('home/save_task', 'Home::save_task');

    // View Task
    $routes->get('tasks/(:num)', 'Home::view_task/$1');

    // Edit Task
    $routes->get('tasks/(:num)/edit', 'Home::edit_task/$1');
    $routes->post('tasks/(:num)/update', 'Home::update_task/$1');

    // Task Actions (AJAX)
    $routes->post('tasks/(:num)/status', 'Home::update_status/$1');
    $routes->post('tasks/(:num)/archive', 'Home::archive_task/$1');
    $routes->post('tasks/(:num)/restore', 'Home::restore_task/$1');
    $routes->delete('tasks/(:num)', 'Home::delete_task/$1');
});

// // ============================================
// // Task Management Routes (Admin/Head)
// // ============================================


$routes->group('admin', ['filter' => 'auth:admin,head'], function ($routes) {
    // Create Task
    $routes->get('tasks/create', 'Admin::create_task');
    $routes->post('tasks/save', 'Admin::save_task');

    // Task Assignment
    $routes->get('tasks/(:num)/assign', 'Admin::assign_users/$1');
    $routes->post('tasks/(:num)/assign-user', 'Admin::assign_user/$1');
    $routes->post('tasks/(:num)/remove-user', 'Admin::remove_user/$1');

    // Task Actions
    $routes->post('tasks/(:num)/archive', 'Admin::archive_task/$1');
    $routes->post('tasks/(:num)/restore', 'Admin::restore_task/$1');

    // Statistics
    $routes->get('stats', 'Admin::get_stats');

    // Search
    $routes->get('search-tasks', 'Admin::search_tasks');

    // Export
    $routes->get('tasks/export', 'Admin::export_tasks');
});

// // ============================================
// // Admin Only Routes
// // ============================================

$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {

    // Bulk Operations
    $routes->post('tasks/bulk-update-status', 'Admin::bulk_update_status');

    // User Management (if needed)
    $routes->get('users', 'Admin::manage_users');
    $routes->get('users/(:num)/edit', 'Admin::edit_user/$1');
    $routes->post('users/(:num)/update', 'Admin::update_user/$1');
    $routes->delete('users/(:num)', 'Admin::delete_user/$1');
});

// // ============================================
// // API Routes (Optional - for AJAX/Mobile)
// // ============================================

$routes->group('api', ['filter' => 'auth'], function ($routes) {

    // Tasks
    $routes->get('tasks', 'Api\TaskController::index');
    $routes->get('tasks/(:num)', 'Api\TaskController::show/$1');
    $routes->post('tasks', 'Api\TaskController::create');
    $routes->put('tasks/(:num)', 'Api\TaskController::update/$1');
    $routes->delete('tasks/(:num)', 'Api\TaskController::delete/$1');

    // Task Status
    $routes->patch('tasks/(:num)/status', 'Api\TaskController::updateStatus/$1');

    // Task Assignment
    $routes->post('tasks/(:num)/assign', 'Api\TaskController::assignUser/$1');
    $routes->delete('tasks/(:num)/assign/(:num)', 'Api\TaskController::removeUser/$1/$2');

    // Statistics
    $routes->get('stats', 'Api\TaskController::getStats');

    // Search
    $routes->get('search', 'Api\TaskController::search');
});

// // ============================================
// // Error Pages
// // ============================================

$routes->set404Override(function () {
    return view('errors/html/error_404');
});

// // ============================================
// // CLI Routes (For Maintenance Tasks)
// // ============================================

// // Run via: php spark tasks:archive-old
$routes->cli('tasks/archive-old', 'Cli\TaskMaintenance::archiveOld');

// // Run via: php spark tasks:send-reminders
$routes->cli('tasks/send-reminders', 'Cli\TaskMaintenance::sendReminders');
