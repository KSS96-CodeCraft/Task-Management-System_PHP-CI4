<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController; // or BaseController if not using REST

use App\Models\TaskModel;


class Auth extends ResourceController
{
    protected $model;
    protected $db;
    public function __construct()
    {

        $this->taskModel = new TaskModel();
        $this->session = \Config\Services::session();
    }
    /**
     * Login Page
     */
    public function login()
    {
        // // If already logged in, redirect to dashboard
        if ($this->session->get('isLoggedIn')) {
            $role = $this->session->get('role');
            if (in_array($role, ['admin', 'head'])) {
                return redirect()->to('/admin/dashboard');
            }
            return redirect()->to('/dashboard');
        }

        return view('login');
    }

    /**
     * Process Login
     */
    public function authenticate()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Get user from database
        $user = $this->taskModel->get_specific_columns(
            'users',
            'id, name, email, password, role',
            ['email' => $email],
            null,
            1
        );

        // Check if user exists and password is correct
        if (!empty($user) && password_verify($password, $user[0]['password'])) {
            // Set session data
            $sessionData = [
                'user_id' => $user[0]['id'],
                'name' => $user[0]['name'],
                'email' => $user[0]['email'],
                'role' => $user[0]['role'],
                'isLoggedIn' => true
            ];

            $this->session->set($sessionData);

            // Log login activity (optional)
            $this->logLoginActivity($user[0]['id']);

            // Redirect based on role
            if (in_array($user[0]['role'], ['admin', 'head'])) {
                return redirect()->to('/admin/dashboard')->with('success', 'Welcome back, ' . $user[0]['name'] . '!');
            }

            return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user[0]['name'] . '!');
        }

        // Authentication failed
        return redirect()->back()->withInput()->with('error', 'Invalid email or password');
    }

    /**
     * Registration Page
     */
    public function register()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('register');
    }

    /**
     * Process Registration
     */
    public function store()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ], [
            'email' => [
                'is_unique' => 'This email is already registered'
            ],
            'confirm_password' => [
                'matches' => 'Passwords do not match'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Prepare user data
        $userData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role' => 'user', // Default role
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Insert user
        $userId = $this->taskModel->insert_to_tb('users', $userData);

        if ($userId) {
            // Auto-login after registration
            $this->session->set([
                'user_id' => $userId,
                'name' => $userData['name'],
                'email' => $userData['email'],
                'role' => $userData['role'],
                'isLoggedIn' => true
            ]);

            return redirect()->to('/dashboard')->with('success', 'Registration successful! Welcome to Task Management System.');
        }

        return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Log logout activity (optional)
        if ($this->session->get('isLoggedIn')) {
            $this->logLogoutActivity($this->session->get('user_id'));
        }

        // Destroy session
        $this->session->destroy();

        return redirect()->to('/login')->with('success', 'You have been logged out successfully');
    }

    /**
     * Forgot Password Page
     */
    public function forgot_password()
    {
        return view('forgot_password');
    }

    /**
     * Send Password Reset Email
     */
    public function send_reset_link()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');

        // Check if user exists
        $user = $this->taskModel->get_specific_columns(
            'users',
            'id, name, email',
            ['email' => $email],
            null,
            1
        );

        if (empty($user)) {
            // Don't reveal if email exists or not (security best practice)
            return redirect()->back()->with('success', 'If your email is registered, you will receive a password reset link.');
        }

        // Generate reset token (you'll need to create a password_resets table)
        $token = bin2hex(random_bytes(32));
        $resetData = [
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Save token to database (requires password_resets table)
        // $this->taskModel->insert_to_tb('password_resets', $resetData);

        // Send email (you'll need to configure email settings)
        // $this->sendPasswordResetEmail($email, $token);

        return redirect()->back()->with('success', 'If your email is registered, you will receive a password reset link.');
    }

    /**
     * Reset Password Page
     */
    public function reset_password($token)
    {
        // Verify token exists and is valid
        // $reset = $this->taskModel->get_specific_columns('password_resets', '*', ['token' => $token]);

        // For now, just show the form
        return view('reset_password', ['token' => $token]);
    }

    /**
     * Process Password Reset
     */
    public function update_password()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'token' => 'required',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Verify token and get email
        // $reset = $this->taskModel->get_specific_columns('password_resets', 'email', ['token' => $token]);

        // Update password
        // $this->taskModel->updateRecord('users', 
        //     ['password' => password_hash($password, PASSWORD_BCRYPT)],
        //     ['email' => $reset[0]['email']]
        // );

        // Delete used token
        // $this->taskModel->deleteRecord('password_resets', ['token' => $token]);

        return redirect()->to('/login')->with('success', 'Password reset successful! Please login with your new password.');
    }

    /**
     * Change Password (for logged-in users)
     */
    public function change_password()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('change_password');
    }

    /**
     * Process Password Change
     */
    public function update_my_password()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_new_password' => 'required|matches[new_password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors());
        }

        $userId = $this->session->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // Get current user password
        $user = $this->taskModel->get_specific_columns(
            'users',
            'password',
            ['id' => $userId],
            null,
            1
        );

        // Verify current password
        if (!password_verify($currentPassword, $user[0]['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect');
        }

        // Update password
        $result = $this->taskModel->updateRecord(
            'users',
            ['password' => password_hash($newPassword, PASSWORD_BCRYPT)],
            ['id' => $userId]
        );

        if ($result === 'updated') {
            return redirect()->to('/dashboard')->with('success', 'Password changed successfully');
        }

        return redirect()->back()->with('error', 'Failed to change password');
    }

    /**
     * User Profile Page
     */
    public function profile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');

        // Get user data
        $user = $this->taskModel->get_specific_columns(
            'users',
            'id, name, email, role, created_at',
            ['id' => $userId],
            null,
            1
        );

        // Get user statistics
        $stats = $this->taskModel->getTaskStats($userId, $user[0]['role']);

        $data = [
            'user' => $user[0],
            'stats' => $stats
        ];

        return view('profile', $data);
    }

    /**
     * Update Profile
     */
    public function update_profile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = $this->session->get('user_id');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[255]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]"
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $updateData = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->taskModel->updateRecord('users', $updateData, ['id' => $userId]);

        if ($result === 'updated') {
            // Update session data
            $this->session->set([
                'name' => $updateData['name'],
                'email' => $updateData['email']
            ]);

            return redirect()->to('/profile')->with('success', 'Profile updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update profile');
    }

    /**
     * Check if email exists (AJAX)
     */
    public function check_email()
    {
        $email = $this->request->getGet('email');

        $user = $this->taskModel->get_specific_columns(
            'users',
            'id',
            ['email' => $email],
            null,
            1
        );

        return $this->response->setJSON([
            'exists' => !empty($user)
        ]);
    }

    /**
     * Log login activity (optional)
     */
    private function logLoginActivity($userId)
    {
        // You can create a login_logs table to track login activity
        // $logData = [
        //     'user_id' => $userId,
        //     'ip_address' => $this->request->getIPAddress(),
        //     'user_agent' => $this->request->getUserAgent()->getAgentString(),
        //     'created_at' => date('Y-m-d H:i:s')
        // ];
        // $this->taskModel->insert_to_tb('login_logs', $logData);
    }

    /**
     * Log logout activity (optional)
     */
    private function logLogoutActivity($userId)
    {
        // Similar to login activity logging
    }

    /**
     * Send password reset email (requires email configuration)
     */
    private function sendPasswordResetEmail($email, $token)
    {
        // Configure email settings in app/Config/Email.php
        // $emailService = \Config\Services::email();
        // 
        // $resetLink = base_url("auth/reset-password/{$token}");
        // 
        // $emailService->setTo($email);
        // $emailService->setSubject('Password Reset Request');
        // $emailService->setMessage("Click here to reset your password: {$resetLink}");
        // 
        // return $emailService->send();
    }
}
