# 📋 Task Manager — Web Application

> A modern, full-stack task management platform built with **Laravel 12**, **Inertia.js v3**, **Vue 3**, and **Tailwind CSS**. It serves as both a responsive web application and the central API backend for the companion Flutter mobile app.

---

## 📑 Table of Contents

- [Overview](#-overview)
- [Key Features](#-key-features)
- [Architecture & Tech Stack](#-architecture--tech-stack)
- [Application Navigation & User Flows](#-application-navigation--user-flows)
  - [1. Guest & Authentication Flows](#1-guest--authentication-flows)
  - [2. User Dashboard & Task Management](#2-user-dashboard--task-management)
  - [3. Admin Dashboard & Platform Analytics](#3-admin-dashboard--platform-analytics)
  - [4. Settings & Account Customization](#4-settings--account-customization)
- [API Backend (for Mobile Client)](#-api-backend-for-mobile-client)
- [Getting Started & Local Setup](#-getting-started--local-setup)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Environment Configuration](#environment-configuration)
  - [Running the Application](#running-the-application)
- [Running Tests](#-running-tests)
- [Directory Structure](#-directory-structure)

---

## 🌟 Overview

The **Task Manager Web Application** is designed to help individuals and teams organize their day-to-day workflow. It provides a real-time, categorized task dashboard, secure user management, and an administration portal that monitors platform-wide adoption across web and mobile clients.

In addition to serving the Inertia + Vue single-page application (SPA), the backend exposes a secure REST API via **Laravel Sanctum** that powers the companion **Flutter Mobile Application** (`task_manager_mobile`).

---

## ✨ Key Features

### 1. 🛡️ Robust Authentication & Security
- **Laravel Fortify** powered authentication engine:
  - User registration with optional email verification (`MustVerifyEmail`).
  - Secure login with rate limiting (`throttle:5,1`) and password hashing.
  - Password reset flows via email tokens.
  - Two-Factor Authentication (2FA) support and recovery codes.
  - Password confirmation for sensitive administrative operations.

### 2. 📝 Intelligent Task Management
- **Smart Categorization Engine**: Tasks are automatically grouped into dynamic buckets based on due dates and status:
  - **Today**: Active tasks due before midnight today.
  - **Upcoming**: Scheduled future tasks.
  - **Past Due**: Overdue tasks requiring immediate attention (visual alert styling).
  - **Completed**: Finished tasks with completion timestamps.
- **Task Lifecycle**:
  - Create tasks with title, description, and required due dates.
  - Quick inline completion toggle (one-click checkbox).
  - Modal-based editing for updating details or rescheduling deadlines.
  - Safe deletion with prompt confirmation.
- **Dynamic Clock Synchronization**: Real-time timer refreshes task categorization every 30 seconds without requiring page reloads.

### 3. 📊 Admin Dashboard & Platform Telemetry
- **Role-Based Access Control (RBAC)**: Protected via `admin` middleware (`role === 'admin'`).
- **Telemetry & Client Usage Breakdown**:
  - Automatically identifies whether user requests originate from the **Vue Web SPA** or the **Flutter Mobile App**.
  - Tracks `last_client_used` (`vue-web` vs `flutter-mobile`) and `last_login_at`.
- **System Metrics**:
  - Total registered users count.
  - Comparative platform adoption counters (Web vs Mobile).
- **Comprehensive User Directory**:
  - Searchable by user name or email address.
  - Multi-attribute filters: Filter by role (`admin` / `user`), status (`active` / `suspended`), and client platform (`vue-web` / `flutter-mobile` / `never logged in`).
  - User status badges, role indicators, and last-active timestamps.

### 4. 🎨 User Experience & Theming
- Built with **Tailwind CSS** and **Radix Vue / Shadcn** accessible UI primitives.
- Dark mode, light mode, and system preference synchronization.
- Elegant sidebar navigation with collapsible modes, breadcrumbs, and user dropdowns.

---

## 🛠️ Architecture & Tech Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend Framework** | Laravel 12.x / PHP 8.4+ |
| **Authentication & Tokens** | Laravel Fortify, Laravel Sanctum |
| **SPA Bridge** | Inertia.js v3 (`inertiajs/inertia-laravel`) |
| **Frontend Framework** | Vue 3 (Composition API, `<script setup>`), TypeScript |
| **Styling & Icons** | Tailwind CSS, Lucide Icons (`@lucide/vue`) |
| **UI Primitives** | Radix Vue / Shadcn Vue |
| **Build Tooling** | Vite, PostCSS |
| **Testing** | Pest PHP / PHPUnit |

---

## 🗺️ Application Navigation & User Flows

### 1. Guest & Authentication Flows

```
           [ Landing Page (/) ]
              /             \
      [ Login (/login) ]    [ Register (/register) ]
              \
      [ Forgot Password (/forgot-password) ]
              ↓
      [ Reset Password (/reset-password) ]
```

- **Home / Landing Page (`/`)**: Displays the welcome portal with direct calls-to-action to log in or register.
- **Login (`/login`)**: Email and password authentication with "Remember Me" and link to password recovery.
- **Register (`/register`)**: New account registration.
- **Forgot Password (`/forgot-password`)**: Request password reset link sent to user's email.
- **Reset Password (`/reset-password`)**: Set a new password using token from reset email.

---

### 2. User Dashboard & Task Management

```
[ App Shell / Sidebar ]
  ├── 📊 Dashboard (/dashboard or /tasks)
  │     ├── Metric Overview Cards (Today, Upcoming, Past Due, Completed)
  │     ├── "Add Task" Dialog Modal
  │     ├── Task Sections (Today, Upcoming, Past Due, Completed)
  │     └── Task Actions (Toggle Complete, Edit Modal, Delete)
  └── 👤 User Menu (Bottom Sidebar / Header)
        ├── Profile (/settings/profile)
        ├── Security (/settings/security)
        ├── Appearance (/settings/appearance)
        └── Logout
```

#### Navigating the Dashboard:
1. **Header Overview**: Shows personal greeting (`Welcome back, {User}!`), today's formatted date, and the **"+ Add Task"** button.
2. **Metric Summary Cards**:
   - Four top cards display total task counts for **Today**, **Upcoming**, **Past Due**, and **Completed**.
3. **Task Cards & Sections**:
   - Scroll down to review tasks separated into designated sections.
   - Click the circle icon on any card to immediately toggle completion status.
   - Click the pencil icon to open the **Edit Task** dialog to modify title, description, or due date.
   - Click the trash icon to remove a task.
4. **Creating a Task**:
   - Click **"+ Add Task"** at the top right of the dashboard.
   - Enter **Title**, optional **Description**, and **Due Date & Time**.
   - Submit to instantly update the task categories.

---

### 3. Admin Dashboard & Platform Analytics(Only on the web side)

> **Note**: Accessible exclusively to users with `role: 'admin'`. Standard users attempting to access `/admin/*` receive an authorization error.

```
[ Admin Sidebar Menu ]
  └── 🛡️ Admin Dashboard (/admin/dashboard)
        ├── Total Users Metric Card
        ├── Web Client Usage Metric Card (vue-web)
        ├── Mobile Client Usage Metric Card (flutter-mobile)
        └── User Management Table
              ├── Search Bar (Name / Email)
              ├── Role Filter (All, Admin, User)
              ├── Status Filter (All, Active, Suspended)
              ├── Client Filter (All, Vue Web, Flutter Mobile, Never)
              └── User List (Badges, Roles, Last Active)
```

#### Navigating the Admin Portal:
1. In the sidebar, admins see an **Admin** section with the **Admin Dashboard** link (`/admin/dashboard`).
2. **Platform KPI Metrics**:
   - Total registered users count.
   - Active web client sessions count (`vue-web`).
   - Active mobile client sessions count (`flutter-mobile`).
3. **User Management & Filtering**:
   - **Search Input**: Type name or email for instant real-time filtering.
   - **Dropdown Filters**: Filter users by Role (`Admin` vs `User`), Status (`Active` vs `Suspended`), or Client Platform (`Vue Web`, `Flutter Mobile`, or `Never Logged In`).
   - **User Row Details**: Displays avatar/initials, name, email, verified badge, role badge, status badge, last client used indicator, and last login timestamp.

---

### 4. Settings & Account Customization

Access these pages via the bottom user menu in the sidebar:
- **Profile (`/settings/profile`)**: Update your display name and email address. Trigger verification emails if email changes.
- **Security (`/settings/security`)**: Change password and manage Two-Factor Authentication (2FA) recovery codes.
- **Appearance (`/settings/appearance`)**: Select preferred theme:
  - ☀️ **Light Mode**
  - 🌙 **Dark Mode**
  - 💻 **System Preference**

---

## 📡 API Backend (for Mobile Client)

The Laravel backend provides a versioned REST API under `/api/v1` for the Flutter mobile application (`task_manager_mobile`). All protected endpoints require a Bearer token issued by Laravel Sanctum.

### API Endpoints Summary

| Method | Endpoint | Auth Required | Description |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/login` | No (Throttled) | Authenticate user & receive Sanctum API Bearer token |
| `POST` | `/api/v1/forgot-password` | No (Throttled) | Request password reset email |
| `POST` | `/api/v1/reset-password` | No (Throttled) | Reset password with token from email |
| `GET` | `/api/v1/user` | Sanctum Bearer | Get authenticated user profile & verification status |
| `POST` | `/api/v1/logout` | Sanctum Bearer | Revoke current access token |
| `GET` | `/api/v1/tasks` | Sanctum Bearer | List all tasks belonging to the authenticated user |
| `POST` | `/api/v1/tasks` | Sanctum Bearer | Create a new task (`title`, `description`, `due_at`) |
| `GET` | `/api/v1/tasks/{id}` | Sanctum Bearer | View specific task details |
| `PATCH` | `/api/v1/tasks/{id}` | Sanctum Bearer | Update task details |
| `PATCH` | `/api/v1/tasks/{id}/completion` | Sanctum Bearer | Toggle completion status (`completed_at`) |
| `DELETE` | `/api/v1/tasks/{id}` | Sanctum Bearer | Delete a task |

---

## 🚀 Getting Started & Local Setup

### Prerequisites

Ensure you have the following installed on your machine:
- **PHP** >= 8.3 (PHP 8.4 recommended)
- **Composer** (v2.x)
- **Node.js** (v18.x or v20+) & **npm**
- **SQLite** (default) or **MySQL** / **PostgreSQL**

---

### Installation

1. Navigate to the web application directory:
   ```bash
   cd task_manager
   ```

2. Run the automated setup script:
   ```bash
   composer run setup
   ```
   *This command installs PHP dependencies, creates `.env`, generates the application key, runs database migrations, and installs/builds npm frontend assets.*

   **Or run the steps manually:**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   npm install
   npm run build
   ```

---

### Environment Configuration

Inspect the `.env` file and configure your database and app settings:

```dotenv
APP_NAME="Task Manager"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=task_manager
# DB_USERNAME=root
# DB_PASSWORD=
```

#### Seeding Database & Creating Admin User:
You can run database seeders to populate initial data:
```bash
php artisan db:seed
```

To grant a user **admin** privileges, run `tinker`:
```bash
php artisan tinker --execute 'App\Models\User::first()->update(["role" => "admin"]);'
```

---

### Running the Application

Start the local development server (concurrently runs Laravel backend and Vite frontend):

```bash
composer run dev
```

Alternatively, run them in separate terminals:
```bash
# Terminal 1: Laravel backend API & Inertia server
php artisan serve --port=8000

# Terminal 2: Vite hot-module replacement
npm run dev
```

Once running:
- Open your browser at: **`http://localhost:8000`**
- Companion API is accessible at: **`http://localhost:8000/api/v1`**

---

## 🧪 Running Tests

Run the backend test suite using Pest PHP:

```bash
php artisan test
# or
vendor/bin/pest
```

Run code formatting checks:
```bash
npm run lint
# or for PHP
vendor/bin/pint
```

---

## 📁 Directory Structure

```
task_manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php         # Admin analytics & user table
│   │   │   ├── TaskController.php          # Web Inertia task handlers
│   │   │   ├── Api/                        # REST API controllers for Flutter
│   │   │   │   ├── AuthController.php      # Mobile login / logout / token
│   │   │   │   ├── PasswordResetController.php
│   │   │   │   └── TaskController.php      # Mobile task API
│   │   │   └── Settings/                   # Profile, security controllers
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php         # Admin access protection
│   │       └── TrackUserActivity.php       # Client detection (Web vs Mobile)
│   └── Models/
│       ├── Task.php                        # Task model & relationships
│       └── User.php                        # User model, roles & Sanctum tokens
├── bootstrap/
│   └── app.php                             # App routing, middleware & apiPrefix (/api/v1)
├── database/
│   └── migrations/                         # Tasks, users, roles & telemetry migrations
├── resources/
│   ├── css/                                # Tailwind styling
│   └── js/
│       ├── pages/
│       │   ├── Welcome.vue                 # Landing page
│       │   ├── Tasks/Index.vue             # User task dashboard
│       │   ├── Admin/Dashboard.vue         # Admin telemetry & user management
│       │   ├── auth/                       # Login, Register, Forgot Password
│       │   └── settings/                   # Profile, Security, Appearance
│       ├── components/                     # Reusable UI & layout elements
│       └── layouts/                        # Authenticated and guest layouts
└── routes/
    ├── web.php                             # Web & Inertia routes
    ├── api.php                             # Versioned mobile API routes
    └── settings.php                        # User profile settings routes
```

---

*Part of the Task Manager multi-platform suite. See [`task_manager_mobile/README.md`](../task_manager_mobile/README.md) for mobile app documentation.*
