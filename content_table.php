<?php
// content_table.php - High Fidelity Table Viewer

$table = isset($_GET['table']) ? $_GET['table'] : '';

// Validation
$allowed_tables = [
    'students', 'teachers', 'staff', 'parents', 'users',
    'classes', 'subjects', 'class_subjects', 'teacher_subjects',
    'enrollments', 'timetable', 'attendance', 'grades',
    'payments', 'receipts', 'suppliers',
    'roles', 'addresses'
];

if (!in_array($table, $allowed_tables)) {
    echo "<div class='alert alert-danger'>Invalid table selected.</div>";
    return;
}

try {
    // Get column names
    $stmt = $pdo->prepare("DESCRIBE `$table`");
    $stmt->execute();
    $columns_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $columns = array_column($columns_info, 'Field');

    // Build Dynamic JOIN Query
    $selectFields = ["`$table`.*"];
    $joins = [];
    
    // Heuristic Map: which table gives what display column
    // This could also be a config or dynamic detection
    $tableDisplayMap = [
        'parents' => 'full_name',
        'students' => 'full_name',
        'teachers' => 'full_name',
        'users' => 'username',
        'classes' => 'class_name',
        'subjects' => 'subject_name',
        'addresses' => 'city', // or street?
        'roles' => 'role_name'
    ];

    // Track searchable fields (including joined ones)
    $searchableFields = [];

    foreach($columns as $col) {
        // Condition: Primary Key OR Column Name contains "name", "username", "title"
        $isPK = ($col === $columns[0]);
        $isName = (strpos($col, 'name') !== false || strpos($col, 'username') !== false || strpos($col, 'title') !== false || strpos($col, 'email') !== false);

        if ($isPK || $isName) {
            $searchableFields[] = "`$table`.`$col`";
        }

        // Check for FK
        if (substr($col, -3) === '_id' && $col !== $columns[0]) {
            $fkTableBase = substr($col, 0, -3);
            
            // Singular/Plural fixes
            if ($fkTableBase === 'class') $fkTable = 'classes';
            else if ($fkTableBase === 'address') $fkTable = 'addresses';
            else if ($fkTableBase === 'role') $fkTable = 'roles'; 
            else if ($fkTableBase === 'subject') $fkTable = 'subjects';
            else $fkTable = $fkTableBase . 's'; 
            
            // Verify join table exists (check whitelist keys) and we have a display field preference
            if (in_array($fkTable, $allowed_tables) && isset($tableDisplayMap[$fkTable])) {
                $displayCol = $tableDisplayMap[$fkTable];
                
                // Alias: parent_id -> parent_id_joined
                $alias = $col . '_joined';
                $selectFields[] = "`$fkTable`.`$displayCol` AS `$alias`";
                
                // Add Joined Column to Searchable Fields IF it is relevant (Name/Title)
                // We assume the display map usually points to a name, but let's double check
                if (strpos($displayCol, 'name') !== false || strpos($displayCol, 'username') !== false || strpos($displayCol, 'title') !== false || strpos($displayCol, 'city') !== false) {
                     $searchableFields[] = "`$fkTable`.`$displayCol`";
                }

                $pkName = $fkTableBase . '_id'; 
                
                $joins[] = "LEFT JOIN `$fkTable` ON `$table`.`$col` = `$fkTable`.`$pkName`";
            }
        }
    }

    // Search Logic
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $searchWhere = "";
    $searchParams = [];

    if ($search) {
        $searchConditions = [];
        // Use the collected searchableFields instead of just raw columns
        foreach($searchableFields as $field) {
            $searchConditions[] = "$field LIKE ?";
            $searchParams[] = "%$search%";
        }
        
        if (!empty($searchConditions)) {
            $searchWhere = " WHERE (" . implode(" OR ", $searchConditions) . ")";
        }
    }

    $sql = "SELECT " . implode(', ', $selectFields) . " FROM `$table` " . implode(' ', $joins) . $searchWhere . " ORDER BY `$table`." . $columns[0] . " DESC LIMIT 50";

    // Get Data
    $stmt = $pdo->prepare($sql);
    $stmt->execute($searchParams);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $table_title = ucfirst(str_replace('_', ' ', $table));
    $record_count = count($rows); 

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    return;
}

// Helpers for UI logic
function getInitials($name) {
    $parts = explode(' ', $name);
    return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}

function getRandomColor() {
    $colors = ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-dark'];
    return $colors[array_rand($colors)];
}

// Highlight Helper
function highlightMatch($text, $search) {
    if ($text === null) $text = ''; 
    $text = htmlspecialchars($text);
    if (!$search) return $text;
    
    // Case insensitive replace
    return preg_replace('/(' . preg_quote($search, '/') . ')/i', '<mark class="p-0 bg-warning bg-opacity-25 text-dark fw-bold">$1</mark>', $text);
}

// Check for specific columns to enable smart UI
$has_name = false;
$name_col = '';
$has_status = false;
$status_col = '';
$has_email = false;
$email_col = '';

foreach($columns as $col) {
    if (strpos($col, 'name') !== false && !$has_name) { $has_name = true; $name_col = $col; }
    if (strpos($col, 'status') !== false || strpos($col, 'active') !== false) { $has_status = true; $status_col = $col; }
    if (strpos($col, 'email') !== false) { $has_email = true; $email_col = $col; }
}

?>



