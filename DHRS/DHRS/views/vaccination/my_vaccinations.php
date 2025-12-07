<?php
/**
 * Vaccinations View for Citizens
 */

// Set page title
$page_title = 'My Vaccinations';

// Start output buffering
ob_start();
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($citizen['profile_image'])): ?>
                        <img src="uploads/profile/<?php echo $citizen['profile_image']; ?>" alt="Profile Image" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <img src="assets/img/default-profile.png" alt="Default Profile" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php endif; ?>
                    <h5 class="mb-1"><?php echo $citizen['first_name'] . ' ' . $citizen['last_name']; ?></h5>
                    <p class="text-muted mb-3">Health ID: <?php echo $citizen['health_id']; ?></p>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Quick Links</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <a href="index.php?controller=citizen&action=dashboard" class="text-decoration-none">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="list-group-item px-0">
                            <a href="index.php?controller=citizen&action=profile" class="text-decoration-none">
                                <i class="fas fa-user me-2"></i>My Profile
                            </a>
                        </li>
                        <li class="list-group-item px-0">
                            <a href="index.php?controller=citizen&action=health_profile" class="text-decoration-none">
                                <i class="fas fa-heartbeat me-2"></i>Health Profile
                            </a>
                        </li>
                        <li class="list-group-item px-0">
                            <a href="index.php?controller=appointment&action=my_appointments" class="text-decoration-none">
                                <i class="fas fa-calendar-check me-2"></i>My Appointments
                            </a>
                        </li>
                        <li class="list-group-item px-0">
                            <a href="index.php?controller=medical_record&action=my_records" class="text-decoration-none">
                                <i class="fas fa-file-medical me-2"></i>Medical Records
                            </a>
                        </li>
                        <li class="list-group-item px-0">
                            <a href="index.php?controller=prescription&action=my_prescriptions" class="text-decoration-none">
                                <i class="fas fa-prescription me-2"></i>Prescriptions
                            </a>
                        </li>
                        <li class="list-group-item px-0">
                            <a href="index.php?controller=lab_report&action=my_reports" class="text-decoration-none">
                                <i class="fas fa-flask me-2"></i>Lab Reports
                            </a>
                        </li>
                        <li class="list-group-item px-0 active">
                            <a href="index.php?controller=vaccination&action=my_vaccinations" class="text-decoration-none text-white">
                                <i class="fas fa-syringe me-2"></i>Vaccinations
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-syringe me-2"></i>My Vaccinations</h4>
                    <div>
                        <span class="badge bg-light text-dark"><?php echo count($vaccinations); ?> Records</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($vaccinations)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-syringe fa-4x text-muted mb-4"></i>
                            <h5 class="text-muted">No Vaccination Records Found</h5>
                            <p class="text-muted">You don't have any vaccination records yet. Records will appear here after you receive vaccinations.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Vaccination</th>
                                        <th>Dose</th>
                                        <th>Next Due</th>
                                        <th>Administered By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vaccinations as $vaccination): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?php echo date('M d, Y', strtotime($vaccination['vaccination_date'])); ?></span>
                                                    <small class="text-muted"><?php echo date('h:i A', strtotime($vaccination['created_at'])); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?php echo htmlspecialchars($vaccination['vaccination_name']); ?></span>
                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($vaccination['description'], 0, 50)) . (strlen($vaccination['description']) > 50 ? '...' : ''); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    $progress = ($vaccination['dose_number'] / $vaccination['doses_required']) * 100;
                                                    if ($progress >= 100) echo 'success';
                                                    elseif ($progress >= 50) echo 'warning';
                                                    else echo 'info';
                                                ?>">
                                                    <?php echo $vaccination['dose_number'] . '/' . $vaccination['doses_required']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($vaccination['next_due_date'])): ?>
                                                    <span class="fw-bold"><?php echo date('M d, Y', strtotime($vaccination['next_due_date'])); ?></span>
                                                    <?php if (strtotime($vaccination['next_due_date']) < time()): ?>
                                                        <small class="text-danger d-block">Overdue</small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Complete</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($vaccination['administered_by'])): ?>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($vaccination['administered_by']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not specified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="index.php?controller=vaccination&action=view&id=<?php echo $vaccination['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="index.php?controller=vaccination&action=certificate&id=<?php echo $vaccination['id']; ?>" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-certificate me-1"></i>Certificate
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.375rem;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}
</style>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>
