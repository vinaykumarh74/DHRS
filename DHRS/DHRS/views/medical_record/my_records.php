<?php
/**
 * Medical Records View for Citizens
 */

// Set page title
$page_title = 'My Medical Records';

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
                        <li class="list-group-item px-0 active">
                            <a href="index.php?controller=medical_record&action=my_records" class="text-decoration-none text-white">
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
                        <li class="list-group-item px-0">
                            <a href="index.php?controller=vaccination&action=my_vaccinations" class="text-decoration-none">
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
                    <h4 class="mb-0"><i class="fas fa-file-medical me-2"></i>My Medical Records</h4>
                    <div>
                        <span class="badge bg-light text-dark"><?php echo count($medical_records); ?> Records</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($medical_records)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-medical fa-4x text-muted mb-4"></i>
                            <h5 class="text-muted">No Medical Records Found</h5>
                            <p class="text-muted">You don't have any medical records yet. Records will appear here after your doctor visits.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Doctor</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medical_records as $record): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?php echo date('M d, Y', strtotime($record['record_date'])); ?></span>
                                                    <small class="text-muted"><?php echo date('h:i A', strtotime($record['created_at'])); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    switch($record['record_type']) {
                                                        case 'general': echo 'primary'; break;
                                                        case 'chronic': echo 'warning'; break;
                                                        case 'emergency': echo 'danger'; break;
                                                        case 'vaccination': echo 'success'; break;
                                                        default: echo 'secondary';
                                                    }
                                                ?>">
                                                    <?php echo ucfirst($record['record_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold"><?php echo htmlspecialchars($record['title']); ?></span>
                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($record['description'], 0, 50)) . (strlen($record['description']) > 50 ? '...' : ''); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">Dr. <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></span>
                                                    <small class="text-muted"><?php echo htmlspecialchars($record['specialization']); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="index.php?controller=medical_record&action=view&id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </a>
                                                    <a href="index.php?controller=medical_record&action=download&id=<?php echo $record['id']; ?>" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-download me-1"></i>Download
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
