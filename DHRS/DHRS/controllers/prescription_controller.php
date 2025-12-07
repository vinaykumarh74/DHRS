<?php
/**
 * Prescription Controller
 * 
 * This controller handles all prescription-related functionality including
 * creating, viewing, downloading, and managing prescriptions.
 */

class PrescriptionController {
    /**
     * Create a new prescription
     */
    public function create() {
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'Only doctors can create prescriptions.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $appointment_id = isset($_GET['appointment_id']) ? (int)$_GET['appointment_id'] : 0;
        
        if ($appointment_id === 0) {
            set_flash_message('error', 'Invalid appointment ID.');
            redirect('index.php?controller=doctor&action=dashboard');
        }
        
        $db = get_db_connection();
        
        // Get appointment details
        $stmt = $db->prepare("SELECT a.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                            c.health_id, c.gender, c.date_of_birth,
                            CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
                            d.specialization, d.license_number
                            FROM appointments a 
                            JOIN citizens c ON a.citizen_id = c.id 
                            JOIN doctors d ON a.doctor_id = d.id 
                            WHERE a.id = ? AND d.user_id = ? AND a.status = 'completed'");
        $stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Appointment not found or cannot create prescription.');
            redirect('index.php?controller=doctor&action=dashboard');
        }
        
        $appointment = $result->fetch_assoc();
        
        // Check if prescription already exists
        $stmt = $db->prepare("SELECT id FROM prescriptions WHERE appointment_id = ?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            set_flash_message('error', 'Prescription already exists for this appointment.');
            redirect('index.php?controller=doctor&action=dashboard');
        }
        
        $stmt->close();
        $db->close();
        
        // Include the create prescription view
        include('views/prescription/create.php');
    }
    
