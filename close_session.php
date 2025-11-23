<?php
require_once 'db_connect.php';

$message = '';
$message_type = '';
$sessions = [];

// Handle form submission to close a session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_id'])) {
    $session_id = (int)$_POST['session_id'];
    
    $conn = getDBConnection();
    
    if ($conn !== null) {
        try {
            $stmt = $conn->prepare("UPDATE attendance_sessions SET status = 'closed' WHERE id = :id");
            $stmt->execute([':id' => $session_id]);
            
            $message = "Session successfully closed!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error closing session: " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Database connection failed. Please check your configuration.";
        $message_type = "error";
    }
}

// Load all sessions
$conn = getDBConnection();

if ($conn !== null) {
    try {
        $stmt = $conn->query("SELECT * FROM attendance_sessions ORDER BY date DESC, created_at DESC");
        $sessions = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error_message = "Error loading sessions: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Close Attendance Session</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="create_session.php" class="nav-link">Create Session</a></li>
                <li><a href="close_session.php" class="nav-link active">Close Sessions</a></li>
                <li><a href="test_sessions.php" class="nav-link">Test Sessions</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Close Attendance Sessions</h2>
            
            <?php if (!empty($message)): ?>
                <div style="padding: 1rem; border-radius: 4px; margin-bottom: 1rem; <?php echo $message_type === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($conn === null): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <p>Database connection failed. Please check your configuration.</p>
                    <a href="test_connection.php" class="btn btn-primary" style="margin-top: 1rem;">Test Connection</a>
                </div>
            <?php elseif (empty($sessions)): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <p>No sessions found. Please create sessions first.</p>
                    <a href="create_session.php" class="btn btn-primary" style="margin-top: 1rem;">Create Session</a>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Session ID</th>
                                <th>Course ID</th>
                                <th>Group ID</th>
                                <th>Date</th>
                                <th>Opened By</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sessions as $session): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($session['id']); ?></td>
                                    <td><?php echo htmlspecialchars($session['course_id']); ?></td>
                                    <td><?php echo htmlspecialchars($session['group_id']); ?></td>
                                    <td><?php echo htmlspecialchars($session['date']); ?></td>
                                    <td><?php echo htmlspecialchars($session['opened_by']); ?></td>
                                    <td>
                                        <span style="padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.9rem; 
                                              <?php echo $session['status'] === 'open' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'; ?>">
                                            <?php echo strtoupper($session['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($session['status'] === 'open'): ?>
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="session_id" value="<?php echo $session['id']; ?>">
                                                <button type="submit" class="btn btn-secondary" 
                                                        style="padding: 0.5rem 1rem; font-size: 0.9rem; background-color: #dc3545;"
                                                        onclick="return confirm('Are you sure you want to close this session?');">
                                                    Close
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #999;">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 1rem;">
                    <a href="create_session.php" class="btn btn-primary">Create New Session</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

