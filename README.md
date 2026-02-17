# 📋 TaskFlow — Task Management System

A role-based task management web application built with **CodeIgniter 4** and **MySQL**. Supports three user roles — Admin, Head, and User — each with tailored dashboards, task workflows, and assignment controls.

---

## ✨ Features

### 🔐 Authentication
- Secure login & registration with password hashing (`bcrypt`)
- Role-based session management
- Protected routes via CI4 Auth Filter

### 👥 Role System
| Role | Capabilities |
|------|-------------|
| **Admin** | Full access — manage all tasks, all users, archive/restore, view all reports |
| **Head** | Create & manage own tasks, assign contributors, archive own tasks |
| **User** | View assigned tasks, update task status, view personal dashboard |

### 📌 Task Management
- Create, edit, view, and delete tasks
- Set **priority** (Low / Medium / High) and **due dates**
- Update task **status** (Pending → In Progress → Completed)
- **Archive** tasks (hidden from active view, restorable)
- **Restore** archived tasks back to active

### 👤 Task Assignment
- Assign multiple contributors to any task
- Each task has one **Owner** and multiple **Contributors**
- Tasks with multiple users cannot be deleted (only archived)

### 📊 Dashboards
- **Admin/Head Dashboard** — stats, active tasks table, archived section, filters
- **User Dashboard** — assigned tasks, personal stats, due-today highlights
- **All Tasks Page** — filterable by status, priority, overdue, due today
- **Archived Tasks Page** — grid/list view with restore & delete

### 🔎 Filtering & Search
- Filter tasks by: All, Pending, In Progress, Completed, Overdue, My Tasks, Due Today
- Live client-side search by title/description
- Priority dropdown filter
- Client-side pagination (10 rows/page)

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 8.1+, CodeIgniter 4 |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ |
| **Frontend** | Vanilla HTML/CSS/JS (no frontend framework) |
| **Fonts** | Google Fonts (Crimson Pro, Inter, Spectral, Rubik) |
| **Session** | CI4 File-based sessions |
| **Auth** | Custom CI4 Auth Filter |

---

## ⚙️ Requirements

Before you begin, make sure you have the following installed:

- **PHP** `>= 8.1` with extensions:
  - `intl`
  - `mbstring`
  - `mysqlnd`
  - `json` (enabled by default)
- **MySQL** `>= 5.7` or **MariaDB** `>= 10.3`
- **Composer** `>= 2.0`
- **Apache** or **Nginx** web server
- **mod_rewrite** enabled (Apache)

---

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/KSS96CodeCraft/taskmanagement.git
cd taskmanagement
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Create the Database

Open your MySQL client (phpMyAdmin, TablePlus, CLI, etc.) and create a new database:

```sql
CREATE DATABASE task_management_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;
```

### 4. Import the Database Schema

Run the SQL schema file to create all required tables:

```bash
mysql -u root -p task_management_system < database/schema.sql
```

> **Tables created:**
> - `users` — stores user accounts and roles
> - `tasks` — task records with status, priority, due dates
> - `task_user` — pivot table for task assignments (owner / contributor)
> - `task_activities` — activity log for each task

#### Manual Table Creation (if no schema.sql)

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'head', 'user') NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed') NOT NULL DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    due_date DATE,
    created_by INT,
    completed_at DATETIME DEFAULT NULL,
    archived_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE task_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    responsibility ENUM('owner', 'contributor') NOT NULL DEFAULT 'contributor',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE task_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Seed an Admin Account

```sql
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@taskflow.com', '$2y$12$XoGBHRLh6./D7MNDlTasPub6YZQiNgNvDR5gq0m.SvzJSSPniqJE6', 'admin');
-- Default password: admin123
```

> ⚠️ Change the admin password immediately after first login.

### 5. Configure the Environment

Copy the example environment file:

```bash
cp env .env
```

Open `.env` and update the following values:

```ini
# ── App ────────────────────────────────────────
CI_ENVIRONMENT = development

# ── Base URL ───────────────────────────────────
app.baseURL = 'http://localhost/taskmanagement/'

# ── Database ───────────────────────────────────
database.default.hostname = localhost
database.default.database = task_management_system
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi
database.default.port     = 3306
```

### 6. Configure the Database File (Alternative)

If you prefer, edit `app/Config/Database.php` directly:

