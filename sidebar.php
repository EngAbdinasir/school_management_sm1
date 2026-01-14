<nav class="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="brand-logo">
            <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
            <span>AL IMRA SCHOOL</span>
        </a>
    </div>

    <!-- Current Database Widget -->
    <div class="current-db-card">
        <div>
            <div class="current-db-label">AL IMRA</div>
            <div class="fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-database-fill text-primary"></i>
                <span>SCHOOLDB</span>
            </div>
        </div>
        <div class="db-status-badge">
            <i class="bi bi-circle-fill" style="font-size: 0.5rem; margin-right: 4px;"></i>Active
        </div>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-title">Tables</div>
        
        <!-- Academic Group (Collapsible) -->
        <div class="group-wrapper mb-2">
            <div class="nav-link justify-content-between" data-bs-toggle="collapse" href="#academicCollapse" role="button" aria-expanded="false" aria-controls="academicCollapse" style="cursor: pointer;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard"></i> 
                    <span>Academic</span>
                </div>
                <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.3s;"></i>
            </div>
            <div class="collapse show" id="academicCollapse">
                <div class="ps-3 border-start ms-3 border-secondary border-opacity-25 filter-group">
                    <a href="?table=classes" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'classes') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-book"></i> Classes
                    </a>
                    <a href="?table=subjects" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'subjects') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-journal-text"></i> Subjects
                    </a>
                    <a href="?table=timetable" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'timetable') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-calendar-week"></i> Timetable
                    </a>
                    <a href="?table=class_subjects" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'class_subjects') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-link-45deg"></i> Class Subjects
                    </a>
                    <a href="?table=grades" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'grades') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-star"></i> Grades
                    </a>
                    <a href="?table=enrollments" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'enrollments') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-person-check"></i> Enrollments
                    </a>
                    <a href="?table=teacher_subjects" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'teacher_subjects') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-person-badge"></i> Teacher Subjects
                    </a>
                    <a href="?table=attendance" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'attendance') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-calendar-check"></i> Attendance
                    </a>
                </div>
            </div>
        </div>

        <!-- People Group -->
        <div class="group-wrapper mb-2">
            <div class="nav-link justify-content-between" data-bs-toggle="collapse" href="#peopleCollapse" role="button" aria-expanded="true" aria-controls="peopleCollapse" style="cursor: pointer;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people"></i> 
                    <span>People</span>
                </div>
                <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.3s;"></i>
            </div>
            <div class="collapse show" id="peopleCollapse">
                <div class="ps-3 border-start ms-3 border-secondary border-opacity-25 filter-group">
                    <a href="?table=students" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'students') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-person-badge"></i> Students
                    </a>
                    <a href="?table=teachers" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'teachers') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-person-video2"></i> Teachers
                    </a>
                    <a href="?table=staff" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'staff') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-briefcase"></i> Staff
                    </a>
                    <a href="?table=parents" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'parents') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-people-fill"></i> Parents
                    </a>
                </div>
            </div>
        </div>

        <!-- Finance Group (Collapsible) -->
        <div class="group-wrapper mb-2">
            <div class="nav-link justify-content-between" data-bs-toggle="collapse" href="#financeCollapse" role="button" aria-expanded="false" aria-controls="financeCollapse" style="cursor: pointer;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-cash-coin"></i> 
                    <span>Finance</span>
                </div>
                <i class="bi bi-chevron-down" style="font-size: 0.7rem; transition: transform 0.3s;"></i>
            </div>
            <div class="collapse show" id="financeCollapse">
                <div class="ps-3 border-start ms-3 border-secondary border-opacity-25 filter-group">
                    <a href="?table=payments" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'payments') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-credit-card"></i> Payments
                    </a>
                    <a href="?table=receipts" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'receipts') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-receipt"></i> Receipts
                    </a>
                    <a href="?table=suppliers" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'suppliers') ? 'active-highlighted' : ''; ?>">
                        <i class="bi bi-truck"></i> Suppliers
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="sidebar-section">
        <div class="sidebar-title">System</div>
        <a href="?table=users" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'users') ? 'active-highlighted' : ''; ?>">
            <i class="bi bi-person-circle"></i> Users
        </a>
        <a href="?table=roles" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'roles') ? 'active-highlighted' : ''; ?>">
            <i class="bi bi-shield-lock"></i> Roles
        </a>
        <a href="?table=addresses" class="nav-link <?php echo (isset($_GET['table']) && $_GET['table'] == 'addresses') ? 'active-highlighted' : ''; ?>">
            <i class="bi bi-geo-alt"></i> Addresses
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="#" class="d-flex align-items-center text-decoration-none gap-3" style="color: var(--text-heading);">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; background: var(--primary-gradient); color: white; box-shadow: var(--shadow-sm);">
                    RA
                </div>
                <div>
                    <div class="fw-bold" style="font-size: 0.9rem;">Root Admin</div>
                    <div class="text-muted" style="font-size: 0.75rem;">localhost</div>
                </div>
            </a>
            <a href="logout.php" class="btn-icon text-danger" title="Logout" style="width: 38px; height: 38px;">
                <i class="bi bi-box-arrow-right" style="font-size: 1.2rem;"></i>
            </a>
        </div>
    </div>
</nav>

<script>
// Smooth chevron rotation on collapse
document.addEventListener('DOMContentLoaded', function() {
    const collapseElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
    collapseElements.forEach(el => {
        el.addEventListener('click', function() {
            const chevron = this.querySelector('.bi-chevron-down');
            if (chevron) {
                setTimeout(() => {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    chevron.style.transform = isExpanded ? 'rotate(180deg)' : 'rotate(0deg)';
                }, 10);
            }
        });
    });
});
</script>
