<?php
/**
 * Forgot Password Page View
 */

// Set page title
$page_title = 'Forgot Password';

// Start output buffering
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Forgot Password</h2>
                        <p class="text-muted">Enter your email address to receive a password reset link</p>
                    </div>
                    
                    <?php display_flash_message(); ?>
                    
                    <form action="index.php?controller=auth&action=forgot_password" method="post" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="email" class="form-label required-field">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your registered email" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                            <small class="form-text text-muted mt-2">We'll send a verification code to this email address.</small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
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