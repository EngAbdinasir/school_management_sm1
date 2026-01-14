<?php
require_once 'db.php';

echo "<h2>Payments Table Structure</h2>";

try {
    $stmt = $pdo->query("DESCRIBE payments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "<td>{$col['Extra']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Show foreign keys
    echo "<h3>Foreign Key Constraints</h3>";
    $stmt = $pdo->query("
        SELECT 
            CONSTRAINT_NAME,
            COLUMN_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = 'SCHOOLDB' 
        AND TABLE_NAME = 'payments'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    $fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($fks)) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Constraint</th><th>Column</th><th>References</th></tr>";
        foreach ($fks as $fk) {
            echo "<tr>";
            echo "<td>{$fk['CONSTRAINT_NAME']}</td>";
            echo "<td>{$fk['COLUMN_NAME']}</td>";
            echo "<td>{$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
