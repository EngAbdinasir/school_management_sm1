<?php
// api_handler.php - Backend for AJAX requests
header('Content-Type: application/json');
require_once 'db.php';

// Basic security check (ensure user is logged in if applicable, though session usually handles this via include in index)
// Since this is called via AJAX, we should probably start session if not started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for POST action
$action = $_POST['action'] ?? '';

if (!$action) {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    exit;
}

$table = $_POST['table'] ?? '';
$id = $_POST['id'] ?? '';

// Whitelist tables
$allowed_tables = [
    'students', 'teachers', 'staff', 'parents', 'users',
    'classes', 'subjects', 'class_subjects', 'teacher_subjects',
    'enrollments', 'timetable', 'attendance', 'grades',
    'payments', 'receipts', 'suppliers',
    'roles', 'addresses'
];

if (!in_array($table, $allowed_tables)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid table']);
    exit;
}

// Helper function to map foreign key field names to table names
function getForeignKeyTableName($fieldName) {
    // Remove _id suffix
    $baseName = substr($fieldName, 0, -3);
    
    // Handle special cases and irregular plurals
    $specialMappings = [
        'class' => 'classes',
        'subject' => 'subjects',
        'address' => 'addresses',
        'staff' => 'staff',  // staff is already plural
        'parent' => 'parents',
        'student' => 'students',
        'teacher' => 'teachers',
        'user' => 'users',
        'supplier' => 'suppliers',
        'role' => 'roles',
        'grade' => 'grades'
    ];
    
    // Check if we have a special mapping
    if (isset($specialMappings[$baseName])) {
        return $specialMappings[$baseName];
    }
    
    // Default: add 's' for simple pluralization
    return $baseName . 's';
}

