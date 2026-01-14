<?php
require_once 'db.php';

try {
    // 1. Insert/Update Admin User
    $email = 'admin@school.com';
    $password = 'admin123'; // In a real app, hash this!
    $username = 'Root Admin';
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "User $email already exists. Updating password...\n";
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->execute([$password, $email]);
    } else {
        echo "Creating user $email...\n";
        // Assuming table columns based on previous inspection
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role_id, is_active) VALUES (?, ?, ?, 1, 1)");
        $stmt->execute([$username, $email, $password]);
    }
    
    echo "Seed completed successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
