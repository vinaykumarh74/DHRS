-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2025 at 11:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dhrs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE `administrators` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `reason` text NOT NULL,
  `consultation_type` enum('in-person','telemedicine','home-visit') DEFAULT 'in-person',
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `diagnosis` text DEFAULT NULL,
  `treatment_notes` text DEFAULT NULL,
  `next_visit_date` date DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_by` int(11) DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'user', 1, NULL, 'User logged in: admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 10:35:05'),
(2, 1, 'logout', 'user', 1, NULL, 'User logged out: admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 10:35:18'),
(3, 1, 'login', 'user', 1, NULL, 'User logged in: admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 10:37:22'),
(4, 1, 'user_activated', 'admin', 1, NULL, 'Admin activated user: testone (ID: 2)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 10:37:33'),
(5, 1, 'logout', 'user', 1, NULL, 'User logged out: admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 10:37:43'),
(6, 2, 'login', 'user', 2, NULL, 'User logged in: testone', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', '2025-10-26 10:37:55');

-- --------------------------------------------------------

--
-- Table structure for table `chronic_conditions`
--

CREATE TABLE `chronic_conditions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chronic_conditions`
--

INSERT INTO `chronic_conditions` (`id`, `name`, `description`, `category`, `created_at`, `updated_at`) VALUES
(1, 'Diabetes Type 2', 'A chronic condition affecting how your body metabolizes glucose', 'Endocrine', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(2, 'Hypertension', 'High blood pressure that can lead to serious health problems', 'Cardiovascular', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(3, 'Asthma', 'A condition that affects the airways in the lungs', 'Respiratory', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(4, 'Arthritis', 'Inflammation and stiffness in the joints', 'Musculoskeletal', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(5, 'Depression', 'A mood disorder that causes persistent feelings of sadness', 'Mental Health', '2025-10-26 10:12:51', '2025-10-26 10:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `citizens`
--

CREATE TABLE `citizens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `health_id` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(15) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `citizens`
--

INSERT INTO `citizens` (`id`, `user_id`, `first_name`, `last_name`, `date_of_birth`, `gender`, `health_id`, `address`, `city`, `state`, `postal_code`, `emergency_contact_name`, `emergency_contact_phone`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 2, 'SAYYAD', 'SADRUDDIN', '1900-01-01', 'other', 'CIT2025000002', 'Address not provided', 'City not provided', 'State not provided', '000000', NULL, NULL, NULL, '2025-10-26 10:36:20', '2025-10-26 10:36:20');

-- --------------------------------------------------------

--
-- Table structure for table `citizen_chronic_conditions`
--

CREATE TABLE `citizen_chronic_conditions` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `condition_id` int(11) NOT NULL,
  `diagnosed_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `citizen_health_profiles`
--

CREATE TABLE `citizen_health_profiles` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `height` decimal(5,2) DEFAULT NULL COMMENT 'Height in cm',
  `weight` decimal(5,2) DEFAULT NULL COMMENT 'Weight in kg',
  `allergies` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `family_medical_history` text DEFAULT NULL,
  `lifestyle_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `citizen_vaccinations`
--

CREATE TABLE `citizen_vaccinations` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `vaccination_id` int(11) NOT NULL,
  `dose_number` int(11) NOT NULL,
  `vaccination_date` date NOT NULL,
  `next_due_date` date DEFAULT NULL,
  `administered_by` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `qualification` varchar(255) NOT NULL,
  `experience_years` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedules`
--

CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `max_appointments` int(11) DEFAULT 0,
  `appointment_duration` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_reports`
--

