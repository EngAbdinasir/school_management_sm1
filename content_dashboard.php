<?php
// content_dashboard.php

// 1. Data Fetching (Reusing existing logic)
function getCount($pdo, $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        return $stmt->fetchColumn();
    } catch (Exception $e) { return 0; }
}
function getSum($pdo, $table, $column) {
    try {
        @$stmt = $pdo->query("SELECT SUM(`$column`) FROM `$table`");
        return $stmt->fetchColumn() ?: 0;
    } catch (Exception $e) { return 0; }
}

$count_users = getCount($pdo, 'students'); // "Total Students"
$count_staff = getCount($pdo, 'teachers') + getCount($pdo, 'users'); // "Staff Members"
$revenue = getSum($pdo, 'payments', 'amount'); // "Revenue"
// Mock Attendance Rate
$attendance_rate = 94; 

// 2. Growth Data (User Engagement)
$growth_labels = [];
$growth_data = [];
for ($i = 9; $i >= 0; $i--) {
    $growth_labels[] = date('M d', strtotime("-$i days"));
    $growth_data[] = rand(50, 200);
}

// 3. Recent Activity Mock
$recent_activities = [
    ['user' => 'Sarah J.', 'action' => 'Updated Grade Book', 'time' => '2 mins ago', 'avatar' => 'S'],
    ['user' => 'Mike T.', 'action' => 'Added New Student', 'time' => '1 hour ago', 'avatar' => 'M'],
    ['user' => 'Eleanor P.', 'action' => 'Generated Report', 'time' => '3 hours ago', 'avatar' => 'E'],
    ['user' => 'Robert C.', 'action' => 'Payment processed', 'time' => '5 hours ago', 'avatar' => 'R']
];

?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .dashboard-hero {
        background: linear-gradient(135deg, rgba(102,126,234,0.12), rgba(118,75,162,0.12));
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px 22px;
        box-shadow: var(--shadow-md);
        margin-bottom: 20px;
    }
    .dashboard-hero h4 {
        font-weight: 800;
        color: var(--text-heading);
    }
    .dashboard-hero p {
        color: var(--text-color);
        margin-bottom: 0;
    }
    .metric-card {
        border: 1px solid var(--border-color);
        border-radius: 14px;
        background: var(--card-bg);
        box-shadow: var(--shadow-sm);
        padding: 16px;
        transition: all 0.3s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
    }
    .card-elevated {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        background: var(--card-bg);
    }
    [data-theme="dark"] .dashboard-hero {
        background: linear-gradient(135deg, rgba(102,126,234,0.2), rgba(118,75,162,0.2));
    }
</style>

<div class="dashboard-hero">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="mb-1">Analytics Overview</h4>
            <p class="small">Live snapshot of key school metrics</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-light text-dark border">Realtime</span>
            <span class="badge bg-primary text-white">Updated Today</span>
        </div>
    </div>
</div>

<!-- INFO CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="text-muted fw-semibold text-uppercase small mb-1">Total Students</div>
                    <div class="h3 fw-bold text-dark mb-0"><?php echo number_format($count_users); ?></div>
                </div>
                <div class="metric-icon" style="background: linear-gradient(135deg, #34d399, #10b981);"><i class="bi bi-mortarboard-fill"></i></div>
            </div>
            <div class="d-flex align-items-center text-success fw-semibold small">
                <i class="bi bi-graph-up-arrow me-1"></i> 5.2% <span class="text-muted fw-normal ms-1">vs last month</span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="text-muted fw-semibold text-uppercase small mb-1">Staff Members</div>
                    <div class="h3 fw-bold text-dark mb-0"><?php echo number_format($count_staff); ?></div>
                </div>
                <div class="metric-icon" style="background: linear-gradient(135deg, #60a5fa, #2563eb);"><i class="bi bi-briefcase-fill"></i></div>
            </div>
            <div class="d-flex align-items-center text-success fw-semibold small">
                <i class="bi bi-graph-up-arrow me-1"></i> 1.8% <span class="text-muted fw-normal ms-1">vs last month</span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="text-muted fw-semibold text-uppercase small mb-1">Revenue</div>
                    <div class="h3 fw-bold text-dark mb-0">$<?php echo number_format($revenue); ?></div>
                </div>
                <div class="metric-icon" style="background: linear-gradient(135deg, #a855f7, #7c3aed);"><i class="bi bi-cash-coin"></i></div>
            </div>
            <div class="d-flex align-items-center text-success fw-semibold small">
                <i class="bi bi-graph-up-arrow me-1"></i> 12.4% <span class="text-muted fw-normal ms-1">vs last month</span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="text-muted fw-semibold text-uppercase small mb-1">Attendance Rate</div>
                    <div class="h3 fw-bold text-dark mb-0"><?php echo $attendance_rate; ?>%</div>
                </div>
                <div class="metric-icon" style="background: linear-gradient(135deg, #fbbf24, #f59e0b);"><i class="bi bi-calendar-check-fill"></i></div>
            </div>
            <div class="d-flex align-items-center text-danger fw-semibold small">
                <i class="bi bi-graph-down-arrow me-1"></i> 0.5% <span class="text-muted fw-normal ms-1">vs last month</span>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS ROW -->
