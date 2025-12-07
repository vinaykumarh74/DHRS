<?php
/**
 * Citizen Controller
 * Handles all citizen-related functionality
 */

class CitizenController {
    /**
     * Display citizen dashboard
     */
    public function dashboard() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Get citizen data
        $citizen_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get citizen profile
        $stmt = $db->prepare("SELECT c.*, u.email, u.phone FROM citizens c 
                            JOIN users u ON c.user_id = u.id 
                            WHERE c.user_id = ?");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $citizen = $result->fetch_assoc();
        
        // Get upcoming appointments count
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments 
                            WHERE citizen_id = ? AND appointment_date >= CURDATE() 
                            AND status = 'confirmed'");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $upcoming_appointments_count = $row['count'];
        
        // Get upcoming appointments (limit 5)
        $stmt = $db->prepare("SELECT a.*, 
                            CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
                            d.specialization 
                            FROM appointments a 
                            JOIN doctors d ON a.doctor_id = d.id 
                            WHERE a.citizen_id = ? AND a.appointment_date >= CURDATE() 
                            AND a.status = 'confirmed' 
                            ORDER BY a.appointment_date ASC, a.appointment_time ASC 
                            LIMIT 5");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $upcoming_appointments = [];
        while ($row = $result->fetch_assoc()) {
            $upcoming_appointments[] = $row;
        }
        
        // Get active prescriptions count
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM prescriptions 
                            WHERE citizen_id = ? AND is_active = 1");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $active_prescriptions_count = $row['count'];
        
        // Get recent prescriptions (limit 5)
        $stmt = $db->prepare("SELECT p.*, 
                            CONCAT(d.first_name, ' ', d.last_name) as doctor_name 
                            FROM prescriptions p 
                            JOIN doctors d ON p.doctor_id = d.id 
                            WHERE p.citizen_id = ? 
                            ORDER BY p.created_at DESC 
                            LIMIT 5");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $recent_prescriptions = [];
        while ($row = $result->fetch_assoc()) {
            $recent_prescriptions[] = $row;
        }
        
        // Get lab reports count
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM lab_reports 
                            WHERE citizen_id = ?");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $lab_reports_count = $row['count'];
        
        // Get vaccinations count
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM citizen_vaccinations 
                            WHERE citizen_id = ?");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $vaccinations_count = $row['count'];
        
        // Get health summary
        $health_summary = $this->get_health_summary($citizen_id);
        
        // Get recent notifications (limit 5)
        $stmt = $db->prepare("SELECT * FROM notifications 
                            WHERE user_id = ? 
                            ORDER BY created_at DESC 
                            LIMIT 5");
        $stmt->bind_param("i", $citizen_id);
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
        include('views/citizen/dashboard.php');
    }
    
    /**
     * Display citizen profile
     */
    public function profile() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Get citizen data
        $citizen_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get citizen profile
        $stmt = $db->prepare("SELECT c.*, u.email, u.phone, u.created_at 
                            FROM citizens c 
                            JOIN users u ON c.user_id = u.id 
                            WHERE c.user_id = ?");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $citizen = $result->fetch_assoc();
        
        // Close database connection
        $stmt->close();
        $db->close();
        
        // Include the profile view
        include('views/citizen/profile.php');
    }
    
    /**
     * Update citizen profile
     */
    public function update_profile() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $first_name = sanitize_input($_POST['first_name']);
            $last_name = sanitize_input($_POST['last_name']);
            $date_of_birth = sanitize_input($_POST['date_of_birth']);
            $gender = sanitize_input($_POST['gender']);
            $address = sanitize_input($_POST['address']);
            $city = sanitize_input($_POST['city']);
            $state = sanitize_input($_POST['state']);
            $postal_code = sanitize_input($_POST['postal_code']);
            $emergency_contact_name = sanitize_input($_POST['emergency_contact_name']);
            $emergency_contact_phone = sanitize_input($_POST['emergency_contact_phone']);
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
                    $profile_image = 'citizen_' . $user_id . '_' . time() . '.' . $file_extension;
                    
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
                    
                    // Update citizen table
                    if ($profile_image) {
                        $stmt = $db->prepare("UPDATE citizens SET 
                                            first_name = ?, last_name = ?, date_of_birth = ?, 
                                            gender = ?, address = ?, city = ?, state = ?, 
                                            postal_code = ?, emergency_contact_name = ?, 
                                            emergency_contact_phone = ?, profile_image = ? 
                                            WHERE user_id = ?");
                        $stmt->bind_param("sssssssssssi", $first_name, $last_name, $date_of_birth, 
                                        $gender, $address, $city, $state, $postal_code, 
                                        $emergency_contact_name, $emergency_contact_phone, 
                                        $profile_image, $user_id);
                    } else {
                        $stmt = $db->prepare("UPDATE citizens SET 
                                            first_name = ?, last_name = ?, date_of_birth = ?, 
                                            gender = ?, address = ?, city = ?, state = ?, 
                                            postal_code = ?, emergency_contact_name = ?, 
                                            emergency_contact_phone = ? 
                                            WHERE user_id = ?");
                        $stmt->bind_param("ssssssssssi", $first_name, $last_name, $date_of_birth, 
                                        $gender, $address, $city, $state, $postal_code, 
                                        $emergency_contact_name, $emergency_contact_phone, 
                                        $user_id);
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
                    redirect('index.php?controller=citizen&action=profile');
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
                redirect('index.php?controller=citizen&action=profile');
            }
            
            // Close database connection
            $stmt->close();
            $db->close();
        } else {
            // If not POST request, redirect to profile page
            redirect('index.php?controller=citizen&action=profile');
        }
    }
    
    /**
     * Display health profile
     */
    public function health_profile() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Get citizen data
        $citizen_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get citizen profile
        $stmt = $db->prepare("SELECT c.*, u.email, u.phone 
                            FROM citizens c 
                            JOIN users u ON c.user_id = u.id 
                            WHERE c.user_id = ?");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $citizen = $result->fetch_assoc();
        
        // Get health profile data
        $health_profile = $this->get_health_profile($citizen_id);
        
        // Get all chronic conditions for dropdown
        $stmt = $db->prepare("SELECT * FROM chronic_conditions ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $all_chronic_conditions = [];
        while ($row = $result->fetch_assoc()) {
            $all_chronic_conditions[] = $row;
        }
        
        // Close database connection
        $stmt->close();
        $db->close();
        
        // Include the health profile view
        include('views/citizen/health_profile.php');
    }
    
    /**
     * Update health profile
     */
    public function update_health_profile() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to access this page.');
            redirect('index.php?controller=auth&action=login');
        }
        
        // Check if form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $blood_group = sanitize_input($_POST['blood_group']);
            $height = sanitize_input($_POST['height']);
            $weight = sanitize_input($_POST['weight']);
            $allergies = isset($_POST['allergies']) ? sanitize_input($_POST['allergies']) : '';
            $current_medications = isset($_POST['current_medications']) ? sanitize_input($_POST['current_medications']) : '';
            $family_medical_history = isset($_POST['family_medical_history']) ? sanitize_input($_POST['family_medical_history']) : '';
            $lifestyle_info = isset($_POST['lifestyle_info']) ? sanitize_input($_POST['lifestyle_info']) : '';
            $chronic_conditions = isset($_POST['chronic_conditions']) ? $_POST['chronic_conditions'] : [];
            
            // Validate form data
            $errors = [];
            
            if (empty($blood_group)) {
                $errors[] = 'Blood group is required.';
            }
            
            if (empty($height)) {
                $errors[] = 'Height is required.';
            } elseif (!is_numeric($height) || $height <= 0) {
                $errors[] = 'Height must be a positive number.';
            }
            
            if (empty($weight)) {
                $errors[] = 'Weight is required.';
            } elseif (!is_numeric($weight) || $weight <= 0) {
                $errors[] = 'Weight must be a positive number.';
            }
            
            // If there are no errors, update health profile
            if (empty($errors)) {
                $citizen_id = $_SESSION['user_id'];
                $db = get_db_connection();
                
                // Start transaction
                $db->begin_transaction();
                
                try {
                    // Update or insert health profile
                    $stmt = $db->prepare("SELECT id FROM citizen_health_profiles WHERE citizen_id = ?");
                    $stmt->bind_param("i", $citizen_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        // Update existing profile
                        $row = $result->fetch_assoc();
                        $profile_id = $row['id'];
                        
                        $stmt = $db->prepare("UPDATE citizen_health_profiles SET 
                                            blood_group = ?, height = ?, weight = ?, 
                                            allergies = ?, current_medications = ?, 
                                            family_medical_history = ?, lifestyle_info = ?, 
                                            updated_at = NOW() 
                                            WHERE id = ?");
                        $stmt->bind_param("sddsssi", $blood_group, $height, $weight, 
                                        $allergies, $current_medications, 
                                        $family_medical_history, $lifestyle_info, 
                                        $profile_id);
                        $stmt->execute();
                    } else {
                        // Insert new profile
                        $stmt = $db->prepare("INSERT INTO citizen_health_profiles 
                                            (citizen_id, blood_group, height, weight, 
                                            allergies, current_medications, 
                                            family_medical_history, lifestyle_info, 
                                            created_at, updated_at) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                        $stmt->bind_param("isddssss", $citizen_id, $blood_group, $height, $weight, 
                                        $allergies, $current_medications, 
                                        $family_medical_history, $lifestyle_info);
                        $stmt->execute();
                        $profile_id = $db->insert_id;
                    }
                    
                    // Delete existing chronic conditions
                    $stmt = $db->prepare("DELETE FROM citizen_chronic_conditions WHERE citizen_id = ?");
                    $stmt->bind_param("i", $citizen_id);
                    $stmt->execute();
                    
                    // Insert new chronic conditions
                    if (!empty($chronic_conditions)) {
                        $stmt = $db->prepare("INSERT INTO citizen_chronic_conditions 
                                            (citizen_id, condition_id, diagnosed_date, notes) 
                                            VALUES (?, ?, NOW(), '')");
                        
                        foreach ($chronic_conditions as $condition_id) {
                            $stmt->bind_param("ii", $citizen_id, $condition_id);
                            $stmt->execute();
                        }
                    }
                    
                    // Commit transaction
                    $db->commit();
                    
                    // Log the activity
                    log_activity($citizen_id, 'health_profile_update', 'Updated health profile information');
                    
                    // Set success message
                    set_flash_message('success', 'Your health profile has been updated successfully.');
                    
                    // Redirect to health profile page
                    redirect('index.php?controller=citizen&action=health_profile');
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $db->rollback();
                    $errors[] = 'An error occurred while updating your health profile. Please try again.';
                }
                
                // Close database connection
                $stmt->close();
                $db->close();
            }
            
            // If there are errors, display them
            if (!empty($errors)) {
                // Set error message
                set_flash_message('error', implode('<br>', $errors));
                
                // Redirect back to health profile page
                redirect('index.php?controller=citizen&action=health_profile');
            }
        } else {
            // If not POST request, redirect to health profile page
            redirect('index.php?controller=citizen&action=health_profile');
        }
    }
    
    /**
     * Get health profile data
     * 
     * @param int $citizen_id Citizen ID
     * @return array Health profile data
     */
    private function get_health_profile($citizen_id) {
        $db = get_db_connection();
        
        // Get health profile
        $stmt = $db->prepare("SELECT * FROM citizen_health_profiles WHERE citizen_id = ?");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $health_profile = $result->fetch_assoc();
            
            // Get chronic conditions
            $stmt = $db->prepare("SELECT cc.*, c.name, c.description 
                                FROM citizen_chronic_conditions cc 
                                JOIN chronic_conditions c ON cc.condition_id = c.id 
                                WHERE cc.citizen_id = ?");
            $stmt->bind_param("i", $citizen_id);
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
                'blood_group' => '',
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
        
        return $health_profile;
    }
    
    /**
     * Get health summary data for dashboard
     * 
     * @param int $citizen_id Citizen ID
     * @return array Health summary data
     */
    private function get_health_summary($citizen_id) {
        $health_profile = $this->get_health_profile($citizen_id);
        
        // If health profile is empty, return null
        if (empty($health_profile['blood_group'])) {
            return null;
        }
        
        // Prepare health summary
        $health_summary = [
            'blood_group' => $health_profile['blood_group'],
            'height' => $health_profile['height'],
            'weight' => $health_profile['weight'],
            'bmi' => $health_profile['bmi'],
            'bmi_category' => $health_profile['bmi_category'],
            'chronic_conditions' => $health_profile['chronic_conditions']
        ];
        
        // Process allergies
        if (!empty($health_profile['allergies'])) {
            $health_summary['allergies'] = array_map('trim', explode(',', $health_profile['allergies']));
        } else {
            $health_summary['allergies'] = [];
        }
        
        return $health_summary;
    }
}