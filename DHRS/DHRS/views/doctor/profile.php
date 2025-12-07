<?php
/**
 * Doctor Profile View
 */

// Set page title
$page_title = 'Doctor Profile';

// Start output buffering
ob_start();
?>

<div class="container py-4">
    <div class="row">
        <!-- Sidebar / Quick Links -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="profile-image-container mb-3">
                        <?php if (!empty($doctor['profile_image'])): ?>
                            <img src="uploads/profile/<?php echo htmlspecialchars($doctor['profile_image']); ?>" alt="Profile Image" class="img-fluid rounded-circle profile-image">
                        <?php else: ?>
                            <div class="profile-image-placeholder rounded-circle">
                                <i class="fas fa-user-md fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="mb-1">Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h5>
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                    <button class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-2"></i> Edit Profile
                    </button>
                </div>
                <div class="list-group list-group-flush">
                    <a href="index.php?controller=doctor&action=dashboard" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a href="index.php?controller=doctor&action=my_patients" class="list-group-item list-group-item-action">
                        <i class="fas fa-users me-2"></i> My Patients
                    </a>
                    <a href="index.php?controller=doctor&action=my_schedule" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-alt me-2"></i> My Schedule
                    </a>
                    <a href="index.php?controller=appointment&action=doctor_appointments" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-check me-2"></i> Appointments
                    </a>
                    <a href="index.php?controller=prescription&action=doctor_prescriptions" class="list-group-item list-group-item-action">
                        <i class="fas fa-prescription me-2"></i> Prescriptions
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Flash messages are now handled by layout.php -->
            
            <!-- Personal Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user-md me-2 text-primary"></i> Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Full Name</label>
                            <p class="mb-0">Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Date of Birth</label>
                            <p class="mb-0"><?php echo date('d M Y', strtotime($doctor['date_of_birth'])); ?> (<?php echo calculate_age($doctor['date_of_birth']); ?> years)</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Gender</label>
                            <p class="mb-0"><?php echo htmlspecialchars(ucfirst($doctor['gender'])); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Specialization</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">License Number</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['license_number']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Qualification</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['qualification']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Experience</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['experience_years']); ?> years</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Consultation Fee</label>
                            <p class="mb-0">₹<?php echo htmlspecialchars($doctor['consultation_fee']); ?></p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Bio</label>
                            <p class="mb-0"><?php echo !empty($doctor['bio']) ? htmlspecialchars($doctor['bio']) : 'No bio provided'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Address Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i> Address Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small">Address</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['address']); ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">City</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['city']); ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">State</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['state']); ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Postal Code</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['postal_code']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-address-card me-2 text-success"></i> Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Email Address</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['email']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Phone Number</label>
                            <p class="mb-0"><?php echo htmlspecialchars($doctor['phone']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2 text-info"></i> Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Account Created</label>
                            <p class="mb-0"><?php echo date('d M Y', strtotime($doctor['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Password</label>
                            <p class="mb-0">********</p>
                            <a href="index.php?controller=auth&action=change_password" class="btn btn-sm btn-outline-secondary mt-2">
                                <i class="fas fa-key me-2"></i> Change Password
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
            <form action="index.php?controller=doctor&action=update_profile" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <h6 class="form-label">Personal Information</h6>
                            <hr>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($doctor['first_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($doctor['last_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($doctor['date_of_birth']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male" <?php echo $doctor['gender'] === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo $doctor['gender'] === 'female' ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo $doctor['gender'] === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="specialization" name="specialization" value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="license_number" class="form-label">License Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="license_number" name="license_number" value="<?php echo htmlspecialchars($doctor['license_number']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="qualification" class="form-label">Qualification <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qualification" name="qualification" value="<?php echo htmlspecialchars($doctor['qualification']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="experience_years" class="form-label">Years of Experience <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?php echo htmlspecialchars($doctor['experience_years']); ?>" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="consultation_fee" class="form-label">Consultation Fee (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="consultation_fee" name="consultation_fee" value="<?php echo htmlspecialchars($doctor['consultation_fee']); ?>" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/gif">
                            <small class="form-text text-muted">Upload a profile image (JPG, PNG, or GIF, max 2MB)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Profile Image</label>
                            <div class="profile-preview-container">
                                <?php if (!empty($doctor['profile_image'])): ?>
                                    <img src="uploads/profile/<?php echo htmlspecialchars($doctor['profile_image']); ?>" alt="Current Profile Image" class="img-thumbnail profile-preview">
                                <?php else: ?>
                                    <div class="profile-preview-placeholder">
                                        <i class="fas fa-user-md"></i>
                                        <span>No Image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($doctor['bio']); ?></textarea>
                        </div>
                        
                        <div class="col-12 mb-4 mt-2">
                            <h6 class="form-label">Address Information</h6>
                            <hr>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($doctor['address']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($doctor['city']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($doctor['state']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($doctor['postal_code']); ?>" required>
                        </div>
                        
                        <div class="col-12 mb-4 mt-2">
                            <h6 class="form-label">Contact Information</h6>
                            <hr>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required>
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
    // Preview profile image before upload
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const previewContainer = document.querySelector('.profile-preview-container');
                previewContainer.innerHTML = `<img src="${event.target.result}" alt="Profile Preview" class="img-thumbnail profile-preview">`;
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
