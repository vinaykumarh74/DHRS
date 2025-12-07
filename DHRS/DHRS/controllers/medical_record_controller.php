<?php
/**
 * Medical Record Controller
 * 
 * Handles medical record operations for citizens and doctors
 */

class MedicalRecordController {
    
    /**
     * Display medical records for a citizen
     */
    public function my_records() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to view medical records.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $citizen_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get citizen's medical records
        $stmt = $db->prepare("
            SELECT mr.*, d.first_name, d.last_name, d.specialization
            FROM medical_records mr
            LEFT JOIN doctors d ON mr.doctor_id = d.id
            WHERE mr.citizen_id = ?
            ORDER BY mr.record_date DESC
        ");
        $stmt->bind_param('i', $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $medical_records = $result->fetch_all(MYSQLI_ASSOC);
        
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
        include('views/medical_record/my_records.php');
    }
    
    /**
     * View a specific medical record
     */
    public function view() {
        // Check if user is logged in
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to view medical records.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $record_id = $_GET['id'] ?? null;
        if (!$record_id) {
            set_flash_message('error', 'Medical record ID is required.');
            redirect('index.php?controller=medical_record&action=my_records');
        }
        
        $db = get_db_connection();
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'];
        
        // Get medical record with access control
        if ($user_role === 'citizen') {
            $stmt = $db->prepare("
                SELECT mr.*, d.first_name, d.last_name, d.specialization, d.license_number
                FROM medical_records mr
                LEFT JOIN doctors d ON mr.doctor_id = d.user_id
                WHERE mr.id = ? AND mr.citizen_id = ?
            ");
            $stmt->bind_param('ii', $record_id, $user_id);
        } elseif ($user_role === 'doctor') {
            $stmt = $db->prepare("
                SELECT mr.*, c.first_name, c.last_name, c.health_id, d.first_name as doctor_first_name, d.last_name as doctor_last_name
                FROM medical_records mr
                LEFT JOIN citizens c ON mr.citizen_id = c.user_id
                LEFT JOIN doctors d ON mr.doctor_id = d.user_id
                WHERE mr.id = ? AND mr.doctor_id = ?
            ");
            $stmt->bind_param('ii', $record_id, $user_id);
        } else {
            set_flash_message('error', 'You do not have permission to view this medical record.');
            redirect('index.php?controller=home&action=index');
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $medical_record = $result->fetch_assoc();
        
        if (!$medical_record) {
            set_flash_message('error', 'Medical record not found or you do not have permission to view it.');
            redirect('index.php?controller=medical_record&action=my_records');
        }
        
        // Include the view
        include('views/medical_record/view.php');
    }
    
    /**
     * Create a new medical record (for doctors)
     */
    public function create() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to create medical records.');
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
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $citizen_id = sanitize_input($_POST['citizen_id']);
            $record_type = sanitize_input($_POST['record_type']);
            $title = sanitize_input($_POST['title']);
            $description = sanitize_input($_POST['description']);
            $record_date = sanitize_input($_POST['record_date']);
            
            // Validate required fields
            if (empty($citizen_id) || empty($record_type) || empty($title) || empty($description)) {
                set_flash_message('error', 'Please fill in all required fields.');
            } else {
                // Get doctor ID from doctors table
                $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
                $stmt->bind_param('i', $doctor_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $doctor_record = $result->fetch_assoc();
                $doctor_table_id = $doctor_record['id'];
                
                // Insert medical record
                $stmt = $db->prepare("
                    INSERT INTO medical_records (citizen_id, doctor_id, record_type, title, description, record_date, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param('iissss', $citizen_id, $doctor_table_id, $record_type, $title, $description, $record_date);
                
                if ($stmt->execute()) {
                    set_flash_message('success', 'Medical record created successfully.');
                    redirect('index.php?controller=medical_record&action=my_records');
                } else {
                    set_flash_message('error', 'Failed to create medical record. Please try again.');
                }
            }
        }
        
        // Include the view
        include('views/medical_record/create.php');
    }
    
    /**
     * Update a medical record
     */
    public function update() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to update medical records.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $record_id = $_GET['id'] ?? null;
        if (!$record_id) {
            set_flash_message('error', 'Medical record ID is required.');
            redirect('index.php?controller=medical_record&action=my_records');
        }
        
        $doctor_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Check if record exists and belongs to this doctor
        $stmt = $db->prepare("
            SELECT * FROM medical_records 
            WHERE id = ? AND doctor_id = ?
        ");
        $stmt->bind_param('ii', $record_id, $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $medical_record = $result->fetch_assoc();
        
        if (!$medical_record) {
            set_flash_message('error', 'Medical record not found or you do not have permission to update it.');
            redirect('index.php?controller=medical_record&action=my_records');
        }
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $record_type = sanitize_input($_POST['record_type']);
            $title = sanitize_input($_POST['title']);
            $description = sanitize_input($_POST['description']);
            $diagnosis = sanitize_input($_POST['diagnosis']);
            $treatment = sanitize_input($_POST['treatment']);
            $medications = sanitize_input($_POST['medications']);
            $record_date = sanitize_input($_POST['record_date']);
            
            // Update medical record
            $stmt = $db->prepare("
                UPDATE medical_records 
                SET record_type = ?, title = ?, description = ?, diagnosis = ?, treatment = ?, medications = ?, record_date = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param('sssssssi', $record_type, $title, $description, $diagnosis, $treatment, $medications, $record_date, $record_id);
            
            if ($stmt->execute()) {
                set_flash_message('success', 'Medical record updated successfully.');
                redirect('index.php?controller=medical_record&action=view&id=' . $record_id);
            } else {
                set_flash_message('error', 'Failed to update medical record. Please try again.');
            }
        }
        
        // Include the view
        include('views/medical_record/edit.php');
    }
    
    /**
     * Delete a medical record
     */
    public function delete() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to delete medical records.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $record_id = $_GET['id'] ?? null;
        if (!$record_id) {
            set_flash_message('error', 'Medical record ID is required.');
            redirect('index.php?controller=medical_record&action=my_records');
        }
        
        $doctor_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Check if record exists and belongs to this doctor
        $stmt = $db->prepare("
            SELECT * FROM medical_records 
            WHERE id = ? AND doctor_id = ?
        ");
        $stmt->bind_param('ii', $record_id, $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $medical_record = $result->fetch_assoc();
        
        if (!$medical_record) {
            set_flash_message('error', 'Medical record not found or you do not have permission to delete it.');
            redirect('index.php?controller=medical_record&action=my_records');
        }
        
        // Delete medical record
        $stmt = $db->prepare("DELETE FROM medical_records WHERE id = ?");
        $stmt->bind_param('i', $record_id);
        
        if ($stmt->execute()) {
            set_flash_message('success', 'Medical record deleted successfully.');
        } else {
            set_flash_message('error', 'Failed to delete medical record. Please try again.');
        }
        
        redirect('index.php?controller=medical_record&action=my_records');
    }
    
    /**
     * Download a medical record
     */
    public function download() {
        // Check if user is logged in
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to download medical records.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $record_id = $_GET['id'] ?? null;
        if (!$record_id) {
            set_flash_message('error', 'Medical record ID is required.');
            redirect('index.php?controller=medical_record&action=my_records');
        }
        
        $db = get_db_connection();
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'];
        
        // Get medical record with access control
        if ($user_role === 'citizen') {
            $stmt = $db->prepare("
                SELECT mr.*, c.first_name, c.last_name, c.health_id, d.first_name as doctor_first_name, d.last_name as doctor_last_name
                FROM medical_records mr
                LEFT JOIN citizens c ON mr.citizen_id = c.user_id
                LEFT JOIN doctors d ON mr.doctor_id = d.user_id
                WHERE mr.id = ? AND mr.citizen_id = ?
            ");
            $stmt->bind_param('ii', $record_id, $user_id);
        } elseif ($user_role === 'doctor') {
            $stmt = $db->prepare("
                SELECT mr.*, c.first_name, c.last_name, c.health_id, d.first_name as doctor_first_name, d.last_name as doctor_last_name
                FROM medical_records mr
                LEFT JOIN citizens c ON mr.citizen_id = c.user_id
                LEFT JOIN doctors d ON mr.doctor_id = d.user_id
                WHERE mr.id = ? AND mr.doctor_id = ?
            ");
            $stmt->bind_param('ii', $record_id, $user_id);
        } else {
            set_flash_message('error', 'You do not have permission to download this medical record.');
            redirect('index.php?controller=home&action=index');
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $medical_record = $result->fetch_assoc();
        
        if (!$medical_record) {
            set_flash_message('error', 'Medical record not found or you do not have permission to download it.');
            redirect('index.php?controller=medical_record&action=my_records');
        }
        
        // Generate PDF content (simplified version)
        $content = "MEDICAL RECORD\n";
        $content .= "==============\n\n";
        $content .= "Patient: " . $medical_record['first_name'] . " " . $medical_record['last_name'] . "\n";
        $content .= "Health ID: " . $medical_record['health_id'] . "\n";
        $content .= "Doctor: Dr. " . $medical_record['doctor_first_name'] . " " . $medical_record['doctor_last_name'] . "\n";
        $content .= "Record Date: " . date('F d, Y', strtotime($medical_record['record_date'])) . "\n";
        $content .= "Record Type: " . ucfirst($medical_record['record_type']) . "\n\n";
        $content .= "Title: " . $medical_record['title'] . "\n\n";
        $content .= "Description:\n" . $medical_record['description'] . "\n\n";
        
        if (!empty($medical_record['diagnosis'])) {
            $content .= "Diagnosis:\n" . $medical_record['diagnosis'] . "\n\n";
        }
        
        if (!empty($medical_record['treatment'])) {
            $content .= "Treatment:\n" . $medical_record['treatment'] . "\n\n";
        }
        
        if (!empty($medical_record['medications'])) {
            $content .= "Medications:\n" . $medical_record['medications'] . "\n\n";
        }
        
        $content .= "Generated on: " . date('F d, Y H:i:s') . "\n";
        
        // Set headers for download
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="medical_record_' . $record_id . '.txt"');
        header('Content-Length: ' . strlen($content));
        
        echo $content;
        exit;
    }
    
    /**
     * Upload a medical record file
     */
    public function upload() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to upload medical records.');
            redirect('index.php?controller=auth&action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['medical_file'])) {
            $citizen_id = sanitize_input($_POST['citizen_id']);
            $record_type = sanitize_input($_POST['record_type']);
            $title = sanitize_input($_POST['title']);
            $description = sanitize_input($_POST['description']);
            
            // Validate file upload
            $file = $_FILES['medical_file'];
            $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_extension, $allowed_types)) {
                set_flash_message('error', 'Invalid file type. Only PDF, DOC, DOCX, JPG, JPEG, and PNG files are allowed.');
                redirect('index.php?controller=medical_record&action=create');
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
                set_flash_message('error', 'File size too large. Maximum size is 5MB.');
                redirect('index.php?controller=medical_record&action=create');
            }
            
            // Create uploads directory if it doesn't exist
            $upload_dir = 'uploads/medical_records/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $filename = uniqid() . '_' . $file['name'];
            $file_path = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $db = get_db_connection();
                $doctor_id = $_SESSION['user_id'];
                
                // Insert medical record with file
                $stmt = $db->prepare("
                    INSERT INTO medical_records (citizen_id, doctor_id, record_type, title, description, file_path, record_date, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->bind_param('iissss', $citizen_id, $doctor_id, $record_type, $title, $description, $file_path);
                
                if ($stmt->execute()) {
                    set_flash_message('success', 'Medical record uploaded successfully.');
                    redirect('index.php?controller=medical_record&action=my_records');
                } else {
                    set_flash_message('error', 'Failed to save medical record. Please try again.');
                }
            } else {
                set_flash_message('error', 'Failed to upload file. Please try again.');
            }
        }
        
        redirect('index.php?controller=medical_record&action=create');
    }
}
