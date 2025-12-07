-- Updated Digital Health Record System Database Schema
-- This file contains the complete schema needed for the DHRS system

-- Drop database if exists (be careful with this in production)
DROP DATABASE IF EXISTS dhrs_db;

-- Create database
CREATE DATABASE dhrs_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE dhrs_db;

-- Create users table (for all user types)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    role ENUM('citizen', 'doctor', 'admin') NOT NULL,
    status ENUM('active', 'inactive', 'pending') NOT NULL DEFAULT 'pending',
    otp VARCHAR(6) DEFAULT NULL,
    otp_expiry DATETIME DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_token_expiry DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add index for reset token
CREATE INDEX idx_reset_token ON users(reset_token);

-- Create citizens table
CREATE TABLE citizens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    health_id VARCHAR(20) UNIQUE NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    emergency_contact_name VARCHAR(100) DEFAULT NULL,
    emergency_contact_phone VARCHAR(15) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create doctors table
CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    qualification VARCHAR(255) NOT NULL,
    experience_years INT NOT NULL,
    bio TEXT DEFAULT NULL,
    consultation_fee DECIMAL(10, 2) DEFAULT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create administrators table
CREATE TABLE administrators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    department VARCHAR(100) DEFAULT NULL,
    position VARCHAR(100) DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create doctor_schedules table
CREATE TABLE doctor_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    start_time TIME DEFAULT NULL,
    end_time TIME DEFAULT NULL,
    max_appointments INT DEFAULT 0,
    appointment_duration INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_doctor_day (doctor_id, day_of_week)
);

-- Create appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT NOT NULL,
    consultation_type ENUM('in-person', 'telemedicine', 'home-visit') DEFAULT 'in-person',
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    diagnosis TEXT DEFAULT NULL,
    treatment_notes TEXT DEFAULT NULL,
    next_visit_date DATE DEFAULT NULL,
    cancellation_reason TEXT DEFAULT NULL,
    cancelled_by INT DEFAULT NULL,
    cancelled_at DATETIME DEFAULT NULL,
    confirmed_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (cancelled_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Create prescriptions table
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    citizen_id INT NOT NULL,
    doctor_id INT NOT NULL,
    diagnosis TEXT NOT NULL,
    instructions TEXT NOT NULL,
    follow_up_date DATE DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create prescription_medications table
CREATE TABLE prescription_medications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT NOT NULL,
    medication_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    duration VARCHAR(100) NOT NULL,
    special_instructions TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
);

-- Create citizen_health_profiles table
CREATE TABLE citizen_health_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    height DECIMAL(5,2) DEFAULT NULL COMMENT 'Height in cm',
    weight DECIMAL(5,2) DEFAULT NULL COMMENT 'Weight in kg',
    allergies TEXT DEFAULT NULL,
    current_medications TEXT DEFAULT NULL,
    family_medical_history TEXT DEFAULT NULL,
    lifestyle_info TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    UNIQUE KEY unique_citizen_health (citizen_id)
);

-- Create chronic_conditions table
CREATE TABLE chronic_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create citizen_chronic_conditions table
CREATE TABLE citizen_chronic_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    condition_id INT NOT NULL,
    diagnosed_date DATE NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (condition_id) REFERENCES chronic_conditions(id) ON DELETE CASCADE
);

-- Create medical_records table
CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    doctor_id INT NOT NULL,
    record_type ENUM('general', 'chronic', 'emergency', 'vaccination') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    record_date DATE NOT NULL,
    attachments VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create lab_tests table
CREATE TABLE lab_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    normal_range VARCHAR(255) DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create lab_reports table
CREATE TABLE lab_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    doctor_id INT DEFAULT NULL,
    test_id INT NOT NULL,
    report_date DATE NOT NULL,
    lab_name VARCHAR(255) NOT NULL,
    technician_name VARCHAR(255) DEFAULT NULL,
    report_file VARCHAR(255) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL,
    FOREIGN KEY (test_id) REFERENCES lab_tests(id) ON DELETE CASCADE
);

-- Create vaccinations table
CREATE TABLE vaccinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    recommended_age VARCHAR(100) DEFAULT NULL,
    doses_required INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create citizen_vaccinations table
CREATE TABLE citizen_vaccinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    vaccination_id INT NOT NULL,
    dose_number INT NOT NULL,
    vaccination_date DATE NOT NULL,
    next_due_date DATE DEFAULT NULL,
    administered_by VARCHAR(255) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (vaccination_id) REFERENCES vaccinations(id) ON DELETE CASCADE
);

-- Create notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    related_id INT DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create system_settings table
CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create audit_logs table
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(100) NOT NULL,
    record_id INT DEFAULT NULL,
    old_values TEXT DEFAULT NULL,
    new_values TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create activity_logs table
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample data for chronic conditions
INSERT INTO chronic_conditions (name, description, category) VALUES
('Diabetes Type 2', 'A chronic condition affecting how your body metabolizes glucose', 'Endocrine'),
('Hypertension', 'High blood pressure that can lead to serious health problems', 'Cardiovascular'),
('Asthma', 'A condition that affects the airways in the lungs', 'Respiratory'),
('Arthritis', 'Inflammation and stiffness in the joints', 'Musculoskeletal'),
('Depression', 'A mood disorder that causes persistent feelings of sadness', 'Mental Health');

-- Insert sample data for lab tests
INSERT INTO lab_tests (name, description, normal_range, unit) VALUES
('Complete Blood Count (CBC)', 'Measures various components of blood', '4.5-11.0', 'K/µL'),
('Hemoglobin', 'Measures oxygen-carrying protein in red blood cells', '12-16', 'g/dL'),
('Blood Glucose (Fasting)', 'Measures blood sugar levels after fasting', '70-100', 'mg/dL'),
('Cholesterol Total', 'Measures total cholesterol levels', '<200', 'mg/dL'),
('Blood Pressure', 'Measures systolic and diastolic pressure', '120/80', 'mmHg');

-- Insert sample data for vaccinations
INSERT INTO vaccinations (name, description, recommended_age, doses_required) VALUES
('Influenza', 'Annual flu vaccine', '6 months and older', 1),
('Tetanus, Diphtheria, Pertussis (Tdap)', 'Protection against three serious diseases', '11-64 years', 1),
('Pneumococcal', 'Protection against pneumococcal disease', '65 years and older', 1),
('Shingles', 'Protection against shingles', '50 years and older', 2),
('COVID-19', 'Protection against COVID-19', '12 years and older', 2);

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('site_name', 'Digital Health Record System', 'Name of the healthcare system'),
('admin_email', 'admin@dhrs.com', 'Primary administrator email address'),
('maintenance_mode', 'false', 'Whether the system is in maintenance mode'),
('max_file_size', '5242880', 'Maximum file upload size in bytes (5MB)'),
('session_timeout', '3600', 'Session timeout in seconds (1 hour)');

-- Create indexes for better performance
CREATE INDEX idx_appointments_citizen_date ON appointments(citizen_id, appointment_date);
CREATE INDEX idx_appointments_doctor_date ON appointments(doctor_id, appointment_date);
CREATE INDEX idx_appointments_status ON appointments(status);
CREATE INDEX idx_prescriptions_citizen ON prescriptions(citizen_id);
CREATE INDEX idx_prescriptions_doctor ON prescriptions(doctor_id);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);
CREATE INDEX idx_activity_logs_user ON activity_logs(user_id);
CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
