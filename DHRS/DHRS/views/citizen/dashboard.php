<?php
/**
 * Citizen Dashboard View
 */

// Set page title
$page_title = 'Citizen Dashboard';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <?php if (!empty($citizen['profile_image'])): ?>
                                <img src="uploads/profile/<?php echo htmlspecialchars($citizen['profile_image']); ?>" alt="Profile" class="rounded-circle profile-img" width="80" height="80">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                    <?php echo strtoupper(substr($citizen['first_name'], 0, 1) . substr($citizen['last_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h1 class="h3 mb-1">Welcome, <?php echo htmlspecialchars($citizen['first_name'] . ' ' . $citizen['last_name']); ?></h1>
                            <p class="text-muted mb-0">Health ID: <?php echo htmlspecialchars($citizen['health_id']); ?></p>
                        </div>
                        <div class="d-none d-md-block">
                            <a href="index.php?controller=citizen&action=profile" class="btn btn-outline-primary"><i class="fas fa-user-edit me-2"></i>Edit Profile</a>
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
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Upcoming Appointments</h6>
                            <h2 class="mb-0"><?php echo $upcoming_appointments_count; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?controller=appointment&action=my_appointments" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon me-3">
                            <i class="fas fa-prescription"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Active Prescriptions</h6>
                            <h2 class="mb-0"><?php echo $active_prescriptions_count; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?controller=prescription&action=my_prescriptions" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon me-3">
                            <i class="fas fa-flask"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Lab Reports</h6>
                            <h2 class="mb-0"><?php echo $lab_reports_count; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?controller=lab_report&action=my_reports" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="dashboard-icon me-3">
                            <i class="fas fa-syringe"></i>
                        </div>
                        <div>
                            <h6 class="card-title mb-1">Vaccinations</h6>
                            <h2 class="mb-0"><?php echo $vaccinations_count; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end">
                    <a href="index.php?controller=vaccination&action=my_vaccinations" class="text-decoration-none">View All <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Appointments -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Upcoming Appointments</h5>
                        <a href="index.php?controller=appointment&action=book" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Book New</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($upcoming_appointments)): ?>
                        <div class="text-center p-4">
                            <img src="assets/images/no-appointments.svg" alt="No Appointments" class="img-fluid mb-3" style="max-width: 150px;">
                            <p class="mb-0">You don't have any upcoming appointments.</p>
                            <a href="index.php?controller=appointment&action=book" class="btn btn-sm btn-outline-primary mt-3">Book an Appointment</a>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($upcoming_appointments as $appointment): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="appointment-date text-center p-2 rounded bg-light">
                                                <div class="month text-uppercase small"><?php echo date('M', strtotime($appointment['appointment_date'])); ?></div>
                                                <div class="day fw-bold"><?php echo date('d', strtotime($appointment['appointment_date'])); ?></div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h6>
                                            <p class="text-muted small mb-0"><?php echo htmlspecialchars($appointment['specialization']); ?></p>
                                            <div class="d-flex align-items-center small mt-1">
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                <span><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></span>
                                                <span class="mx-2">|</span>
                                                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                <span><?php echo htmlspecialchars($appointment['location']); ?></span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <a href="index.php?controller=appointment&action=view&id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($upcoming_appointments)): ?>
                    <div class="card-footer bg-transparent text-end">
                        <a href="index.php?controller=appointment&action=my_appointments" class="text-decoration-none">View All Appointments <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Prescriptions -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <h5 class="mb-0">Recent Prescriptions</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recent_prescriptions)): ?>
                        <div class="text-center p-4">
                            <img src="assets/images/no-prescriptions.svg" alt="No Prescriptions" class="img-fluid mb-3" style="max-width: 150px;">
                            <p class="mb-0">You don't have any recent prescriptions.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_prescriptions as $prescription): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-prescription text-primary fa-lg"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">Prescription #<?php echo $prescription['id']; ?></h6>
                                            <p class="text-muted small mb-0">Dr. <?php echo htmlspecialchars($prescription['doctor_name']); ?></p>
                                            <div class="d-flex align-items-center small mt-1">
                                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                                <span><?php echo date('d M Y', strtotime($prescription['created_at'])); ?></span>
                                                <span class="mx-2">|</span>
                                                <span class="badge bg-<?php echo ($prescription['is_active'] ? 'success' : 'secondary'); ?>">
                                                    <?php echo ($prescription['is_active'] ? 'Active' : 'Expired'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <a href="index.php?controller=prescription&action=view&id=<?php echo $prescription['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($recent_prescriptions)): ?>
                    <div class="card-footer bg-transparent text-end">
                        <a href="index.php?controller=prescription&action=my_prescriptions" class="text-decoration-none">View All Prescriptions <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Health Summary -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <h5 class="mb-0">Health Summary</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($health_summary)): ?>
                        <div class="text-center p-3">
                            <img src="assets/images/health-summary.svg" alt="Health Summary" class="img-fluid mb-3" style="max-width: 150px;">
                            <p class="mb-0">Your health summary is not available yet.</p>
                            <a href="index.php?controller=citizen&action=health_profile" class="btn btn-sm btn-outline-primary mt-3">Complete Health Profile</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <!-- Basic Health Info -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Basic Info</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Blood Group:</span>
                                                <span class="fw-bold"><?php echo htmlspecialchars($health_summary['blood_group']); ?></span>
                                            </li>
                                            <li class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Height:</span>
                                                <span class="fw-bold"><?php echo htmlspecialchars($health_summary['height']); ?> cm</span>
                                            </li>
                                            <li class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Weight:</span>
                                                <span class="fw-bold"><?php echo htmlspecialchars($health_summary['weight']); ?> kg</span>
                                            </li>
                                            <li class="d-flex justify-content-between">
                                                <span class="text-muted">BMI:</span>
                                                <span class="fw-bold"><?php echo htmlspecialchars($health_summary['bmi']); ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Allergies -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Allergies</h6>
                                        <?php if (empty($health_summary['allergies'])): ?>
                                            <p class="text-muted mb-0">No allergies recorded.</p>
                                        <?php else: ?>
                                            <ul class="mb-0">
                                                <?php foreach ($health_summary['allergies'] as $allergy): ?>
                                                    <li><?php echo htmlspecialchars($allergy); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chronic Conditions -->
                            <div class="col-12">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Chronic Conditions</h6>
                                        <?php if (empty($health_summary['chronic_conditions'])): ?>
                                            <p class="text-muted mb-0">No chronic conditions recorded.</p>
                                        <?php else: ?>
                                            <div class="row">
                                                <?php foreach ($health_summary['chronic_conditions'] as $condition): ?>
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                                                            <span><?php echo htmlspecialchars($condition['name']); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-transparent text-end">
                    <a href="index.php?controller=citizen&action=health_profile" class="text-decoration-none">View Complete Health Profile <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <h5 class="mb-0">Recent Notifications</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recent_notifications)): ?>
                        <div class="text-center p-4">
                            <img src="assets/images/no-notifications.svg" alt="No Notifications" class="img-fluid mb-3" style="max-width: 150px;">
                            <p class="mb-0">You don't have any recent notifications.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_notifications as $notification): ?>
                                <a href="<?php echo htmlspecialchars($notification['link']); ?>" class="list-group-item list-group-item-action p-3 <?php echo ($notification['is_read'] ? '' : 'notification-item unread'); ?>">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle bg-light p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="<?php echo htmlspecialchars($notification['icon']); ?> text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                                <small class="text-muted notification-time"><?php echo time_elapsed_string($notification['created_at']); ?></small>
                                            </div>
                                            <p class="mb-0 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($recent_notifications)): ?>
                    <div class="card-footer bg-transparent text-end">
                        <a href="index.php?controller=notification&action=index" class="text-decoration-none">View All Notifications <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="index.php?controller=appointment&action=book" class="text-decoration-none">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-plus fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="mb-0">Book Appointment</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?controller=medical_record&action=my_records" class="text-decoration-none">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="mb-3">
                                        <i class="fas fa-file-medical fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="mb-0">Medical Records</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?controller=lab_report&action=my_reports" class="text-decoration-none">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="mb-3">
                                        <i class="fas fa-flask fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="mb-0">Lab Reports</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?controller=vaccination&action=my_vaccinations" class="text-decoration-none">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="mb-3">
                                        <i class="fas fa-syringe fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="mb-0">Vaccinations</h6>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
include('views/layout.php');
?>