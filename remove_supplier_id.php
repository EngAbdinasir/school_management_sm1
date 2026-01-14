<?php
/**
 * Migration: Remove supplier_id from payments table
 * 
 * This script will:
 * 1. Drop the foreign key constraint on supplier_id
 * 2. Drop the supplier_id column from the payments table
 */

require_once 'db.php';

echo "<h2>Migration: Remove supplier_id from payments table</h2>";

try {
    // Start transaction
    $pdo->beginTransaction();
    
    echo "<h3>Step 1: Finding foreign key constraint...</h3>";
    
    // Find the foreign key constraint name
    $stmt = $pdo->query("
        SELECT CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = 'SCHOOLDB' 
        AND TABLE_NAME = 'payments'
        AND COLUMN_NAME = 'supplier_id'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    $constraint = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($constraint) {
        $constraintName = $constraint['CONSTRAINT_NAME'];
        echo "<p>✓ Found constraint: <code>$constraintName</code></p>";
        
        echo "<h3>Step 2: Dropping foreign key constraint...</h3>";
        $pdo->exec("ALTER TABLE payments DROP FOREIGN KEY `$constraintName`");
        echo "<p>✓ Foreign key constraint dropped successfully</p>";
    } else {
        echo "<p>ℹ️ No foreign key constraint found for supplier_id</p>";
    }
    
    echo "<h3>Step 3: Dropping supplier_id column...</h3>";
    $pdo->exec("ALTER TABLE payments DROP COLUMN supplier_id");
    echo "<p>✓ Column supplier_id dropped successfully</p>";
    
    // Commit transaction
    $pdo->commit();
    
    echo "<h3 style='color: green;'>✅ Migration completed successfully!</h3>";
    
    // Show updated table structure
    echo "<h3>Updated Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE payments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href='index.php?table=payments'>← Back to Payments</a></p>";
    
} catch (Exception $e) {
    // Rollback on error
    $pdo->rollBack();
    echo "<h3 style='color: red;'>❌ Migration failed!</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>The database has been rolled back to its previous state.</p>";
}
?>
