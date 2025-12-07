<?php
/**
 * Citizen Profile View
 */

// Set page title
$page_title = 'My Profile';

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
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-2"></i>Edit Profile
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
                        <li class="list-group-item px-0 active">
                            <a href="index.php?controller=citizen&action=profile" class="text-decoration-none text-white">
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
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Full Name</h6>
                            <p class="mb-0"><?php echo $citizen['first_name'] . ' ' . $citizen['last_name']; ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Health ID</h6>
                            <p class="mb-0"><?php echo $citizen['health_id']; ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Date of Birth</h6>
                            <p class="mb-0"><?php echo date('F d, Y', strtotime($citizen['date_of_birth'])); ?> (Age: <?php echo calculate_age($citizen['date_of_birth']); ?>)</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Gender</h6>
                            <p class="mb-0"><?php echo ucfirst($citizen['gender']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Email Address</h6>
                            <p class="mb-0"><?php echo $citizen['email']; ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Phone Number</h6>
                            <p class="mb-0"><?php echo $citizen['phone']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Address Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <h6 class="text-muted">Address</h6>
                            <p class="mb-0"><?php echo $citizen['address']; ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">City</h6>
                            <p class="mb-0"><?php echo $citizen['city']; ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">State</h6>
                            <p class="mb-0"><?php echo $citizen['state']; ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">Postal Code</h6>
                            <p class="mb-0"><?php echo $citizen['postal_code']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-phone-alt me-2"></i>Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Emergency Contact Name</h6>
                            <p class="mb-0"><?php echo !empty($citizen['emergency_contact_name']) ? $citizen['emergency_contact_name'] : 'Not provided'; ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Emergency Contact Phone</h6>
                            <p class="mb-0"><?php echo !empty($citizen['emergency_contact_phone']) ? $citizen['emergency_contact_phone'] : 'Not provided'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Account Created</h6>
                            <p class="mb-0"><?php echo date('F d, Y', strtotime($citizen['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Password</h6>
                            <p class="mb-0">********</p>
                            <a href="index.php?controller=auth&action=change_password" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-key me-1"></i>Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?controller=citizen&action=update_profile" method="post" enctype="multipart/form-data">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12 text-center mb-3">
                            <div class="profile-image-container position-relative d-inline-block">
                                <?php if (!empty($citizen['profile_image'])): ?>
                                    <img src="uploads/profile/<?php echo $citizen['profile_image']; ?>" alt="Profile Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" id="profileImagePreview">
                                <?php else: ?>
                                    <img src="assets/img/default-profile.png" alt="Default Profile" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" id="profileImagePreview">
                                <?php endif; ?>
                                <div class="upload-btn-wrapper position-absolute bottom-0 end-0">
                                    <button class="btn btn-sm btn-primary rounded-circle" type="button">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                    <input type="file" name="profile_image" id="profileImageInput" accept="image/*">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">Click on the camera icon to upload a new profile image</small>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Personal Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $citizen['first_name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $citizen['last_name']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $citizen['date_of_birth']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" <?php echo ($citizen['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($citizen['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo ($citizen['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $citizen['email']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $citizen['phone']; ?>" required>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Address Information</h6>
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo $citizen['address']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" value="<?php echo $citizen['city']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="state" name="state" value="<?php echo $citizen['state']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo $citizen['postal_code']; ?>" required>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Emergency Contact</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo $citizen['emergency_contact_name']; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                            <input type="tel" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo $citizen['emergency_contact_phone']; ?>">
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
    // Profile image preview
    document.getElementById('profileImageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImagePreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>