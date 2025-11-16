<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate today's date in YYYY-MM-DD format
    $today = date('Y-m-d');
    $attendance_file = "attendance_{$today}.json";
    
    // Check if attendance file for today already exists
    if (file_exists($attendance_file)) {
        $message = "Attendance for today has already been taken.";
        $message_type = "error";
    } else {
        // Get attendance data from form
        $attendance_data = [];
        
        if (isset($_POST['attendance']) && is_array($_POST['attendance'])) {
            foreach ($_POST['attendance'] as $student_id => $status) {
                $attendance_data[] = [
                    'student_id' => $student_id,
                    'status' => $status
                ];
            }
        }
        
        // Save attendance data to JSON file
        $json_data = json_encode($attendance_data, JSON_PRETTY_PRINT);
        if (file_put_contents($attendance_file, $json_data) !== false) {
            $message = "Attendance for today has been successfully saved!";
            $message_type = "success";
        } else {
            $message = "Failed to save attendance data.";
            $message_type = "error";
        }
    }
}

// Load students from students.json
$students = [];
$json_file = 'students.json';

if (file_exists($json_file)) {
    $json_data = file_get_contents($json_file);
    $students = json_decode($json_data, true);
    if ($students === null) {
        $students = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> - Take Attendance - Student Attendance System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="attendance.php" class="nav-link">Attendance List</a></li>
                <li><a href="add-student.php" class="nav-link">Add Student</a></li>
                <li><a href="take_attendance.php" class="nav-link active">Take Attendance</a></li>
                <li><a href="reports.html" class="nav-link">Reports</a></li>
                <li><a href="#" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Take Attendance</h2>
            
            <?php if (isset($message)): ?>
                <div style="padding: 1rem; border-radius: 4px; margin-bottom: 1rem; <?php echo $message_type === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($students)): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <p>No students found. Please add students first using the Add Student form.</p>
                    <a href="add-student.php" class="btn btn-primary" style="margin-top: 1rem;">Add Student</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="table-wrapper">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['group']); ?></td>
                                        <td>
                                            <label style="margin-right: 1rem; cursor: pointer;">
                                                <input type="radio" name="attendance[<?php echo htmlspecialchars($student['student_id']); ?>]" 
                                                       value="present" checked style="margin-right: 0.5rem;"> Present
                                            </label>
                                            <label style="cursor: pointer;">
                                                <input type="radio" name="attendance[<?php echo htmlspecialchars($student['student_id']); ?>]" 
                                                       value="absent" style="margin-right: 0.5rem;"> Absent
                                            </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Submit Attendance</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

