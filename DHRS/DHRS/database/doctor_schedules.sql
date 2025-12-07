-- Doctor Schedules Table

USE dhrs_db;

-- Create doctor_schedules table
CREATE TABLE IF NOT EXISTS doctor_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 0,
    start_time TIME DEFAULT NULL,
    end_time TIME DEFAULT NULL,
    max_appointments INT NOT NULL DEFAULT 10,
    appointment_duration INT NOT NULL DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_doctor_day (doctor_id, day_of_week)
);