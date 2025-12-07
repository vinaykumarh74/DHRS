<?php
/**
 * Doctor Controller
 * Handles all doctor-related functionality
 */

class DoctorController {
    /**
     * Display doctor dashboard
     */
    public function dashboard() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Get doctor data
        $doctor_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get doctor profile
        $stmt = $db->prepare("SELECT d.*, u.email, u.phone FROM doctors d 
                            JOIN users u ON d.user_id = u.id 
                            WHERE d.user_id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        
        // Get today's appointments count
        $today = date('Y-m-d');
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments 
                            WHERE doctor_id = ? AND appointment_date = ? 
                            AND status = 'confirmed'");
        $stmt->bind_param("is", $doctor['id'], $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $todays_appointments_count = $row['count'];
        
        // Get today's appointments
        $stmt = $db->prepare("SELECT a.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                            c.health_id, c.gender, c.date_of_birth 
                            FROM appointments a 
                            JOIN citizens c ON a.citizen_id = c.id 
                            WHERE a.doctor_id = ? AND a.appointment_date = ? 
                            AND a.status = 'confirmed' 
                            ORDER BY a.appointment_time ASC");
        $stmt->bind_param("is", $doctor['id'], $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $todays_appointments = [];
        while ($row = $result->fetch_assoc()) {
            $todays_appointments[] = $row;
        }
        
        // Get upcoming appointments count (excluding today)
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments 
                            WHERE doctor_id = ? AND appointment_date > ? 
                            AND status = 'confirmed'");
        $stmt->bind_param("is", $doctor['id'], $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $upcoming_appointments_count = $row['count'];
        
        // Get upcoming appointments (limit 5, excluding today)
        $stmt = $db->prepare("SELECT a.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                            c.health_id 
                            FROM appointments a 
                            JOIN citizens c ON a.citizen_id = c.id 
                            WHERE a.doctor_id = ? AND a.appointment_date > ? 
                            AND a.status = 'confirmed' 
                            ORDER BY a.appointment_date ASC, a.appointment_time ASC 
                            LIMIT 5");
        $stmt->bind_param("is", $doctor['id'], $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $upcoming_appointments = [];
        while ($row = $result->fetch_assoc()) {
            $upcoming_appointments[] = $row;
        }
        
        // Get recent prescriptions (limit 5)
        $stmt = $db->prepare("SELECT p.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                            c.health_id 
                            FROM prescriptions p 
                            JOIN citizens c ON p.citizen_id = c.id 
                            WHERE p.doctor_id = ? 
                            ORDER BY p.created_at DESC 
                            LIMIT 5");
        $stmt->bind_param("i", $doctor['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $recent_prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $recent_prescriptions[] = $row;
        }
        
        // Get total patients count
        $stmt = $db->prepare("SELECT COUNT(DISTINCT citizen_id) as count 
                            FROM appointments 
                            WHERE doctor_id = ?");
        $stmt->bind_param("i", $doctor['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_patients_count = $row['count'];
        
        // Get recent notifications (limit 5)
        $stmt = $db->prepare("SELECT * FROM notifications 
                            WHERE user_id = ? 
                            ORDER BY created_at DESC 
                            LIMIT 5");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $recent_notifications = [];
        while ($row = $result->fetch_assoc()) {
            $recent_notifications[] = $row;
        }
        
        // Close database connection
        $stmt->close();
        $db->close();
        
        // Include the dashboard view
        include('views/doctor/dashboard.php');
    }
    
    /**
     * Display doctor profile
     */
    public function profile() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Get doctor data
        $doctor_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get doctor profile
        $stmt = $db->prepare("SELECT d.*, u.email, u.phone, u.created_at 
                            FROM doctors d 
                            JOIN users u ON d.user_id = u.id 
                            WHERE d.user_id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        
        // Close database connection
        $stmt->close();
        $db->close();
        
        // Include the profile view
        include('views/doctor/profile.php');
    }
    
    /**
     * Update doctor profile
     */
    public function update_profile() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $first_name = sanitize_input($_POST['first_name']);
            $last_name = sanitize_input($_POST['last_name']);
            $date_of_birth = sanitize_input($_POST['date_of_birth']);
            $gender = sanitize_input($_POST['gender']);
            $specialization = sanitize_input($_POST['specialization']);
            $license_number = sanitize_input($_POST['license_number']);
            $qualification = sanitize_input($_POST['qualification']);
            $experience_years = sanitize_input($_POST['experience_years']);
            $bio = sanitize_input($_POST['bio']);
            $consultation_fee = sanitize_input($_POST['consultation_fee']);
            $address = sanitize_input($_POST['address']);
            $city = sanitize_input($_POST['city']);
            $state = sanitize_input($_POST['state']);
            $postal_code = sanitize_input($_POST['postal_code']);
            $email = sanitize_input($_POST['email']);
            $phone = sanitize_input($_POST['phone']);
            
            // Validate form data
            $errors = [];
            
            if (empty($first_name)) {
                $errors[] = 'First name is required.';
            }
            
            if (empty($last_name)) {
                $errors[] = 'Last name is required.';
            }
            
            if (empty($date_of_birth)) {
                $errors[] = 'Date of birth is required.';
            } elseif (strtotime($date_of_birth) > time()) {
                $errors[] = 'Date of birth cannot be in the future.';
            }
            
            if (empty($gender)) {
                $errors[] = 'Gender is required.';
            }
            
            if (empty($specialization)) {
                $errors[] = 'Specialization is required.';
            }
            
            if (empty($license_number)) {
                $errors[] = 'License number is required.';
            }
            
            if (empty($qualification)) {
                $errors[] = 'Qualification is required.';
            }
            
            if (empty($experience_years)) {
                $errors[] = 'Years of experience is required.';
            } elseif (!is_numeric($experience_years) || $experience_years < 0) {
                $errors[] = 'Years of experience must be a positive number.';
            }
            
            if (empty($consultation_fee)) {
                $errors[] = 'Consultation fee is required.';
            } elseif (!is_numeric($consultation_fee) || $consultation_fee < 0) {
                $errors[] = 'Consultation fee must be a positive number.';
            }
            
            if (empty($address)) {
                $errors[] = 'Address is required.';
            }
            
            if (empty($city)) {
                $errors[] = 'City is required.';
            }
            
            if (empty($state)) {
                $errors[] = 'State is required.';
            }
            
            if (empty($postal_code)) {
                $errors[] = 'Postal code is required.';
            }
            
            if (empty($email)) {
                $errors[] = 'Email is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format.';
            }
            
            if (empty($phone)) {
                $errors[] = 'Phone number is required.';
            } elseif (!validate_phone($phone)) {
                $errors[] = 'Invalid phone number format.';
            }
            
            // Check if email already exists for another user
            $db = get_db_connection();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $user_id = $_SESSION['user_id'];
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = 'Email address is already in use by another account.';
            }
            
            // Check if phone already exists for another user
            $stmt = $db->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
            $stmt->bind_param("si", $phone, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = 'Phone number is already in use by another account.';
            }
            
            // Check if license number already exists for another doctor
            $stmt = $db->prepare("SELECT id FROM doctors WHERE license_number = ? AND user_id != ?");
            $stmt->bind_param("si", $license_number, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = 'License number is already in use by another doctor.';
            }
            
            // Handle profile image upload
            $profile_image = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 2 * 1024 * 1024; // 2MB
                
                if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
                    $errors[] = 'Invalid file type. Only JPG, PNG, and GIF files are allowed.';
                } elseif ($_FILES['profile_image']['size'] > $max_size) {
                    $errors[] = 'File size exceeds the maximum limit of 2MB.';
                } else {
                    // Generate unique filename
                    $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                    $profile_image = 'doctor_' . $user_id . '_' . time() . '.' . $file_extension;
                    
                    // Create uploads directory if it doesn't exist
                    $upload_dir = 'uploads/profile/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Move uploaded file
                    $upload_path = $upload_dir . $profile_image;
                    if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                        $errors[] = 'Failed to upload profile image. Please try again.';
                        $profile_image = null;
                    }
                }
            }
            
            // If there are no errors, update profile
            if (empty($errors)) {
                // Start transaction
                $db->begin_transaction();
                
                try {
                    // Update user table
                    $stmt = $db->prepare("UPDATE users SET email = ?, phone = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $email, $phone, $user_id);
                    $stmt->execute();
                    
                    // Update doctor table
                    if ($profile_image) {
                        $stmt = $db->prepare("UPDATE doctors SET 
                                            first_name = ?, last_name = ?, date_of_birth = ?, 
                                            gender = ?, specialization = ?, license_number = ?, 
                                            qualification = ?, experience_years = ?, bio = ?, 
                                            consultation_fee = ?, address = ?, city = ?, 
                                            state = ?, postal_code = ?, profile_image = ? 
                                            WHERE user_id = ?");
                        $stmt->bind_param("sssssssisdsssi", $first_name, $last_name, $date_of_birth, 
                                        $gender, $specialization, $license_number, 
                                        $qualification, $experience_years, $bio, 
                                        $consultation_fee, $address, $city, 
                                        $state, $postal_code, $profile_image, $user_id);
                    } else {
                        $stmt = $db->prepare("UPDATE doctors SET 
                                            first_name = ?, last_name = ?, date_of_birth = ?, 
                                            gender = ?, specialization = ?, license_number = ?, 
                                            qualification = ?, experience_years = ?, bio = ?, 
                                            consultation_fee = ?, address = ?, city = ?, 
                                            state = ?, postal_code = ? 
                                            WHERE user_id = ?");
                        $stmt->bind_param("sssssssisdsssi", $first_name, $last_name, $date_of_birth, 
                                        $gender, $specialization, $license_number, 
                                        $qualification, $experience_years, $bio, 
                                        $consultation_fee, $address, $city, 
                                        $state, $postal_code, $user_id);
                    }
                    $stmt->execute();
                    
                    // Commit transaction
                    $db->commit();
                    
                    // Update session data
                    $_SESSION['name'] = $first_name . ' ' . $last_name;
                    $_SESSION['email'] = $email;
                    
                    // Log the activity
                    log_activity($user_id, 'profile_update', 'Updated profile information');
                    
                    // Set success message
                    set_flash_message('success', 'Your profile has been updated successfully.');
                    
                    // Redirect to profile page
                    redirect('index.php?controller=doctor&action=profile');
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $db->rollback();
                    $errors[] = 'An error occurred while updating your profile. Please try again.';
                }
            }
            
            // If there are errors, display them
            if (!empty($errors)) {
                // Set error message
                set_flash_message('error', implode('<br>', $errors));
                
                // Redirect back to profile page
                redirect('index.php?controller=doctor&action=profile');
            }
            
            // Close database connection
            $stmt->close();
            $db->close();
        } else {
            // If not POST request, redirect to profile page
            redirect('index.php?controller=doctor&action=profile');
        }
    }
    
    /**
     * Display doctor's patients
     */
    public function my_patients() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Get doctor data
        $doctor_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get doctor profile
        $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        
        // Get pagination parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get search parameter
        $search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
        
        // Prepare search condition
        $search_condition = '';
        $search_params = [];
        $param_types = '';
        
        if (!empty($search)) {
            $search_condition = " AND (c.first_name LIKE ? OR c.last_name LIKE ? OR c.health_id LIKE ?)"; 
            $search_params = ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%'];
            $param_types = 'sss';
        }
        
        // Get total patients count
        $query = "SELECT COUNT(DISTINCT c.id) as total 
                FROM citizens c 
                JOIN appointments a ON c.id = a.citizen_id 
                WHERE a.doctor_id = ?" . $search_condition;
        
        $stmt = $db->prepare($query);
        
        if (!empty($search)) {
            $stmt->bind_param('i' . $param_types, $doctor['id'], ...$search_params);
        } else {
            $stmt->bind_param('i', $doctor['id']);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_patients = $row['total'];
        
        // Calculate total pages
        $total_pages = ceil($total_patients / $limit);
        
        // Get patients list
        $query = "SELECT DISTINCT c.*, 
                (SELECT COUNT(*) FROM appointments WHERE citizen_id = c.id AND doctor_id = ?) as appointment_count, 
                (SELECT MAX(appointment_date) FROM appointments WHERE citizen_id = c.id AND doctor_id = ?) as last_visit 
                FROM citizens c 
                JOIN appointments a ON c.id = a.citizen_id 
                WHERE a.doctor_id = ?" . $search_condition . "
                ORDER BY c.first_name, c.last_name 
                LIMIT ? OFFSET ?";
        
        $stmt = $db->prepare($query);
        
        if (!empty($search)) {
            $all_params = array_merge([$doctor['id'], $doctor['id'], $doctor['id']], $search_params, [$limit, $offset]);
            $stmt->bind_param('iii' . $param_types . 'ii', ...$all_params);
        } else {
            $stmt->bind_param('iiiii', $doctor['id'], $doctor['id'], $doctor['id'], $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
        
        // Close database connection
        $stmt->close();
        $db->close();
        
        // Include the patients view
        include('views/doctor/my_patients.php');
    }
    
    /**
     * Display patient details
     */
    public function patient_details() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Check if patient ID is provided
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            set_flash_message('error', 'Invalid patient ID.');
            redirect('index.php?controller=doctor&action=my_patients');
        }
        
        $patient_id = (int)$_GET['id'];
        $doctor_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get doctor ID
        $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        
        // Check if the patient has had an appointment with this doctor
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments 
                            WHERE citizen_id = ? AND doctor_id = ?");
        $stmt->bind_param("ii", $patient_id, $doctor['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] == 0) {
            set_flash_message('error', 'You do not have permission to view this patient\'s details.');
            redirect('index.php?controller=doctor&action=my_patients');
        }
        
        // Get patient details
        $stmt = $db->prepare("SELECT c.*, u.email, u.phone 
                            FROM citizens c 
                            JOIN users u ON c.user_id = u.id 
                            WHERE c.id = ?");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $patient = $result->fetch_assoc();
        
        // Get patient health profile
        $stmt = $db->prepare("SELECT * FROM citizen_health_profiles WHERE citizen_id = ?");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $health_profile = $result->fetch_assoc();
            
            // Get chronic conditions
            $stmt = $db->prepare("SELECT cc.*, c.name, c.description 
                                FROM citizen_chronic_conditions cc 
                                JOIN chronic_conditions c ON cc.condition_id = c.id 
                                WHERE cc.citizen_id = ?");
            $stmt->bind_param("i", $patient_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $health_profile['chronic_conditions'] = [];
            while ($row = $result->fetch_assoc()) {
                $health_profile['chronic_conditions'][] = $row;
            }
            
            // Calculate BMI
            if ($health_profile['height'] > 0 && $health_profile['weight'] > 0) {
                $height_m = $health_profile['height'] / 100; // Convert cm to m
                $health_profile['bmi'] = round($health_profile['weight'] / ($height_m * $height_m), 1);
                
                // Determine BMI category
                if ($health_profile['bmi'] < 18.5) {
                    $health_profile['bmi_category'] = 'Underweight';
                } elseif ($health_profile['bmi'] >= 18.5 && $health_profile['bmi'] < 25) {
                    $health_profile['bmi_category'] = 'Normal weight';
                } elseif ($health_profile['bmi'] >= 25 && $health_profile['bmi'] < 30) {
                    $health_profile['bmi_category'] = 'Overweight';
                } else {
                    $health_profile['bmi_category'] = 'Obesity';
                }
            } else {
                $health_profile['bmi'] = 0;
                $health_profile['bmi_category'] = 'Unknown';
            }
        } else {
            // Return empty health profile
            $health_profile = [
                'blood_group' => 'Not provided',
                'height' => 0,
                'weight' => 0,
                'bmi' => 0,
                'bmi_category' => 'Unknown',
                'allergies' => '',
                'current_medications' => '',
                'family_medical_history' => '',
                'lifestyle_info' => '',
                'chronic_conditions' => []
            ];
        }
        
        // Get appointment history
        $stmt = $db->prepare("SELECT a.* 
                            FROM appointments a 
                            WHERE a.citizen_id = ? AND a.doctor_id = ? 
                            ORDER BY a.appointment_date DESC, a.appointment_time DESC");
        $stmt->bind_param("ii", $patient_id, $doctor['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        
        // Get medical records
        $stmt = $db->prepare("SELECT mr.* 
                            FROM medical_records mr 
                            WHERE mr.citizen_id = ? AND mr.doctor_id = ? 
                            ORDER BY mr.created_at DESC");
        $stmt->bind_param("ii", $patient_id, $doctor['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $medical_records = [];
        while ($row = $result->fetch_assoc()) {
            $medical_records[] = $row;
        }
        
        // Get prescriptions
        $stmt = $db->prepare("SELECT p.* 
                            FROM prescriptions p 
                            WHERE p.citizen_id = ? AND p.doctor_id = ? 
                            ORDER BY p.created_at DESC");
        $stmt->bind_param("ii", $patient_id, $doctor['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $prescriptions[] = $row;
        }
        
        // Get lab reports
        $stmt = $db->prepare("SELECT lr.*, lt.name as test_name 
                            FROM lab_reports lr 
                            JOIN lab_tests lt ON lr.test_id = lt.id 
                            WHERE lr.citizen_id = ? AND lr.doctor_id = ? 
                            ORDER BY lr.created_at DESC");
        $stmt->bind_param("ii", $patient_id, $doctor['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $lab_reports = [];
        while ($row = $result->fetch_assoc()) {
            $lab_reports[] = $row;
        }
        
        // Close database connection
        $stmt->close();
        $db->close();
        
        // Include the patient details view
        include('views/doctor/patient_details.php');
    }
    
    /**
     * Display doctor's schedule
     */
    public function my_schedule() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Get doctor data
        $doctor_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get doctor profile
        $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        
        // Get doctor's schedule
        $stmt = $db->prepare("SELECT * FROM doctor_schedules WHERE doctor_id = ? ORDER BY day_of_week");
        $stmt->bind_param("i", $doctor['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedules = [];
        while ($row = $result->fetch_assoc()) {
            $schedules[$row['day_of_week']] = $row;
        }
        
        // Close database connection
        $stmt->close();
        $db->close();
        
        // Include the schedule view
        include('views/doctor/my_schedule.php');
    }
    
    /**
     * Update doctor's schedule
     */
    public function update_schedule() {
        // Check if user is logged in and is a doctor
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get doctor data
            $doctor_id = $_SESSION['user_id'];
            $db = get_db_connection();
            
            // Get doctor profile
            $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
            $stmt->bind_param("i", $doctor_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();
            
            // Start transaction
            $db->begin_transaction();
            
            try {
                // Days of the week
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                
                foreach ($days as $day) {
                    $is_available = isset($_POST[$day . '_available']) ? 1 : 0;
                    $start_time = isset($_POST[$day . '_start_time']) ? sanitize_input($_POST[$day . '_start_time']) : null;
                    $end_time = isset($_POST[$day . '_end_time']) ? sanitize_input($_POST[$day . '_end_time']) : null;
                    $max_appointments = isset($_POST[$day . '_max_appointments']) ? (int)sanitize_input($_POST[$day . '_max_appointments']) : 0;
                    $appointment_duration = isset($_POST[$day . '_appointment_duration']) ? (int)sanitize_input($_POST[$day . '_appointment_duration']) : 0;
                    
                    // Check if schedule exists for this day
                    $stmt = $db->prepare("SELECT id FROM doctor_schedules WHERE doctor_id = ? AND day_of_week = ?");
                    $stmt->bind_param("is", $doctor['id'], $day);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        // Update existing schedule
                        $row = $result->fetch_assoc();
                        $schedule_id = $row['id'];
                        
                        $stmt = $db->prepare("UPDATE doctor_schedules SET 
                                            is_available = ?, start_time = ?, end_time = ?, 
                                            max_appointments = ?, appointment_duration = ? 
                                            WHERE id = ?");
                        $stmt->bind_param("issiii", $is_available, $start_time, $end_time, 
                                        $max_appointments, $appointment_duration, $schedule_id);
                        $stmt->execute();
                    } else {
                        // Insert new schedule
                        $stmt = $db->prepare("INSERT INTO doctor_schedules 
                                            (doctor_id, day_of_week, is_available, start_time, end_time, 
                                            max_appointments, appointment_duration) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("isisiii", $doctor['id'], $day, $is_available, $start_time, $end_time, 
                                        $max_appointments, $appointment_duration);
                        $stmt->execute();
                    }
                }
                
                // Commit transaction
                $db->commit();
                
                // Log the activity
                log_activity($doctor_id, 'schedule_update', 'Updated schedule information');
                
                // Set success message
                set_flash_message('success', 'Your schedule has been updated successfully.');
                
                // Redirect to schedule page
                redirect('index.php?controller=doctor&action=my_schedule');
            } catch (Exception $e) {
                // Rollback transaction on error
                $db->rollback();
                set_flash_message('error', 'An error occurred while updating your schedule. Please try again.');
                redirect('index.php?controller=doctor&action=my_schedule');
            }
            
            // Close database connection
            $stmt->close();
            $db->close();
        } else {
            // If not POST request, redirect to schedule page
            redirect('index.php?controller=doctor&action=my_schedule');
        }
    }
}