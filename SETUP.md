# Quick Setup Guide

## Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL

## Step 2: Create Database
1. Open browser: http://localhost/phpmyadmin
2. Click "Import" tab
3. Choose file: `database.sql`
4. Click "Go"

The database `attendance_system_v2` will be created with sample data.

## Step 3: Configure Database (if needed)
Edit `config.php` if your MySQL credentials are different:
```php
define('DB_USER', 'root');  // Change if needed
define('DB_PASS', '');      // Change if needed
```

## Step 4: Access the System
1. Copy this folder to: `C:\xampp\htdocs\attendance_v2\`
2. Open browser: http://localhost/attendance_v2/

## Step 5: Login
**Admin Account:**
- Username: `admin`
- Password: `password`

**Note:** To use the system fully, you need to:
1. Add students via Admin Panel
2. Add professors via Admin Panel
3. Assign courses to professors (via course_assignments table or create UI)
4. Enroll students in courses (via course_enrollments table or create UI)

## File Structure
- All PHP files are in `/api/`
- All HTML pages are in `/pages/`
- Styles are in `/assets/css/`
- Scripts are in `/assets/js/`
- Uploads go to `/uploads/`

## Testing
1. Login as admin
2. Add a student
3. Add a professor
4. Login as professor and create a session
5. Mark attendance
6. View statistics

Enjoy!






