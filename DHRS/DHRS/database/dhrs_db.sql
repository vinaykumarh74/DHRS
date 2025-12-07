-- Digital Health Record System Database Schema

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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create citizens table
CREATE TABLE citizens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') DEFAULT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    emergency_contact_name VARCHAR(100) DEFAULT NULL,
    emergency_contact_phone VARCHAR(15) DEFAULT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
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
    specialization VARCHAR(100) NOT NULL,
    qualification VARCHAR(255) NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    experience_years INT NOT NULL,
    clinic_address TEXT DEFAULT NULL,
    consultation_fee DECIMAL(10, 2) DEFAULT NULL,
    available_days SET('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    available_time_start TIME NOT NULL,
    available_time_end TIME NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
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
    profile_picture VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create medical_records table
CREATE TABLE medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    record_type ENUM('general', 'chronic', 'emergency', 'vaccination') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    record_date DATE NOT NULL,
    attachments VARCHAR(255) DEFAULT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Create appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled', 'no-show') NOT NULL DEFAULT 'scheduled',
    reason TEXT NOT NULL,
    notes TEXT DEFAULT NULL,
    is_telemedicine BOOLEAN DEFAULT FALSE,
    meeting_link VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create prescriptions table
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    citizen_id INT NOT NULL,
    doctor_id INT NOT NULL,
    diagnosis TEXT NOT NULL,
    notes TEXT DEFAULT NULL,
    follow_up_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Create prescription_medicines table
CREATE TABLE prescription_medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT NOT NULL,
    medicine_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    duration VARCHAR(100) NOT NULL,
    instructions TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
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
    prescription_id INT DEFAULT NULL,
    report_date DATE NOT NULL,
    lab_name VARCHAR(255) NOT NULL,
    technician_name VARCHAR(255) DEFAULT NULL,
    report_file VARCHAR(255) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL,
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE SET NULL
);

-- Create lab_report_results table
CREATE TABLE lab_report_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_report_id INT NOT NULL,
    lab_test_id INT NOT NULL,
    result VARCHAR(255) NOT NULL,
    is_abnormal BOOLEAN DEFAULT FALSE,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lab_report_id) REFERENCES lab_reports(id) ON DELETE CASCADE,
    FOREIGN KEY (lab_test_id) REFERENCES lab_tests(id) ON DELETE CASCADE
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
    dose_number INT NOT NULL DEFAULT 1,
    vaccination_date DATE NOT NULL,
    administered_by VARCHAR(255) DEFAULT NULL,
    administered_at VARCHAR(255) DEFAULT NULL,
    batch_number VARCHAR(100) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    certificate_file VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (vaccination_id) REFERENCES vaccinations(id) ON DELETE CASCADE
);

-- Create chronic_conditions table
CREATE TABLE chronic_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create citizen_chronic_conditions table
CREATE TABLE citizen_chronic_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id INT NOT NULL,
    chronic_condition_id INT NOT NULL,
    diagnosed_date DATE NOT NULL,
    diagnosed_by VARCHAR(255) DEFAULT NULL,
    severity ENUM('mild', 'moderate', 'severe') DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES citizens(id) ON DELETE CASCADE,
    FOREIGN KEY (chronic_condition_id) REFERENCES chronic_conditions(id) ON DELETE CASCADE
);

-- Create notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('appointment', 'prescription', 'lab_report', 'vaccination', 'system') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    related_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create audit_logs table
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(255) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INT DEFAULT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
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

-- Insert default admin user
INSERT INTO users (username, password, email, phone, role, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@dhrs.com', '9876543210', 'admin', 'active');

-- Insert admin details
INSERT INTO administrators (user_id, first_name, last_name, department, position) VALUES
(1, 'System', 'Administrator', 'IT', 'System Administrator');

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('system_name', 'Digital Health Record System', 'Name of the system'),
('system_version', '1.0.0', 'Current system version'),
('maintenance_mode', 'false', 'System maintenance mode'),
('appointment_slots', '30', 'Appointment slot duration in minutes'),
('max_appointments_per_day', '20', 'Maximum appointments per doctor per day'),
('enable_telemedicine', 'true', 'Enable telemedicine features'),
('enable_sms_notifications', 'true', 'Enable SMS notifications'),
('enable_email_notifications', 'true', 'Enable email notifications');

-- Insert some sample lab tests
INSERT INTO lab_tests (name, description, normal_range, unit) VALUES
('Complete Blood Count (CBC)', 'Measures several components and features of blood', NULL, NULL),
('Blood Glucose Fasting', 'Measures the amount of glucose in blood', '70-100', 'mg/dL'),
('Hemoglobin A1c', 'Measures average blood glucose for the past 3 months', '4.0-5.6', '%'),
('Lipid Panel', 'Measures cholesterol and triglycerides', NULL, NULL),
('Liver Function Test', 'Measures enzymes and proteins in liver', NULL, NULL),
('Kidney Function Test', 'Measures substances filtered by kidneys', NULL, NULL),
('Thyroid Function Test', 'Measures thyroid hormones', NULL, NULL),
('Vitamin D', 'Measures vitamin D level in blood', '30-100', 'ng/mL'),
('Vitamin B12', 'Measures vitamin B12 level in blood', '200-900', 'pg/mL'),
('COVID-19 RT-PCR', 'Test for detecting COVID-19 virus', 'Negative', NULL);

-- Insert some sample vaccinations
INSERT INTO vaccinations (name, description, recommended_age, doses_required) VALUES
('BCG', 'Bacillus Calmette-Guerin vaccine for tuberculosis', 'At birth', 1),
('Hepatitis B', 'Vaccine for Hepatitis B', 'At birth, 6 weeks, 14 weeks', 3),
('OPV', 'Oral Polio Vaccine', 'At birth, 6 weeks, 10 weeks, 14 weeks', 4),
('Rotavirus', 'Vaccine for Rotavirus', '6 weeks, 10 weeks, 14 weeks', 3),
('DPT', 'Diphtheria, Pertussis, and Tetanus vaccine', '6 weeks, 10 weeks, 14 weeks', 3),
('Measles', 'Vaccine for Measles', '9 months', 1),
('MMR', 'Measles, Mumps, and Rubella vaccine', '15 months, 5 years', 2),
('Typhoid', 'Vaccine for Typhoid', '2 years', 1),
('COVID-19', 'Vaccine for COVID-19', '18 years and above', 2),
('Influenza', 'Seasonal Flu Vaccine', 'Yearly', 1);

-- Insert some sample chronic conditions
INSERT INTO chronic_conditions (name, description) VALUES
('Diabetes Mellitus Type 2', 'A chronic condition affecting the way the body processes blood sugar (glucose)'),
('Hypertension', 'Abnormally high blood pressure in the arteries'),
('Asthma', 'A condition in which airways narrow and swell and may produce extra mucus'),
('Chronic Obstructive Pulmonary Disease (COPD)', 'A chronic inflammatory lung disease that causes obstructed airflow from the lungs'),
('Coronary Artery Disease', 'Damage or disease in the heart major blood vessels'),
('Chronic Kidney Disease', 'A condition characterized by a gradual loss of kidney function over time'),
('Rheumatoid Arthritis', 'An inflammatory disorder affecting many joints, including those in the hands and feet'),
('Hypothyroidism', 'A condition in which the thyroid gland doesn\'t produce enough thyroid hormone'),
('Epilepsy', 'A central nervous system disorder in which brain activity becomes abnormal, causing seizures'),
('Depression', 'A mental health disorder characterized by persistently depressed mood or loss of interest in activities');