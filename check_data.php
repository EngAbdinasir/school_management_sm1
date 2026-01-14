<?php
require_once 'db.php';

echo "<h2>Database Check</h2>";

// Check staff table
echo "<h3>Staff Table:</h3>";
$stmt = $pdo->query("SELECT * FROM staff LIMIT 10");
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($staff)) {
    echo "<p style='color: red;'>⚠️ No staff records found! This is why the foreign key constraint is failing.</p>";
    echo "<p>You need to add staff members before creating payment records.</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>";
    foreach (array_keys($staff[0]) as $col) {
        echo "<th>$col</th>";
    }
    echo "</tr>";
    foreach ($staff as $row) {
        echo "<tr>";
        foreach ($row as $val) {
            echo "<td>$val</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Check suppliers table
echo "<h3>Suppliers Table:</h3>";
$stmt = $pdo->query("SELECT * FROM suppliers LIMIT 10");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($suppliers)) {
    echo "<p style='color: red;'>⚠️ No supplier records found!</p>";
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr>";
    foreach (array_keys($suppliers[0]) as $col) {
        echo "<th>$col</th>";
    }
    echo "</tr>";
    foreach ($suppliers as $row) {
        echo "<tr>";
        foreach ($row as $val) {
            echo "<td>$val</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
?>
