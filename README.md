# Attendance System V2

A modern, clean attendance management system built with HTML, CSS, PHP, and AJAX.

## Features

### Admin Panel
- Dashboard with statistics
- Add/Delete Students
- Add/Delete Professors  
- Manage Users
- Full user management system

### Professor Dashboard
- View assigned courses
- Create and manage attendance sessions
- Mark attendance for students
- View attendance statistics per group and overall
- Close sessions to prevent further edits

### Student Dashboard
- View enrolled courses
- Check attendance status
- Submit absence justifications (with PDF upload)
- View attendance history

## Installation

### Requirements
- XAMPP (or any PHP/MySQL server)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Setup Steps

1. **Import Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database.sql` file
   - This will create the `attendance_system_v2` database

2. **Configure Database**
   - Edit `config.php` if your database credentials differ:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'attendance_system_v2');
     ```

3. **Set Permissions**
   - Make sure the `uploads/` directory exists and is writable
   - The system will create it automatically if it doesn't exist

4. **Access the System**
   - Place the project in your web server directory (e.g., `htdocs/attendance_v2`)
   - Open browser: `http://localhost/attendance_v2`

## Default Login

- **Admin**: 
  - Username: `admin`
  - Password: `password`

## Project Structure

```
attendance_v2/
├── api/                    # PHP API endpoints
│   ├── auth.php           # Authentication
│   ├── users.php          # User management
│   ├── students.php       # Student CRUD
│   ├── professors.php     # Professor CRUD
│   ├── courses.php        # Course management
│   ├── groups.php         # Group management
│   ├── sessions.php       # Attendance sessions
│   ├── attendance.php     # Attendance records
│   ├── statistics.php     # Statistics API
│   ├── enrollments.php    # Course enrollments
│   └── upload.php         # File upload handler
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   └── js/
│       └── app.js         # Core JavaScript utilities
├── pages/
│   ├── admin/             # Admin pages
│   ├── professor/         # Professor pages
│   └── student/           # Student pages
├── uploads/               # PDF justifications storage
├── config.php             # Configuration
├── db_connect.php         # Database connection
├── database.sql           # Database schema
└── index.html             # Login page
```

## API Endpoints

All API endpoints return JSON:
- `POST /api/auth.php` - Login
- `GET /api/users.php` - List users
- `POST /api/users.php` - Create user
- `DELETE /api/users.php?id=X` - Delete user
- `GET /api/students.php` - List students
- `POST /api/students.php` - Create student
- `DELETE /api/students.php?id=X` - Delete student
- Similar endpoints for professors, courses, sessions, etc.

## Features

- ✅ Role-based authentication
- ✅ Modern, responsive UI
- ✅ AJAX-based interactions
- ✅ PDF justification upload
- ✅ Statistics with charts
- ✅ Clean database design
- ✅ Secure password hashing

## Notes

- All passwords use PHP's `password_hash()`
- Default test password is `password` for quick setup
- File uploads limited to PDF files
- Maximum upload size: 5MB

## License

This project is created for educational purposes.






