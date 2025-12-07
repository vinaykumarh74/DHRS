<?php
/**
 * Services Page View
 */

// Set page title
$page_title = 'Our Services';

// Start output buffering to inject into layout
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8 text-center">
            <h1 class="display-4 fw-bold mb-3">Our Services</h1>
            <p class="lead text-muted">Discover the comprehensive healthcare services offered by our Digital Health Record System</p>
        </div>
    </div>
    
    <div class="row g-4">
        <?php foreach ($services as $service): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    <div class="card-body p-4 text-center">
                        <div class="icon-box mb-3">
                            <i class="fas <?php echo $service['icon']; ?> fa-3x text-primary"></i>
                        </div>
                        <h3 class="card-title h4 mb-3"><?php echo $service['title']; ?></h3>
                        <p class="card-text text-muted"><?php echo $service['description']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="row mt-5 pt-4">
        <div class="col-12 text-center">
            <h2 class="h3 mb-4">Need more information?</h2>
            <p class="mb-4">Contact our support team or check our FAQ section for more details about our services.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="index.php?controller=home&action=contact" class="btn btn-primary">Contact Us</a>
                <a href="index.php?controller=home&action=faq" class="btn btn-outline-secondary">View FAQ</a>
            </div>
        </div>
    </div>
</div>

<?php
// Capture and include via main layout
$content = ob_get_clean();
include('views/layout.php');
?>