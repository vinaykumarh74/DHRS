<?php
/**
 * Contact Page View
 * 
 * This view displays the contact form and contact information
 */

// Set page title
$page_title = 'Contact Us';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-envelope me-3"></i>
                    Contact Us
                </h1>
                <p class="lead text-muted">
                    Get in touch with us. We'd love to hear from you and answer any questions you may have.
                </p>
            </div>

            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-8 mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-paper-plane me-2"></i>
                                Send us a Message
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($message)): ?>
                                <div class="alert alert-<?php echo $message_type === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                                    <i class="fas fa-<?php echo $message_type === 'error' ? 'exclamation-triangle' : 'check-circle'; ?> me-2"></i>
                                    <?php echo htmlspecialchars($message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form action="index.php?controller=home&action=contact" method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   placeholder="Your Name" required 
                                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                                            <label for="name">Your Name *</label>
                                            <div class="invalid-feedback">Please provide your name.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   placeholder="Your Email" required 
                                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                            <label for="email">Your Email *</label>
                                            <div class="invalid-feedback">Please provide a valid email address.</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="subject" name="subject" 
                                               placeholder="Subject" required 
                                               value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                                        <label for="subject">Subject *</label>
                                        <div class="invalid-feedback">Please provide a subject.</div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="message" name="message" 
                                                  placeholder="Your Message" style="height: 150px;" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                                        <label for="message">Your Message *</label>
                                        <div class="invalid-feedback">Please provide your message.</div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Get in Touch
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <!-- Contact Details -->
                            <div class="mb-4">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Address</h6>
                                        <p class="text-muted mb-0">
                                            123 Health Street<br>
                                            Ballari, Karnataka 583104<br>
                                            India
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Phone</h6>
                                        <p class="text-muted mb-0">
                                            <a href="tel:+911234567890" class="text-decoration-none">
                                                +91 1234567890
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start mb-3">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Email</h6>
                                        <p class="text-muted mb-0">
                                            <a href="mailto:info@dhrs.com" class="text-decoration-none">
                                                info@dhrs.com
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-start">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Office Hours</h6>
                                        <p class="text-muted mb-0">
                                            Monday - Friday: 9:00 AM - 6:00 PM<br>
                                            Saturday: 9:00 AM - 2:00 PM<br>
                                            Sunday: Closed
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media -->
                            <div class="border-top pt-4">
                                <h6 class="mb-3">Follow Us</h6>
                                <div class="d-flex gap-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-info btn-sm">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger btn-sm">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card bg-light border-0">
                        <div class="card-body p-4">
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <i class="fas fa-headset text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6>24/7 Support</h6>
                                    <p class="text-muted mb-0">Our support team is available around the clock to help you.</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <i class="fas fa-shield-alt text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>Secure & Private</h6>
                                    <p class="text-muted mb-0">Your data is protected with enterprise-grade security.</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <i class="fas fa-rocket text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6>Fast Response</h6>
                                    <p class="text-muted mb-0">We typically respond to inquiries within 24 hours.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for contact page -->
<style>
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #007bff;
}

.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
}

.card {
    border-radius: 12px;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .lead {
        font-size: 1rem;
    }
}
</style>

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
