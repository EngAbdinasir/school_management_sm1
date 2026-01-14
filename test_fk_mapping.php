<?php
// Test the foreign key table name mapping
require_once 'api_handler.php';

echo "<h2>Foreign Key Table Name Mapping Test</h2>";

$testFields = [
    'staff_id',
    'supplier_id',
    'class_id',
    'subject_id',
    'student_id',
    'teacher_id',
    'parent_id',
    'user_id',
    'address_id',
    'role_id'
];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field Name</th><th>Mapped Table</th><th>Status</th></tr>";

foreach ($testFields as $field) {
    $tableName = getForeignKeyTableName($field);
    $allowed_tables = [
        'students', 'teachers', 'staff', 'parents', 'users',
        'classes', 'subjects', 'class_subjects', 'teacher_subjects',
        'enrollments', 'timetable', 'attendance', 'grades',
        'payments', 'receipts', 'suppliers',
        'roles', 'addresses'
    ];
    
    $status = in_array($tableName, $allowed_tables) ? 
        "<span style='color: green;'>✓ Valid</span>" : 
        "<span style='color: red;'>✗ Invalid</span>";
    
    echo "<tr>";
    echo "<td><code>$field</code></td>";
    echo "<td><code>$tableName</code></td>";
    echo "<td>$status</td>";
    echo "</tr>";
}

echo "</table>";
?>
