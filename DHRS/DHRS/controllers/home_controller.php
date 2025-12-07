<?php
/**
 * Home Controller
 * 
 * This controller handles the main pages of the application
 * such as home page, about, contact, services, and FAQ.
 */

class HomeController {
    /**
     * Display the home page
     */
    public function index() {
        // Check if system is in maintenance mode
        if (is_maintenance_mode() && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            include('views/maintenance.php');
            exit();
        }
        
        // Include the home page view
        include('views/home/index.php');
    }
    
    /**
     * Display the about page
     */
    public function about() {
        // Include the about page view
        include('views/home/about.php');
    }
    
    /**
     * Display the contact page
     */
    public function contact() {
        $message = '';
        $message_type = '';
        
        // Handle contact form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
            $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
            $subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : '';
            $message_text = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
            
            // Validate form data
            if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
                $message = 'All fields are required';
                $message_type = 'error';
            } elseif (!is_valid_email($email)) {
                $message = 'Invalid email address';
                $message_type = 'error';
            } else {
                // Send email to admin
                $admin_email = get_setting('admin_email', 'admin@dhrs.com');
                $email_subject = "Contact Form: $subject";
                $email_body = "<h2>Contact Form Submission</h2>"
                            . "<p><strong>Name:</strong> $name</p>"
                            . "<p><strong>Email:</strong> $email</p>"
                            . "<p><strong>Subject:</strong> $subject</p>"
                            . "<p><strong>Message:</strong></p>"
                            . "<p>$message_text</p>";
                
                if (send_email($admin_email, $email_subject, $email_body)) {
                    $message = 'Your message has been sent. We will get back to you soon.';
                    $message_type = 'success';
                    
                    // Log the contact form submission
                    log_audit('contact_form', 'system', 0, "Contact form submitted by $name ($email)");
                    
                    // Clear form data
                    $name = $email = $subject = $message_text = '';
                } else {
                    $message = 'Failed to send message. Please try again later.';
                    $message_type = 'error';
                }
            }
        }
        
        // Include the contact page view
        include('views/home/contact.php');
    }
    
    /**
     * Display the services page
     */
    public function services() {
        // Get services from database or configuration
        $services = [
            [
                'title' => 'Electronic Health Records',
                'description' => 'Secure and accessible electronic health records for citizens.',
                'icon' => 'fa-file-medical'
            ],
            [
                'title' => 'Online Appointments',
                'description' => 'Book and manage appointments with doctors online.',
                'icon' => 'fa-calendar-check'
            ],
            [
                'title' => 'Telemedicine',
                'description' => 'Virtual consultations with doctors from the comfort of your home.',
                'icon' => 'fa-video'
            ],
            [
                'title' => 'Prescription Management',
                'description' => 'Digital prescriptions and medication tracking.',
                'icon' => 'fa-prescription'
            ],
            [
                'title' => 'Lab Reports',
                'description' => 'Access and manage your lab reports online.',
                'icon' => 'fa-flask'
            ],
            [
                'title' => 'Vaccination Tracking',
                'description' => 'Track and manage your vaccination history.',
                'icon' => 'fa-syringe'
            ]
        ];
        
        // Include the services page view
        include('views/home/services.php');
    }
    
    /**
     * Display the FAQ page
     */
    public function faq() {
        // Get FAQs from database or configuration
        $faqs = [
            [
                'question' => 'How do I register for the Digital Health Record System?',
                'answer' => 'You can register by clicking on the "Register" button on the homepage and filling out the registration form. You will need to provide your basic information and verify your account through OTP sent to your phone and email.'
            ],
            [
                'question' => 'Is my health information secure?',
                'answer' => 'Yes, we take security very seriously. Your health information is encrypted and stored securely. Only authorized healthcare providers and you can access your health records.'
            ],
            [
                'question' => 'How do I book an appointment with a doctor?',
                'answer' => 'After logging in, go to the "Appointments" section in your dashboard. Click on "Book Appointment", select a doctor, choose an available date and time, and confirm your appointment.'
            ],
            [
                'question' => 'Can I access my health records from my mobile phone?',
                'answer' => 'Yes, the Digital Health Record System is responsive and can be accessed from any device with a web browser, including smartphones and tablets.'
            ],
            [
                'question' => 'How do I view my lab reports?',
                'answer' => 'After logging in, go to the "Lab Reports" section in your dashboard. You can view and download all your lab reports from there.'
            ],
            [
                'question' => 'What should I do if I forget my password?',
                'answer' => 'Click on the "Forgot Password" link on the login page. Enter your registered email address, and we will send you instructions to reset your password.'
            ],
            [
                'question' => 'How can I update my personal information?',
                'answer' => 'After logging in, go to the "Profile" section in your dashboard. Click on "Edit Profile" to update your personal information.'
            ],
            [
                'question' => 'Is there a mobile app available?',
                'answer' => 'Currently, we offer a responsive web application that works well on mobile devices. A dedicated mobile app is under development and will be available soon.'
            ],
            [
                'question' => 'How do I view my prescription history?',
                'answer' => 'After logging in, go to the "Prescriptions" section in your dashboard. You can view and download all your prescriptions from there.'
            ],
            [
                'question' => 'Can I share my health records with a doctor outside the system?',
                'answer' => 'Yes, you can download your health records as PDF files and share them with any healthcare provider. We also plan to add a feature to securely share your records directly with external healthcare providers in the future.'
            ]
        ];
        
        // Include the FAQ page view
        include('views/home/faq.php');
    }
}