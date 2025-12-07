<?php
/**
 * Patient Details View for Doctor
 */

// Set page title
$page_title = 'Patient Details';

// Start output buffering
ob_start();
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title"><i class="fas fa-user text-primary me-2"></i> Patient Details</h2>
                <div>
                    <a href="index.php?controller=doctor&action=my_patients" class="btn btn-outline-primary me-2">
                        <i class="fas fa-arrow-left me-2"></i> Back to Patients
                    </a>
                    <div class="btn-group">
                        <a href="index.php?controller=prescription&action=create&citizen_id=<?php echo $patient['id']; ?>" class="btn btn-success">
                            <i class="fas fa-prescription me-2"></i> Create Prescription
                        </a>
                        <a href="index.php?controller=medical_record&action=create&citizen_id=<?php echo $patient['id']; ?>" class="btn btn-info text-white">
                            <i class="fas fa-notes-medical me-2"></i> Create Medical Record
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Flash messages are now handled by layout.php -->
    
    <div class="row">
        <!-- Patient Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-id-card text-primary me-2"></i> Patient Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <?php if (!empty($patient['profile_image'])): ?>
                            <img src="uploads/profile/<?php echo htmlspecialchars($patient['profile_image']); ?>" alt="Profile Image" class="img-fluid rounded-circle patient-profile-image mb-3">
                        <?php else: ?>
                            <div class="patient-profile-placeholder rounded-circle bg-primary text-white mb-3">
                                <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h4>
                        <p class="text-muted mb-0">Health ID: <?php echo htmlspecialchars($patient['health_id']); ?></p>
                    </div>
                    
                    <div class="patient-info-list">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-birthday-cake text-muted me-2"></i> Date of Birth</div>
                            <div class="info-value">
                                <?php echo date('d M Y', strtotime($patient['date_of_birth'])); ?>
                                <span class="text-muted">(<?php echo calculate_age($patient['date_of_birth']); ?> years)</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-venus-mars text-muted me-2"></i> Gender</div>
                            <div class="info-value"><?php echo ucfirst(htmlspecialchars($patient['gender'])); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-phone-alt text-muted me-2"></i> Phone</div>
                            <div class="info-value"><?php echo htmlspecialchars($patient['phone']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-envelope text-muted me-2"></i> Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($patient['email']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-map-marker-alt text-muted me-2"></i> Address</div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($patient['address']); ?><br>
                                <?php echo htmlspecialchars($patient['city'] . ', ' . $patient['state'] . ' - ' . $patient['postal_code']); ?>
                            </div>
                        </div>
                        <?php if (!empty($patient['emergency_contact_name'])): ?>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-ambulance text-muted me-2"></i> Emergency Contact</div>
                                <div class="info-value">
                                    <?php echo htmlspecialchars($patient['emergency_contact_name']); ?><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($patient['emergency_contact_relationship']); ?></span><br>
                                    <?php echo htmlspecialchars($patient['emergency_contact_phone']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Health Profile -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-heartbeat text-danger me-2"></i> Health Profile</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Health Info -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary">Basic Health Information</h6>
                            <div class="table-responsive">
                                <table class="table table-borderless table-sm">
                                    <tbody>
                                        <tr>
                                            <th width="40%">Blood Group</th>
                                            <td>
                                                <?php if ($health_profile['blood_group'] !== 'Not provided'): ?>
                                                    <span class="badge bg-danger"><?php echo htmlspecialchars($health_profile['blood_group']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not provided</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Height</th>
                                            <td>
                                                <?php if ($health_profile['height'] > 0): ?>
                                                    <?php echo htmlspecialchars($health_profile['height']); ?> cm
                                                <?php else: ?>
                                                    <span class="text-muted">Not provided</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Weight</th>
                                            <td>
                                                <?php if ($health_profile['weight'] > 0): ?>
                                                    <?php echo htmlspecialchars($health_profile['weight']); ?> kg
                                                <?php else: ?>
                                                    <span class="text-muted">Not provided</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>BMI</th>
                                            <td>
                                                <?php if ($health_profile['bmi'] > 0): ?>
                                                    <?php echo htmlspecialchars($health_profile['bmi']); ?>
                                                    <span class="ms-2 badge bg-<?php echo get_bmi_color($health_profile['bmi']); ?>">
                                                        <?php echo htmlspecialchars($health_profile['bmi_category']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not calculated</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Chronic Conditions -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary">Chronic Conditions</h6>
                            <?php if (empty($health_profile['chronic_conditions'])): ?>
                                <p class="text-muted">No chronic conditions reported.</p>
                            <?php else: ?>
                                <ul class="list-group">
                                    <?php foreach ($health_profile['chronic_conditions'] as $condition): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <?php echo htmlspecialchars($condition['name']); ?>
                                            <span class="badge bg-primary rounded-pill">
                                                <?php echo date('M Y', strtotime($condition['diagnosed_date'])); ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Allergies -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary">Allergies</h6>
                            <?php if (empty($health_profile['allergies'])): ?>
                                <p class="text-muted">No allergies reported.</p>
                            <?php else: ?>
                                <p><?php echo nl2br(htmlspecialchars($health_profile['allergies'])); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Current Medications -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary">Current Medications</h6>
                            <?php if (empty($health_profile['current_medications'])): ?>
                                <p class="text-muted">No current medications reported.</p>
                            <?php else: ?>
                                <p><?php echo nl2br(htmlspecialchars($health_profile['current_medications'])); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Family Medical History -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary">Family Medical History</h6>
                            <?php if (empty($health_profile['family_medical_history'])): ?>
                                <p class="text-muted">No family medical history reported.</p>
                            <?php else: ?>
                                <p><?php echo nl2br(htmlspecialchars($health_profile['family_medical_history'])); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Lifestyle Information -->
                        <div class="col-md-6 mb-4">
                            <h6 class="text-primary">Lifestyle Information</h6>
                            <?php if (empty($health_profile['lifestyle_info'])): ?>
                                <p class="text-muted">No lifestyle information reported.</p>
                            <?php else: ?>
                                <p><?php echo nl2br(htmlspecialchars($health_profile['lifestyle_info'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tabs for Medical History -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="patientTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="true">
                        <i class="fas fa-calendar-check me-2"></i> Appointments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="medical-records-tab" data-bs-toggle="tab" data-bs-target="#medical-records" type="button" role="tab" aria-controls="medical-records" aria-selected="false">
                        <i class="fas fa-notes-medical me-2"></i> Medical Records
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab" aria-controls="prescriptions" aria-selected="false">
                        <i class="fas fa-prescription me-2"></i> Prescriptions
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lab-reports-tab" data-bs-toggle="tab" data-bs-target="#lab-reports" type="button" role="tab" aria-controls="lab-reports" aria-selected="false">
                        <i class="fas fa-flask me-2"></i> Lab Reports
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="patientTabsContent">
                <!-- Appointments Tab -->
                <div class="tab-pane fade show active" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                    <?php if (empty($appointments)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                            <h5 class="text-muted">No appointment history</h5>
                            <p class="text-muted">This patient has no appointments with you yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                        <tr>
                                            <td>
                                                <?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?><br>
                                                <small class="text-muted"><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo get_appointment_status_color($appointment['status']); ?>">
                                                    <?php echo ucfirst($appointment['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="index.php?controller=appointment&action=view&id=<?php echo $appointment['id']; ?>" class="btn btn-outline-primary" title="View Appointment">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($appointment['status'] === 'confirmed'): ?>
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
                
                <!-- Medical Records Tab -->
                <div class="tab-pane fade" id="medical-records" role="tabpanel" aria-labelledby="medical-records-tab">
                    <?php if (empty($medical_records)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-medical-alt text-muted fa-3x mb-3"></i>
                            <h5 class="text-muted">No medical records</h5>
                            <p class="text-muted">You haven't created any medical records for this patient yet.</p>
                            <a href="index.php?controller=medical_record&action=create&citizen_id=<?php echo $patient['id']; ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> Create Medical Record
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Diagnosis</th>
                                        <th>Symptoms</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medical_records as $record): ?>
                                        <tr>
                                            <td>
                                                <?php echo date('d M Y', strtotime($record['created_at'])); ?><br>
                                                <small class="text-muted"><?php echo date('h:i A', strtotime($record['created_at'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($record['diagnosis']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($record['symptoms'], 0, 50) . (strlen($record['symptoms']) > 50 ? '...' : '')); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="index.php?controller=medical_record&action=view&id=<?php echo $record['id']; ?>" class="btn btn-outline-primary" title="View Record">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?controller=medical_record&action=download&id=<?php echo $record['id']; ?>" class="btn btn-outline-info" title="Download PDF">
                                                        <i class="fas fa-download"></i>
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
                
                <!-- Prescriptions Tab -->
                <div class="tab-pane fade" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab">
                    <?php if (empty($prescriptions)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-prescription-bottle text-muted fa-3x mb-3"></i>
                            <h5 class="text-muted">No prescriptions</h5>
                            <p class="text-muted">You haven't created any prescriptions for this patient yet.</p>
                            <a href="index.php?controller=prescription&action=create&citizen_id=<?php echo $patient['id']; ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> Create Prescription
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Diagnosis</th>
                                        <th>Medicines</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prescriptions as $prescription): ?>
                                        <tr>
                                            <td>
                                                <?php echo date('d M Y', strtotime($prescription['created_at'])); ?><br>
                                                <small class="text-muted"><?php echo date('h:i A', strtotime($prescription['created_at'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($prescription['diagnosis']); ?></td>
                                            <td>
                                                <?php 
                                                    $medicines = json_decode($prescription['medicines'], true);
                                                    echo count($medicines) . ' medicine(s)';
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="index.php?controller=prescription&action=view&id=<?php echo $prescription['id']; ?>" class="btn btn-outline-primary" title="View Prescription">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?controller=prescription&action=download&id=<?php echo $prescription['id']; ?>" class="btn btn-outline-info" title="Download PDF">
                                                        <i class="fas fa-download"></i>
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
                
                <!-- Lab Reports Tab -->
                <div class="tab-pane fade" id="lab-reports" role="tabpanel" aria-labelledby="lab-reports-tab">
                    <?php if (empty($lab_reports)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-vial text-muted fa-3x mb-3"></i>
                            <h5 class="text-muted">No lab reports</h5>
                            <p class="text-muted">No lab reports have been created for this patient yet.</p>
                            <a href="index.php?controller=lab_report&action=create&citizen_id=<?php echo $patient['id']; ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> Create Lab Report
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Test</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lab_reports as $report): ?>
                                        <tr>
                                            <td>
                                                <?php echo date('d M Y', strtotime($report['created_at'])); ?><br>
                                                <small class="text-muted"><?php echo date('h:i A', strtotime($report['created_at'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($report['test_name']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo get_lab_report_status_color($report['status']); ?>">
                                                    <?php echo ucfirst($report['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="index.php?controller=lab_report&action=view&id=<?php echo $report['id']; ?>" class="btn btn-outline-primary" title="View Report">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($report['status'] === 'completed'): ?>
                                                        <a href="index.php?controller=lab_report&action=download&id=<?php echo $report['id']; ?>" class="btn btn-outline-info" title="Download PDF">
                                                            <i class="fas fa-download"></i>
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

<style>
    .patient-profile-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }
    
    .patient-profile-placeholder {
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 36px;
        margin: 0 auto;
    }
    
    .patient-info-list .info-item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .patient-info-list .info-item:last-child {
        border-bottom: none;
    }
    
    .patient-info-list .info-label {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    
    .patient-info-list .info-value {
        font-weight: 500;
    }
</style>

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

// Helper function to get lab report status color
function get_lab_report_status_color($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'in_progress':
            return 'info';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Helper function to get BMI color
function get_bmi_color($bmi) {
    if ($bmi < 18.5) {
        return 'warning'; // Underweight
    } elseif ($bmi >= 18.5 && $bmi < 25) {
        return 'success'; // Normal weight
    } elseif ($bmi >= 25 && $bmi < 30) {
        return 'warning'; // Overweight
    } else {
        return 'danger'; // Obesity
    }
}
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>