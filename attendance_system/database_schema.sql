-- Attendance System Database Schema with Algerian Data

CREATE DATABASE IF NOT EXISTS attendance_system;
USE attendance_system;

-- Users table (for authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'professor', 'student') NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(50) NOT NULL UNIQUE,
    course_name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Groups table
CREATE TABLE IF NOT EXISTS groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_code VARCHAR(50) NOT NULL UNIQUE,
    group_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    matricule VARCHAR(50) NOT NULL UNIQUE,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    group_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL,
    INDEX idx_matricule (matricule),
    INDEX idx_group (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Professors table
CREATE TABLE IF NOT EXISTS professors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    professor_code VARCHAR(50) NOT NULL UNIQUE,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_professor_code (professor_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Course enrollments (students enrolled in courses)
CREATE TABLE IF NOT EXISTS course_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    group_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, course_id, group_id),
    INDEX idx_course (course_id),
    INDEX idx_group (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Course assignments (professors assigned to courses)
CREATE TABLE IF NOT EXISTS course_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    professor_id INT NOT NULL,
    course_id INT NOT NULL,
    group_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professor_id) REFERENCES professors(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (professor_id, course_id, group_id),
    INDEX idx_professor (professor_id),
    INDEX idx_course (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Attendance sessions
CREATE TABLE IF NOT EXISTS attendance_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    group_id INT NOT NULL,
    professor_id INT NOT NULL,
    session_date DATE NOT NULL,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (professor_id) REFERENCES professors(id) ON DELETE CASCADE,
    INDEX idx_course (course_id),
    INDEX idx_group (group_id),
    INDEX idx_date (session_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Attendance records
CREATE TABLE IF NOT EXISTS attendance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    student_id INT NOT NULL,
    status ENUM('present', 'absent', 'justified') DEFAULT 'absent',
    justification TEXT,
    marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES attendance_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (session_id, student_id),
    INDEX idx_session (session_id),
    INDEX idx_student (student_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INSERT ALGERIAN DATA
-- ============================================

-- Insert users (password is 'password' for all)
INSERT INTO users (username, password, role, fullname, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'مدير النظام', 'admin@univ-alger.dz'),
('bensaid', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', 'أحمد بن سعيد', 'a.bensaid@univ-alger.dz'),
('khelifi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', 'فاطمة خليفي', 'f.khelifi@univ-alger.dz'),
('meziane', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', 'محمد مزiane', 'm.meziane@univ-alger.dz'),
('student001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'يوسف بوعزة', 'y.bouazza@student.univ-alger.dz'),
('student002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'سارة بن علي', 's.benali@student.univ-alger.dz'),
('student003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'عمر شريف', 'o.cherif@student.univ-alger.dz'),
('student004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'ليلى قاسم', 'l.kacem@student.univ-alger.dz'),
('student005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'خالد عمار', 'k.ammar@student.univ-alger.dz'),
('student006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'نورا بوزيان', 'n.bouziyan@student.univ-alger.dz');

-- Insert courses (Algerian university courses)
INSERT INTO courses (course_code, course_name, description) VALUES
('INFO101', 'مقدمة في علوم الحاسوب', 'Introduction to Computer Science - أساسيات البرمجة وهياكل البيانات'),
('INFO102', 'هياكل البيانات والخوارزميات', 'Data Structures and Algorithms - دراسة هياكل البيانات الأساسية'),
('INFO201', 'قواعد البيانات', 'Database Systems - تصميم وإدارة قواعد البيانات'),
('INFO202', 'شبكات الحاسوب', 'Computer Networks - أساسيات الشبكات والبروتوكولات'),
('MATH101', 'الرياضيات 1', 'Mathematics I - الجبر والتحليل'),
('MATH102', 'الرياضيات 2', 'Mathematics II - التفاضل والتكامل');

-- Insert groups
INSERT INTO groups (group_code, group_name) VALUES
('G1', 'المجموعة 1'),
('G2', 'المجموعة 2'),
('G3', 'المجموعة 3'),
('G4', 'المجموعة 4');

-- Insert professors
INSERT INTO professors (user_id, professor_code, fullname, email) 
SELECT id, 'PROF001', fullname, email FROM users WHERE username = 'bensaid';

INSERT INTO professors (user_id, professor_code, fullname, email) 
SELECT id, 'PROF002', fullname, email FROM users WHERE username = 'khelifi';

INSERT INTO professors (user_id, professor_code, fullname, email) 
SELECT id, 'PROF003', fullname, email FROM users WHERE username = 'meziane';

-- Insert students
INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024001', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'student001' AND g.group_code = 'G1' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024002', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'student002' AND g.group_code = 'G1' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024003', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'student003' AND g.group_code = 'G2' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024004', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'student004' AND g.group_code = 'G2' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024005', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'student005' AND g.group_code = 'G1' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024006', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'student006' AND g.group_code = 'G3' 
LIMIT 1;

-- Enroll students in courses
-- Student 001 (2024001) - Group G1
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024001' AND c.course_code = 'INFO101' AND g.group_code = 'G1';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024001' AND c.course_code = 'INFO102' AND g.group_code = 'G1';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024001' AND c.course_code = 'MATH101' AND g.group_code = 'G1';

-- Student 002 (2024002) - Group G1
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024002' AND c.course_code = 'INFO101' AND g.group_code = 'G1';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024002' AND c.course_code = 'INFO201' AND g.group_code = 'G1';

-- Student 003 (2024003) - Group G2
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024003' AND c.course_code = 'INFO101' AND g.group_code = 'G2';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024003' AND c.course_code = 'INFO202' AND g.group_code = 'G2';

-- Student 004 (2024004) - Group G2
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024004' AND c.course_code = 'INFO101' AND g.group_code = 'G2';

-- Student 005 (2024005) - Group G1
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024005' AND c.course_code = 'INFO101' AND g.group_code = 'G1';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024005' AND c.course_code = 'MATH102' AND g.group_code = 'G1';

-- Student 006 (2024006) - Group G3
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024006' AND c.course_code = 'INFO102' AND g.group_code = 'G3';

-- Assign professors to courses
-- Professor Bensaid (PROF001) - INFO101, INFO102
INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF001' AND c.course_code = 'INFO101' AND g.group_code = 'G1';

INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF001' AND c.course_code = 'INFO101' AND g.group_code = 'G2';

INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF001' AND c.course_code = 'INFO102' AND g.group_code = 'G1';

-- Professor Khelifi (PROF002) - INFO201, INFO202
INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF002' AND c.course_code = 'INFO201' AND g.group_code = 'G1';

INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF002' AND c.course_code = 'INFO202' AND g.group_code = 'G2';

-- Professor Meziane (PROF003) - MATH101, MATH102
INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF003' AND c.course_code = 'MATH101' AND g.group_code = 'G1';

INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF003' AND c.course_code = 'MATH102' AND g.group_code = 'G1';

-- Insert some sample attendance sessions
INSERT INTO attendance_sessions (course_id, group_id, professor_id, session_date, status)
SELECT c.id, g.id, p.id, '2024-11-15', 'closed'
FROM courses c, groups g, professors p
WHERE c.course_code = 'INFO101' AND g.group_code = 'G1' AND p.professor_code = 'PROF001'
LIMIT 1;

INSERT INTO attendance_sessions (course_id, group_id, professor_id, session_date, status)
SELECT c.id, g.id, p.id, '2024-11-18', 'closed'
FROM courses c, groups g, professors p
WHERE c.course_code = 'INFO101' AND g.group_code = 'G1' AND p.professor_code = 'PROF001'
LIMIT 1;

INSERT INTO attendance_sessions (course_id, group_id, professor_id, session_date, status)
SELECT c.id, g.id, p.id, '2024-11-20', 'open'
FROM courses c, groups g, professors p
WHERE c.course_code = 'INFO101' AND g.group_code = 'G1' AND p.professor_code = 'PROF001'
LIMIT 1;

-- Insert sample attendance records for first session
INSERT INTO attendance_records (session_id, student_id, status)
SELECT s.id, st.id, 'present'
FROM attendance_sessions s, students st
WHERE s.session_date = '2024-11-15' AND st.matricule IN ('2024001', '2024002', '2024005')
LIMIT 3;

INSERT INTO attendance_records (session_id, student_id, status)
SELECT s.id, st.id, 'absent'
FROM attendance_sessions s, students st
WHERE s.session_date = '2024-11-15' AND st.matricule = '2024001' AND NOT EXISTS (
    SELECT 1 FROM attendance_records ar WHERE ar.session_id = s.id AND ar.student_id = st.id
)
LIMIT 0;

-- Note: Password for all users is 'password'
-- All emails use @univ-alger.dz domain (Algerian university domain)
