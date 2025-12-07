<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
    
    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-heartbeat me-2"></i>
                    <?php echo APP_NAME; ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] === 'home' && (!isset($_GET['action']) || $_GET['action'] === 'index')) ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-home me-1"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] === 'home' && isset($_GET['action']) && $_GET['action'] === 'services') ? 'active' : ''; ?>" href="index.php?controller=home&action=services">
                                <i class="fas fa-stethoscope me-1"></i> Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] === 'home' && isset($_GET['action']) && $_GET['action'] === 'about') ? 'active' : ''; ?>" href="index.php?controller=home&action=about">
                                <i class="fas fa-info-circle me-1"></i> About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] === 'home' && isset($_GET['action']) && $_GET['action'] === 'contact') ? 'active' : ''; ?>" href="index.php?controller=home&action=contact">
                                <i class="fas fa-envelope me-1"></i> Contact
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] === 'home' && isset($_GET['action']) && $_GET['action'] === 'faq') ? 'active' : ''; ?>" href="index.php?controller=home&action=faq">
                                <i class="fas fa-question-circle me-1"></i> FAQ
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if (is_logged_in()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <?php echo $_SESSION['username']; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <?php if ($_SESSION['role'] === 'citizen'): ?>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=citizen&action=dashboard">
                                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=citizen&action=profile">
                                                <i class="fas fa-user me-1"></i> Profile
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=citizen&action=health_profile">
                                                <i class="fas fa-file-medical me-1"></i> Health Profile
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=appointment&action=book">
                                                <i class="fas fa-calendar-check me-1"></i> Book Appointment
                                            </a>
                                        </li>
                                    <?php elseif ($_SESSION['role'] === 'doctor'): ?>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=doctor&action=dashboard">
                                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=doctor&action=profile">
                                                <i class="fas fa-user-md me-1"></i> Profile
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=doctor&action=my_schedule">
                                                <i class="fas fa-calendar-check me-1"></i> My Schedule
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=doctor&action=my_patients">
                                                <i class="fas fa-users me-1"></i> My Patients
                                            </a>
                                        </li>
                                    <?php elseif ($_SESSION['role'] === 'admin'): ?>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=admin&action=dashboard">
                                                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=admin&action=statistics">
                                                <i class="fas fa-chart-line me-1"></i> Statistics
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=admin&action=manage_users">
                                                <i class="fas fa-users-cog me-1"></i> User Management
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="index.php?controller=admin&action=settings">
                                                <i class="fas fa-cogs me-1"></i> Settings
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="index.php?controller=auth&action=logout">
                                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] === 'auth' && isset($_GET['action']) && $_GET['action'] === 'login') ? 'active' : ''; ?>" href="index.php?controller=auth&action=login">
                                    <i class="fas fa-sign-in-alt me-1"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] === 'auth' && isset($_GET['action']) && $_GET['action'] === 'register') ? 'active' : ''; ?>" href="index.php?controller=auth&action=register">
                                    <i class="fas fa-user-plus me-1"></i> Register
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <?php echo display_flash_message(); ?>
            
            <?php if (isset($content)): ?>
                <?php echo $content; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Digital Health Record System</h5>
                    <p>A comprehensive digital health record system for citizens, doctors, and administrators.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="index.php?controller=home&action=services" class="text-white">Services</a></li>
                        <li><a href="index.php?controller=home&action=about" class="text-white">About</a></li>
                        <li><a href="index.php?controller=home&action=contact" class="text-white">Contact</a></li>
                        <li><a href="index.php?controller=home&action=faq" class="text-white">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address>
                        <i class="fas fa-map-marker-alt me-2"></i> 123 Health Street, Ballari<br>
                        <i class="fas fa-phone me-2"></i> +91 1234567890<br>
                        <i class="fas fa-envelope me-2"></i> <a href="mailto:info@dhrs.com" class="text-white">info@dhrs.com</a>
                    </address>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Digital Health Record System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Designed and Developed for Ballari</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    
    <?php if (isset($extra_js)): ?>
        <?php echo $extra_js; ?>
    <?php endif; ?>
</body>
</html>