<?php
require_once 'db_connect.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Database Connection Test</h1>
        
        <?php
        $conn = getDBConnection();
        
        if ($conn !== null) {
            echo '<div class="success">';
            echo '<h2>✓ Connection successful</h2>';
            echo '<p>Database connection established successfully.</p>';
            
            // Display database info
            try {
                $stmt = $conn->query("SELECT DATABASE() as db_name, VERSION() as db_version");
                $info = $stmt->fetch();
                echo '<p><strong>Database:</strong> ' . htmlspecialchars($info['db_name']) . '</p>';
                echo '<p><strong>MySQL Version:</strong> ' . htmlspecialchars($info['db_version']) . '</p>';
            } catch (PDOException $e) {
                // Ignore query errors
            }
            
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h2>✗ Connection failed</h2>';
            echo '<p>Unable to connect to the database. Please check your configuration in config.php</p>';
            echo '<p>Check the db_errors.log file for detailed error information.</p>';
            echo '</div>';
        }
        ?>
        
        <p><a href="config.php" style="color: #0066cc;">View Configuration</a></p>
    </div>
</body>
</html>