<style>
    .table-header-section {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border-color);
    }

    .table-title {
        font-size: 1.75rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .record-badge {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .action-btn {
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid var(--border-color);
        background: var(--card-bg);
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: var(--primary-blue);
    }

    .search-card {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .search-card:focus-within {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .table-card {
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .table thead {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08), rgba(118, 75, 162, 0.08));
    }

    .table thead th {
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-heading);
        padding: 1.25rem 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .table tbody tr:hover {
        background: rgba(102, 126, 234, 0.03);
        transform: scale(1.01);
    }

    .table tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
    }

    .btn-edit, .btn-delete {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border: 2px solid var(--border-color);
    }

    .btn-edit:hover {
        background: rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
        color: #3b82f6;
        transform: translateY(-2px);
    }

    .btn-delete:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: #ef4444;
        color: #ef4444;
        transform: translateY(-2px);
    }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }

    .pagination-wrapper {
        background: var(--main-bg);
        padding: 1.25rem 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .page-link {
        border-radius: 8px;
        margin: 0 2px;
        border: none;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .page-link:hover {
        background: var(--active-bg);
        color: var(--primary-blue);
    }
</style>

<div class="table-header-section">
    <div class="row align-items-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-3">
                <h2 class="table-title mb-0"><?php echo $table_title; ?> Records</h2>
                <span class="record-badge">
                    <i class="bi bi-database me-1"></i><?php echo $record_count; ?> Records
                </span>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <div class="d-flex gap-2 justify-content-end">
                <button class="btn action-btn">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <button class="btn action-btn">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <button class="btn btn-primary btn-insert" data-table="<?php echo $table; ?>">
                    <i class="bi bi-plus-lg me-1"></i> Insert New Record
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Search Bar -->
<div class="search-card">
    <div class="d-flex align-items-center">
        <i class="bi bi-search text-muted fs-5 me-3" id="searchIconBtn" style="cursor: pointer;" title="Click to Search"></i>
        <input type="text" id="tableSearchInput" class="form-control border-0 shadow-none form-control-lg bg-transparent" placeholder="Search by name, ID, or class..." value="<?php echo htmlspecialchars($search); ?>">
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4" style="width: 50px;">
                        <input type="checkbox" class="form-check-input">
                    </th>
                    <?php foreach($columns as $col): ?>
                        <th class="text-secondary text-uppercase small fw-bold py-3" style="letter-spacing: 0.5px;"><?php echo str_replace('_', ' ', $col); ?></th>
                    <?php endforeach; ?>
                    <th class="text-end pe-4">STATUS/ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($rows)): ?>
                    <tr>
                        <td colspan="<?php echo count($columns) + 2; ?>" class="empty-state">
                            <i class="bi bi-inbox text-muted"></i>
                            <h5 class="text-muted mt-3">No records found</h5>
                            <p class="text-muted small">Try adjusting your search or filters</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($rows as $row): ?>
                        <tr>
                            <td class="ps-4">
                                <input class="form-check-input" type="checkbox">
                            </td>
                            
                            <?php foreach($columns as $col): ?>
                                <td>
                                    <?php if ($col == $name_col): ?>
                                        <!-- Smart Avatar Name Cell -->
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white <?php echo getRandomColor(); ?>" style="width: 40px; height: 40px; font-weight: bold;">
                                                <?php echo getInitials($row[$col] ?? ''); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo highlightMatch($row[$col] ?? '', $search); ?></div>
                                                <?php if ($has_email): ?>
                                                    <div class="text-muted small"><?php echo highlightMatch($row[$email_col] ?? '', $search); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php elseif (isset($row[$col . '_joined'])): ?>
                                         <!-- JOINED COLUMN DISPLAY -->
                                         <span class="badge bg-light text-dark border"><?php echo highlightMatch($row[$col . '_joined'] ?? '', $search); ?></span>
                                         <span class="text-muted small ms-1" style="font-size: 0.65em;">(#<?php echo highlightMatch($row[$col] ?? '', $search); ?>)</span>
                                    <?php elseif ($col == $columns[0]): ?>
                                         <!-- ID Column Styling -->
                                         <span class="text-muted small text-uppercase">#<?php echo highlightMatch($row[$col] ?? '', $search); ?></span>
                                    <?php elseif ($col == $status_col): ?>
                                        <!-- Skip, handled in last column but also show raw here just in case -->
                                         <?php echo highlightMatch($row[$col] ?? '', $search); ?>
                                    <?php else: ?>
                                        <span class="text-secondary"><?php echo highlightMatch($row[$col] ?? '', $search); ?></span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            
                            <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <?php if ($has_status): ?>
                                            <?php 
                                                // Mock status logic if 1/0
                                                $status = $row[$status_col];
                                                $badge_class = 'bg-success bg-opacity-10 text-success';
                                                $badge_text = 'Active';
                                                if ($status == '0' || strtolower($status) == 'inactive') {
                                                    $badge_class = 'bg-danger bg-opacity-10 text-danger';
                                                    $badge_text = 'Inactive';
                                                } elseif (strtolower($status) == 'pending') {
                                                    $badge_class = 'bg-warning bg-opacity-10 text-warning';
                                                    $badge_text = 'Pending';
                                                }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?> rounded-pill px-3 py-2 me-2"><?php echo $badge_text; ?></span>
                                        <?php endif; ?>
                                        
                                        <button class="btn btn-edit" data-id="<?php echo $row[$columns[0]]; ?>" data-table="<?php echo $table; ?>" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-delete" data-id="<?php echo $row[$columns[0]]; ?>" data-table="<?php echo $table; ?>" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Enhanced Pagination -->
        <div class="pagination-wrapper d-flex justify-content-between align-items-center">
            <div class="text-muted small fw-medium">
                Showing <span class="fw-bold text-dark">1</span> to <span class="fw-bold text-dark"><?php echo $record_count; ?></span> of <span class="fw-bold text-dark"><?php echo $record_count * 5; ?></span> results
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">...</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">42</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
