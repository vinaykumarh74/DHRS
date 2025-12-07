<?php
/**
 * Lab Report Controller
 * 
 * Handles lab report operations for citizens and doctors
 */

class LabReportController {
    
    /**
     * Display lab reports for a citizen
     */
    public function my_reports() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to view lab reports.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $citizen_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get citizen's lab reports
        $stmt = $db->prepare("
            SELECT lr.*, lt.name as test_name, lt.description as test_description, 
                   d.first_name, d.last_name, d.specialization
            FROM lab_reports lr
            LEFT JOIN lab_tests lt ON lr.test_id = lt.id
            LEFT JOIN doctors d ON lr.doctor_id = d.id
            WHERE lr.citizen_id = ?
            ORDER BY lr.report_date DESC
        ");
        $stmt->bind_param('i', $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $lab_reports = $result->fetch_all(MYSQLI_ASSOC);
        
        // Get citizen info for display
        $stmt = $db->prepare("
            SELECT c.*, u.email, u.phone
            FROM citizens c
            JOIN users u ON c.user_id = u.id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param('i', $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $citizen = $result->fetch_assoc();
        
        // Include the view
        include('views/lab_report/my_reports.php');
    }
    
    /**
     * View a specific lab report
     */
    public function view() {
        // Check if user is logged in
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to view lab reports.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $report_id = $_GET['id'] ?? null;
        if (!$report_id) {
            set_flash_message('error', 'Lab report ID is required.');
            redirect('index.php?controller=lab_report&action=my_reports');
        }
        
        $db = get_db_connection();
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'];
        
        // Get lab report with access control
        if ($user_role === 'citizen') {
            $stmt = $db->prepare("
                SELECT lr.*, lt.name as test_name, lt.description as test_description,
                       d.first_name, d.last_name, d.specialization, d.license_number
                FROM lab_reports lr
                LEFT JOIN lab_tests lt ON lr.test_id = lt.id
                LEFT JOIN doctors d ON lr.doctor_id = d.id
                WHERE lr.id = ? AND lr.citizen_id = ?
            ");
            $stmt->bind_param('ii', $report_id, $user_id);
        } elseif ($user_role === 'doctor') {
            $stmt = $db->prepare("
                SELECT lr.*, lt.name as test_name, lt.description as test_description,
                       c.first_name, c.last_name, c.health_id, d.first_name as doctor_first_name, d.last_name as doctor_last_name
                FROM lab_reports lr
                LEFT JOIN lab_tests lt ON lr.test_id = lt.id
                LEFT JOIN citizens c ON lr.citizen_id = c.user_id
                LEFT JOIN doctors d ON lr.doctor_id = d.id
                WHERE lr.id = ? AND lr.doctor_id = ?
            ");
            $stmt->bind_param('ii', $report_id, $user_id);
        } else {
            set_flash_message('error', 'You do not have permission to view this lab report.');
            redirect('index.php?controller=home&action=index');
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $lab_report = $result->fetch_assoc();
        
        if (!$lab_report) {
            set_flash_message('error', 'Lab report not found or you do not have permission to view it.');
            redirect('index.php?controller=lab_report&action=my_reports');
        }
        
        // Include the view
        include('views/lab_report/view.php');
    }
    
    /**
     * Create a new lab report (for doctors)
     */
    public function create() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to create lab reports.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $doctor_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get doctor info
        $stmt = $db->prepare("
            SELECT d.*, u.email, u.phone
            FROM doctors d
            JOIN users u ON d.user_id = u.id
            WHERE d.user_id = ?
        ");
        $stmt->bind_param('i', $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        
        // Get patients for dropdown
        $stmt = $db->prepare("
            SELECT c.*, u.email, u.phone
            FROM citizens c
            JOIN users u ON c.user_id = u.id
            WHERE u.status = 'active'
            ORDER BY c.first_name, c.last_name
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $patients = $result->fetch_all(MYSQLI_ASSOC);
        
        // Get available lab tests
        $stmt = $db->prepare("
            SELECT * FROM lab_tests
            ORDER BY name
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $lab_tests = $result->fetch_all(MYSQLI_ASSOC);
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $citizen_id = sanitize_input($_POST['citizen_id']);
            $test_id = sanitize_input($_POST['test_id']);
            $report_date = sanitize_input($_POST['report_date']);
            $lab_name = sanitize_input($_POST['lab_name']);
            $technician_name = sanitize_input($_POST['technician_name']);
            $notes = sanitize_input($_POST['notes']);
            
            // Validate required fields
            if (empty($citizen_id) || empty($test_id) || empty($report_date) || empty($lab_name)) {
                set_flash_message('error', 'Please fill in all required fields.');
            } else {
                // Get doctor ID from doctors table
                $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
                $stmt->bind_param('i', $doctor_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $doctor_record = $result->fetch_assoc();
                $doctor_table_id = $doctor_record['id'];
                
                // Insert lab report
                $stmt = $db->prepare("
                    INSERT INTO lab_reports (citizen_id, doctor_id, test_id, report_date, lab_name, technician_name, notes, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param('iiissss', $citizen_id, $doctor_table_id, $test_id, $report_date, $lab_name, $technician_name, $notes);
                
                if ($stmt->execute()) {
                    set_flash_message('success', 'Lab report created successfully.');
                    redirect('index.php?controller=lab_report&action=my_reports');
                } else {
                    set_flash_message('error', 'Failed to create lab report. Please try again.');
                }
            }
        }
        
        // Include the view
        include('views/lab_report/create.php');
    }
    
    /**
     * Download a lab report
     */
    public function download() {
        // Check if user is logged in
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to download lab reports.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $report_id = $_GET['id'] ?? null;
        if (!$report_id) {
            set_flash_message('error', 'Lab report ID is required.');
            redirect('index.php?controller=lab_report&action=my_reports');
        }
        
        $db = get_db_connection();
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'];
        
        // Get lab report with access control
        if ($user_role === 'citizen') {
            $stmt = $db->prepare("
                SELECT lr.*, lt.name as test_name, c.first_name, c.last_name, c.health_id, d.first_name as doctor_first_name, d.last_name as doctor_last_name
                FROM lab_reports lr
                LEFT JOIN lab_tests lt ON lr.test_id = lt.id
                LEFT JOIN citizens c ON lr.citizen_id = c.user_id
                LEFT JOIN doctors d ON lr.doctor_id = d.id
                WHERE lr.id = ? AND lr.citizen_id = ?
            ");
            $stmt->bind_param('ii', $report_id, $user_id);
        } elseif ($user_role === 'doctor') {
            $stmt = $db->prepare("
                SELECT lr.*, lt.name as test_name, c.first_name, c.last_name, c.health_id, d.first_name as doctor_first_name, d.last_name as doctor_last_name
                FROM lab_reports lr
                LEFT JOIN lab_tests lt ON lr.test_id = lt.id
                LEFT JOIN citizens c ON lr.citizen_id = c.user_id
                LEFT JOIN doctors d ON lr.doctor_id = d.id
                WHERE lr.id = ? AND lr.doctor_id = ?
            ");
            $stmt->bind_param('ii', $report_id, $user_id);
        } else {
            set_flash_message('error', 'You do not have permission to download this lab report.');
            redirect('index.php?controller=home&action=index');
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $lab_report = $result->fetch_assoc();
        
        if (!$lab_report) {
            set_flash_message('error', 'Lab report not found or you do not have permission to download it.');
            redirect('index.php?controller=lab_report&action=my_reports');
        }
        
        // Generate PDF content (simplified version)
        $content = "LAB REPORT\n";
        $content .= "==========\n\n";
        $content .= "Patient: " . $lab_report['first_name'] . " " . $lab_report['last_name'] . "\n";
        $content .= "Health ID: " . $lab_report['health_id'] . "\n";
        $content .= "Doctor: Dr. " . $lab_report['doctor_first_name'] . " " . $lab_report['doctor_last_name'] . "\n";
        $content .= "Report Date: " . date('F d, Y', strtotime($lab_report['report_date'])) . "\n";
        $content .= "Test Name: " . $lab_report['test_name'] . "\n";
        $content .= "Lab Name: " . $lab_report['lab_name'] . "\n";
        $content .= "Technician: " . $lab_report['technician_name'] . "\n\n";
        
        if (!empty($lab_report['notes'])) {
            $content .= "Notes:\n" . $lab_report['notes'] . "\n\n";
        }
        
        $content .= "Generated on: " . date('F d, Y H:i:s') . "\n";
        
        // Set headers for download
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="lab_report_' . $report_id . '.txt"');
        header('Content-Length: ' . strlen($content));
        
        echo $content;
        exit;
    }
}
