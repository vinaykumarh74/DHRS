<?php
/**
 * FAQ Page View
 * 
 * This view displays frequently asked questions about the DHRS system
 */

// Set page title
$page_title = 'Frequently Asked Questions';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="fas fa-question-circle me-3"></i>
                    Frequently Asked Questions
                </h1>
                <p class="lead text-muted">
                    Find answers to common questions about the Digital Health Record System
                </p>
            </div>

            <!-- FAQ Content -->
            <div class="accordion" id="faqAccordion">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                            <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?php echo $index; ?>" 
                                    aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                    aria-controls="collapse<?php echo $index; ?>">
                                <i class="fas fa-question-circle text-primary me-3"></i>
                                <?php echo htmlspecialchars($faq['question']); ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $index; ?>" 
                             class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                             aria-labelledby="heading<?php echo $index; ?>" 
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-lightbulb text-warning me-3 mt-1"></i>
                                    <div>
                                        <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Additional Help Section -->
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-headset text-primary mb-3" style="font-size: 3rem;"></i>
                            <h5 class="card-title">Need More Help?</h5>
                            <p class="card-text text-muted">
                                Can't find the answer you're looking for? Our support team is here to help.
                            </p>
                            <a href="index.php?controller=home&action=contact" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-book text-success mb-3" style="font-size: 3rem;"></i>
                            <h5 class="card-title">User Guide</h5>
                            <p class="card-text text-muted">
                                Learn how to make the most of your Digital Health Record System.
                            </p>
                            <a href="#" class="btn btn-success" onclick="alert('User guide coming soon!')">
                                <i class="fas fa-download me-2"></i>Download Guide
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card bg-light border-0">
                        <div class="card-body p-4">
                            <h5 class="card-title text-center mb-4">
                                <i class="fas fa-link me-2"></i>Quick Links
                            </h5>
                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <a href="index.php?controller=auth&action=register" class="text-decoration-none">
                                        <div class="p-3 border rounded hover-shadow">
                                            <i class="fas fa-user-plus text-primary mb-2" style="font-size: 2rem;"></i>
                                            <h6>Register</h6>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="index.php?controller=auth&action=login" class="text-decoration-none">
                                        <div class="p-3 border rounded hover-shadow">
                                            <i class="fas fa-sign-in-alt text-success mb-2" style="font-size: 2rem;"></i>
                                            <h6>Login</h6>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="index.php?controller=home&action=services" class="text-decoration-none">
                                        <div class="p-3 border rounded hover-shadow">
                                            <i class="fas fa-stethoscope text-info mb-2" style="font-size: 2rem;"></i>
                                            <h6>Services</h6>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="index.php?controller=home&action=about" class="text-decoration-none">
                                        <div class="p-3 border rounded hover-shadow">
                                            <i class="fas fa-info-circle text-warning mb-2" style="font-size: 2rem;"></i>
                                            <h6>About Us</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for FAQ page -->
<style>
.accordion-button {
    font-weight: 600;
    color: #2c3e50;
    background-color: #f8f9fa;
    border: none;
    box-shadow: none;
}

.accordion-button:not(.collapsed) {
    background-color: #e3f2fd;
    color: #1976d2;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.accordion-body {
    background-color: #ffffff;
    border-top: 1px solid #e9ecef;
}

.hover-shadow:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

.card {
    border-radius: 12px;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.accordion-item {
    border-radius: 8px !important;
    overflow: hidden;
}

.accordion-item:first-child {
    border-top-left-radius: 8px !important;
    border-top-right-radius: 8px !important;
}

.accordion-item:last-child {
    border-bottom-left-radius: 8px !important;
    border-bottom-right-radius: 8px !important;
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

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
include('views/layout.php');
?>
