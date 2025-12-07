<?php
/**
 * Prescriptions View for Citizens
 */

// Set page title
$page_title = 'My Prescriptions';

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
                        <li class="list-group-item px-0 active">
                            <a href="index.php?controller=prescription&action=my_prescriptions" class="text-decoration-none text-white">
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
                    <h4 class="mb-0"><i class="fas fa-prescription me-2"></i>My Prescriptions</h4>
                    <div>
                        <span class="badge bg-light text-dark"><?php echo count($prescriptions); ?> Prescriptions</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($prescriptions)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-prescription fa-4x text-muted mb-4"></i>
                            <h5 class="text-muted">No Prescriptions Found</h5>
                            <p class="text-muted">You don't have any prescriptions yet. Prescriptions will appear here after your doctor visits.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Doctor</th>
                                        <th>Diagnosis</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prescriptions as $prescription): ?>
                                        <tr>
                                            <td><?php echo format_date($prescription['created_at']); ?></td>
                                            <td>Dr. <?php echo htmlspecialchars($prescription['doctor_name']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($prescription['specialization']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($prescription['diagnosis']); ?></td>
                                            <td>
                                                <?php if (isset($prescription['is_active']) && $prescription['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Completed</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="index.php?controller=prescription&action=view&id=<?php echo $prescription['id']; ?>" class="btn btn-sm btn-outline-primary mb-1">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                                <a href="index.php?controller=prescription&action=download&id=<?php echo $prescription['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </a>
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

<?php
$content = ob_get_clean();
include('views/layout.php');
?>