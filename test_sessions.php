<?php
require_once 'db_connect.php';

$message = '';
$message_type = '';
$inserted_sessions = [];

// Handle form submission to insert test sessions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert_test'])) {
    $test_sessions = [
        [
            'course_id' => 'CS101',
            'group_id' => 'G1',
            'date' => date('Y-m-d'),
            'opened_by' => 'PROF001',
            'status' => 'open'
        ],
        [
            'course_id' => 'CS102',
            'group_id' => 'G2',
            'date' => date('Y-m-d', strtotime('-1 day')),
            'opened_by' => 'PROF002',
            'status' => 'open'
        ],
        [
            'course_id' => 'CS103',
            'group_id' => 'G1',
            'date' => date('Y-m-d', strtotime('-2 days')),
            'opened_by' => 'PROF001',
            'status' => 'closed'
        ]
    ];
    
    $conn = getDBConnection();
    
    if ($conn !== null) {
        try {
            $stmt = $conn->prepare("INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status) VALUES (:course_id, :group_id, :date, :opened_by, :status)");
            
            foreach ($test_sessions as $session) {
                $stmt->execute([
                    ':course_id' => $session['course_id'],
                    ':group_id' => $session['group_id'],
                    ':date' => $session['date'],
                    ':opened_by' => $session['opened_by'],
                    ':status' => $session['status']
                ]);
                $inserted_sessions[] = [
                    'id' => $conn->lastInsertId(),
                    'data' => $session
                ];
            }
            
            $message = "Successfully inserted " . count($inserted_sessions) . " test sessions!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error inserting test sessions: " . $e->getMessage();
            $message_type = "error";
        }
    } else {
        $message = "Database connection failed. Please check your configuration.";
        $message_type = "error";
    }
}

// Load all sessions
$sessions = [];
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
    <title>Test Sessions</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="create_session.php" class="nav-link">Create Session</a></li>
                <li><a href="close_session.php" class="nav-link">Close Sessions</a></li>
                <li><a href="test_sessions.php" class="nav-link active">Test Sessions</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Test Sessions</h2>
            
            <?php if (!empty($message)): ?>
                <div style="padding: 1rem; border-radius: 4px; margin-bottom: 1rem; <?php echo $message_type === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($inserted_sessions)): ?>
                <div style="background-color: #e8f5e9; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                    <h3>Inserted Sessions:</h3>
                    <ul>
                        <?php foreach ($inserted_sessions as $session): ?>
                            <li>Session ID: <?php echo $session['id']; ?> - 
                                Course: <?php echo htmlspecialchars($session['data']['course_id']); ?>, 
                                Group: <?php echo htmlspecialchars($session['data']['group_id']); ?>, 
                                Date: <?php echo htmlspecialchars($session['data']['date']); ?>, 
                                Status: <?php echo htmlspecialchars($session['data']['status']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div style="margin-bottom: 2rem;">
                <h3>Insert Test Sessions</h3>
                <p>Click the button below to insert 2-3 test sessions into the database.</p>
                <form method="POST" action="" style="display: inline;">
                    <input type="hidden" name="insert_test" value="1">
                    <button type="submit" class="btn btn-primary" 
                            onclick="return confirm('This will insert 2-3 test sessions. Continue?');">
                        Insert Test Sessions
                    </button>
                </form>
            </div>
            
            <h3>All Sessions</h3>
            
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
                    <p>No sessions found. Create or insert test sessions first.</p>
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
                                <th>Created At</th>
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
                                    <td><?php echo htmlspecialchars($session['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

