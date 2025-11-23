<?php
require_once 'db_connect.php';

$students = [];
$conn = getDBConnection();

if ($conn !== null) {
    try {
        $stmt = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
        $students = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error_message = "Error loading students: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Students</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="list_students.php" class="nav-link active">List Students</a></li>
                <li><a href="add_student_db.php" class="nav-link">Add Student</a></li>
                <li><a href="test_connection.php" class="nav-link">Test Connection</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Student List</h2>
            
            <?php if (isset($error_message)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    Student successfully deleted!
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?php 
                    if ($_GET['error'] == 'notfound') {
                        echo "Student not found.";
                    } elseif ($_GET['error'] == 'connection') {
                        echo "Database connection failed.";
                    } else {
                        echo "Error: " . htmlspecialchars($_GET['error']);
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if ($conn === null): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <p>Database connection failed. Please check your configuration.</p>
                    <a href="test_connection.php" class="btn btn-primary" style="margin-top: 1rem;">Test Connection</a>
                </div>
            <?php elseif (empty($students)): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <p>No students found. Please add students first.</p>
                    <a href="add_student_db.php" class="btn btn-primary" style="margin-top: 1rem;">Add Student</a>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Matricule</th>
                                <th>Group ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($student['matricule']); ?></td>
                                    <td><?php echo htmlspecialchars($student['group_id']); ?></td>
                                    <td>
                                        <a href="update_student.php?id=<?php echo $student['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem; margin-right: 0.5rem;">Update</a>
                                        <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem; background-color: #dc3545;" 
                                           onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1rem;">
                    <a href="add_student_db.php" class="btn btn-primary">Add New Student</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

