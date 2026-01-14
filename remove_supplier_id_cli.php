<?php
/**
 * CLI Migration: Remove supplier_id from payments table
 */

require_once 'db.php';

echo "=== Migration: Remove supplier_id from payments table ===\n\n";

try {
    // Start transaction
    $pdo->beginTransaction();
    
    echo "Step 1: Finding foreign key constraint...\n";
    
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
        echo "✓ Found constraint: $constraintName\n";
        
        echo "\nStep 2: Dropping foreign key constraint...\n";
        $pdo->exec("ALTER TABLE payments DROP FOREIGN KEY `$constraintName`");
        echo "✓ Foreign key constraint dropped successfully\n";
    } else {
        echo "ℹ️  No foreign key constraint found for supplier_id\n";
    }
    
    echo "\nStep 3: Dropping supplier_id column...\n";
    $pdo->exec("ALTER TABLE payments DROP COLUMN supplier_id");
    echo "✓ Column supplier_id dropped successfully\n";
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n✅ Migration completed successfully!\n\n";
    
    // Show updated table structure
    echo "Updated Table Structure:\n";
    echo str_repeat("-", 60) . "\n";
    $stmt = $pdo->query("DESCRIBE payments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        printf("%-20s %-15s %-10s %-10s\n", 
            $col['Field'], 
            $col['Type'], 
            $col['Null'], 
            $col['Key']
        );
    }
    echo str_repeat("-", 60) . "\n";
    
} catch (Exception $e) {
    // Rollback on error
    $pdo->rollBack();
    echo "\n❌ Migration failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "The database has been rolled back to its previous state.\n";
    exit(1);
}
?>
