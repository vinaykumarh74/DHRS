<?php
/**
 * Appointment Controller
 * 
 * This controller handles all appointment-related functionality including
 * booking, viewing, canceling, rescheduling, and completing appointments.
 */

class AppointmentController {
    /**
     * Display appointment booking form
     */
    public function book() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to book appointments.');
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
        
        // Get available doctors (allow active doctors with verified or pending status)
        $stmt = $db->prepare("SELECT d.*, u.email, u.phone 
                            FROM doctors d 
                            JOIN users u ON d.user_id = u.id 
                            WHERE u.status = 'active' AND d.verification_status IN ('verified','pending')
                            ORDER BY d.first_name, d.last_name");
        $stmt->execute();
        $result = $stmt->get_result();
        $doctors = [];
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        
        // Get citizen's existing appointments to check for conflicts
        $stmt = $db->prepare("SELECT appointment_date, appointment_time 
                            FROM appointments 
                            WHERE citizen_id = ? AND status IN ('confirmed', 'pending')
                            AND appointment_date >= CURDATE()");
        $stmt->bind_param("i", $citizen_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing_appointments = [];
        while ($row = $result->fetch_assoc()) {
            $existing_appointments[] = $row;
        }
        
        $stmt->close();
        $db->close();
        
        // Include the booking view
        include('views/appointment/book.php');
    }
    
    /**
     * Process appointment booking
     */
    public function process_booking() {
        // Check if user is logged in and is a citizen
        if (!is_logged_in() || $_SESSION['role'] !== 'citizen') {
            set_flash_message('error', 'You must be logged in as a citizen to book appointments.');
            redirect('index.php?controller=auth&action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?controller=appointment&action=book');
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Get the citizen_id from the citizens table based on user_id
        $db = get_db_connection();
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
        
        $doctor_id = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
        $appointment_date = isset($_POST['appointment_date']) ? sanitize_input($_POST['appointment_date']) : '';
        $appointment_time = isset($_POST['appointment_time']) ? sanitize_input($_POST['appointment_time']) : '';
        $reason = isset($_POST['reason']) ? sanitize_input($_POST['reason']) : '';
        $preferred_consultation_type = isset($_POST['consultation_type']) ? sanitize_input($_POST['consultation_type']) : 'in-person';
        
        // Validate inputs
        $errors = [];
        
        if (empty($doctor_id)) {
            $errors[] = 'Please select a doctor.';
        }
        
        if (empty($appointment_date)) {
            $errors[] = 'Please select an appointment date.';
        } elseif (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Appointment date cannot be in the past.';
        }
        
        if (empty($appointment_time)) {
            $errors[] = 'Please select an appointment time.';
        }
        
        if (empty($reason)) {
            $errors[] = 'Please provide a reason for the appointment.';
        }
        
        if (empty($errors)) {
            $db = get_db_connection();
            
            // Check if doctor is available on the selected date and time
            $stmt = $db->prepare("SELECT ds.* FROM doctor_schedules ds 
                                JOIN doctors d ON ds.doctor_id = d.id 
                                WHERE d.id = ? AND ds.day_of_week = LOWER(DAYNAME(?)) 
                                AND ds.is_available = 1");
            $stmt->bind_param("is", $doctor_id, $appointment_date);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $errors[] = 'Doctor is not available on the selected date.';
            } else {
                $schedule = $result->fetch_assoc();
                
                // Check if time is within doctor's available hours
                if ($appointment_time < $schedule['start_time'] || $appointment_time > $schedule['end_time']) {
                    $errors[] = 'Appointment time is outside doctor\'s available hours.';
                }
                
                // Check if slot is already booked
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments 
                                    WHERE doctor_id = ? AND appointment_date = ? 
                                    AND appointment_time = ? AND status IN ('confirmed', 'pending')");
                $stmt->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    $errors[] = 'This time slot is already booked. Please select another time.';
                }
                
                // Check for conflicts with existing appointments
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM appointments 
                                    WHERE citizen_id = ? AND appointment_date = ? 
                                    AND status IN ('confirmed', 'pending')");
                $stmt->bind_param("is", $citizen_id, $appointment_date);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    $errors[] = 'You already have an appointment on this date.';
                }
            }
            
            if (empty($errors)) {
                // Insert appointment
                $stmt = $db->prepare("INSERT INTO appointments 
                                    (citizen_id, doctor_id, appointment_date, appointment_time, 
                                    reason, consultation_type, status, created_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
                $stmt->bind_param("iissss", $citizen_id, $doctor_id, $appointment_date, 
                                $appointment_time, $reason, $preferred_consultation_type);
                
                if ($stmt->execute()) {
                    $appointment_id = $stmt->insert_id;
                    
                    // Send notification to doctor
                    $this->send_appointment_notification($doctor_id, $appointment_id, 'new_appointment');
                    
                    // Log the activity
                    log_activity($citizen_id, 'appointment_booked', "Booked appointment with doctor ID: $doctor_id");
                    
                    set_flash_message('success', 'Appointment booked successfully! The doctor will confirm your appointment soon.');
                    redirect('index.php?controller=citizen&action=dashboard');
                } else {
                    $errors[] = 'Failed to book appointment. Please try again.';
                }
            }
            
            $stmt->close();
            $db->close();
        }
        
        if (!empty($errors)) {
            set_flash_message('error', implode('<br>', $errors));
            redirect('index.php?controller=appointment&action=book');
        }
    }
    
    /**
     * View appointment details
     */
    public function view() {
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to view appointments.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($appointment_id === 0) {
            set_flash_message('error', 'Invalid appointment ID.');
            redirect('index.php');
        }
        
        $db = get_db_connection();
        
        // Get appointment details
        $stmt = $db->prepare("SELECT a.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                            c.health_id, c.gender, c.date_of_birth,
                            CONCAT(d.first_name, ' ', d.last_name) as doctor_name, 
                            d.specialization, d.consultation_fee
                            FROM appointments a 
                            JOIN citizens c ON a.citizen_id = c.id 
                            JOIN doctors d ON a.doctor_id = d.id 
                            WHERE a.id = ?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Appointment not found.');
            redirect('index.php');
        }
        
        $appointment = $result->fetch_assoc();
        
        // Check if user has permission to view this appointment
        if ($_SESSION['role'] === 'citizen') {
            $stmt = $db->prepare("SELECT id FROM citizens WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $citizen = $result->fetch_assoc();
            
            if ($appointment['citizen_id'] != $citizen['id']) {
                set_flash_message('error', 'You do not have permission to view this appointment.');
                redirect('index.php');
            }
            $stmt->close();
        }
        
        if ($_SESSION['role'] === 'doctor') {
            $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();
            
            if ($appointment['doctor_id'] !== $doctor['id']) {
                set_flash_message('error', 'You do not have permission to view this appointment.');
                redirect('index.php');
            }
        }
        
        $stmt->close();
        $db->close();
        
        // Include the appointment view
        include('views/appointment/view.php');
    }
    
    /**
     * Cancel appointment
     */
    public function cancel() {
        if (!is_logged_in()) {
            set_flash_message('error', 'You must be logged in to cancel appointments.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        $reason = isset($_POST['reason']) ? sanitize_input($_POST['reason']) : '';
        
        if ($appointment_id === 0) {
            set_flash_message('error', 'Invalid appointment ID.');
            redirect('index.php');
        }
        
        $db = get_db_connection();
        
        // Get appointment details
        $stmt = $db->prepare("SELECT a.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name,
                            CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                            FROM appointments a 
                            JOIN citizens c ON a.citizen_id = c.id 
                            JOIN doctors d ON a.doctor_id = d.id 
                            WHERE a.id = ?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Appointment not found.');
            redirect('index.php');
        }
        
        $appointment = $result->fetch_assoc();
        
        // Check if user has permission to cancel this appointment
        if ($_SESSION['role'] === 'citizen') {
            $stmt = $db->prepare("SELECT id FROM citizens WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $citizen = $result->fetch_assoc();
            
            if ($appointment['citizen_id'] != $citizen['id']) {
                set_flash_message('error', 'You do not have permission to cancel this appointment.');
                redirect('index.php');
            }
            $stmt->close();
        }
        
        if ($_SESSION['role'] === 'doctor') {
            $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();
            
            if ($appointment['doctor_id'] !== $doctor['id']) {
                set_flash_message('error', 'You do not have permission to cancel this appointment.');
                redirect('index.php');
            }
        }
        
        // Check if appointment can be cancelled
        if ($appointment['status'] === 'cancelled') {
            set_flash_message('error', 'Appointment is already cancelled.');
            redirect('index.php?controller=appointment&action=view&id=' . $appointment_id);
        }
        
        if ($appointment['status'] === 'completed') {
            set_flash_message('error', 'Cannot cancel completed appointments.');
            redirect('index.php?controller=appointment&action=view&id=' . $appointment_id);
        }
        
        // Update appointment status
        $stmt = $db->prepare("UPDATE appointments SET 
                            status = 'cancelled', 
                            cancellation_reason = ?, 
                            cancelled_by = ?, 
                            cancelled_at = NOW() 
                            WHERE id = ?");
        $stmt->bind_param("sii", $reason, $_SESSION['user_id'], $appointment_id);
        
        if ($stmt->execute()) {
            // Send notification
            if ($_SESSION['role'] === 'citizen') {
                $this->send_appointment_notification($appointment['doctor_id'], $appointment_id, 'appointment_cancelled');
            } else {
                $this->send_appointment_notification($appointment['citizen_id'], $appointment_id, 'appointment_cancelled');
            }
            
            // Log the activity
            log_activity($_SESSION['user_id'], 'appointment_cancelled', "Cancelled appointment ID: $appointment_id");
            
            set_flash_message('success', 'Appointment cancelled successfully.');
            
            if ($_SESSION['role'] === 'citizen') {
                redirect('index.php?controller=citizen&action=dashboard');
            } else {
                redirect('index.php?controller=doctor&action=dashboard');
            }
        } else {
            set_flash_message('error', 'Failed to cancel appointment. Please try again.');
            redirect('index.php?controller=appointment&action=view&id=' . $appointment_id);
        }
        
        $stmt->close();
        $db->close();
    }
    
    /**
     * Complete appointment
     */
    public function complete() {
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'Only doctors can complete appointments.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        $diagnosis = isset($_POST['diagnosis']) ? sanitize_input($_POST['diagnosis']) : '';
        $treatment_notes = isset($_POST['treatment_notes']) ? sanitize_input($_POST['treatment_notes']) : '';
        $next_visit_date = isset($_POST['next_visit_date']) ? sanitize_input($_POST['next_visit_date']) : '';
        
        if ($appointment_id === 0) {
            set_flash_message('error', 'Invalid appointment ID.');
            redirect('index.php');
        }
        
        $db = get_db_connection();
        
        // Get appointment details
        $stmt = $db->prepare("SELECT a.* FROM appointments a 
                            JOIN doctors d ON a.doctor_id = d.id 
                            WHERE a.id = ? AND d.user_id = ?");
        $stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Appointment not found or you do not have permission.');
            redirect('index.php');
        }
        
        $appointment = $result->fetch_assoc();
        
        if ($appointment['status'] !== 'confirmed') {
            set_flash_message('error', 'Only confirmed appointments can be completed.');
            redirect('index.php?controller=appointment&action=view&id=' . $appointment_id);
        }
        
        // Update appointment status
        $stmt = $db->prepare("UPDATE appointments SET 
                            status = 'completed', 
                            diagnosis = ?, 
                            treatment_notes = ?, 
                            next_visit_date = ?, 
                            completed_at = NOW() 
                            WHERE id = ?");
        $stmt->bind_param("sssi", $diagnosis, $treatment_notes, $next_visit_date, $appointment_id);
        
        if ($stmt->execute()) {
            // Send notification to citizen
            $this->send_appointment_notification($appointment['citizen_id'], $appointment_id, 'appointment_completed');
            
            // Log the activity
            log_activity($_SESSION['user_id'], 'appointment_completed', "Completed appointment ID: $appointment_id");
            
            set_flash_message('success', 'Appointment completed successfully.');
            redirect('index.php?controller=doctor&action=dashboard');
        } else {
            set_flash_message('error', 'Failed to complete appointment. Please try again.');
            redirect('index.php?controller=appointment&action=view&id=' . $appointment_id);
        }
        
        $stmt->close();
        $db->close();
    }
    
    /**
     * Confirm appointment
     */
    public function confirm() {
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'Only doctors can confirm appointments.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        
        if ($appointment_id === 0) {
            set_flash_message('error', 'Invalid appointment ID.');
            redirect('index.php');
        }
        
        $db = get_db_connection();
        
        // Get appointment details
        $stmt = $db->prepare("SELECT a.* FROM appointments a 
                            JOIN doctors d ON a.doctor_id = d.id 
                            WHERE a.id = ? AND d.user_id = ? AND a.status = 'pending'");
        $stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Appointment not found or cannot be confirmed.');
            redirect('index.php');
        }
        
        $appointment = $result->fetch_assoc();
        
        // Update appointment status
        $stmt = $db->prepare("UPDATE appointments SET 
                            status = 'confirmed', 
                            confirmed_at = NOW() 
                            WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);
        
        if ($stmt->execute()) {
            // Send notification to citizen
            $this->send_appointment_notification($appointment['citizen_id'], $appointment_id, 'appointment_confirmed');
            
            // Log the activity
            log_activity($_SESSION['user_id'], 'appointment_confirmed', "Confirmed appointment ID: $appointment_id");
            
            set_flash_message('success', 'Appointment confirmed successfully.');
            redirect('index.php?controller=doctor&action=dashboard');
        } else {
            set_flash_message('error', 'Failed to confirm appointment. Please try again.');
            redirect('index.php?controller=appointment&action=view&id=' . $appointment_id);
        }
        
        $stmt->close();
        $db->close();
    }
    
    /**
     * Display all appointments for a doctor
     */
    public function doctor_appointments() {
        if (!is_logged_in() || $_SESSION['role'] !== 'doctor') {
            set_flash_message('error', 'You must be logged in as a doctor to view appointments.');
            redirect('index.php?controller=auth&action=login');
        }
        
        $user_id = $_SESSION['user_id'];
        $db = get_db_connection();
        
        // Get the doctor_id from the doctors table based on user_id
        $stmt = $db->prepare("SELECT id FROM doctors WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            set_flash_message('error', 'Doctor profile not found.');
            redirect('index.php?controller=doctor&action=profile');
        }
        
        $doctor = $result->fetch_assoc();
        $doctor_id = $doctor['id'];
        
        // Get all appointments for this doctor
        $stmt = $db->prepare("SELECT a.*, 
                            CONCAT(c.first_name, ' ', c.last_name) as citizen_name, 
                            c.health_id, c.gender, c.date_of_birth 
                            FROM appointments a 
                            JOIN citizens c ON a.citizen_id = c.id 
                            WHERE a.doctor_id = ? 
                            ORDER BY a.appointment_date DESC, a.appointment_time DESC");
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        
        $stmt->close();
        $db->close();
        
        // Include the view
        include('views/appointment/doctor_appointments.php');
    }
    
    /**
     * Send appointment notification
     */
    private function send_appointment_notification($recipient_id, $appointment_id, $type) {
        $db = get_db_connection();
        
        $messages = [
            'new_appointment' => 'You have a new appointment request.',
            'appointment_confirmed' => 'Your appointment has been confirmed.',
            'appointment_cancelled' => 'Your appointment has been cancelled.',
            'appointment_completed' => 'Your appointment has been completed.'
        ];
        
        $message = $messages[$type] ?? 'Appointment notification.';
        
        // Check if recipient_id is a doctor_id or citizen_id and get the corresponding user_id
        if ($_SESSION['role'] === 'citizen') {
            // If citizen is sending notification, recipient is a doctor
            $stmt = $db->prepare("SELECT user_id FROM doctors WHERE id = ?");
        } else {
            // If doctor is sending notification, recipient is a citizen
            $stmt = $db->prepare("SELECT user_id FROM citizens WHERE id = ?");
        }
        
        $stmt->bind_param("i", $recipient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            // Log error but don't stop execution
            error_log("Could not find user_id for recipient_id: $recipient_id in role: {$_SESSION['role']}");
            $stmt->close();
            $db->close();
            return;
        }
        
        $recipient = $result->fetch_assoc();
        $user_id = $recipient['user_id'];
        $stmt->close();
        
        $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type, related_id, created_at) 
                            VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("isssi", $user_id, $type, $message, $type, $appointment_id);
        $stmt->execute();
        
        $stmt->close();
        $db->close();
    }
}
