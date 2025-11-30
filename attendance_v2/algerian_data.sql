-- ============================================
-- Algerian Test Data (Latin Alphabet)
-- Import this after importing database.sql
-- ============================================

USE attendance_system_v2;

-- Insert Algerian Users (password is 'password' for all)
INSERT INTO users (username, password, role, fullname, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator', 'admin@univ-alger.dz'),
('bensaid_a', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', 'Ahmed Bensaid', 'ahmed.bensaid@univ-alger.dz'),
('khelifi_f', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', 'Fatima Khelifi', 'fatima.khelifi@univ-alger.dz'),
('meziane_m', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', 'Mohamed Meziane', 'mohamed.meziane@univ-alger.dz'),
('bouazza_y', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Youssef Bouazza', 'youssef.bouazza@student.univ-alger.dz'),
('benali_s', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Sarah Benali', 'sarah.benali@student.univ-alger.dz'),
('cherif_o', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Omar Cherif', 'omar.cherif@student.univ-alger.dz'),
('kacem_l', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Leila Kacem', 'leila.kacem@student.univ-alger.dz'),
('ammar_k', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Khaled Ammar', 'khaled.ammar@student.univ-alger.dz'),
('bouziyan_n', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Nora Bouziyan', 'nora.bouziyan@student.univ-alger.dz'),
('taleb_r', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Rania Taleb', 'rania.taleb@student.univ-alger.dz'),
('hamoudi_b', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Bilal Hamoudi', 'bilal.hamoudi@student.univ-alger.dz'),
('zitouni_d', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Djamila Zitouni', 'djamila.zitouni@student.univ-alger.dz'),
('benhamida_a', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Aicha Benhamida', 'aicha.benhamida@student.univ-alger.dz'),
('slimani_i', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'Ibrahim Slimani', 'ibrahim.slimani@student.univ-alger.dz');

-- Insert Algerian Groups
INSERT INTO groups (group_code, group_name) VALUES
('G1', 'Groupe 1'),
('G2', 'Groupe 2'),
('G3', 'Groupe 3'),
('G4', 'Groupe 4');

-- Insert Algerian Courses
INSERT INTO courses (course_code, course_name, description) VALUES
('INFO101', 'Introduction a l''Informatique', 'Introduction to Computer Science - Programming basics and data structures'),
('INFO102', 'Structures de Donnees et Algorithmes', 'Data Structures and Algorithms - Fundamental data structures study'),
('INFO201', 'Bases de Donnees', 'Database Systems - Database design and management'),
('INFO202', 'Reseaux Informatiques', 'Computer Networks - Network fundamentals and protocols'),
('MATH101', 'Mathematiques 1', 'Mathematics I - Algebra and Analysis'),
('MATH102', 'Mathematiques 2', 'Mathematics II - Differential and Integral Calculus');

-- Insert Professors
INSERT INTO professors (user_id, professor_code, fullname, email) 
SELECT id, 'PROF001', fullname, email FROM users WHERE username = 'bensaid_a';

INSERT INTO professors (user_id, professor_code, fullname, email) 
SELECT id, 'PROF002', fullname, email FROM users WHERE username = 'khelifi_f';

INSERT INTO professors (user_id, professor_code, fullname, email) 
SELECT id, 'PROF003', fullname, email FROM users WHERE username = 'meziane_m';

-- Insert Students with Groups
INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024001', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'bouazza_y' AND g.group_code = 'G1' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024002', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'benali_s' AND g.group_code = 'G1' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024003', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'cherif_o' AND g.group_code = 'G2' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024004', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'kacem_l' AND g.group_code = 'G2' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024005', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'ammar_k' AND g.group_code = 'G1' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024006', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'bouziyan_n' AND g.group_code = 'G3' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024007', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'taleb_r' AND g.group_code = 'G1' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024008', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'hamoudi_b' AND g.group_code = 'G2' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024009', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'zitouni_d' AND g.group_code = 'G3' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024010', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'benhamida_a' AND g.group_code = 'G1' 
LIMIT 1;

INSERT INTO students (user_id, matricule, fullname, email, group_id) 
SELECT u.id, '2024011', u.fullname, u.email, g.id 
FROM users u, groups g 
WHERE u.username = 'slimani_i' AND g.group_code = 'G2' 
LIMIT 1;

-- Enroll Students in Courses
-- Student 001 (Youssef Bouazza) - Group G1
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

-- Student 002 (Sarah Benali) - Group G1
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024002' AND c.course_code = 'INFO101' AND g.group_code = 'G1';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024002' AND c.course_code = 'INFO201' AND g.group_code = 'G1';

-- Student 003 (Omar Cherif) - Group G2
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024003' AND c.course_code = 'INFO101' AND g.group_code = 'G2';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024003' AND c.course_code = 'INFO202' AND g.group_code = 'G2';

-- Student 004 (Leila Kacem) - Group G2
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024004' AND c.course_code = 'INFO101' AND g.group_code = 'G2';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024004' AND c.course_code = 'MATH101' AND g.group_code = 'G2';

-- Student 005 (Khaled Ammar) - Group G1
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024005' AND c.course_code = 'INFO101' AND g.group_code = 'G1';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024005' AND c.course_code = 'MATH102' AND g.group_code = 'G1';

-- Student 006 (Nora Bouziyan) - Group G3
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024006' AND c.course_code = 'INFO102' AND g.group_code = 'G3';

-- Student 007 (Rania Taleb) - Group G1
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024007' AND c.course_code = 'INFO101' AND g.group_code = 'G1';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024007' AND c.course_code = 'INFO102' AND g.group_code = 'G1';

-- Student 008 (Bilal Hamoudi) - Group G2
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024008' AND c.course_code = 'INFO101' AND g.group_code = 'G2';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024008' AND c.course_code = 'INFO202' AND g.group_code = 'G2';

-- Student 009 (Djamila Zitouni) - Group G3
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024009' AND c.course_code = 'INFO102' AND g.group_code = 'G3';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024009' AND c.course_code = 'MATH101' AND g.group_code = 'G3';

-- Student 010 (Aicha Benhamida) - Group G1
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024010' AND c.course_code = 'INFO101' AND g.group_code = 'G1';

-- Student 011 (Ibrahim Slimani) - Group G2
INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024011' AND c.course_code = 'INFO101' AND g.group_code = 'G2';

INSERT INTO course_enrollments (student_id, course_id, group_id)
SELECT s.id, c.id, g.id
FROM students s, courses c, groups g
WHERE s.matricule = '2024011' AND c.course_code = 'MATH101' AND g.group_code = 'G2';

-- Assign Professors to Courses
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

INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF001' AND c.course_code = 'INFO102' AND g.group_code = 'G3';

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
WHERE p.professor_code = 'PROF003' AND c.course_code = 'MATH101' AND g.group_code = 'G2';

INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF003' AND c.course_code = 'MATH101' AND g.group_code = 'G3';

INSERT INTO course_assignments (professor_id, course_id, group_id)
SELECT p.id, c.id, g.id
FROM professors p, courses c, groups g
WHERE p.professor_code = 'PROF003' AND c.course_code = 'MATH102' AND g.group_code = 'G1';

-- Insert Sample Attendance Sessions
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

INSERT INTO attendance_sessions (course_id, group_id, professor_id, session_date, status)
SELECT c.id, g.id, p.id, '2024-11-17', 'closed'
FROM courses c, groups g, professors p
WHERE c.course_code = 'INFO101' AND g.group_code = 'G2' AND p.professor_code = 'PROF001'
LIMIT 1;

-- Insert Sample Attendance Records for first session (2024-11-15)
INSERT INTO attendance_records (session_id, student_id, status)
SELECT s.id, st.id, 'present'
FROM attendance_sessions s, students st
WHERE s.session_date = '2024-11-15' AND st.matricule IN ('2024001', '2024002', '2024005', '2024007', '2024010');

INSERT INTO attendance_records (session_id, student_id, status)
SELECT s.id, st.id, 'absent'
FROM attendance_sessions s, students st
WHERE s.session_date = '2024-11-15' AND st.matricule NOT IN ('2024001', '2024002', '2024005', '2024007', '2024010')
AND st.group_id = (SELECT group_id FROM attendance_sessions WHERE session_date = '2024-11-15' LIMIT 1);

-- Insert Sample Attendance Records for second session (2024-11-18)
INSERT INTO attendance_records (session_id, student_id, status)
SELECT s.id, st.id, 'present'
FROM attendance_sessions s, students st
WHERE s.session_date = '2024-11-18' AND st.matricule IN ('2024001', '2024002', '2024005', '2024010');

INSERT INTO attendance_records (session_id, student_id, status)
SELECT s.id, st.id, 'absent'
FROM attendance_sessions s, students st
WHERE s.session_date = '2024-11-18' AND st.matricule IN ('2024007')
AND st.group_id = (SELECT group_id FROM attendance_sessions WHERE session_date = '2024-11-18' LIMIT 1);

-- ============================================
-- Login Credentials Summary
-- ============================================
-- All passwords are: password
--
-- Admin:
--   Username: admin
--   Password: password
--
-- Professors:
--   Username: bensaid_a, khelifi_f, meziane_m
--   Password: password
--
-- Students:
--   Username: bouazza_y, benali_s, cherif_o, kacem_l, ammar_k, bouziyan_n,
--             taleb_r, hamoudi_b, zitouni_d, benhamida_a, slimani_i
--   Password: password
--
-- ============================================






