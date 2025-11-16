<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and validate input
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $group = isset($_POST['group']) ? trim($_POST['group']) : '';
    
    // Validation
    $errors = [];
    if (empty($student_id)) {
        $errors['student_id'] = "Student ID is required.";
    }
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }
    if (empty($group)) {
        $errors['group'] = "Group is required.";
    }
    
    // If validation passes
    if (empty($errors)) {
        // Load existing data from students.json
        $students = [];
        $json_file = 'students.json';
        
        if (file_exists($json_file)) {
            $json_data = file_get_contents($json_file);
            $students = json_decode($json_data, true);
            if ($students === null) {
                $students = [];
            }
        }
        
        // Add new student
        $new_student = [
            'student_id' => $student_id,
            'name' => $name,
            'group' => $group
        ];
        
        $students[] = $new_student;
        
        // Save data back to students.json
        $json_data = json_encode($students, JSON_PRETTY_PRINT);
        if (file_put_contents($json_file, $json_data) !== false) {
            $success_message = "Student successfully added!";
            // Clear form values after successful submission
            $_POST = [];
        } else {
            $error_message = "Failed to save student data.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> - Add Student - Student Attendance System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="attendance.php" class="nav-link">Attendance List</a></li>
                <li><a href="add-student.php" class="nav-link active">Add Student</a></li>
                <li><a href="take_attendance.php" class="nav-link">Take Attendance</a></li>
                <li><a href="reports.html" class="nav-link">Reports</a></li>
                <li><a href="#" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Add New Student</h2>
            
            <?php if (isset($success_message)): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="addStudentForm" class="student-form">
                <div class="form-group">
                    <label for="student_id">Student ID <span class="required">*</span></label>
                    <input type="text" id="student_id" name="student_id" required 
                           value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
                    <span class="error-message" id="studentIdError">
                        <?php if (isset($errors['student_id'])): ?>
                            <?php echo htmlspecialchars($errors['student_id']); ?>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="form-group">
                    <label for="name">Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    <span class="error-message" id="nameError">
                        <?php if (isset($errors['name'])): ?>
                            <?php echo htmlspecialchars($errors['name']); ?>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="form-group">
                    <label for="group">Group <span class="required">*</span></label>
                    <input type="text" id="group" name="group" required 
                           value="<?php echo isset($_POST['group']) ? htmlspecialchars($_POST['group']) : ''; ?>">
                    <span class="error-message" id="groupError">
                        <?php if (isset($errors['group'])): ?>
                            <?php echo htmlspecialchars($errors['group']); ?>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </main>

    <script src="validation.js"></script>
</body>
</html>

