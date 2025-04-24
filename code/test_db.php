<?php
// XAMPP MySQL Database Test for Student Notes Project
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #7b2cbf; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; }
        .warning { color: darkorange; background: #fff3cd; padding: 10px; border-radius: 5px; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow: auto; }
        ul { padding-left: 20px; }
    </style>
</head>
<body>
    <h1>MySQL Database Test for Student Notes Project</h1>';

// Check MySQL extensions
echo '<h2>MySQL Module Check</h2>';
if (extension_loaded('mysqli')) {
    echo '<p class="success">MySQLi extension is loaded ✓</p>';
} else {
    echo '<p class="error">MySQLi extension is NOT loaded ✗</p>';
}

if (extension_loaded('pdo_mysql')) {
    echo '<p class="success">PDO MySQL extension is loaded ✓</p>';
} else {
    echo '<p class="error">PDO MySQL extension is NOT loaded ✗</p>';
}

// Database configuration - matching db.php
$host = 'localhost';  // Use 'localhost' for XAMPP
$dbname = 'student_notes';
$username = 'root';   // Default XAMPP username
$password = '';       // Default XAMPP password (empty)

// Test connection
echo '<h2>XAMPP MySQL Connection Test</h2>';
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo '<p class="success">MySQL Connection Successful! ✓</p>';
    
    // Check if tables exist
    $tables = ['users', 'modules', 'notes', 'admins'];
    $missing_tables = [];
    
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $missing_tables[] = $table;
        }
    }
    
    if (count($missing_tables) > 0) {
        echo '<p class="warning">Database exists but some tables are missing: ' . implode(', ', $missing_tables) . '</p>';
        echo '<p>Please import the <code>student_notes.sql</code> file to create all required tables.</p>';
    } else {
        echo '<p class="success">All required tables exist ✓</p>';
        
        // Count records
        echo '<h3>Database Statistics:</h3>';
        echo '<ul>';
        echo '<li>Users: ' . $conn->query("SELECT COUNT(*) FROM users")->fetchColumn() . ' records</li>';
        echo '<li>Modules: ' . $conn->query("SELECT COUNT(*) FROM modules")->fetchColumn() . ' records</li>';
        echo '<li>Notes: ' . $conn->query("SELECT COUNT(*) FROM notes")->fetchColumn() . ' records</li>';
        echo '</ul>';
    }
    
} catch(PDOException $e) {
    if (strpos($e->getMessage(), "Unknown database '$dbname'") !== false) {
        echo '<p class="error">Database "' . $dbname . '" does not exist ✗</p>';
        echo '<p>Please import the <code>student_notes.sql</code> file to create the database and tables.</p>';
        
        // Check if MySQL server is running by connecting without database name
        try {
            $conn = new PDO("mysql:host=$host", $username, $password);
            echo '<p class="success">MySQL server is running ✓</p>';
        } catch(PDOException $e2) {
            echo '<p class="error">MySQL Server Connection Failed: ' . $e2->getMessage() . ' ✗</p>';
            echo '<p>Please make sure XAMPP is running with MySQL service started.</p>';
        }
    } else {
        echo '<p class="error">MySQL Connection Error: ' . $e->getMessage() . ' ✗</p>';
        echo '<p>Please check your database configuration or make sure XAMPP is running.</p>';
    }
}

echo '<h2>Setup Instructions</h2>';
echo '<ol>
    <li>Start XAMPP Control Panel</li>
    <li>Start Apache and MySQL services</li>
    <li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>
    <li>Import the <code>student_notes.sql</code> file</li>
    <li>Refresh this page to verify the connection</li>
</ol>';

echo '<p><a href="index.php">Return to homepage</a></p>';
echo '</body></html>';
?>