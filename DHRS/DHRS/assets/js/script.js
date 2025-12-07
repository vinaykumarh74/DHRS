/**
 * Main JavaScript file for the Digital Health Record System
 */

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('passwordStrength');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordMatch = document.getElementById('passwordMatch');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]+/)) strength += 1;
            
            switch (strength) {
                case 0:
                case 1:
                    passwordStrength.innerHTML = '<div class="progress"><div class="progress-bar bg-danger" style="width: 20%"></div></div><small class="text-danger">Very Weak</small>';
                    break;
                case 2:
                    passwordStrength.innerHTML = '<div class="progress"><div class="progress-bar bg-warning" style="width: 40%"></div></div><small class="text-warning">Weak</small>';
                    break;
                case 3:
                    passwordStrength.innerHTML = '<div class="progress"><div class="progress-bar bg-info" style="width: 60%"></div></div><small class="text-info">Medium</small>';
                    break;
                case 4:
                    passwordStrength.innerHTML = '<div class="progress"><div class="progress-bar bg-primary" style="width: 80%"></div></div><small class="text-primary">Strong</small>';
                    break;
                case 5:
                    passwordStrength.innerHTML = '<div class="progress"><div class="progress-bar bg-success" style="width: 100%"></div></div><small class="text-success">Very Strong</small>';
                    break;
            }
        });
    }
    
    if (confirmPassword && passwordInput && passwordMatch) {
        confirmPassword.addEventListener('input', function() {
            if (this.value === passwordInput.value) {
                passwordMatch.innerHTML = '<small class="text-success">Passwords match</small>';
            } else {
                passwordMatch.innerHTML = '<small class="text-danger">Passwords do not match</small>';
            }
        });
    }
});