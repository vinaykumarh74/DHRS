<?php
// Maintenance Mode View

// Set page title
$page_title = 'Maintenance';

ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <h1 class="display-5 fw-bold mb-3">We'll be back soon</h1>
            <p class="lead text-muted">Our system is currently undergoing scheduled maintenance. Thank you for your patience.</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include('views/layout.php');
?>


