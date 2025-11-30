# Attendance System

Complete attendance management system with separate interfaces for Professors, Students, and Administrators.

## Project Structure

```
attendance_system/
├── api/                    # PHP API endpoints (backend)
│   ├── sessions.php       # Session management
│   ├── attendance.php     # Attendance records
│   ├── students.php       # Student CRUD
│   ├── courses.php        # Course management
│   ├── groups.php         # Group management
│   └── statistics.php     # Statistics data
├── assets/                 # Shared assets
│   ├── styles.css         # Main stylesheet (green theme)
│   └── common.js          # Common JavaScript functions
├── pages/                  # HTML pages (frontend)
│   ├── professor/         # Professor pages
│   │   ├── home.html      # List sessions per course
│   │   ├── session.html   # Mark attendance
│   │   └── summary.html   # Attendance summary
│   ├── student/           # Student pages
│   │   ├── home.html      # Enrolled courses
│   │   └── attendance.html # View attendance & submit justifications
│   └── admin/             # Admin pages
│       ├── home.html      # Admin dashboard
│       ├── statistics.html   # Statistics with charts
│       └── students.html   # Student management (import/export)
├── config.php             # Database configuration
├── db_connect.php         # Database connection
└── database_schema.sql    # Database schema

```

## Setup Instructions

### 1. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE attendance_system;
```

2. Import the schema:
```bash
mysql -u root -p attendance_system < database_schema.sql
```

Or use phpMyAdmin to import `database_schema.sql`

### 2. Configure Database

Edit `config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'attendance_system');
```

### 3. Access the System

- **Professor Pages:** `pages/professor/home.html`
- **Student Pages:** `pages/student/home.html`
- **Admin Pages:** `pages/admin/home.html`

## Features

### Professor Pages
1. **Home** - View all sessions grouped by course
2. **Session** - Mark attendance for a session
3. **Summary** - View attendance summary by course/group

### Student Pages
1. **Home** - View enrolled courses
2. **Attendance** - View attendance status and submit justifications for absences

### Admin Pages
1. **Home** - Dashboard with key statistics
2. **Statistics** - Charts showing attendance by course, group, and overall distribution
3. **Students** - Manage students with:
   - Add/Edit/Delete students
   - Import from Excel (XLSX format)
   - Export to Excel

## API Endpoints

All API endpoints are in the `api/` folder and return JSON:

- `api/sessions.php` - Session management (GET, POST, PUT)
- `api/attendance.php` - Attendance records (GET, POST, PUT)
- `api/students.php` - Student management (GET, POST, PUT, DELETE)
- `api/courses.php` - Course listing (GET)
- `api/groups.php` - Group listing (GET)
- `api/statistics.php` - Statistics data (GET)

## Technology Stack

- **Frontend:** HTML, CSS, JavaScript (AJAX)
- **Backend:** PHP (PDO)
- **Database:** MySQL
- **Charts:** Chart.js
- **Excel:** SheetJS (xlsx.js)

## Notes

- All pages use AJAX to communicate with PHP APIs
- No page reloads - all interactions are asynchronous
- Same green theme throughout all pages
- Responsive design for mobile devices
- Test users are created in the database schema (password: 'password')

## Default Test Users

- Admin: username `admin`, password `password`
- Professor: username `prof1`, password `password`
- Student: username `student1`, password `password`








