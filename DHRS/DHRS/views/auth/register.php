<?php
/**
 * Registration Page View
 */

// Set page title
$page_title = 'Register';

// Start output buffering
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Create Your Account</h2>
                        <p class="text-muted">Join the Digital Health Record System to manage your health records</p>
                    </div>
                    
                    <!-- Display flash messages -->
                    <?php display_flash_message(); ?>
                    
                    <!-- Note: We're using display_flash_message() function instead of including flash_messages.php -->
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form action="index.php?controller=auth&action=register" method="post" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label required-field">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter your first name" required>
                                <div class="invalid-feedback">Please enter your first name.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label required-field">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter your last name" required>
                                <div class="invalid-feedback">Please enter your last name.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label required-field">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                            <div class="invalid-feedback">Please choose a username.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label required-field">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label required-field">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
                            <div class="invalid-feedback">Please enter a valid phone number.</div>
                            <small class="form-text text-muted">We'll send an OTP to this number for verification.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label required-field">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Please create a password.</div>
                                <div id="passwordStrength" class="mt-2"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label required-field">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                                <div class="invalid-feedback">Please confirm your password.</div>
                                <div id="passwordMatch" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label required-field">Register As</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" selected disabled>Select your role</option>
                                <option value="citizen">Citizen</option>
                                <option value="doctor">Doctor</option>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>
                        
                        <div id="doctorFields" class="d-none">
                            <div class="mb-3">
                                <label for="specialization" class="form-label required-field">Specialization</label>
                                <input type="text" class="form-control" id="specialization" name="specialization" placeholder="Enter your specialization">
                                <div class="invalid-feedback">Please enter your specialization.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="qualification" class="form-label required-field">Qualification</label>
                                <input type="text" class="form-control" id="qualification" name="qualification" placeholder="Enter your qualification (e.g., MBBS, MD, etc.)">
                                <div class="invalid-feedback">Please enter your qualification.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="license_number" class="form-label required-field">Medical License Number</label>
                                <input type="text" class="form-control" id="license_number" name="license_number" placeholder="Enter your license number">
                                <div class="invalid-feedback">Please enter your license number.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="experience_years" class="form-label required-field">Years of Experience</label>
                                <input type="number" class="form-control" id="experience_years" name="experience_years" placeholder="Enter years of experience" min="0" max="50">
                                <div class="invalid-feedback">Please enter your years of experience.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">I agree to the <a href="index.php?controller=home&action=terms" target="_blank">Terms of Service</a> and <a href="index.php?controller=home&action=privacy" target="_blank">Privacy Policy</a></label>
                            <div class="invalid-feedback">You must agree to the terms and conditions.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Register</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p>Already have an account? <a href="index.php?controller=auth&action=login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Role-based field display
    document.getElementById('role').addEventListener('change', function() {
        const doctorFields = document.getElementById('doctorFields');
        const doctorInputs = doctorFields.querySelectorAll('input, select');
        
        if (this.value === 'doctor') {
            doctorFields.classList.remove('d-none');
            doctorInputs.forEach(input => {
                input.required = true;
            });
        } else {
            doctorFields.classList.add('d-none');
            doctorInputs.forEach(input => {
                input.required = false;
                input.value = '';
            });
        }
    });
    
    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthDiv = document.getElementById('passwordStrength');
        
        // Reset
        strengthDiv.className = 'mt-2';
        
        if (password.length === 0) {
            strengthDiv.innerHTML = '';
            return;
        }
        
        // Check strength
        let strength = 0;
        const patterns = [
            /[a-z]+/, // lowercase
            /[A-Z]+/, // uppercase
            /[0-9]+/, // numbers
            /[^a-zA-Z0-9]+/ // special chars
        ];
        
        patterns.forEach(pattern => {
            if (pattern.test(password)) {
                strength++;
            }
        });
        
        if (password.length < 8) {
            strength = Math.min(strength, 1);
        }
        
        // Display strength
        let strengthText = '';
        let strengthClass = '';
        
        switch (strength) {
            case 0:
            case 1:
                strengthText = 'Weak';
                strengthClass = 'text-danger';
                break;
            case 2:
                strengthText = 'Moderate';
                strengthClass = 'text-warning';
                break;
            case 3:
                strengthText = 'Good';
                strengthClass = 'text-info';
                break;
            case 4:
                strengthText = 'Strong';
                strengthClass = 'text-success';
                break;
        }
        
        strengthDiv.innerHTML = `Password strength: <span class="${strengthClass}">${strengthText}</span>`;
    });
    
    // Password match checker
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        const matchDiv = document.getElementById('passwordMatch');
        
        // Reset
        matchDiv.className = 'mt-2';
        
        if (confirmPassword.length === 0) {
            matchDiv.innerHTML = '';
            return;
        }
        
        if (password === confirmPassword) {
            matchDiv.innerHTML = '<span class="text-success">Passwords match</span>';
        } else {
            matchDiv.innerHTML = '<span class="text-danger">Passwords do not match</span>';
        }
    });
    
    // Form validation
    (function() {
        'use strict';
        
        const forms = document.querySelectorAll('.needs-validation');
        
        Array.from(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                // Additional validation
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    event.preventDefault();
                    document.getElementById('passwordMatch').innerHTML = '<span class="text-danger">Passwords do not match</span>';
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
include('views/layout.php');
?>