try {
    // ------------------------------------------------------------------
    // ACTION: FETCH RECORD (Enhanced)
    // ------------------------------------------------------------------
    if ($action === 'fetch_record') {
        if (!$id) throw new Exception("ID required");

        // Dynamically find the primary key
        $stmt = $pdo->prepare("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
        $stmt->execute();
        $pkey_row = $stmt->fetch();
        $primary_key = $pkey_row['Column_name'];

        $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE `$primary_key` = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Get Schema Info for Rich Form
            $stmt = $pdo->prepare("DESCRIBE `$table`");
            $stmt->execute();
            $columns_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $schemaData = [];
            $fkOptions = [];

            foreach($columns_info as $col) {
                $fieldName = $col['Field'];
                $currValue = $row[$fieldName];
                
                $simpleType = 'text';
                if (strpos($col['Type'], 'int') !== false) $simpleType = 'number';
                if (strpos($col['Type'], 'date') !== false) $simpleType = 'date';
                if (strpos($col['Type'], 'text') !== false) $simpleType = 'textarea';
                
                $isAuto = (strpos($col['Extra'], 'auto_increment') !== false);
                $isFk = false;
                $fkTable = '';
                
                 if (!$isAuto && substr($fieldName, -3) === '_id') {
                     $potentialTable = getForeignKeyTableName($fieldName);
                     
                     if (in_array($potentialTable, $allowed_tables)) {
                         $isFk = true;
                         $fkTable = $potentialTable;
                         
                         if (!isset($fkOptions[$fkTable])) {
                             $fk_columns_stmt = $pdo->prepare("DESCRIBE `$fkTable`");
                             $fk_columns_stmt->execute();
                             $fk_cols = $fk_columns_stmt->fetchAll(PDO::FETCH_COLUMN);
                             
                             $displayCol = $fk_cols[0];
                             foreach(['name', 'username', 'title', 'class_name', 'subject_name', 'full_name'] as $candidate) {
                                 foreach($fk_cols as $c) {
                                     if ($c === $candidate || (strpos($c, 'name') !== false && strpos($c, '_id') === false)) {
                                         $displayCol = $c;
                                         break 2;
                                     }
                                 }
                             }
                             
                             $pkVal = $fk_cols[0]; 
                             $opts_stmt = $pdo->prepare("SELECT `$pkVal` as id, `$displayCol` as label FROM `$fkTable` ORDER BY `$displayCol` ASC LIMIT 100");
                             $opts_stmt->execute();
                             $fkOptions[$fkTable] = $opts_stmt->fetchAll(PDO::FETCH_ASSOC);
                         }
                     }
                 }

                $schemaData[$fieldName] = [
                    'value' => $currValue,
                    'type' => $simpleType,
                    'is_auto' => $isAuto,
                    'is_fk' => $isFk,
                    'fk_table' => $fkTable
                ];
            }

            echo json_encode(['status' => 'success', 'data' => ['columns' => $schemaData, 'fk_options' => $fkOptions]]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Record not found']);
        }
    }

    // ------------------------------------------------------------------
    // ACTION: FETCH COLUMNS (For Insert - Enhanced)
    // ------------------------------------------------------------------
    elseif ($action === 'fetch_columns') {
         $stmt = $pdo->prepare("DESCRIBE `$table`");
         $stmt->execute();
         $columns_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
         
         $schemaData = [];
         $fkOptions = [];

         foreach($columns_info as $col) {
             $fieldName = $col['Field'];
             $simpleType = 'text';
             if (strpos($col['Type'], 'int') !== false) $simpleType = 'number';
             if (strpos($col['Type'], 'date') !== false) $simpleType = 'date';
             if (strpos($col['Type'], 'text') !== false) $simpleType = 'textarea';
             
             // Auto-increment detection
             $isAuto = (strpos($col['Extra'], 'auto_increment') !== false);

             // Foreign Key Detection (Heuristic: ends with _id, and not primary key of this table if auto-inc)
             $isFk = false;
             $fkTable = '';
             
             // Simple heuristic: field 'class_id' -> table 'classes'
             // 'created_by' -> 'users' ? (Might need manual mapping later, stick to simple for now)
             if (!$isAuto && substr($fieldName, -3) === '_id') {
                 $potentialTable = getForeignKeyTableName($fieldName);
                 
                 // Verify table exists by whitelist
                 if (in_array($potentialTable, $allowed_tables)) {
                     $isFk = true;
                     $fkTable = $potentialTable;
                     
                     // Fetch Options for this FK if not already fetched
                     if (!isset($fkOptions[$fkTable])) {
                         // Decide what to show (name, title, username?)
                         // We need a smart way to get the "Display Name" column
                         $fk_columns_stmt = $pdo->prepare("DESCRIBE `$fkTable`");
                         $fk_columns_stmt->execute();
                         $fk_cols = $fk_columns_stmt->fetchAll(PDO::FETCH_COLUMN);
                         
                         $displayCol = $fk_cols[0]; // fallback to ID
                         foreach(['name', 'username', 'title', 'class_name', 'subject_name', 'full_name'] as $candidate) {
                             // Check for exact match or contains
                             foreach($fk_cols as $c) {
                                 if ($c === $candidate || (strpos($c, 'name') !== false && strpos($c, '_id') === false)) {
                                     $displayCol = $c;
                                     break 2;
                                 }
                             }
                         }
                         
                         $pkVal = $fk_cols[0]; // Assuming 1st col is PK
                         $opts_stmt = $pdo->prepare("SELECT `$pkVal` as id, `$displayCol` as label FROM `$fkTable` ORDER BY `$displayCol` ASC LIMIT 100");
                         $opts_stmt->execute();
                         $fkOptions[$fkTable] = $opts_stmt->fetchAll(PDO::FETCH_ASSOC);
                     }
                 }
             }

             $schemaData[$fieldName] = [
                 'value' => '',
                 'type' => $simpleType,
                 'is_auto' => $isAuto,
                 'is_fk' => $isFk,
                 'fk_table' => $fkTable
             ];
         }
         
         echo json_encode(['status' => 'success', 'data' => ['columns' => $schemaData, 'fk_options' => $fkOptions]]);
    }

    // ------------------------------------------------------------------
    // ACTION: UPDATE RECORD
    // ------------------------------------------------------------------
    elseif ($action === 'update_record') {
        if (!$id) throw new Exception("ID required");
        
        // Remove action, table, id from the update data
        $updateData = $_POST;
        unset($updateData['action'], $updateData['table'], $updateData['id']);
        
        if (empty($updateData)) {
            echo json_encode(['status' => 'success', 'message' => 'No changes made']);
            exit;
        }

        // Get Primary Key
        $stmt = $pdo->prepare("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
        $stmt->execute();
        $pkey_row = $stmt->fetch();
        $primary_key = $pkey_row['Column_name'];

        // Build Update Query
        $setClauses = [];
        $params = [];
        
        // Verify columns actually exist
        $stmt = $pdo->prepare("DESCRIBE `$table`");
        $stmt->execute();
        $db_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($updateData as $key => $value) {
            if (in_array($key, $db_columns)) {
                $setClauses[] = "`$key` = ?";
                $params[] = $value;
            }
        }
        
        if (empty($setClauses)) {
             echo json_encode(['status' => 'error', 'message' => 'No valid columns to update']);
             exit;
        }

        // Validate foreign key references before update
        foreach ($updateData as $key => $value) {
            if (substr($key, -3) === '_id' && !empty($value) && in_array($key, $db_columns)) {
                $potentialTable = getForeignKeyTableName($key);
                
                if (in_array($potentialTable, $allowed_tables)) {
                    // Get the primary key of the referenced table
                    $pkStmt = $pdo->prepare("SHOW KEYS FROM `$potentialTable` WHERE Key_name = 'PRIMARY'");
                    $pkStmt->execute();
                    $pkRow = $pkStmt->fetch();
                    $refPrimaryKey = $pkRow['Column_name'] ?? $key; // fallback to $key if not found
                    
                    // Check if the referenced record exists
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `$potentialTable` WHERE `$refPrimaryKey` = ?");
                    $checkStmt->execute([$value]);
                    $count = $checkStmt->fetchColumn();
                    
                    if ($count == 0) {
                        $friendlyName = ucfirst(str_replace('_', ' ', substr($key, 0, -3)));
                        echo json_encode([
                            'status' => 'error', 
                            'message' => "$friendlyName with ID $value does not exist. Please select a valid $friendlyName."
                        ]);
                        exit;
                    }
                }
            }
        }

        $params[] = $id; // For WHERE clause

        $sql = "UPDATE `$table` SET " . implode(', ', $setClauses) . " WHERE `$primary_key` = ?";
        
        try {
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($params);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => ucfirst($table) . ' updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Update failed']);
            }
        } catch (PDOException $e) {
            // Handle foreign key constraint violations
            if ($e->getCode() == '23000') {
                $errorMsg = $e->getMessage();
                if (preg_match('/FOREIGN KEY \(`([^`]+)`\)/', $errorMsg, $matches)) {
                    $fieldName = $matches[1];
                    $friendlyName = ucfirst(str_replace('_', ' ', substr($fieldName, 0, -3)));
                    echo json_encode([
                        'status' => 'error',
                        'message' => "Invalid $friendlyName selected. The referenced $friendlyName does not exist in the database."
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Foreign key constraint violation. Please ensure all referenced records exist.'
                    ]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }
    }

    // ------------------------------------------------------------------
    // ACTION: INSERT RECORD
    // ------------------------------------------------------------------
    elseif ($action === 'insert_record') {
        $insertData = $_POST;
        unset($insertData['action'], $insertData['table'], $insertData['id']); // ID might be empty or auto-inc

        if (empty($insertData)) {
            echo json_encode(['status' => 'error', 'message' => 'No data provided']);
            exit;
        }

        // Verify columns
        $stmt = $pdo->prepare("DESCRIBE `$table`");
        $stmt->execute();
        $db_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Filter valid columns
        $validData = [];
        foreach ($insertData as $key => $value) {
            if (in_array($key, $db_columns)) {
                $validData[$key] = $value;
            }
        }

        if (empty($validData)) {
             echo json_encode(['status' => 'error', 'message' => 'No valid columns to insert']);
             exit;
        }

        // Validate foreign key references before insert
        foreach ($validData as $key => $value) {
            if (substr($key, -3) === '_id' && !empty($value)) {
                $potentialTable = getForeignKeyTableName($key);
                
                if (in_array($potentialTable, $allowed_tables)) {
                    // Get the primary key of the referenced table
                    $pkStmt = $pdo->prepare("SHOW KEYS FROM `$potentialTable` WHERE Key_name = 'PRIMARY'");
                    $pkStmt->execute();
                    $pkRow = $pkStmt->fetch();
                    $refPrimaryKey = $pkRow['Column_name'] ?? $key; // fallback to $key if not found
                    
                    // Check if the referenced record exists
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `$potentialTable` WHERE `$refPrimaryKey` = ?");
                    $checkStmt->execute([$value]);
                    $count = $checkStmt->fetchColumn();
                    
                    if ($count == 0) {
                        $friendlyName = ucfirst(str_replace('_', ' ', substr($key, 0, -3)));
                        echo json_encode([
                            'status' => 'error', 
                            'message' => "$friendlyName with ID $value does not exist. Please select a valid $friendlyName."
                        ]);
                        exit;
                    }
                }
            }
        }

        $columnsStr = implode(", ", array_map(function($c) { return "`$c`"; }, array_keys($validData)));
        $placeholders = implode(", ", array_fill(0, count($validData), "?"));
        $values = array_values($validData);

        $sql = "INSERT INTO `$table` ($columnsStr) VALUES ($placeholders)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Record created successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
            }
        } catch (PDOException $e) {
            // Handle foreign key constraint violations
            if ($e->getCode() == '23000') {
                // Extract the constraint name from error message
                $errorMsg = $e->getMessage();
                if (preg_match('/FOREIGN KEY \(`([^`]+)`\)/', $errorMsg, $matches)) {
                    $fieldName = $matches[1];
                    $friendlyName = ucfirst(str_replace('_', ' ', substr($fieldName, 0, -3)));
                    echo json_encode([
                        'status' => 'error',
                        'message' => "Invalid $friendlyName selected. The referenced $friendlyName does not exist in the database."
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Foreign key constraint violation. Please ensure all referenced records exist.'
                    ]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }
    }

    // ------------------------------------------------------------------
    // ACTION: DELETE RECORD
    // ------------------------------------------------------------------
    elseif ($action === 'delete_record') {
        if (!$id) throw new Exception("ID required");

        // Get Primary Key
        $stmt = $pdo->prepare("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
        $stmt->execute();
        $pkey_row = $stmt->fetch();
        $primary_key = $pkey_row['Column_name'];

        // Special-case: prevent deleting roles that are still assigned to users
        // This matches the FK seen in the UI error: users.role_id -> roles.role_id
        if ($table === 'roles') {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `role_id` = ?");
            $checkStmt->execute([$id]);
            $inUseCount = (int)$checkStmt->fetchColumn();

            if ($inUseCount > 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => "Can't delete this role because it is assigned to $inUseCount user(s). Reassign those users to another role first."
                ]);
                exit;
            }
        }

        // Prevent deleting payments that have linked receipts (if the column exists)
        if ($table === 'payments') {
            $colCheck = $pdo->prepare("SHOW COLUMNS FROM `receipts` LIKE 'payment_id'");
            $colCheck->execute();
            if ($colCheck->fetch()) {
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM `receipts` WHERE `payment_id` = ?");
                $checkStmt->execute([$id]);
                $linkedReceipts = (int)$checkStmt->fetchColumn();
                if ($linkedReceipts > 0) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => "Can't delete this payment because $linkedReceipts receipt(s) reference it. Delete or reassign those receipts first."
                    ]);
                    exit;
                }
            }
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM `$table` WHERE `$primary_key` = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
            }
        } catch (PDOException $e) {
            // Handle foreign key constraint violations (e.g., attempting to delete a parent row)
            if ($e->getCode() == '23000') {
                echo json_encode([
                    'status' => 'error',
                    'message' => "Can't delete this record because it is being used by other records. Remove or reassign the related records first."
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
