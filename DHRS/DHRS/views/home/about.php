<?php
/**
 * About Page View
 * 
 * This view displays information about the Digital Health Record System
 */

// Set page title
$page_title = 'About Us';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-5">
    <!-- Hero Section -->
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <h1 class="display-4 fw-bold text-primary mb-4">
                About Digital Health Record System
            </h1>
            <p class="lead text-muted mb-4">
                A comprehensive digital health record system designed specifically for the citizens, 
                doctors, and administrators of Ballari. We're revolutionizing healthcare management 
                through technology.
            </p>
            <div class="d-flex gap-3">
                <a href="index.php?controller=auth&action=register" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Get Started
                </a>
                <a href="index.php?controller=home&action=contact" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-envelope me-2"></i>Contact Us
                </a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="text-center">
                <i class="fas fa-heartbeat text-primary" style="font-size: 8rem; opacity: 0.1;"></i>
            </div>
        </div>
    </div>

    <!-- Mission & Vision -->
    <div class="row mb-5">
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-bullseye text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="card-title text-center mb-3">Our Mission</h3>
                    <p class="card-text text-muted text-center">
                        To provide a secure, efficient, and user-friendly digital platform that 
                        streamlines healthcare management, improves patient care, and enhances 
                        the overall healthcare experience for all stakeholders in Ballari.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-eye text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="card-title text-center mb-3">Our Vision</h3>
                    <p class="card-text text-muted text-center">
                        To become the leading digital health platform in Karnataka, setting 
                        the standard for healthcare technology and ensuring that quality 
                        healthcare is accessible to every citizen.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-5">Key Features</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-users" style="font-size: 2rem;"></i>
                </div>
                <h5>Multi-User Platform</h5>
                <p class="text-muted">Designed for citizens, doctors, and administrators with role-based access control.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="text-center">
                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-shield-alt" style="font-size: 2rem;"></i>
                </div>
                <h5>Secure & Private</h5>
                <p class="text-muted">Advanced security measures to protect your sensitive health information.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="text-center">
                <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-mobile-alt" style="font-size: 2rem;"></i>
                </div>
                <h5>Mobile Friendly</h5>
                <p class="text-muted">Access your health records and book appointments from any device, anywhere.</p>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-primary text-white border-0">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Our Impact</h2>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <h3 class="display-4 fw-bold">1000+</h3>
                            <p class="mb-0">Citizens Served</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h3 class="display-4 fw-bold">50+</h3>
                            <p class="mb-0">Verified Doctors</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h3 class="display-4 fw-bold">5000+</h3>
                            <p class="mb-0">Appointments Booked</p>
                        </div>
                        <div class="col-md-3 mb-3">
                            <h3 class="display-4 fw-bold">99.9%</h3>
                            <p class="mb-0">Uptime</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-5">Our Team</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="fas fa-user-md text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title">Healthcare Professionals</h5>
                    <p class="card-text text-muted">Experienced doctors and medical professionals ensuring quality care.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="fas fa-code text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title">Technology Experts</h5>
                    <p class="card-text text-muted">Skilled developers and IT professionals building robust solutions.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                        <i class="fas fa-headset text-info" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title">Support Team</h5>
                    <p class="card-text text-muted">Dedicated support staff ready to help you with any questions.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-light border-0 text-center">
                <div class="card-body p-5">
                    <h3 class="mb-3">Ready to Get Started?</h3>
                    <p class="text-muted mb-4">Join thousands of citizens who are already using our digital health platform.</p>
                    <a href="index.php?controller=auth&action=register" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-user-plus me-2"></i>Register Now
                    </a>
                    <a href="index.php?controller=home&action=contact" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Get in Touch
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Get the contents of the output buffer
$content = ob_get_clean();

// Include the layout template
include('views/layout.php');
?>
