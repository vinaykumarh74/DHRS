<?php
// Page Not Found View

// Set page title
$page_title = 'Page Not Found';

// Start output buffering
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <h1 class="display-4 fw-bold mb-3">404 - Page Not Found</h1>
            <p class="lead text-muted mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Go to Homepage
            </a>
        </div>
    </div>
    <div class="text-center mt-4">
        <p class="text-muted">If you believe this is an error, please contact support.</p>
    </div>
    </div>
<?php
// Inject into layout
$content = ob_get_clean();
include('views/layout.php');
?>


