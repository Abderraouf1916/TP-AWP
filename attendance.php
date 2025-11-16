<?php
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

// Load all attendance files
$attendance_files = glob('attendance_*.json');
$sessions = [];
$session_dates = [];

// Sort attendance files by date (most recent first)
if (!empty($attendance_files)) {
    // Extract dates and sort
    $dates = [];
    foreach ($attendance_files as $file) {
        // Extract date from filename: attendance_YYYY-MM-DD.json
        if (preg_match('/attendance_(\d{4}-\d{2}-\d{2})\.json/', $file, $matches)) {
            $dates[$matches[1]] = $file;
        }
    }
    krsort($dates); // Sort by date descending
    
    // Take up to 6 most recent attendance files (for S1-S6)
    $session_files = array_slice($dates, 0, 6, true);
    
    foreach ($session_files as $date => $file) {
        $session_dates[] = $date;
        $attendance_data = json_decode(file_get_contents($file), true);
        if ($attendance_data === null) {
            $attendance_data = [];
        }
        
        // Create a map of student_id => status for this session
        $session_map = [];
        foreach ($attendance_data as $record) {
            if (isset($record['student_id']) && isset($record['status'])) {
                $session_map[$record['student_id']] = $record['status'];
            }
        }
        $sessions[] = $session_map;
    }
}

// Ensure we have 6 sessions (pad with empty arrays if needed)
while (count($sessions) < 6) {
    $sessions[] = [];
    $session_dates[] = null;
}

