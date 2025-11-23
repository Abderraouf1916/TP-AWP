# Simple Database Setup Guide

## What You Need First
- MySQL database running (usually with XAMPP, WAMP, or MAMP)
- A web browser

---

## Step 1: Edit the Database Password

1. Open the file `config.php` 
2. Find these lines:
```php
define('DB_USER', 'root');    // Change if your username is different
define('DB_PASS', '');        // ADD YOUR PASSWORD HERE if you have one
```
3. If your MySQL has a password, add it. Example:
```php
define('DB_PASS', 'mypassword123');
```
4. Save the file

---

## Step 2: Create the Database

### Method 1: Using phpMyAdmin (EASIEST)
1. Open your browser
2. Go to: `http://localhost/phpmyadmin`
3. Click on "New" button (left side)
4. Type: `attendance_system`
5. Click "Create"

### Method 2: Using MySQL Command Line
```sql
CREATE DATABASE attendance_system;
```

---

## Step 3: Create Tables

### Method 1: Using phpMyAdmin (EASIEST)
1. In phpMyAdmin, click on `attendance_system` database (left side)
2. Click "SQL" tab at the top
3. Open the file `create_tables.sql` in Notepad
4. Copy ALL the text from `create_tables.sql`
5. Paste it into the big box in phpMyAdmin
6. Click "Go" button
7. You should see: "2 queries executed successfully" ✅

---

## Step 4: Test It Works

1. Open browser
2. Go to: `http://localhost/PAW/test_connection.php`
3. If you see "Connection successful" - YOU DID IT! ✅
4. If you see "Connection failed" - check Step 1 again (password might be wrong)

---

## Now You Can Use These Pages:

✅ **Add Students:** `http://localhost/PAW/add_student_db.php`
✅ **View Students:** `http://localhost/PAW/list_students.php`
✅ **Create Session:** `http://localhost/PAW/create_session.php`
✅ **Close Session:** `http://localhost/PAW/close_session.php`

---

## Quick Checklist

- [ ] Opened `config.php` and added database password (if you have one)
- [ ] Created database named `attendance_system` in phpMyAdmin
- [ ] Copied SQL from `create_tables.sql` and ran it in phpMyAdmin
- [ ] Tested connection at `test_connection.php` and saw "Connection successful"

If all checkboxes are done, you're ready! 🎉

