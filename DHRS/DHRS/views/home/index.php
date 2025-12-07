<?php
/**
 * Home Page View
 */

// Set page title
$page_title = 'Home';

// Start output buffering
ob_start();
?>

<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Digital Health Record System</h1>
                <p class="lead mb-4">A comprehensive digital health record system for citizens, doctors, and administrators of Ballari. Manage your health records, appointments, prescriptions, and more in one secure platform.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <?php if (!is_logged_in()): ?>
                        <a href="index.php?controller=auth&action=register" class="btn btn-primary btn-lg px-4 me-md-2">Get Started</a>
                        <a href="index.php?controller=auth&action=login" class="btn btn-outline-secondary btn-lg px-4">Login</a>
                    <?php else: ?>
                        <?php if ($_SESSION['role'] === 'citizen'): ?>
                            <a href="index.php?controller=citizen&action=dashboard" class="btn btn-primary btn-lg px-4 me-md-2">Dashboard</a>
                            <a href="index.php?controller=appointment&action=book" class="btn btn-outline-secondary btn-lg px-4">Book Appointment</a>
                        <?php elseif ($_SESSION['role'] === 'doctor'): ?>
                            <a href="index.php?controller=doctor&action=dashboard" class="btn btn-primary btn-lg px-4 me-md-2">Dashboard</a>
                            <a href="index.php?controller=doctor&action=my_schedule" class="btn btn-outline-secondary btn-lg px-4">My Schedule</a>
                        <?php elseif ($_SESSION['role'] === 'admin'): ?>
                            <a href="index.php?controller=admin&action=dashboard" class="btn btn-primary btn-lg px-4 me-md-2">Dashboard</a>
                            <a href="index.php?controller=admin&action=manage_users" class="btn btn-outline-secondary btn-lg px-4">Manage Users</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <img src="assets/images/hero-image.svg" alt="Digital Health" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Key Features</h2>
            <p class="lead">Discover the powerful features of our Digital Health Record System</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                            <i class="fas fa-file-medical fa-2x"></i>
                        </div>
                        <h5 class="card-title">Electronic Health Records</h5>
                        <p class="card-text">Securely store and access your complete health records anytime, anywhere.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h5 class="card-title">Online Appointments</h5>
                        <p class="card-text">Book and manage appointments with doctors online without any hassle.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                            <i class="fas fa-prescription fa-2x"></i>
                        </div>
                        <h5 class="card-title">Digital Prescriptions</h5>
                        <p class="card-text">Receive and access your prescriptions digitally, eliminating paper-based records.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                            <i class="fas fa-flask fa-2x"></i>
                        </div>
                        <h5 class="card-title">Lab Reports</h5>
                        <p class="card-text">View and download your lab reports directly from the platform.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                            <i class="fas fa-video fa-2x"></i>
                        </div>
                        <h5 class="card-title">Telemedicine</h5>
                        <p class="card-text">Consult with doctors remotely through secure video consultations.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3">
                            <i class="fas fa-syringe fa-2x"></i>
                        </div>
                        <h5 class="card-title">Vaccination Tracking</h5>
                        <p class="card-text">Keep track of your vaccination history and get timely reminders.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">How It Works</h2>
            <p class="lead">Simple steps to get started with our Digital Health Record System</p>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-circle bg-primary text-white mb-3">1</div>
                    <h5>Register</h5>
                    <p>Create your account with basic information and verify through OTP.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-circle bg-primary text-white mb-3">2</div>
                    <h5>Complete Profile</h5>
                    <p>Fill in your health profile with relevant medical information.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-circle bg-primary text-white mb-3">3</div>
                    <h5>Book Appointments</h5>
                    <p>Search for doctors and book appointments based on availability.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <div class="step-circle bg-primary text-white mb-3">4</div>
                    <h5>Manage Health</h5>
                    <p>Access your health records, prescriptions, and lab reports anytime.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">What Our Users Say</h2>
            <p class="lead">Hear from citizens and doctors who use our Digital Health Record System</p>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="assets/images/testimonial-1.svg" alt="User" class="rounded-circle" width="60">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Rajesh Kumar</h5>
                                <p class="text-muted mb-0">Citizen</p>
                            </div>
                        </div>
                        <p class="card-text">"The Digital Health Record System has made managing my family's health records so much easier. I can book appointments, view prescriptions, and track vaccinations all in one place."</p>
                        <div class="text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="assets/images/testimonial-2.svg" alt="User" class="rounded-circle" width="60">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Dr. Priya Sharma</h5>
                                <p class="text-muted mb-0">Cardiologist</p>
                            </div>
                        </div>
                        <p class="card-text">"As a doctor, this system has streamlined my practice. I can easily access patient records, manage appointments, and issue digital prescriptions, saving time and reducing errors."</p>
                        <div class="text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="assets/images/testimonial-3.svg" alt="User" class="rounded-circle" width="60">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Lakshmi Devi</h5>
                                <p class="text-muted mb-0">Senior Citizen</p>
                            </div>
                        </div>
                        <p class="card-text">"The telemedicine feature has been a blessing for me. At my age, traveling to the hospital for regular check-ups was difficult. Now I can consult with my doctor from home."</p>
                        <div class="text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <h2 class="fw-bold mb-3">Ready to take control of your health records?</h2>
                <p class="lead mb-0">Join thousands of citizens and healthcare providers who are already benefiting from our Digital Health Record System.</p>
            </div>
            <div class="col-lg-3 text-lg-end mt-4 mt-lg-0">
                <?php if (!is_logged_in()): ?>
                    <a href="index.php?controller=auth&action=register" class="btn btn-light btn-lg px-4">Register Now</a>
                <?php else: ?>
                    <a href="index.php?controller=home&action=services" class="btn btn-light btn-lg px-4">Explore Services</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
include('views/layout.php');
?>