```php
public array $default = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'your_password',
    'database' => 'task_management_system',
    'DBDriver' => 'MySQLi',
    'port'     => 3306,
    'charset'  => 'utf8mb4',
    'DBCollat' => 'utf8mb4_general_ci',
];
```

### 7. Set Writable Permissions

```bash
chmod -R 775 writable/
```

On Windows (XAMPP/WAMP) this is handled automatically.

### 8. Configure the Web Server

#### Apache (XAMPP / WAMP)

Place the project inside your `htdocs` (XAMPP) or `www` (WAMP) folder:

```
htdocs/
└── taskmanagement/   ← project root
    ├── app/
    ├── public/
    ├── system/
    └── index.php
```

Make sure `mod_rewrite` is enabled and the `.htaccess` in the project root is present.

Update `app/Config/App.php`:

```php
public string $baseURL = 'http://localhost/taskmanagement/';
public string $indexPage = '';  // Remove index.php from URLs
```

#### Using CI4 Dev Server (Quickest)

```bash
php spark serve
```

Then open: [http://localhost:8080](http://localhost:8080)

#### Nginx

```nginx
server {
    listen 80;
    server_name taskflow.local;
    root /var/www/taskmanagement/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 📁 Project Structure

```
taskmanagement/
├── app/
│   ├── Config/
│   │   ├── App.php            # Base URL, app settings
│   │   ├── Database.php       # DB connection config
│   │   ├── Filters.php        # Auth filter registration
│   │   └── Routes.php         # All application routes
│   ├── Controllers/
│   │   ├── Auth.php           # Login, register, logout
│   │   ├── Home.php           # User dashboard & task actions
│   │   └── Admin.php          # Admin/Head dashboard & management
│   ├── Filters/
│   │   └── auth.php           # Role-based route protection
│   ├── Models/
│   │   └── TaskModel.php      # Core CRUD model (shared)
│   └── Views/
│       ├── login.php
│       ├── register.php
│       ├── admin_dashboard.php
│       ├── admin_tasks.php
│       ├── user_dashboard.php
│       ├── task_detail.php
│       ├── task_form.php
│       ├── task_assign.php
│       └── task_archived.php
├── public/                    # Web root (point server here)
├── writable/                  # Logs, cache, sessions (needs write permission)
├── .env                       # Environment config (not committed)
├── composer.json
└── spark                      # CI4 CLI tool
```

---

## 🔑 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@taskflow.com | admin123 |

> Create Head and User accounts via the **Register** page or directly in the database with the appropriate `role` value.

---

## 🗺️ Application Routes

| Method | URL | Controller | Access |
|--------|-----|-----------|--------|
| GET | `/login` | Auth::login | Public |
| POST | `/login` | Auth::authenticate | Public |
| GET | `/register` | Auth::register | Public |
| GET | `/dashboard` | Home::index | All roles |
| GET | `/admin/dashboard` | Admin::dashboard | Admin, Head |
| GET | `/admin/tasks` | Admin::tasks | Admin, Head |
| GET | `/tasks/create` | Home::create_task | Auth |
| GET | `/tasks/:id` | Home::view_task | Auth |
| GET | `/tasks/:id/edit` | Home::edit_task | Auth |
| POST | `/tasks/:id/archive` | Home::archive_task | Auth |
| POST | `/tasks/:id/restore` | Home::restore_task | Auth |
| GET | `/admin/tasks/:id/assign` | Admin::assign_users | Admin, Head |
| POST | `/admin/tasks/:id/assign-user` | Admin::assign_user | Admin, Head |
| GET | `/logout` | Auth::logout | Auth |

---

## 🐛 Troubleshooting

**White screen / 500 error**
```bash
# Check CI4 logs
tail -f writable/logs/log-$(date +%Y-%m-%d).php
```

**Database connection refused**
- Verify credentials in `.env` or `app/Config/Database.php`
- Ensure MySQL service is running: `sudo service mysql start`

**"Unable to write session"**
```bash
chmod -R 775 writable/session/
```

**Composer not found**
```bash
# Install Composer globally
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

**mod_rewrite not working (Apache)**
```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit changes: `git commit -m "Add your feature"`
4. Push to branch: `git push origin feature/your-feature`
5. Open a Pull Request

---

## 📄 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.
