<?php
/**
 * Reset Password Page View
 */

// Set page title
$page_title = 'Reset Password';

// Start output buffering
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Reset Your Password</h2>
                        <p class="text-muted">Create a new password for your account</p>
                    </div>
                    
                    <?php display_flash_message(); ?>
                    
                    <form action="index.php?controller=auth&action=reset_password" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
                        <input type="hidden" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                        
                        <div class="mb-3">
                            <label for="password" class="form-label required-field">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your new password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please enter a new password.</div>
                            <div id="passwordStrength" class="mt-2"></div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label required-field">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                            </div>
                            <div class="invalid-feedback">Please confirm your new password.</div>
                            <div id="passwordMatch" class="mt-2"></div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p>Remember your password? <a href="index.php?controller=auth&action=login">Login</a></p>
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
    
    // Password strength checker
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