// Function to split name into last name and first name
function splitName($name) {
    $parts = explode(' ', trim($name), 2);
    if (count($parts) == 2) {
        return ['last' => $parts[0], 'first' => $parts[1]];
    }
    return ['last' => $name, 'first' => $name];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> - Attendance List - Student Attendance System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="nav-title">Attendance System</h1>
            <ul class="nav-menu">
                <li><a href="attendance.php" class="nav-link active">Attendance List</a></li>
                <li><a href="add-student.php" class="nav-link">Add Student</a></li>
                <li><a href="take_attendance.php" class="nav-link">Take Attendance</a></li>
                <li><a href="reports.html" class="nav-link">Reports</a></li>
                <li><a href="#" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="content">
            <h2>Student Attendance List</h2>
            <div class="controls">
                <label for="searchName" style="margin-right:0.5rem; font-weight:600;">Search by Name</label>
                <input id="searchName" type="text" placeholder="Last or First name" style="padding:0.5rem; margin-right:0.75rem; border-radius:4px; border:1px solid #ccc;" />
                <button id="highlightExcellentBtn" class="btn btn-primary">Highlight Excellent Students</button>
                <button id="resetColorsBtn" class="btn btn-secondary">Reset Colors</button>
            </div>
            
            <?php if (empty($students)): ?>
                <div style="padding: 2rem; text-align: center; color: #666;">
                    <p>No students found. Please add students first using the Add Student form.</p>
                    <a href="add-student.php" class="btn btn-primary" style="margin-top: 1rem;">Add Student</a>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table id="attendanceTable" class="attendance-table">
                        <thead>
                            <tr>
                                <th rowspan="2">Student ID</th>
                                <th rowspan="2">Last Name</th>
                                <th rowspan="2">First Name</th>
                                <th rowspan="2">Course</th>
                                <th colspan="2">S1</th>
                                <th colspan="2">S2</th>
                                <th colspan="2">S3</th>
                                <th colspan="2">S4</th>
                                <th colspan="2">S5</th>
                                <th colspan="2">S6</th>
                                <th rowspan="2">Absence</th>
                                <th rowspan="2">Participation</th>
                                <th rowspan="2">Message</th>
                            </tr>
                            <tr>
                                <th>P</th>
                                <th>Pa</th>
                                <th>P</th>
                                <th>Pa</th>
                                <th>P</th>
                                <th>Pa</th>
                                <th>P</th>
                                <th>Pa</th>
                                <th>P</th>
                                <th>Pa</th>
                                <th>P</th>
                                <th>Pa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): 
                                $name_parts = splitName($student['name']);
                                $student_id = htmlspecialchars($student['student_id']);
                            ?>
                                <tr>
                                    <td><?php echo $student_id; ?></td>
                                    <td><?php echo htmlspecialchars($name_parts['last']); ?></td>
                                    <td><?php echo htmlspecialchars($name_parts['first']); ?></td>
                                    <td><?php echo htmlspecialchars($student['group']); ?></td>
                                    <?php 
                                    // Generate checkboxes for each session (S1-S6)
                                    for ($i = 0; $i < 6; $i++): 
                                        $is_present = false;
                                        $has_attendance = false;
                                        
                                        if (isset($sessions[$i][$student['student_id']])) {
                                            $has_attendance = true;
                                            $is_present = ($sessions[$i][$student['student_id']] === 'present');
                                        }
                                    ?>
                                        <td>
                                            <input type="checkbox" <?php echo $is_present ? 'checked' : ''; ?> 
                                                   <?php if (!$has_attendance): ?>disabled style="opacity: 0.5;"<?php endif; ?>>
                                        </td>
                                        <td>
                                            <input type="checkbox" <?php echo $is_present ? 'checked' : ''; ?>>
                                        </td>
                                    <?php endfor; ?>
                                    <td class="absence-count">0 Abs</td>
                                    <td class="participation-count">0 Par</td>
                                    <td class="message-cell"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (!empty($session_dates) && !empty(array_filter($session_dates))): ?>
                    <div style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                        <strong>Sessions:</strong>
                        <?php 
                        foreach ($session_dates as $index => $date): 
                            if ($date !== null): 
                                echo "S" . ($index + 1) . " = " . $date;
                                if ($index < count($session_dates) - 1 && array_filter(array_slice($session_dates, $index + 1))): 
                                    echo " | ";
                                endif;
                            endif;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
    <script>
        // Add search functionality after script.js initializes
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit to ensure script.js has initialized
            setTimeout(function() {
                const searchInput = document.getElementById('searchName');
                const table = document.getElementById('attendanceTable');
                
                if (searchInput && table) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        const rows = table.querySelectorAll('tbody tr');
                        
                        rows.forEach(function(row) {
                            if (row.cells.length >= 3) {
                                const lastName = row.cells[1].textContent.toLowerCase();
                                const firstName = row.cells[2].textContent.toLowerCase();
                                
                                if (lastName.includes(searchTerm) || firstName.includes(searchTerm)) {
                                    row.style.display = '';
                                } else {
                                    row.style.display = 'none';
                                }
                            }
                        });
                    });
                }
                
                // Highlight excellent students - wait for script.js to update counts
                const highlightBtn = document.getElementById('highlightExcellentBtn');
                const resetBtn = document.getElementById('resetColorsBtn');
                
                if (highlightBtn) {
                    highlightBtn.addEventListener('click', function() {
                        const rows = table.querySelectorAll('tbody tr');
                        rows.forEach(function(row) {
                            const absenceCell = row.querySelector('.absence-count');
                            const participationCell = row.querySelector('.participation-count');
                            
                            if (absenceCell && participationCell) {
                                // Extract number from text like "2 Abs" or "4 Par"
                                const absences = parseInt(absenceCell.textContent) || 0;
                                const participations = parseInt(participationCell.textContent) || 0;
                                
                                if (absences < 3 && participations >= 4) {
                                    row.classList.add('excellent-flash');
                                    setTimeout(() => row.classList.remove('excellent-flash'), 2000);
                                }
                            }
                        });
                    });
                }
                
                if (resetBtn) {
                    resetBtn.addEventListener('click', function() {
                        const rows = table.querySelectorAll('tbody tr');
                        rows.forEach(function(row) {
                            row.classList.remove('excellent-flash');
                            // Let script.js handle the highlighting on checkbox change
                            // Trigger a recalculation by simulating an update
                            const absenceCell = row.querySelector('.absence-count');
                            if (absenceCell) {
                                const absencesText = absenceCell.textContent;
                                const absences = parseInt(absencesText) || 0;
                                
                                // Remove all highlight classes
                                row.classList.remove('highlight-green', 'highlight-yellow', 'highlight-red');
                                
                                // Reapply based on absence count
                                if (absences < 3) {
                                    row.classList.add('highlight-green');
                                } else if (absences >= 3 && absences <= 4) {
                                    row.classList.add('highlight-yellow');
                                } else if (absences >= 5) {
                                    row.classList.add('highlight-red');
                                }
                            }
                        });
                    });
                }
            }, 100);
        });
    </script>
</body>
</html>

