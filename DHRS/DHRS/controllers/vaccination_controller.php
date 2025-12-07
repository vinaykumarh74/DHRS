<?php
/**
 * Vaccination Controller
 * 
 * Handles vaccination operations for citizens and doctors
 */

class VaccinationController {
    
    /**
     * Display vaccinations for a citizen
     */
    public function my_vaccinations() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to view vaccinations.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $user_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get the citizen_id from the citizens table based on user_id
        $stmt = $db->prepare("SELECT id FROM citizens WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Citizen profile not found. Please complete your profile first.');
            redirect('index.php?controller=citizen&action=profile');
        }
        
        $citizen = $result->fetch_assoc();
        $citizen_id = $citizen['id'];
        $stmt->close();
        
        // Get citizen's vaccinations
        $stmt = $db->prepare("
            SELECT cv.*, v.name as vaccination_name, v.description, v.recommended_age, v.doses_required
            FROM citizen_vaccinations cv
            LEFT JOIN vaccinations v ON cv.vaccination_id = v.id
            WHERE cv.citizen_id = ?
            ORDER BY cv.vaccination_date DESC
        ");
        $stmt->bind_param('i', $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $vaccinations = $result->fetch_all(MYSQLI_ASSOC);
        
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
        include('views/vaccination/my_vaccinations.php');
    }
    
    /**
     * View a specific vaccination record
     */
    public function view() {
        // Check if user is logged in
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to view vaccination records.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $vaccination_id = $_GET['id'] ?? null;
        if (!$vaccination_id) {
            set_flash_message('error', 'Vaccination record ID is required.');
            redirect('index.php?controller=vaccination&action=my_vaccinations');
        }
        
        $db = get_db_connection();
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'];
        
        // Get vaccination record with access control
        if ($user_role === 'citizen') {
            $stmt = $db->prepare("
                SELECT cv.*, v.name as vaccination_name, v.description, v.recommended_age, v.doses_required,
                       c.first_name, c.last_name, c.health_id
                FROM citizen_vaccinations cv
                LEFT JOIN vaccinations v ON cv.vaccination_id = v.id
                LEFT JOIN citizens c ON cv.citizen_id = c.user_id
                WHERE cv.id = ? AND cv.citizen_id = ?
            ");
            $stmt->bind_param('ii', $vaccination_id, $user_id);
        } elseif ($user_role === 'doctor') {
            $stmt = $db->prepare("
                SELECT cv.*, v.name as vaccination_name, v.description, v.recommended_age, v.doses_required,
                       c.first_name, c.last_name, c.health_id
                FROM citizen_vaccinations cv
                LEFT JOIN vaccinations v ON cv.vaccination_id = v.id
                LEFT JOIN citizens c ON cv.citizen_id = c.user_id
                WHERE cv.id = ? AND cv.doctor_id = ?
            ");
            $stmt->bind_param('ii', $vaccination_id, $user_id);
        } else {
            set_flash_message('error', 'You do not have permission to view this vaccination record.');
            redirect('index.php?controller=home&action=index');
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $vaccination = $result->fetch_assoc();
        
        if (!$vaccination) {
            set_flash_message('error', 'Vaccination record not found or you do not have permission to view it.');
            redirect('index.php?controller=vaccination&action=my_vaccinations');
        }
        
        // Include the view
        include('views/vaccination/view.php');
    }
    
    /**
     * Record a new vaccination (for doctors)
     */
    public function record() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to record vaccinations.');
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
        
        // Get available vaccinations
        $stmt = $db->prepare("
            SELECT * FROM vaccinations
            ORDER BY name
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $available_vaccinations = $result->fetch_all(MYSQLI_ASSOC);
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $citizen_id = sanitize_input($_POST['citizen_id']);
            $vaccination_id = sanitize_input($_POST['vaccination_id']);
            $dose_number = sanitize_input($_POST['dose_number']);
            $vaccination_date = sanitize_input($_POST['vaccination_date']);
            $next_due_date = sanitize_input($_POST['next_due_date']);
            $administered_by = sanitize_input($_POST['administered_by']);
            $notes = sanitize_input($_POST['notes']);
            
            // Validate required fields
            if (empty($citizen_id) || empty($vaccination_id) || empty($dose_number) || empty($vaccination_date)) {
                set_flash_message('error', 'Please fill in all required fields.');
            } else {
                // Get doctor ID from doctors table
                $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
                $stmt->bind_param('i', $doctor_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $doctor_record = $result->fetch_assoc();
                $doctor_table_id = $doctor_record['id'];
                
                // Insert vaccination record
                $stmt = $db->prepare("
                    INSERT INTO citizen_vaccinations (citizen_id, vaccination_id, dose_number, vaccination_date, next_due_date, administered_by, notes, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param('iiissss', $citizen_id, $vaccination_id, $dose_number, $vaccination_date, $next_due_date, $administered_by, $notes);
                
                if ($stmt->execute()) {
                    set_flash_message('success', 'Vaccination recorded successfully.');
                    redirect('index.php?controller=vaccination&action=my_vaccinations');
                } else {
                    set_flash_message('error', 'Failed to record vaccination. Please try again.');
                }
            }
        }
        
        // Include the view
        include('views/vaccination/record.php');
    }
    
    /**
     * Generate vaccination certificate
     */
    public function certificate() {
        // Check if user is logged in
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to generate vaccination certificates.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $vaccination_id = $_GET['id'] ?? null;
        if (!$vaccination_id) {
            set_flash_message('error', 'Vaccination record ID is required.');
            redirect('index.php?controller=vaccination&action=my_vaccinations');
        }
        
        $db = get_db_connection();
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'];
        
        // Get vaccination record with access control
        if ($user_role === 'citizen') {
            $stmt = $db->prepare("
                SELECT cv.*, v.name as vaccination_name, v.description, v.recommended_age, v.doses_required,
                       c.first_name, c.last_name, c.health_id, c.date_of_birth
                FROM citizen_vaccinations cv
                LEFT JOIN vaccinations v ON cv.vaccination_id = v.id
                LEFT JOIN citizens c ON cv.citizen_id = c.user_id
                WHERE cv.id = ? AND cv.citizen_id = ?
            ");
            $stmt->bind_param('ii', $vaccination_id, $user_id);
        } else {
            set_flash_message('error', 'You do not have permission to generate this certificate.');
            redirect('index.php?controller=home&action=index');
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $vaccination = $result->fetch_assoc();
        
        if (!$vaccination) {
            set_flash_message('error', 'Vaccination record not found or you do not have permission to generate certificate.');
            redirect('index.php?controller=vaccination&action=my_vaccinations');
        }
        
        // Generate certificate content
        $content = "VACCINATION CERTIFICATE\n";
        $content .= "======================\n\n";
        $content .= "This is to certify that\n\n";
        $content .= "Name: " . $vaccination['first_name'] . " " . $vaccination['last_name'] . "\n";
        $content .= "Health ID: " . $vaccination['health_id'] . "\n";
        $content .= "Date of Birth: " . date('F d, Y', strtotime($vaccination['date_of_birth'])) . "\n\n";
        $content .= "has been vaccinated with\n\n";
        $content .= "Vaccination: " . $vaccination['vaccination_name'] . "\n";
        $content .= "Dose Number: " . $vaccination['dose_number'] . " of " . $vaccination['doses_required'] . "\n";
        $content .= "Date of Vaccination: " . date('F d, Y', strtotime($vaccination['vaccination_date'])) . "\n";
        $content .= "Administered by: " . $vaccination['administered_by'] . "\n\n";
        
        if (!empty($vaccination['next_due_date'])) {
            $content .= "Next Due Date: " . date('F d, Y', strtotime($vaccination['next_due_date'])) . "\n\n";
        }
        
        if (!empty($vaccination['notes'])) {
            $content .= "Notes: " . $vaccination['notes'] . "\n\n";
        }
        
        $content .= "This certificate is generated by the Digital Health Record System\n";
        $content .= "Generated on: " . date('F d, Y H:i:s') . "\n";
        
        // Set headers for download
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="vaccination_certificate_' . $vaccination_id . '.txt"');
        header('Content-Length: ' . strlen($content));
        
        echo $content;
        exit;
    }
}
