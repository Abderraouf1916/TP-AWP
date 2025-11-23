<?php
require_once 'db_connect.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        $conn = getDBConnection();
        
        if ($conn !== null) {
            try {
                $stmt = $conn->prepare("INSERT INTO students (fullname, matricule, group_id) VALUES (:fullname, :matricule, :group_id)");
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':matricule' => $matricule,
                    ':group_id' => $group_id
                ]);
                
                $message = "Student successfully added!";
                $message_type = "success";
                // Clear form
                $_POST = [];
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry
                    $message = "Error: A student with this matricule already exists.";
                    $message_type = "error";
                } else {
                    $message = "Error adding student: " . $e->getMessage();
                    $message_type = "error";
                }
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
    <title>Add Student - Database</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="list_students.php" class="nav-link">List Students</a></li>
                <li><a href="add_student_db.php" class="nav-link active">Add Student</a></li>
                <li><a href="test_connection.php" class="nav-link">Test Connection</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Add New Student (Database)</h2>
            
            <?php if (!empty($message)): ?>
                <div style="padding: 1rem; border-radius: 4px; margin-bottom: 1rem; <?php echo $message_type === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="student-form">
                <div class="form-group">
                    <label for="fullname">Full Name <span class="required">*</span></label>
                    <input type="text" id="fullname" name="fullname" required 
                           value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="matricule">Matricule <span class="required">*</span></label>
                    <input type="text" id="matricule" name="matricule" required 
                           value="<?php echo isset($_POST['matricule']) ? htmlspecialchars($_POST['matricule']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="group_id">Group ID <span class="required">*</span></label>
                    <input type="text" id="group_id" name="group_id" required 
                           value="<?php echo isset($_POST['group_id']) ? htmlspecialchars($_POST['group_id']) : ''; ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

