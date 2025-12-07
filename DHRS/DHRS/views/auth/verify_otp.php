<?php
/**
 * OTP Verification Page View
 */

// Set page title
$page_title = 'Verify OTP';

// Start output buffering
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Verify Your Account</h2>
                        <p class="text-muted">Enter the verification code sent to your phone</p>
                    </div>
                    
                    <!-- Debug session data -->
                    <div style="display: none;">
                        <?php echo "Session ID: " . session_id(); ?>
                        <pre><?php print_r($_SESSION); ?></pre>
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
                    
                    <form action="index.php?controller=auth&action=verify_otp" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="user_id" value="<?php echo isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id']) : ''; ?>">
                        <input type="hidden" name="phone" value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>">
                        
                        <div class="mb-4">
                            <label for="otp" class="form-label required-field">Verification Code (OTP)</label>
                            <div class="otp-input-container d-flex justify-content-between mb-3">
                                <input type="text" class="form-control otp-input text-center" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="form-control otp-input text-center" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="form-control otp-input text-center" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="form-control otp-input text-center" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="form-control otp-input text-center" maxlength="1" pattern="[0-9]" required>
                                <input type="text" class="form-control otp-input text-center" maxlength="1" pattern="[0-9]" required>
                            </div>
                            <input type="hidden" id="otp" name="otp" required>
                            <div class="invalid-feedback">Please enter the verification code.</div>
                            <div class="text-center">
                                <div id="countdown" class="text-muted mb-2">Code expires in: <span id="timer">05:00</span></div>
                                <p class="mb-0">Didn't receive the code? 
                                    <a href="javascript:void(0);" id="resendOtp" class="disabled" onclick="resendOTP()">Resend Code</a>
                                </p>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Verify Account</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p>Already verified? <a href="index.php?controller=auth&action=login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .otp-input-container {
        gap: 10px;
    }
    
    .otp-input {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        font-weight: 600;
    }
</style>

<script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // OTP input handling
        const otpInputs = document.querySelectorAll('.otp-input');
        const otpField = document.getElementById('otp');
        
        // Focus on first input when page loads
        if (otpInputs.length > 0) {
            setTimeout(() => otpInputs[0].focus(), 100);
        }
        
        otpInputs.forEach((input, index) => {
            // Handle input
            input.addEventListener('input', function() {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
                
                // Auto focus next input
                if (this.value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                // Update hidden field with complete OTP
                updateOTPValue();
            });
            
            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
            
            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').trim().substring(0, 6);
                
                if (/^[0-9]+$/.test(pasteData)) {
                    for (let i = 0; i < pasteData.length && i < otpInputs.length; i++) {
                        otpInputs[i].value = pasteData[i];
                    }
                    
                    // Focus on the next empty input or the last one
                    const nextIndex = Math.min(pasteData.length, otpInputs.length - 1);
                    otpInputs[nextIndex].focus();
                    
                    updateOTPValue();
                }
            });
        });
        
        function updateOTPValue() {
            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value;
            });
            otpField.value = otp;
            console.log('OTP Value:', otp); // Debug log for verification
        }
    });
    
    // Countdown timer
    let timeLeft = 300; // 5 minutes in seconds
    const timerElement = document.getElementById('timer');
    const resendButton = document.getElementById('resendOtp');
    
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            resendButton.classList.remove('disabled');
            document.getElementById('countdown').textContent = 'Code expired. Please request a new one.';
        } else {
            timeLeft--;
        }
    }
    
    let timerInterval = setInterval(updateTimer, 1000);
    updateTimer(); // Initial call
    
    // Resend OTP function
    function resendOTP() {
        if (resendButton.classList.contains('disabled')) {
            return;
        }
        
        const userId = document.querySelector('input[name="user_id"]').value;
        const phone = document.querySelector('input[name="phone"]').value;
        
        // Disable the button during the request
        resendButton.classList.add('disabled');
        resendButton.textContent = 'Sending...';
        
        // Send AJAX request to resend OTP
        fetch(`index.php?controller=auth&action=resend_otp&user_id=${userId}&phone=${phone}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset timer
                timeLeft = 300;
                timerInterval = setInterval(updateTimer, 1000);
                updateTimer();
                
                // Show success message
                alert('A new verification code has been sent to your phone.');
            } else {
                // Show error message
                alert(data.message || 'Failed to resend verification code. Please try again.');
                resendButton.classList.remove('disabled');
            }
            
            resendButton.textContent = 'Resend Code';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            resendButton.classList.remove('disabled');
            resendButton.textContent = 'Resend Code';
        });
    }
    
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
                
                // Check if OTP is complete
                const otp = document.getElementById('otp').value;
                if (otp.length !== 6) {
                    event.preventDefault();
                    alert('Please enter the complete 6-digit verification code.');
                    return;
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