    /**
     * Process prescription creation
     */
    public function process_create() {
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'Only doctors can create prescriptions.');
            redirect('index.php?controller=auth&action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=prescription&action=create');
        }
        
        $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        $diagnosis = isset($_POST['diagnosis']) ? sanitize_input($_POST['diagnosis']) : '';
        $medications = isset($_POST['medications']) ? $_POST['medications'] : [];
        $instructions = isset($_POST['instructions']) ? sanitize_input($_POST['instructions']) : '';
        $follow_up_date = isset($_POST['follow_up_date']) ? sanitize_input($_POST['follow_up_date']) : '';
        $notes = isset($_POST['notes']) ? sanitize_input($_POST['notes']) : '';
        
        // Validate inputs
        $errors = [];
        
        if (empty($appointment_id)) {
            $errors[] = 'Invalid appointment ID.';
        }
        
        if (empty($diagnosis)) {
            $errors[] = 'Diagnosis is required.';
        }
        
        if (empty($medications) || !is_array($medications)) {
            $errors[] = 'At least one medication is required.';
        }
        
        if (empty($instructions)) {
            $errors[] = 'Instructions are required.';
        }
        
        if (empty($errors)) {
            $db = get_db_connection();
            
            // Start transaction
            $db->begin_transaction();
            
            try {
                // Get appointment details
                $stmt = $db->prepare("SELECT a.*, d.id as doctor_id FROM appointments a 
                                    JOIN doctors d ON a.doctor_id = d.id 
                                    WHERE a.id = ? AND d.user_id = ?");
                $stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception('Appointment not found or access denied.');
                }
                
                $appointment = $result->fetch_assoc();
                
                // Insert prescription
                $stmt = $db->prepare("INSERT INTO prescriptions 
                                    (appointment_id, citizen_id, doctor_id, diagnosis, 
                                    instructions, follow_up_date, notes, created_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("iiissss", $appointment_id, $appointment['citizen_id'], 
                                $appointment['doctor_id'], $diagnosis, $instructions, 
                                $follow_up_date, $notes);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to create prescription.');
                }
                
                $prescription_id = $stmt->insert_id;
                
                // Insert medications
                foreach ($medications as $medication) {
                    if (!empty($medication['name']) && !empty($medication['dosage'])) {
                        $stmt = $db->prepare("INSERT INTO prescription_medications 
                                            (prescription_id, medication_name, dosage, frequency, 
                                            duration, special_instructions) 
                                            VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("isssss", $prescription_id, $medication['name'], 
                                        $medication['dosage'], $medication['frequency'], 
                                        $medication['duration'], $medication['special_instructions']);
                        $stmt->execute();
                    }
                }
                
                // Commit transaction
                $db->commit();
                
                // Log the activity
                log_activity($_SESSION['user_id'], 'prescription_created', "Created prescription ID: $prescription_id");
                
                set_flash_message('success', 'Prescription created successfully.');
                redirect('index.php?controller=prescription&action=view&id=' . $prescription_id);
                
            } catch (Exception $e) {
                // Rollback transaction on error
                $db->rollback();
                $errors[] = $e->getMessage();
            }
            
            $stmt->close();
            $db->close();
        }
        
        if (!empty($errors)) {
            set_flash_message('error', implode('<br>', $errors));
            redirect('index.php?controller=prescription&action=create&appointment_id=' . $appointment_id);
        }
    }
    
    /**
     * View prescription details
     */
    public function view() {
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to view prescriptions.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $prescription_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($prescription_id === 0) {
            set_flash_message('error', 'Invalid prescription ID.');
            redirect('index.php');
        }
        
        $db = get_db_connection();
        
        // Get prescription details
        $stmt = $db->prepare("SELECT p.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                            c.health_id, c.gender, c.date_of_birth,
                            CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
                            d.specialization, d.license_number
                            FROM prescriptions p 
                            JOIN citizens c ON p.citizen_id = c.id 
                            JOIN doctors d ON p.doctor_id = d.id 
                            WHERE p.id = ?");
        $stmt->bind_param("i", $prescription_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Prescription not found.');
            redirect('index.php');
        }
        
        $prescription = $result->fetch_assoc();
        
        // Check if user has permission to view this prescription
        if ($_SESSION['role'] === 'citizen' && $prescription['citizen_id'] !== $_SESSION['user_id']) {
            set_flash_message('error', 'You do not have permission to view this prescription.');
            redirect('index.php');
        }
        
        if ($_SESSION['role'] === 'doctor') {
            $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();
            
            if ($prescription['doctor_id'] !== $doctor['id']) {
                set_flash_message('error', 'You do not have permission to view this prescription.');
                redirect('index.php');
            }
        }
        
        // Get medications
        $stmt = $db->prepare("SELECT * FROM prescription_medications WHERE prescription_id = ? ORDER BY id");
        $stmt->bind_param("i", $prescription_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $medications = [];
        while ($row = $result->fetch_assoc()) {
            $medications[] = $row;
        }
        
        $stmt->close();
        $db->close();
        
        // Include the prescription view
        include('views/prescription/view.php');
    }
    
    /**
     * List prescriptions for the current user
     */
    public function list() {
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to view prescriptions.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $db = get_db_connection();
        
        $prescriptions = [];
        
        if ($role === 'citizen') {
            // Get citizen's prescriptions
            $stmt = $db->prepare("SELECT p.*, 
                                CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
                                d.specialization 
                                FROM prescriptions p 
                                JOIN doctors d ON p.doctor_id = d.id 
                                WHERE p.citizen_id = ? 
                                ORDER BY p.created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $prescriptions[] = $row;
            }
        } elseif ($role === 'doctor') {
            // Get doctor's prescriptions
            $stmt = $db->prepare("SELECT p.*, 
                                CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                                c.health_id 
                                FROM prescriptions p 
                                JOIN citizens c ON p.citizen_id = c.id 
                                JOIN doctors d ON p.doctor_id = d.id 
                                WHERE d.user_id = ? 
                                ORDER BY p.created_at DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $prescriptions[] = $row;
            }
        }
        
        $stmt->close();
        $db->close();
        
        // Include the prescription list view
        include('views/prescription/list.php');
    }
    
    /**
     * Download prescription as PDF
     */
    public function download() {
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to download prescriptions.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $prescription_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($prescription_id === 0) {
            set_flash_message('error', 'Invalid prescription ID.');
            redirect('index.php');
        }
        
        $db = get_db_connection();
        
        // Get prescription details
        $stmt = $db->prepare("SELECT p.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                            c.health_id, c.gender, c.date_of_birth,
                            CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
                            d.specialization, d.license_number
                            FROM prescriptions p 
                            JOIN citizens c ON p.citizen_id = c.id 
                            JOIN doctors d ON p.doctor_id = d.id 
                            WHERE p.id = ?");
        $stmt->bind_param("i", $prescription_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Prescription not found.');
            redirect('index.php');
        }
        
        $prescription = $result->fetch_assoc();
        
        // Check permissions
        if ($_SESSION['role'] === 'citizen' && $prescription['citizen_id'] !== $_SESSION['user_id']) {
            set_flash_message('error', 'You do not have permission to download this prescription.');
            redirect('index.php');
        }
        
        if ($_SESSION['role'] === 'doctor') {
            $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();
            
            if ($prescription['doctor_id'] !== $doctor['id']) {
                set_flash_message('error', 'You do not have permission to download this prescription.');
                redirect('index.php');
            }
        }
        
        // Get medications
        $stmt = $db->prepare("SELECT * FROM prescription_medications WHERE prescription_id = ? ORDER BY id");
        $stmt->bind_param("i", $prescription_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $medications = [];
        while ($row = $result->fetch_assoc()) {
            $medications[] = $row;
        }
        
        $stmt->close();
        $db->close();
        
        // Generate PDF content (basic HTML for now, can be enhanced with proper PDF library)
        $this->generate_pdf($prescription, $medications);
    }
    
    /**
     * Generate PDF content
     */
    private function generate_pdf($prescription, $medications) {
        // Set headers for PDF download
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="prescription_' . $prescription['id'] . '.html"');
        
        // Generate HTML content
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Prescription</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
                .section { margin-bottom: 20px; }
                .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                .medication { border: 1px solid #ddd; padding: 10px; margin: 10px 0; border-radius: 5px; }
                .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Medical Prescription</h1>
                <p>Digital Health Record System</p>
            </div>
            
            <div class="section">
                <h3>Patient Information</h3>
                <p><strong>Name:</strong> ' . htmlspecialchars($prescription['citizen_name']) . '</p>
                <p><strong>Health ID:</strong> ' . htmlspecialchars($prescription['health_id']) . '</p>
                <p><strong>Gender:</strong> ' . ucfirst($prescription['gender']) . '</p>
                <p><strong>Date of Birth:</strong> ' . format_date($prescription['date_of_birth']) . '</p>
            </div>
            
            <div class="section">
                <h3>Doctor Information</h3>
                <p><strong>Name:</strong> Dr. ' . htmlspecialchars($prescription['doctor_name']) . '</p>
                <p><strong>Specialization:</strong> ' . htmlspecialchars($prescription['specialization']) . '</p>
                <p><strong>License Number:</strong> ' . htmlspecialchars($prescription['license_number']) . '</p>
            </div>
            
            <div class="section">
                <h3>Prescription Details</h3>
                <p><strong>Date:</strong> ' . format_date($prescription['created_at']) . '</p>
                <p><strong>Diagnosis:</strong> ' . htmlspecialchars($prescription['diagnosis']) . '</p>
                <p><strong>Instructions:</strong> ' . htmlspecialchars($prescription['instructions']) . '</p>';
        
        if ($prescription['follow_up_date']) {
            $html .= '<p><strong>Follow-up Date:</strong> ' . format_date($prescription['follow_up_date']) . '</p>';
        }
        
        if ($prescription['notes']) {
            $html .= '<p><strong>Notes:</strong> ' . htmlspecialchars($prescription['notes']) . '</p>';
        }
        
        $html .= '</div>
            
            <div class="section">
                <h3>Medications</h3>';
        
        foreach ($medications as $medication) {
            $html .= '
                <div class="medication">
                    <p><strong>Medication:</strong> ' . htmlspecialchars($medication['medication_name']) . '</p>
                    <p><strong>Dosage:</strong> ' . htmlspecialchars($medication['dosage']) . '</p>
                    <p><strong>Frequency:</strong> ' . htmlspecialchars($medication['frequency']) . '</p>
                    <p><strong>Duration:</strong> ' . htmlspecialchars($medication['duration']) . '</p>';
            
            if ($medication['special_instructions']) {
                $html .= '<p><strong>Special Instructions:</strong> ' . htmlspecialchars($medication['special_instructions']) . '</p>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>
            
            <div class="footer">
                <p>This prescription was generated electronically by the Digital Health Record System</p>
                <p>Generated on: ' . date('F j, Y \a\t g:i A') . '</p>
            </div>
        </body>
        </html>';
        
        echo $html;
        exit();
    }
    
    /**
     * Display prescriptions for a citizen
     */
    public function my_prescriptions() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to view prescriptions.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $citizen_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get citizen's prescriptions
        $stmt = $db->prepare("
            SELECT p.*, 
                   CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
                   d.specialization 
            FROM prescriptions p 
            JOIN doctors d ON p.doctor_id = d.id 
            WHERE p.citizen_id = ? 
            ORDER BY p.created_at DESC
        ");
        $stmt->bind_param('i', $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }
        
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
        
        $stmt->close();
        $db->close();
        
        // Include the view
        include('views/prescription/my_prescriptions.php');
    }
}
