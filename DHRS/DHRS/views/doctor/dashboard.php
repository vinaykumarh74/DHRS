<?php
/**
 * Doctor Dashboard View
 */

// Set page title
$page_title = 'Doctor Dashboard';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="welcome-heading">Welcome, Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h2>
                    <p class="text-muted">License #: <?php echo htmlspecialchars($doctor['license_number']); ?> | Specialization: <?php echo htmlspecialchars($doctor['specialization']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Today's Appointments</h5>
                    <h2 class="display-4"><?php echo $todays_appointments_count; ?></h2>
                    <p class="card-text">Scheduled for today</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#today-appointments" class="text-white">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm bg-success text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Upcoming Appointments</h5>
                    <h2 class="display-4"><?php echo $upcoming_appointments_count; ?></h2>
                    <p class="card-text">Future scheduled appointments</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#upcoming-appointments" class="text-white">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm bg-info text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Patients</h5>
                    <h2 class="display-4"><?php echo $total_patients_count; ?></h2>
                    <p class="card-text">Patients under your care</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="index.php?controller=doctor&action=my_patients" class="text-white">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm bg-warning text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Recent Prescriptions</h5>
                    <h2 class="display-4"><?php echo count($recent_prescriptions); ?></h2>
                    <p class="card-text">Recently issued prescriptions</p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="#recent-prescriptions" class="text-white">View Details <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Appointments -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="today-appointments"><i class="fas fa-calendar-day text-primary me-2"></i> Today's Appointments</h5>
                        <span class="badge bg-primary"><?php echo date('d M Y'); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($todays_appointments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-check text-muted fa-3x mb-3"></i>
                            <p class="text-muted">No appointments scheduled for today.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>Health ID</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todays_appointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($appointment['citizen_name']); ?>
                                                <small class="d-block text-muted">
                                                    <?php 
                                                        echo htmlspecialchars($appointment['gender']) . ', '; 
                                                        echo calculate_age($appointment['date_of_birth']) . ' yrs';
                                                    ?>
                                                </small>
                                            </td>
                                            <td><?php echo htmlspecialchars($appointment['health_id']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo get_appointment_status_color($appointment['status']); ?>">
                                                    <?php echo ucfirst($appointment['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="index.php?controller=doctor&action=patient_details&id=<?php echo $appointment['citizen_id']; ?>" class="btn btn-outline-primary" title="View Patient">
                                                        <i class="fas fa-user"></i>
                                                    </a>
                                                    <a href="index.php?controller=appointment&action=view&id=<?php echo $appointment['id']; ?>" class="btn btn-outline-info" title="View Appointment">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?controller=medical_record&action=create&appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-outline-success" title="Create Medical Record">
                                                        <i class="fas fa-notes-medical"></i>
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
                <div class="card-footer bg-white">
                    <a href="index.php?controller=appointment&action=doctor_appointments" class="btn btn-sm btn-outline-primary">View All Appointments</a>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" id="upcoming-appointments"><i class="fas fa-calendar-alt text-success me-2"></i> Upcoming Appointments</h5>
                        <a href="index.php?controller=doctor&action=my_schedule" class="btn btn-sm btn-outline-success">Manage Schedule</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($upcoming_appointments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar text-muted fa-3x mb-3"></i>
                            <p class="text-muted">No upcoming appointments scheduled.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($upcoming_appointments as $appointment): ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($appointment['citizen_name']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Health ID: <?php echo htmlspecialchars($appointment['health_id']); ?></small>
                                            <small class="d-block text-muted">
                                                <?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="index.php?controller=appointment&action=view&id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <a href="index.php?controller=appointment&action=doctor_appointments" class="btn btn-sm btn-outline-success">View All Appointments</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Prescriptions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0" id="recent-prescriptions"><i class="fas fa-prescription text-warning me-2"></i> Recent Prescriptions</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_prescriptions)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-prescription-bottle text-muted fa-3x mb-3"></i>
                            <p class="text-muted">No recent prescriptions found.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($recent_prescriptions as $prescription): ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($prescription['citizen_name']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo format_date($prescription['created_at']); ?>
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Health ID: <?php echo htmlspecialchars($prescription['health_id']); ?></small>
                                            <small class="d-block text-muted">
                                                <?php 
                                                    $medicines = json_decode($prescription['medicines'], true);
                                                    echo count($medicines) . ' medicine(s)';
                                                ?>
                                            </small>
                                        </div>
                                        <div>
                                            <a href="index.php?controller=prescription&action=view&id=<?php echo $prescription['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <a href="index.php?controller=prescription&action=doctor_prescriptions" class="btn btn-sm btn-outline-warning">View All Prescriptions</a>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bell text-danger me-2"></i> Recent Notifications</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_notifications)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash text-muted fa-3x mb-3"></i>
                            <p class="text-muted">No recent notifications.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($recent_notifications as $notification): ?>
                                <a href="<?php echo $notification['link']; ?>" class="list-group-item list-group-item-action <?php echo $notification['is_read'] ? '' : 'unread-notification'; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo format_date($notification['created_at']); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <a href="index.php?controller=notification&action=index" class="btn btn-sm btn-outline-danger">View All Notifications</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bolt text-primary me-2"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="index.php?controller=doctor&action=my_schedule" class="quick-action-link">
                                <div class="quick-action-icon bg-primary text-white">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <span>Manage Schedule</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="index.php?controller=doctor&action=my_patients" class="quick-action-link">
                                <div class="quick-action-icon bg-success text-white">
                                    <i class="fas fa-users"></i>
                                </div>
                                <span>My Patients</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="index.php?controller=prescription&action=create" class="quick-action-link">
                                <div class="quick-action-icon bg-warning text-white">
                                    <i class="fas fa-prescription"></i>
                                </div>
                                <span>Create Prescription</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="index.php?controller=lab_report&action=doctor_reports" class="quick-action-link">
                                <div class="quick-action-icon bg-info text-white">
                                    <i class="fas fa-flask"></i>
                                </div>
                                <span>Lab Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to get appointment status color
if (!function_exists('get_appointment_status_color')) {
function get_appointment_status_color($status) {
    switch ($status) {
        case 'confirmed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'cancelled':
            return 'danger';
        case 'completed':
            return 'info';
        default:
            return 'secondary';
    }
}
}
?>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>
