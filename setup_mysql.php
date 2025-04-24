<?php
// Setup script to create MySQL database and import schema

// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'ZAk&#sql*#*192';
$dbname = 'student_notes';

echo "<h1>MySQL Database Setup</h1>";

try {
    // Connect to MySQL without specifying a database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>Connected to MySQL successfully ✓</p>";
    
    // Check if database exists
    $stmt = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    $dbExists = $stmt->fetchColumn();
    
    if (!$dbExists) {
        // Create database
        $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "<p>Database '$dbname' created successfully ✓</p>";
    } else {
        echo "<p>Database '$dbname' already exists ✓</p>";
    }
    
    // Select the database
    $conn->exec("USE `$dbname`");
    
    // Create tables from SQL dump structure
    echo "<h2>Creating Tables:</h2>";
    
    // Admins table
    $conn->exec("CREATE TABLE IF NOT EXISTS `admins` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(50) NOT NULL,
      `password` varchar(255) NOT NULL,
      `email` varchar(100) NOT NULL,
      `role` varchar(50) DEFAULT 'admin',
      `admin` varchar(30) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "<p>Table 'admins' created ✓</p>";
    
    // Users table
    $conn->exec("CREATE TABLE IF NOT EXISTS `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(50) NOT NULL,
      `password` varchar(255) NOT NULL,
      `email` varchar(100) NOT NULL,
      `id_admin` int(11) DEFAULT NULL,
      `role` enum('user','admin') DEFAULT 'user',
      `created_at` datetime DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `username` (`username`),
      UNIQUE KEY `idx_email` (`email`),
      KEY `users_ibfk_1` (`id_admin`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "<p>Table 'users' created ✓</p>";
    
    // Modules table
    $conn->exec("CREATE TABLE IF NOT EXISTS `modules` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `name` varchar(100) NOT NULL,
      `created_at` datetime DEFAULT current_timestamp(),
      `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "<p>Table 'modules' created ✓</p>";
    
    // Notes table
    $conn->exec("CREATE TABLE IF NOT EXISTS `notes` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `module_id` int(11) NOT NULL,
      `title` varchar(255) NOT NULL,
      `content` text NOT NULL,
      `created_at` datetime DEFAULT current_timestamp(),
      `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      KEY `module_id` (`module_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "<p>Table 'notes' created ✓</p>";
    
    // Add foreign key constraints
    try {
        // Foreign keys for modules table
        $conn->exec("ALTER TABLE `modules` 
          ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE");
        
        // Foreign keys for notes table
        $conn->exec("ALTER TABLE `notes` 
          ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
          ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE");
        
        // Foreign keys for users table
        $conn->exec("ALTER TABLE `users` 
          ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id`) ON DELETE SET NULL");
        
        echo "<p>Foreign key constraints added ✓</p>";
    } catch(PDOException $e) {
        echo "<p>Note: Foreign key constraints not added (might exist already). " . $e->getMessage() . "</p>";
    }
    
    // Insert dummy admin if needed
    $stmt = $conn->query("SELECT COUNT(*) FROM `users` WHERE role = 'admin'");
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount == 0) {
        // Create a default admin user with password 'admin123'
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->exec("INSERT INTO `users` (`username`, `password`, `email`, `role`) VALUES 
            ('admin', '$adminPass', 'admin@example.com', 'admin')");
        echo "<p>Default admin user created ✓</p>";
    } else {
        echo "<p>Admin users already exist ✓</p>";
    }
    
    echo "<h2>Setup Complete!</h2>";
    echo "<p>The MySQL database is now set up and ready to use.</p>";
    echo "<p><a href='index.php'>Return to homepage</a></p>";
    
} catch(PDOException $e) {
    echo "<h2>Setup Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure MySQL is running and accessible with the provided credentials.</p>";
}
?>