<?php
require_once 'db_connect.php';

$message = '';
$message_type = '';
$session_id = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = isset($_POST['course_id']) ? trim($_POST['course_id']) : '';
    $group_id = isset($_POST['group_id']) ? trim($_POST['group_id']) : '';
    $professor_id = isset($_POST['professor_id']) ? trim($_POST['professor_id']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : date('Y-m-d');
    
    // Validation
    $errors = [];
    if (empty($course_id)) {
        $errors[] = "Course ID is required.";
    }
    if (empty($group_id)) {
        $errors[] = "Group ID is required.";
    }
    if (empty($professor_id)) {
        $errors[] = "Professor ID is required.";
    }
    if (empty($date)) {
        $errors[] = "Date is required.";
    }
    
    if (empty($errors)) {
        $conn = getDBConnection();
        
        if ($conn !== null) {
            try {
                $stmt = $conn->prepare("INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status) VALUES (:course_id, :group_id, :date, :opened_by, 'open')");
                $stmt->execute([
                    ':course_id' => $course_id,
                    ':group_id' => $group_id,
                    ':date' => $date,
                    ':opened_by' => $professor_id
                ]);
                
                $session_id = $conn->lastInsertId();
                $message = "Session successfully created! Session ID: " . $session_id;
                $message_type = "success";
                // Clear form
                $_POST = [];
            } catch (PDOException $e) {
                $message = "Error creating session: " . $e->getMessage();
                $message_type = "error";
            }
        } else {
            $message = "Database connection failed. Please check your configuration.";
            $message_type = "error";
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Attendance Session</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="create_session.php" class="nav-link active">Create Session</a></li>
                <li><a href="close_session.php" class="nav-link">Close Sessions</a></li>
                <li><a href="test_sessions.php" class="nav-link">Test Sessions</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Create Attendance Session</h2>
            
            <?php if (!empty($message)): ?>
                <div style="padding: 1rem; border-radius: 4px; margin-bottom: 1rem; <?php echo $message_type === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="student-form">
                <div class="form-group">
                    <label for="course_id">Course ID <span class="required">*</span></label>
                    <input type="text" id="course_id" name="course_id" required 
                           value="<?php echo isset($_POST['course_id']) ? htmlspecialchars($_POST['course_id']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="group_id">Group ID <span class="required">*</span></label>
                    <input type="text" id="group_id" name="group_id" required 
                           value="<?php echo isset($_POST['group_id']) ? htmlspecialchars($_POST['group_id']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="professor_id">Professor ID <span class="required">*</span></label>
                    <input type="text" id="professor_id" name="professor_id" required 
                           value="<?php echo isset($_POST['professor_id']) ? htmlspecialchars($_POST['professor_id']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="date">Date <span class="required">*</span></label>
                    <input type="date" id="date" name="date" required 
                           value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Create Session</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

