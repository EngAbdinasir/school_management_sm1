<?php
require_once 'db.php';
$tables = ['students', 'classes', 'payments', 'receipts'];
foreach($tables as $t) {
    echo "TABLE: $t\n";
    $stmt = $pdo->query("DESCRIBE `$t`");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($cols as $c) {
        echo "  " . $c['Field'] . " (" . $c['Type'] . ") " . $c['Extra'] . "\n";
    }
    echo "\n";
}
?>
