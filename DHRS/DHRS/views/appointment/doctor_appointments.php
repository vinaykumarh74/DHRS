<?php
/**
 * Doctor Appointments View
 */

// Set page title
$page_title = 'My Appointments';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2><i class="fas fa-calendar-check me-2 text-primary"></i> My Appointments</h2>
                        <a href="index.php?controller=doctor&action=dashboard" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="appointmentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="today-tab" data-bs-toggle="tab" data-bs-target="#today" type="button" role="tab" aria-controls="today" aria-selected="true">
                                Today's Appointments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="false">
                                Upcoming Appointments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">
                                Past Appointments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="false">
                                All Appointments
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="appointmentTabsContent">
                        <!-- Today's Appointments Tab -->
                        <div class="tab-pane fade show active" id="today" role="tabpanel" aria-labelledby="today-tab">
                            <?php 
                            $today_appointments = array_filter($appointments, function($appointment) {
                                return $appointment['appointment_date'] == date('Y-m-d');
                            });
                            
                            if (empty($today_appointments)): 
                            ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-day text-muted fa-3x mb-3"></i>
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
                                            <?php foreach ($today_appointments as $appointment): ?>
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
                                                            <?php if ($appointment['status'] == 'confirmed'): ?>
                                                                <a href="index.php?controller=medical_record&action=create&appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-outline-success" title="Create Medical Record">
                                                                    <i class="fas fa-notes-medical"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Upcoming Appointments Tab -->
                        <div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                            <?php 
                            $upcoming_appointments = array_filter($appointments, function($appointment) {
                                return $appointment['appointment_date'] > date('Y-m-d');
                            });
                            
                            if (empty($upcoming_appointments)): 
                            ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-alt text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">No upcoming appointments scheduled.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Health ID</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($upcoming_appointments as $appointment): ?>
                                                <tr>
                                                    <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
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
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Past Appointments Tab -->
                        <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
                            <?php 
                            $past_appointments = array_filter($appointments, function($appointment) {
                                return $appointment['appointment_date'] < date('Y-m-d');
                            });
                            
                            if (empty($past_appointments)): 
                            ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">No past appointments found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Health ID</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($past_appointments as $appointment): ?>
                                                <tr>
                                                    <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
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
                                                            <?php if ($appointment['status'] == 'completed'): ?>
                                                                <a href="index.php?controller=prescription&action=create&appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-outline-success" title="Create Prescription">
                                                                    <i class="fas fa-prescription"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- All Appointments Tab -->
                        <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                            <?php if (empty($appointments)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar text-muted fa-3x mb-3"></i>
                                    <p class="text-muted">No appointments found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Patient</th>
                                                <th>Health ID</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($appointments as $appointment): ?>
                                                <tr>
                                                    <td><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></td>
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
                                                            <?php if ($appointment['status'] == 'confirmed'): ?>
                                                                <a href="index.php?controller=medical_record&action=create&appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-outline-success" title="Create Medical Record">
                                                                    <i class="fas fa-notes-medical"></i>
                                                                </a>
                                                            <?php elseif ($appointment['status'] == 'completed'): ?>
                                                                <a href="index.php?controller=prescription&action=create&appointment_id=<?php echo $appointment['id']; ?>" class="btn btn-outline-success" title="Create Prescription">
                                                                    <i class="fas fa-prescription"></i>
                                                                </a>
                                                            <?php endif; ?>
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
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
include('views/layouts/main.php');
?>