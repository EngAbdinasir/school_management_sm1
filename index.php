<?php
session_start();

// Strict Session Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            /* Modern Light Theme */
            --sidebar-bg: #ffffff;
            --sidebar-width: 280px;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-blue: #667eea;
            --primary-purple: #764ba2;
            --active-bg: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            --active-text: #667eea;
            --text-color: #64748b;
            --text-heading: #1e293b;
            --border-color: #e2e8f0;
            --main-bg: #f8fafc;
            --card-bg: #ffffff;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.12);
            --shadow-xl: 0 20px 60px rgba(0,0,0,0.15);
        }

        [data-theme="dark"] {
            --sidebar-bg: #1e293b;
            --main-bg: #0f172a;
            --card-bg: #1e293b;
            --text-color: #cbd5e1;
            --text-heading: #ffffff;
            --border-color: #334155;
            --active-bg: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
            --active-text: #818cf8;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.3);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.4);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.5);
            --shadow-xl: 0 20px 60px rgba(0,0,0,0.6);
        }    

        /* Dark-mode specific tweaks for newly styled components */
        [data-theme="dark"] .table-header-section,
        [data-theme="dark"] .search-card,
        [data-theme="dark"] .table-card,
        [data-theme="dark"] .pagination-wrapper {
            background: var(--card-bg);
            border-color: var(--border-color);
            box-shadow: var(--shadow-md);
        }

        [data-theme="dark"] .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.08);
        }

        [data-theme="dark"] .action-btn {
            background: #152036;
            border-color: var(--border-color);
            color: var(--text-color);
        }

        [data-theme="dark"] .page-link {
            background: #16233a;
            color: var(--text-color);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-color);
            background: var(--main-bg);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            transition: background 0.3s ease;
        }
        
        h1, h2, h3, h4, h5, h6, .text-dark {
            color: var(--text-heading) !important;
            font-weight: 700;
        }
        
        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background: var(--sidebar-bg);
            color: var(--text-color);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--text-color);
        }
        
        .sidebar .nav-link {
            color: var(--text-color) !important;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        
        .sidebar .nav-link:hover {
            color: var(--primary-blue) !important;
            background: var(--active-bg);
            transform: translateX(4px);
        }
        
        .sidebar .nav-link.active-highlighted {
            background: var(--active-bg);
            color: var(--active-text) !important;
            border-left: 4px solid;
            border-image: var(--primary-gradient) 1;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }
        
        .sidebar .fw-bold {
            color: var(--text-heading);
        }
        
        .brand-logo { 
            color: var(--text-heading) !important; 
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }
        
        .brand-icon {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }
        
        /* Top Bar Search */
        .search-input-group {
            background: var(--main-bg);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.625rem 1rem;
            display: flex;
            align-items: center;
            width: 320px;
            transition: all 0.3s ease;
        }

        .search-input-group:focus-within {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: var(--card-bg);
        }
        
        [data-theme="dark"] .search-input-group { 
            background: #1e293b;
            border-color: #334155;
        }
        
        .search-input-group input {
            border: none;
            background: transparent;
            outline: none;
            color: var(--text-heading);
            width: 100%;
            margin-left: 8px;
            font-size: 0.9rem;
        }

        .search-input-group input::placeholder {
            color: var(--text-color);
        }

        .btn-icon {
            width: 44px; 
            height: 44px;
            display: flex; 
            align-items: center; 
            justify-content: center;
            border-radius: 12px;
            color: var(--text-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            background: transparent;
        }

        .btn-icon:hover { 
            background: var(--active-bg); 
            color: var(--primary-blue);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }


        
        .sidebar-header {
            padding: 1.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-logo {
            font-size: 1.35rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
        }

        .sidebar-section {
            padding: 0 1rem;
            margin-bottom: 1.75rem;
        }

        .sidebar-title {
            color: var(--text-color);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 0.75rem;
            padding-left: 0.75rem;
            opacity: 0.6;
        }

        .nav-link {
            color: var(--text-color) !important;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .nav-link i {
            width: 22px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        .current-db-card {
            background: var(--active-bg);
            margin: 0 1rem 1.5rem 1rem;
            padding: 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .current-db-label {
            font-size: 0.7rem;
            color: var(--text-color);
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
            opacity: 0.7;
            letter-spacing: 0.5px;
        }
        
        .db-status-badge {
            background: var(--primary-gradient);
            color: white;
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1.25rem;
            border-top: 1px solid var(--border-color);
            background: var(--main-bg);
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            background: var(--main-bg);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        .top-bar {
            background: var(--card-bg);
            height: 72px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .breadcrumb-item {
            font-size: 0.9rem;
            color: var(--text-color);
            transition: color 0.2s;
        }

        .breadcrumb-item.active {
            color: var(--primary-blue);
            font-weight: 600;
        }
        
        /* Buttons & Controls */
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            font-weight: 600;
        }
        
        .table-controls-tab {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .ctrl-tab {
            padding: 0.625rem 1.25rem;
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ctrl-tab:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }
        
        .ctrl-tab.active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
            box-shadow: var(--shadow-md);
        }

        /* Modal Enhancements */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: var(--shadow-xl);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        /* Toast Enhancements */
        .toast {
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            border: none;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .search-input-group {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>

    <!-- Mobile Toggle (Visible only on small screens) -->
    <button class="d-lg-none position-fixed top-0 start-0 m-3 btn btn-dark z-3" onclick="document.querySelector('.sidebar').classList.toggle('show')">
        <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <!-- Breadcrumbs -->
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-house text-muted"></i>
                <span class="text-muted">/</span>
                <i class="bi bi-list-ul text-muted"></i>
                <span class="breadcrumb-item">Databases</span>
                <span class="text-muted">/</span>
                <span class="breadcrumb-item active fw-bold">SCHOOLDB</span>
            </div>
            
            <!-- Tools -->
            <div class="d-flex align-items-center gap-3">
                <div class="search-input-group">
                     <i class="bi bi-search text-muted"></i>
                     <input type="text" placeholder="Search data...">
                </div>

                <!-- Icons Group -->
                <button class="btn btn-icon" id="themeToggle" title="Toggle Theme">
                     <i class="bi bi-sun-fill text-warning"></i> 
                </button>
                
                <button class="btn btn-icon position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-25 start-75 translate-middle p-1 bg-danger border border-light rounded-circle" style="width:8px; height:8px;"></span>
                </button>

                <!-- Profile -->
                <div class="d-flex align-items-center gap-2 ms-2 cursor-pointer">
                     <img src="https://ui-avatars.com/api/?name=Eleanor+Pena&background=random" class="rounded-circle" width="36" height="36" alt="Profile">
                </div>
            </div>
        </div>

        <div class="p-4">
            <?php
            if (isset($_GET['table']) && !empty($_GET['table'])) {
                include 'content_table.php';
            } else {
                include 'content_dashboard.php';
            }
            ?>
        </div>
    </main>

    <!-- Enhanced Toast Container (Top Center) -->
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1055; margin-top: 1rem;">
        <div id="liveToast" class="toast align-items-center text-white border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 350px;">
            <div class="d-flex align-items-center p-3">
                <div class="toast-icon me-3" style="font-size: 1.5rem;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="toast-body flex-grow-1 fw-medium">
                    Action successful!
                </div>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Enhanced Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">Edit Record</h5>
                        <small class="text-muted">Update the record information below</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editDataId" name="id">
                        <input type="hidden" id="editDataTable" name="table">
                        <div id="editFormFields" class="row g-4">
                            <!-- Fields will be populated dynamically -->
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-3">Loading form fields...</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveChangesBtn">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Delete Record</h5>
                            <small class="text-muted">This action cannot be undone</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <p class="mb-0">Are you sure you want to delete this record? This action cannot be undone and may affect related data.</p>
                    <input type="hidden" id="deleteDataId">
                    <input type="hidden" id="deleteDataTable">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i>Delete Record
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('themeToggle');
            const htmlEl = document.documentElement;

            // Load Preference (Default Light for EduAdmin)
            const savedTheme = localStorage.getItem('theme') || 'light'; 
            if (savedTheme === 'dark') {
                htmlEl.setAttribute('data-theme', 'dark');
            } else {
                htmlEl.setAttribute('data-theme', 'light');
            }

            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlEl.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                htmlEl.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                // Optional: Toggle Icon logic if needed for single button
                // But we used a simple button, so let CSS handle it or swap icon:
                const icon = themeToggleBtn.querySelector('i');
                if(newTheme === 'dark') {
                    icon.className = 'bi bi-moon-fill text-white';
                } else {
                    icon.className = 'bi bi-sun-fill text-warning';
                }
            });

            const toastEl = document.getElementById('liveToast');
            const toast = new bootstrap.Toast(toastEl);
            const toastBody = toastEl.querySelector('.toast-body');

            function showToast(message, type = 'success') {
                toastBody.textContent = message;
                const bgColors = {
                    'success': 'linear-gradient(135deg, #10b981, #059669)',
                    'danger': 'linear-gradient(135deg, #ef4444, #dc2626)',
                    'warning': 'linear-gradient(135deg, #f59e0b, #d97706)',
                    'info': 'linear-gradient(135deg, #3b82f6, #2563eb)'
                };
                const icons = {
                    'success': 'bi-check-circle-fill',
                    'danger': 'bi-x-circle-fill',
                    'warning': 'bi-exclamation-triangle-fill',
                    'info': 'bi-info-circle-fill'
                };
                toastEl.style.background = bgColors[type] || bgColors.success;
                const iconEl = toastEl.querySelector('.toast-icon i');
                if (iconEl) {
                    iconEl.className = `bi ${icons[type] || icons.success}`;
                }
                toast.show();
            }

            // --- Edit & Insert Functionality ---
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            const editFormFields = document.getElementById('editFormFields');
            const saveChangesBtn = document.getElementById('saveChangesBtn');
            const editModalTitle = document.querySelector('#editModal .modal-title');

            document.addEventListener('click', function(e) {
                // Handle Edit
                const editBtn = e.target.closest('.btn-edit');
                if (editBtn) {
                    const id = editBtn.dataset.id;
                    const table = editBtn.dataset.table;

                    document.getElementById('editDataId').value = id;
                    document.getElementById('editDataTable').value = table;
                    editModalTitle.textContent = 'Edit Record';
                    
                    // Show modal & loading state
                    editFormFields.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
                    editModal.show();

                    // Fetch Data
                    const formData = new FormData();
                    formData.append('action', 'fetch_record');
                    formData.append('table', table);
                    formData.append('id', id);

                    fetch('api_handler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if(res.status === 'success') {
                            renderFormFields(res.data, false);
                        } else {
                            showToast(res.message || 'Error fetching data', 'danger');
                            editModal.hide();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Communication error', 'danger');
                        editModal.hide();
                    });
                    return;
                }

                // Handle Insert
                const insertBtn = e.target.closest('.btn-insert');
                if (insertBtn) {
                    const table = insertBtn.dataset.table;

                    document.getElementById('editDataId').value = ''; // Clear ID for insert
                    document.getElementById('editDataTable').value = table;
                    editModalTitle.textContent = 'Create New Record';
                    
                    // Show modal & loading state
                    editFormFields.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
                    editModal.show();

                    // Fetch Columns (Empty Data)
                    const formData = new FormData();
                    formData.append('action', 'fetch_columns');
                    formData.append('table', table);

                    fetch('api_handler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(res => {
                        if(res.status === 'success') {
                            renderFormFields(res.data, true);
                        } else {
                            showToast(res.message || 'Error fetching columns', 'danger');
                            editModal.hide();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('Communication error', 'danger');
                        editModal.hide();
                    });
                }
            });

            function getFriendlyErrorMessage(fieldName) {
                const name = fieldName.replace(/_/g, ' ');
                if (fieldName.includes('email')) return 'Please enter a valid email address.';
                if (fieldName.includes('date')) return 'Please select a valid date.';
                if (fieldName.includes('_id')) return `Please select a ${name.replace(' id', '')}.`;
                return `Please provide a valid ${name}.`;
            }

            function renderFormFields(responseObj, isInsert) {
                const columns = responseObj.columns;
                const fkOptions = responseObj.fk_options || {};
                
                let html = '';
                
                for (const [key, meta] of Object.entries(columns)) {
                    // meta = { value, type, is_auto, is_fk, fk_table }
                    
                    // Hide Auto-Increment IDs usually
                    if (meta.is_auto) {
                         // Keep hidden input for ID so update works if needed, but usually we use editDataId
                         // If it's the primary key and we are editing, we are already storing it in hidden #editDataId
                         // So we can just skip rendering it visible, or show as static text.
                         // Let's show as disabled text for reference if it's not too intrusive, or just skip.
                         // User asked "all ID is autoincrement" -> implies we should handle them automatically (hide).
                         continue; 
                    }
                    
                    // Skip 'created_at', 'updated_at' usually
                    if (key === 'created_at' || key === 'updated_at') continue;

                    let label = key.replace(/_/g, ' ');
                    let inputHtml = '';
                    const errorMsg = getFriendlyErrorMessage(key);

                    // Check if this is a foreign key field (either marked by backend or by naming convention)
                    const isForeignKey = meta.is_fk || (key.endsWith('_id') && key !== 'id');
                    const fkTableName = meta.fk_table || (key.endsWith('_id') ? key.slice(0, -3) + 's' : '');

                    if (isForeignKey && fkOptions[meta.fk_table]) {
                        // RENDER SELECT - Options Available
                        let optionsHtml = '<option value="">Select...</option>';
                        fkOptions[meta.fk_table].forEach(opt => {
                            const selected = String(opt.id) === String(meta.value) ? 'selected' : '';
                            optionsHtml += `<option value="${opt.id}" ${selected}>${opt.label}</option>`;
                        });
                        inputHtml = `<select class="form-select" name="${key}" required>${optionsHtml}</select>`;
                    } else if (isForeignKey && !fkOptions[meta.fk_table]) {
                        // RENDER SELECT - No Options Available (Empty Table)
                        const friendlyTableName = (meta.fk_table || fkTableName).charAt(0).toUpperCase() + (meta.fk_table || fkTableName).slice(1);
                        inputHtml = `
                            <select class="form-select" name="${key}" required disabled>
                                <option value="">No ${friendlyTableName} available</option>
                            </select>
                            <small class="text-warning d-block mt-1">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Please add ${friendlyTableName} records first before creating this record.
                            </small>
                        `;
                    } else if (meta.type === 'date') {
                        // RENDER DATE
                        inputHtml = `<input type="date" class="form-control" name="${key}" value="${meta.value || ''}" required>`;
                    } else if (meta.type === 'textarea') {
                        // RENDER TEXTAREA
                         inputHtml = `<textarea class="form-control" name="${key}" rows="3" required>${meta.value || ''}</textarea>`;
                    } else {
                        // DEFAULT TEXT
                        inputHtml = `<input type="text" class="form-control" name="${key}" value="${meta.value || ''}" required>`;
                    }

                    html += `
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2">${label} <span class="text-danger">*</span></label>
                            ${inputHtml}
                            <div class="invalid-feedback">${errorMsg}</div>
                        </div>
                    `;
                }
                editFormFields.innerHTML = html;
            }

            saveChangesBtn.addEventListener('click', function(e) {
                // Form Validation Logic
                const form = document.getElementById('editForm');
                if (!form.checkValidity()) {
                    // Trigger browser validation UI
                    form.reportValidity();
                    e.preventDefault();
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    showToast('Please fill out all required fields.', 'warning');
                    return;
                }
                form.classList.add('was-validated');

                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => data[key] = value);

                const id = document.getElementById('editDataId').value;
                const table = document.getElementById('editDataTable').value;
                const action = id ? 'update_record' : 'insert_record';
                
                // Show loading state
                const originalBtnText = saveChangesBtn.textContent;
                saveChangesBtn.disabled = true;
                saveChangesBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                // POST as standard Form Data or JSON? 
                // Previous code used FormData object directly in fetch body, but API expects 'data' json string?
                // Let's check api_handler expectations. 
                // "fetch_record" used FormData. "update_record" in previous steps used FormData?
                // Let's look at the code I'm replacing: 
                // "formData.append('action', 'update_record')... fetch(..., body: formData)"
                // So the API handles multipart/form-data.
                // But my previous attempted replacement used JSON string. 
                // Stick to the existing working pattern (FormData) to avoid API errors.
                
                if (id) {
                    formData.append('action', 'update_record');
                } else {
                    formData.append('action', 'insert_record');
                }

                // Append 'table' if not in form
                if (!formData.has('table')) formData.append('table', table);
                if (!formData.has('id') && id) formData.append('id', id);

                fetch('api_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    if(res.status === 'success') {
                        showToast(res.message || 'Operation successful', 'success');
                        editModal.hide();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(res.message || 'Error saving data', 'danger');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Communication error', 'danger');
                })
                .finally(() => {
                    saveChangesBtn.disabled = false;
                    saveChangesBtn.textContent = 'Save Changes';
                });
            });


            // --- Delete Functionality ---
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            let deleteTargetId = null;
            let deleteTargetTable = null;

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-delete');
                if (!btn) return;

                deleteTargetId = btn.dataset.id;
                deleteTargetTable = btn.dataset.table;
                deleteModal.show();
            });

            confirmDeleteBtn.addEventListener('click', function() {
                const formData = new FormData();
                formData.append('action', 'delete_record');
                formData.append('table', deleteTargetTable);
                formData.append('id', deleteTargetId);

                confirmDeleteBtn.disabled = true;

                fetch('api_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    if(res.status === 'success') {
                        showToast(res.message);
                        deleteModal.hide();
                        // Remove row from UI strictly for visuals before reload
                        const row = document.querySelector(`.btn-delete[data-id="${deleteTargetId}"]`).closest('tr');
                        if (row) row.remove();
                    } else {
                        showToast(res.message || 'Delete failed', 'danger');
                    }
                })
                .catch(err => {
                    showToast('Communication error', 'danger');
                })
                .finally(() => {
                    confirmDeleteBtn.disabled = false;
                });
            });

            // --- Search Functionality ---
// --- Search Functionality ---
            const searchInput = document.getElementById('tableSearchInput');
            const searchIcon = document.getElementById('searchIconBtn');

            function performSearch() {
                if (!searchInput) return;
                const searchTerm = searchInput.value.trim();
                const urlParams = new URLSearchParams(window.location.search);
                if (searchTerm) {
                    urlParams.set('search', searchTerm);
                } else {
                    urlParams.delete('search');
                }
                window.location.search = urlParams.toString();
            }

            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });
            }

            if (searchIcon) {
                searchIcon.addEventListener('click', function() {
                    performSearch();
                });
            }

        });
    </script>
</body>
</html>