<div class="row g-4 mb-4">
    <!-- User Engagement (Teal Smooth Area) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="fw-bold text-dark mb-1">User Engagement</h6>
                    <small class="text-muted">Daily active logins over the last 30 days</small>
                </div>
                <div class="bg-light rounded p-1 d-flex">
                    <button class="btn btn-sm btn-dark text-white rounded px-3">30 Days</button>
                    <button class="btn btn-sm text-muted">90 Days</button>
                </div>
            </div>
            <div style="height: 300px; width: 100%;">
                <canvas id="engagementChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Capacity (Big Donut) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold text-dark mb-1">Data Capacity</h6>
            <small class="text-muted d-block mb-4">System storage overview</small>
            
            <div style="height: 220px; position: relative;">
                <canvas id="capacityDonut"></canvas>
                 <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                    <h2 class="fw-bold m-0 text-dark">76%</h2>
                    <small class="text-muted small fw-bold">USED</small>
                </div>
            </div>
            
            <div class="mt-4">
                 <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted"><span class="text-primary me-2">●</span> System Backups</span>
                    <span class="fw-bold text-dark">450 GB</span>
                 </div>
                 <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted"><span class="text-success me-2">●</span> Media Files</span>
                    <span class="fw-bold text-dark">210 GB</span>
                 </div>
                 <div class="d-flex justify-content-between small">
                    <span class="text-muted"><span style="color: #6366f1" class="me-2">●</span> Student Records</span>
                    <span class="fw-bold text-dark">85 GB</span>
                 </div>
            </div>
        </div>
    </div>
</div>

<!-- BOTTOM ROW -->
<div class="row g-4">
    <!-- Monthly Transactions -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                     <h6 class="fw-bold text-dark mb-1">Monthly Transactions</h6>
                     <small class="text-muted">Revenue vs Expenses</small>
                </div>
                <button class="btn btn-sm btn-light border"><i class="bi bi-bar-chart"></i></button>
            </div>
            <div style="height: 200px; width: 100%;">
                 <canvas id="txChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                     <h6 class="fw-bold text-dark mb-1">Recent Activity</h6>
                     <small class="text-muted">Latest administrative actions</small>
                </div>
                <a href="#" class="text-decoration-none small fw-bold text-primary">View All</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead class="text-muted small fw-bold text-uppercase border-bottom">
                        <tr>
                            <th class="ps-0 fw-normal py-2">User</th>
                            <th class="fw-normal py-2">Action</th>
                            <th class="text-end fw-normal py-2 pe-0">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_activities as $act): ?>
                        <tr>
                            <td class="ps-0 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center small fw-bold text-secondary" style="width: 32px; height: 32px;">
                                        <?php echo $act['avatar']; ?>
                                    </div>
                                    <span class="text-dark fw-bold small"><?php echo $act['user']; ?></span>
                                </div>
                            </td>
                            <td class="text-muted small py-3"><?php echo $act['action']; ?></td>
                            <td class="text-end text-muted small py-3 pe-0"><?php echo $act['time']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const root = document.documentElement;
    const isDark = () => root.getAttribute('data-theme') === 'dark';
    const baseText = () => getComputedStyle(root).getPropertyValue('--text-color') || '#64748b';
    const gridColor = () => isDark() ? '#1f2937' : '#f1f5f9';
    const labelColor = () => isDark() ? '#cbd5e1' : '#94a3b8';
    const primaryColor = () => isDark() ? '#818cf8' : '#667eea';
    const accentColor = () => isDark() ? '#a78bfa' : '#a855f7';
    const successColor = () => isDark() ? '#34d399' : '#10b981';

    // 1. Engagement Chart (Teal Area)
    const ctxEng = document.getElementById('engagementChart').getContext('2d');
    const gradEng = ctxEng.createLinearGradient(0, 0, 0, 300);
    gradEng.addColorStop(0, isDark() ? 'rgba(129, 140, 248, 0.25)' : 'rgba(16, 185, 129, 0.2)');
    gradEng.addColorStop(1, 'rgba(0,0,0,0)');

    new Chart(ctxEng, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($growth_labels); ?>,
            datasets: [{
                label: 'Logins',
                data: <?php echo json_encode($growth_data); ?>,
                borderColor: successColor(),
                backgroundColor: gradEng,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 6
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: gridColor() }, ticks: { color: labelColor() } },
                x: { grid: { display: false }, ticks: { display: true, color: labelColor(), maxTicksLimit: 5 } }
            }
        }
    });

    // 2. Capacity Donut
    const ctxCap = document.getElementById('capacityDonut').getContext('2d');
    new Chart(ctxCap, {
        type: 'doughnut',
        data: {
            labels: ['Backups', 'Media', 'Records', 'Free'],
            datasets: [{
                data: [450, 210, 85, 255], // Total 1000
                backgroundColor: [primaryColor(), successColor(), accentColor(), isDark() ? '#1f2937' : '#f1f5f9'],
                borderWidth: 0,
                cutout: '80%'
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } }
        }
    });

    // 3. Transactions (Bar)
    const ctxTx = document.getElementById('txChart').getContext('2d');
     new Chart(ctxTx, {
        type: 'bar',
        data: {
            labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
            datasets: [{
                data: [12, 19, 10, 15, 8, 12, 10], 
                backgroundColor: isDark() ? '#1f2937' : '#e2e8f0',
                hoverBackgroundColor: primaryColor(),
                borderRadius: 4,
                barThickness: 12
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                 y: { display: false },
                 x: { grid: { display: false }, ticks: { color: labelColor() } }
            }
        }
    });
});
</script>
