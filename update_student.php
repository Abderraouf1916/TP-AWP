<?php
require_once 'db_connect.php';

$message = '';
$message_type = '';
$student = null;

// Get student ID from URL
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($student_id <= 0) {
    header('Location: list_students.php');
    exit;
}

$conn = getDBConnection();

if ($conn !== null) {
    // Load student data
    try {
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute([':id' => $student_id]);
        $student = $stmt->fetch();
        
        if (!$student) {
            header('Location: list_students.php');
            exit;
        }
    } catch (PDOException $e) {
        $message = "Error loading student: " . $e->getMessage();
        $message_type = "error";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = (int)$_POST['student_id'];
    $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
    $matricule = isset($_POST['matricule']) ? trim($_POST['matricule']) : '';
    $group_id = isset($_POST['group_id']) ? trim($_POST['group_id']) : '';
    
    // Validation
    $errors = [];
    if (empty($fullname)) {
        $errors[] = "Full name is required.";
    }
    if (empty($matricule)) {
        $errors[] = "Matricule is required.";
    }
    if (empty($group_id)) {
        $errors[] = "Group ID is required.";
    }
    
    if (empty($errors)) {
        if ($conn !== null) {
            try {
                $stmt = $conn->prepare("UPDATE students SET fullname = :fullname, matricule = :matricule, group_id = :group_id WHERE id = :id");
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':matricule' => $matricule,
                    ':group_id' => $group_id,
                    ':id' => $student_id
                ]);
                
                $message = "Student successfully updated!";
                $message_type = "success";
                
                // Reload student data
                $stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
                $stmt->execute([':id' => $student_id]);
                $student = $stmt->fetch();
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "Error: A student with this matricule already exists.";
                    $message_type = "error";
                } else {
                    $message = "Error updating student: " . $e->getMessage();
                    $message_type = "error";
                }
            }
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "error";
    }
}

if ($conn === null) {
    $message = "Database connection failed. Please check your configuration.";
    $message_type = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="list_students.php" class="nav-link">List Students</a></li>
                <li><a href="add_student_db.php" class="nav-link">Add Student</a></li>
                <li><a href="test_connection.php" class="nav-link">Test Connection</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Update Student</h2>
            
            <?php if (!empty($message)): ?>
                <div style="padding: 1rem; border-radius: 4px; margin-bottom: 1rem; <?php echo $message_type === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($student): ?>
                <form method="POST" action="" class="student-form">
                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                    
                    <div class="form-group">
                        <label for="fullname">Full Name <span class="required">*</span></label>
                        <input type="text" id="fullname" name="fullname" required 
                               value="<?php echo htmlspecialchars($student['fullname']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="matricule">Matricule <span class="required">*</span></label>
                        <input type="text" id="matricule" name="matricule" required 
                               value="<?php echo htmlspecialchars($student['matricule']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="group_id">Group ID <span class="required">*</span></label>
                        <input type="text" id="group_id" name="group_id" required 
                               value="<?php echo htmlspecialchars($student['group_id']); ?>">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Student</button>
                        <a href="list_students.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            <?php else: ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <p>Student not found.</p>
                    <a href="list_students.php" class="btn btn-primary" style="margin-top: 1rem;">Back to List</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

