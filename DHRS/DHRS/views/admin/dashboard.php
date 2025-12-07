<?php
/**
 * Admin Dashboard View
 */

// Set page title
$page_title = 'Admin Dashboard';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h1 class="h3 mb-1">Welcome, <?php echo isset($admin['first_name']) ? htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) : 'Administrator'; ?></h1>
                            <p class="text-muted mb-0">System Administrator Dashboard</p>
                        </div>
                        <div class="d-none d-md-block">
                            <a href="index.php?controller=admin&action=settings" class="btn btn-outline-primary"><i class="fas fa-cogs me-2"></i>System Settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Total Users</h6>
                            <h2 class="mb-0"><?php echo isset($total_users) ? $total_users : '0'; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?controller=admin&action=manage_users" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon me-3">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Citizens</h6>
                            <h2 class="mb-0"><?php echo isset($total_citizens) ? $total_citizens : '0'; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?controller=admin&action=manage_users" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon me-3">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Doctors</h6>
                            <h2 class="mb-0"><?php echo isset($total_doctors) ? $total_doctors : '0'; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?controller=admin&action=manage_users" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon me-3">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Administrators</h6>
                            <h2 class="mb-0"><?php echo isset($total_admins) ? $total_admins : '0'; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?controller=admin&action=manage_users" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i>Manage Users</h5>
                    <p class="card-text">View and manage all system users including citizens, doctors, and administrators.</p>
                    <a href="index.php?controller=admin&action=manage_users" class="btn btn-light">Manage Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-chart-bar me-2"></i>System Statistics</h5>
                    <p class="card-text">View comprehensive system statistics and generate detailed reports.</p>
                    <a href="index.php?controller=admin&action=statistics" class="btn btn-light">View Statistics</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-warning text-dark h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-cogs me-2"></i>System Settings</h5>
                    <p class="card-text">Configure system settings and manage application preferences.</p>
                    <a href="index.php?controller=admin&action=settings" class="btn btn-light">System Settings</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5><i class="fas fa-history me-2"></i>Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // This would normally be populated from the database
                                // For now, we'll just show a placeholder message
                                ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-2"></i>No recent activities
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>