<?php
/**
 * Citizen Health Profile View
 */

// Set page title
$page_title = 'Health Profile';

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
                    <div class="d-grid">
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editHealthProfileModal">
                            <i class="fas fa-edit me-2"></i>Edit Health Profile
                        </button>
                    </div>
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
                        <li class="list-group-item px-0 active">
                            <a href="index.php?controller=citizen&action=health_profile" class="text-decoration-none text-white">
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
            <!-- Flash messages are now handled by layout.php -->
            
            <?php if (empty($health_profile['blood_group'])): ?>
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle me-2"></i>Complete Your Health Profile</h5>
                    <p>Your health profile is incomplete. Please click the "Edit Health Profile" button to add your health information.</p>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Basic Health Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Blood Group</h6>
                                    <p class="mb-0"><?php echo !empty($health_profile['blood_group']) ? $health_profile['blood_group'] : 'Not provided'; ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Height</h6>
                                    <p class="mb-0"><?php echo ($health_profile['height'] > 0) ? $health_profile['height'] . ' cm' : 'Not provided'; ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">Weight</h6>
                                    <p class="mb-0"><?php echo ($health_profile['weight'] > 0) ? $health_profile['weight'] . ' kg' : 'Not provided'; ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-muted">BMI</h6>
                                    <?php if ($health_profile['bmi'] > 0): ?>
                                        <p class="mb-0">
                                            <?php echo $health_profile['bmi']; ?> 
                                            <span class="badge <?php 
                                                if ($health_profile['bmi_category'] === 'Underweight') echo 'bg-warning';
                                                elseif ($health_profile['bmi_category'] === 'Normal weight') echo 'bg-success';
                                                elseif ($health_profile['bmi_category'] === 'Overweight') echo 'bg-warning';
                                                elseif ($health_profile['bmi_category'] === 'Obesity') echo 'bg-danger';
                                                else echo 'bg-secondary';
                                            ?>">
                                                <?php echo $health_profile['bmi_category']; ?>
                                            </span>
                                        </p>
                                    <?php else: ?>
                                        <p class="mb-0">Not available</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-allergies me-2"></i>Allergies</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($health_profile['allergies'])): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach (explode(',', $health_profile['allergies']) as $allergy): ?>
                                        <li class="list-group-item px-0">
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            <?php echo trim($allergy); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted mb-0">No allergies recorded</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-disease me-2"></i>Chronic Conditions</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($health_profile['chronic_conditions'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Condition</th>
                                                <th>Description</th>
                                                <th>Diagnosed Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($health_profile['chronic_conditions'] as $condition): ?>
                                                <tr>
                                                    <td><?php echo $condition['name']; ?></td>
                                                    <td><?php echo $condition['description']; ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($condition['diagnosed_date'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No chronic conditions recorded</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-pills me-2"></i>Current Medications</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($health_profile['current_medications'])): ?>
                                <div class="medication-list">
                                    <?php echo nl2br($health_profile['current_medications']); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No current medications recorded</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-dna me-2"></i>Family Medical History</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($health_profile['family_medical_history'])): ?>
                                <div class="family-history">
                                    <?php echo nl2br($health_profile['family_medical_history']); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No family medical history recorded</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-running me-2"></i>Lifestyle Information</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($health_profile['lifestyle_info'])): ?>
                        <div class="lifestyle-info">
                            <?php echo nl2br($health_profile['lifestyle_info']); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No lifestyle information recorded</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Health Profile Modal -->
<div class="modal fade" id="editHealthProfileModal" tabindex="-1" aria-labelledby="editHealthProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?controller=citizen&action=update_health_profile" method="post">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editHealthProfileModalLabel">Edit Health Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">Basic Health Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <label for="blood_group" class="form-label">Blood Group <span class="text-danger">*</span></label>
                            <select class="form-select" id="blood_group" name="blood_group" required>
                                <option value="">Select Blood Group</option>
                                <option value="A+" <?php echo ($health_profile['blood_group'] === 'A+') ? 'selected' : ''; ?>>A+</option>
                                <option value="A-" <?php echo ($health_profile['blood_group'] === 'A-') ? 'selected' : ''; ?>>A-</option>
                                <option value="B+" <?php echo ($health_profile['blood_group'] === 'B+') ? 'selected' : ''; ?>>B+</option>
                                <option value="B-" <?php echo ($health_profile['blood_group'] === 'B-') ? 'selected' : ''; ?>>B-</option>
                                <option value="AB+" <?php echo ($health_profile['blood_group'] === 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                <option value="AB-" <?php echo ($health_profile['blood_group'] === 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                <option value="O+" <?php echo ($health_profile['blood_group'] === 'O+') ? 'selected' : ''; ?>>O+</option>
                                <option value="O-" <?php echo ($health_profile['blood_group'] === 'O-') ? 'selected' : ''; ?>>O-</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="height" class="form-label">Height (cm) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="height" name="height" value="<?php echo $health_profile['height']; ?>" min="1" step="0.1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="weight" class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="weight" name="weight" value="<?php echo $health_profile['weight']; ?>" min="1" step="0.1" required>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Allergies</h6>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="allergies" class="form-label">Allergies (comma separated)</label>
                            <textarea class="form-control" id="allergies" name="allergies" rows="2"><?php echo $health_profile['allergies']; ?></textarea>
                            <small class="text-muted">E.g., Penicillin, Peanuts, Latex</small>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Chronic Conditions</h6>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Select Chronic Conditions</label>
                            <div class="row">
                                <?php foreach ($all_chronic_conditions as $condition): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="chronic_conditions[]" value="<?php echo $condition['id']; ?>" id="condition_<?php echo $condition['id']; ?>" 
                                                <?php 
                                                if (!empty($health_profile['chronic_conditions'])) {
                                                    foreach ($health_profile['chronic_conditions'] as $citizen_condition) {
                                                        if ($citizen_condition['condition_id'] == $condition['id']) {
                                                            echo 'checked';
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>>
                                            <label class="form-check-label" for="condition_<?php echo $condition['id']; ?>">
                                                <?php echo $condition['name']; ?>
                                                <small class="text-muted d-block"><?php echo $condition['description']; ?></small>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Current Medications</h6>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="current_medications" class="form-label">Current Medications</label>
                            <textarea class="form-control" id="current_medications" name="current_medications" rows="3"><?php echo $health_profile['current_medications']; ?></textarea>
                            <small class="text-muted">List all medications you are currently taking, including dosage and frequency</small>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Family Medical History</h6>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="family_medical_history" class="form-label">Family Medical History</label>
                            <textarea class="form-control" id="family_medical_history" name="family_medical_history" rows="3"><?php echo $health_profile['family_medical_history']; ?></textarea>
                            <small class="text-muted">Include information about diseases or conditions that run in your family</small>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Lifestyle Information</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="lifestyle_info" class="form-label">Lifestyle Information</label>
                            <textarea class="form-control" id="lifestyle_info" name="lifestyle_info" rows="3"><?php echo $health_profile['lifestyle_info']; ?></textarea>
                            <small class="text-muted">Include information about diet, exercise, smoking, alcohol consumption, etc.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Calculate BMI when height or weight changes
    document.getElementById('height').addEventListener('input', calculateBMI);
    document.getElementById('weight').addEventListener('input', calculateBMI);
    
    function calculateBMI() {
        const height = parseFloat(document.getElementById('height').value);
        const weight = parseFloat(document.getElementById('weight').value);
        
        if (height > 0 && weight > 0) {
            const heightInMeters = height / 100;
            const bmi = weight / (heightInMeters * heightInMeters);
            
            // Display BMI (optional)
            console.log('BMI: ' + bmi.toFixed(1));
        }
    }
</script>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>