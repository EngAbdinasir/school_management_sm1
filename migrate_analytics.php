<?php
require_once 'db.php';

try {
    // Check if column exists
    $stmt = $pdo->prepare("DESCRIBE students");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('created_at', $columns)) {
        echo "Adding created_at column to students table...\n";
        $pdo->exec("ALTER TABLE students ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Column added successfully.";
    } else {
        echo "Column created_at already exists.";
    }

    // Also add to teachers and users if missing, for better analytics
    if (!in_array('created_at', $columns)) { // Checking generic approach, but let's just do specific ALTERs safely
         // Assuming users table exists
         $stmt = $pdo->query("DESCRIBE users");
         $user_cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
         if (!in_array('created_at', $user_cols)) {
             $pdo->exec("ALTER TABLE users ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
             echo "Added created_at to users.\n";
         }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