CREATE TABLE `lab_reports` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `test_id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `lab_name` varchar(255) NOT NULL,
  `technician_name` varchar(255) DEFAULT NULL,
  `report_file` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `normal_range` varchar(255) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`id`, `name`, `description`, `normal_range`, `unit`, `created_at`, `updated_at`) VALUES
(1, 'Complete Blood Count (CBC)', 'Measures various components of blood', '4.5-11.0', 'K/µL', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(2, 'Hemoglobin', 'Measures oxygen-carrying protein in red blood cells', '12-16', 'g/dL', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(3, 'Blood Glucose (Fasting)', 'Measures blood sugar levels after fasting', '70-100', 'mg/dL', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(4, 'Cholesterol Total', 'Measures total cholesterol levels', '<200', 'mg/dL', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(5, 'Blood Pressure', 'Measures systolic and diastolic pressure', '120/80', 'mmHg', '2025-10-26 10:12:51', '2025-10-26 10:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `record_type` enum('general','chronic','emergency','vaccination') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `record_date` date NOT NULL,
  `attachments` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `instructions` text NOT NULL,
  `follow_up_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_medications`
--

CREATE TABLE `prescription_medications` (
  `id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `medication_name` varchar(255) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `duration` varchar(100) NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Digital Health Record System', 'Name of the healthcare system', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(2, 'admin_email', 'admin@dhrs.com', 'Primary administrator email address', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(3, 'maintenance_mode', 'false', 'Whether the system is in maintenance mode', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(4, 'max_file_size', '5242880', 'Maximum file upload size in bytes (5MB)', '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(5, 'session_timeout', '3600', 'Session timeout in seconds (1 hour)', '2025-10-26 10:12:51', '2025-10-26 10:12:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `role` enum('citizen','doctor','admin') NOT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'pending',
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `role`, `status`, `otp`, `otp_expiry`, `reset_token`, `reset_token_expiry`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$NmXXvH9ppItNGbWxtiYTYO.2rYPFuPAGo7OxhtE/Pd1M7QFKgrDvO', 'admin@dhrs.com', '9999999999', 'admin', 'active', NULL, NULL, NULL, NULL, '2025-10-26 10:34:58', '2025-10-26 10:34:58'),
(2, 'testone', '$2y$10$iS9a91mAbokjO1UoO3xf5equTBMKemYbqoVk1hpIDGzWmqINjO3fa', 'sayyadkhajasadruddin22@gmail.com', '9036896676', 'citizen', 'active', '750991', '2025-10-26 16:21:20', NULL, NULL, '2025-10-26 10:36:20', '2025-10-26 10:37:33');

-- --------------------------------------------------------

--
-- Table structure for table `vaccinations`
--

CREATE TABLE `vaccinations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `recommended_age` varchar(100) DEFAULT NULL,
  `doses_required` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vaccinations`
--

INSERT INTO `vaccinations` (`id`, `name`, `description`, `recommended_age`, `doses_required`, `created_at`, `updated_at`) VALUES
(1, 'Influenza', 'Annual flu vaccine', '6 months and older', 1, '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(2, 'Tetanus, Diphtheria, Pertussis (Tdap)', 'Protection against three serious diseases', '11-64 years', 1, '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(3, 'Pneumococcal', 'Protection against pneumococcal disease', '65 years and older', 1, '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(4, 'Shingles', 'Protection against shingles', '50 years and older', 2, '2025-10-26 10:12:51', '2025-10-26 10:12:51'),
(5, 'COVID-19', 'Protection against COVID-19', '12 years and older', 2, '2025-10-26 10:12:51', '2025-10-26 10:12:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_logs_user` (`user_id`);

--
-- Indexes for table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cancelled_by` (`cancelled_by`),
  ADD KEY `idx_appointments_citizen_date` (`citizen_id`,`appointment_date`),
  ADD KEY `idx_appointments_doctor_date` (`doctor_id`,`appointment_date`),
  ADD KEY `idx_appointments_status` (`status`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_logs_user` (`user_id`);

--
-- Indexes for table `chronic_conditions`
--
ALTER TABLE `chronic_conditions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `citizens`
--
ALTER TABLE `citizens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `health_id` (`health_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `citizen_chronic_conditions`
--
ALTER TABLE `citizen_chronic_conditions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_id` (`citizen_id`),
  ADD KEY `condition_id` (`condition_id`);

--
-- Indexes for table `citizen_health_profiles`
--
ALTER TABLE `citizen_health_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_citizen_health` (`citizen_id`);

--
-- Indexes for table `citizen_vaccinations`
--
ALTER TABLE `citizen_vaccinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_id` (`citizen_id`),
  ADD KEY `vaccination_id` (`vaccination_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_doctor_day` (`doctor_id`,`day_of_week`);

--
-- Indexes for table `lab_reports`
--
ALTER TABLE `lab_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_id` (`citizen_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `citizen_id` (`citizen_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`is_read`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `idx_prescriptions_citizen` (`citizen_id`),
  ADD KEY `idx_prescriptions_doctor` (`doctor_id`);

--
-- Indexes for table `prescription_medications`
--
ALTER TABLE `prescription_medications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_id` (`prescription_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_reset_token` (`reset_token`);

--
-- Indexes for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chronic_conditions`
--
ALTER TABLE `chronic_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `citizens`
--
ALTER TABLE `citizens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `citizen_chronic_conditions`
--
ALTER TABLE `citizen_chronic_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `citizen_health_profiles`
--
ALTER TABLE `citizen_health_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `citizen_vaccinations`
--
ALTER TABLE `citizen_vaccinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_reports`
--
ALTER TABLE `lab_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription_medications`
--
ALTER TABLE `prescription_medications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vaccinations`
--
ALTER TABLE `vaccinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `administrators`
--
ALTER TABLE `administrators`
  ADD CONSTRAINT `administrators_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `citizens`
--
ALTER TABLE `citizens`
  ADD CONSTRAINT `citizens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `citizen_chronic_conditions`
--
ALTER TABLE `citizen_chronic_conditions`
  ADD CONSTRAINT `citizen_chronic_conditions_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `citizen_chronic_conditions_ibfk_2` FOREIGN KEY (`condition_id`) REFERENCES `chronic_conditions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `citizen_health_profiles`
--
ALTER TABLE `citizen_health_profiles`
  ADD CONSTRAINT `citizen_health_profiles_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `citizen_vaccinations`
--
ALTER TABLE `citizen_vaccinations`
  ADD CONSTRAINT `citizen_vaccinations_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `citizen_vaccinations_ibfk_2` FOREIGN KEY (`vaccination_id`) REFERENCES `vaccinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD CONSTRAINT `doctor_schedules_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lab_reports`
--
ALTER TABLE `lab_reports`
  ADD CONSTRAINT `lab_reports_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lab_reports_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `lab_reports_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `lab_tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`citizen_id`) REFERENCES `citizens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_medications`
--
ALTER TABLE `prescription_medications`
  ADD CONSTRAINT `prescription_medications_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
