-- Education ERP Database Schema and Sample Data
-- This file contains the complete database structure and sample data

-- Create Database
CREATE DATABASE IF NOT EXISTS education_erp;
USE education_erp;

-- Set SQL mode and character set
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Create institutes table
CREATE TABLE `institutes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('preschool','school','college') NOT NULL,
  `code` varchar(255) NOT NULL UNIQUE,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `pincode` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institutes_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create users table
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `mobile` varchar(255) NOT NULL UNIQUE,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','staff','teacher','student','parent') NOT NULL,
  `employee_id` varchar(255) DEFAULT NULL UNIQUE,
  `profile_image` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_mobile_unique` (`mobile`),
  UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  KEY `users_institute_id_foreign` (`institute_id`),
  CONSTRAINT `users_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create classes table
CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `section` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT 30,
  `class_teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `annual_fee` decimal(10,2) DEFAULT 0.00,
  `description` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classes_institute_id_name_section_unique` (`institute_id`,`name`,`section`),
  KEY `classes_class_teacher_id_foreign` (`class_teacher_id`),
  CONSTRAINT `classes_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `classes_class_teacher_id_foreign` FOREIGN KEY (`class_teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create students table
CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `institute_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admission_number` varchar(255) NOT NULL UNIQUE,
  `admission_date` date NOT NULL,
  `roll_number` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `father_phone` varchar(255) DEFAULT NULL,
  `mother_phone` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(255) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `blood_group` varchar(255) DEFAULT NULL,
  `medical_history` text,
  `previous_school` varchar(255) DEFAULT NULL,
  `previous_percentage` decimal(5,2) DEFAULT NULL,
  `documents` json DEFAULT NULL,
  `special_notes` text,
  `transport_required` enum('yes','no') DEFAULT 'no',
  `hostel_required` enum('yes','no') DEFAULT 'no',
  `status` enum('active','inactive','passed_out','transferred') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_admission_number_unique` (`admission_number`),
  KEY `students_user_id_foreign` (`user_id`),
  KEY `students_institute_id_foreign` (`institute_id`),
  KEY `students_class_id_foreign` (`class_id`),
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create subjects table
CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text,
  `type` enum('theory','practical','both') DEFAULT 'theory',
  `max_marks` int(11) DEFAULT 100,
  `pass_marks` int(11) DEFAULT 35,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_institute_id_name_unique` (`institute_id`,`name`),
  CONSTRAINT `subjects_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create fee_structures table
CREATE TABLE `fee_structures` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `institute_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `fee_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `frequency` enum('monthly','quarterly','half_yearly','yearly','one_time') NOT NULL,
  `due_date` date NOT NULL,
  `late_fee` decimal(10,2) DEFAULT 0.00,
  `grace_period_days` int(11) DEFAULT 0,
  `description` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_structures_institute_id_foreign` (`institute_id`),
  KEY `fee_structures_class_id_foreign` (`class_id`),
  CONSTRAINT `fee_structures_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_structures_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create fee_payments table
CREATE TABLE `fee_payments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `fee_structure_id` bigint(20) UNSIGNED NOT NULL,
  `receipt_number` varchar(255) NOT NULL UNIQUE,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `late_fee` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('cash','cheque','bank_transfer','online','card') NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('pending','paid','partial','overdue','cancelled') DEFAULT 'pending',
  `remarks` text,
  `collected_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fee_payments_receipt_number_unique` (`receipt_number`),
  KEY `fee_payments_student_id_foreign` (`student_id`),
  KEY `fee_payments_fee_structure_id_foreign` (`fee_structure_id`),
  CONSTRAINT `fee_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_payments_fee_structure_id_foreign` FOREIGN KEY (`fee_structure_id`) REFERENCES `fee_structures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create attendances table
CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `institute_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','half_day') NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `remarks` text,
  `marked_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_user_id_date_unique` (`user_id`,`date`),
  KEY `attendances_institute_id_foreign` (`institute_id`),
  KEY `attendances_class_id_foreign` (`class_id`),
  KEY `attendances_marked_by_foreign` (`marked_by`),
  CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_institute_id_foreign` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_marked_by_foreign` FOREIGN KEY (`marked_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create personal_access_tokens table for Sanctum
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL UNIQUE,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for institutes
INSERT INTO `institutes` (`id`, `name`, `type`, `code`, `address`, `city`, `state`, `pincode`, `phone`, `email`, `website`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Little Stars Preschool', 'preschool', 'LSP', '123 School Street', 'Mumbai', 'Maharashtra', '400001', '+91-9876543210', 'admin@littlestars.edu', 'https://littlestars.edu', 'Premier preschool education', 'active', NOW(), NOW()),
(2, 'Bright Future School', 'school', 'BFS', '456 Education Lane', 'Delhi', 'Delhi', '110001', '+91-9876543211', 'admin@brightfuture.edu', 'https://brightfuture.edu', 'Quality school education from 1st to 10th', 'active', NOW(), NOW()),
(3, 'Excellence College', 'college', 'EC', '789 College Road', 'Bangalore', 'Karnataka', '560001', '+91-9876543212', 'admin@excellence.edu', 'https://excellence.edu', 'Higher education from 11th to PG', 'active', NOW(), NOW());

-- Insert sample users
INSERT INTO `users` (`id`, `institute_id`, `first_name`, `last_name`, `email`, `mobile`, `password`, `role`, `employee_id`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Super', 'Admin', 'superadmin@erp.com', '9999999999', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', NULL, 'active', NOW(), NOW()),
(2, 1, 'Preschool', 'Admin', 'admin@littlestars.edu', '9876543210', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 'active', NOW(), NOW()),
(3, 2, 'School', 'Admin', 'admin@brightfuture.edu', '9876543211', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 'active', NOW(), NOW()),
(4, 3, 'College', 'Admin', 'admin@excellence.edu', '9876543212', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 'active', NOW(), NOW()),
(5, 1, 'Sarah', 'Johnson', 'sarah.j@littlestars.edu', '9876543220', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'T1001', 'active', NOW(), NOW()),
(6, 2, 'John', 'Smith', 'john.s@brightfuture.edu', '9876543221', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'T1002', 'active', NOW(), NOW()),
(7, 3, 'Dr. Robert', 'Wilson', 'robert.w@excellence.edu', '9876543223', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'T1003', 'active', NOW(), NOW());

-- Insert sample classes
INSERT INTO `classes` (`id`, `institute_id`, `name`, `section`, `capacity`, `annual_fee`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Playgroup', NULL, 20, 50000.00, 'active', NOW(), NOW()),
(2, 1, 'Nursery', NULL, 20, 50000.00, 'active', NOW(), NOW()),
(3, 1, 'Jr. KG', NULL, 20, 50000.00, 'active', NOW(), NOW()),
(4, 1, 'Sr. KG', NULL, 20, 50000.00, 'active', NOW(), NOW()),
(5, 2, 'Class 1', 'A', 30, 75000.00, 'active', NOW(), NOW()),
(6, 2, 'Class 2', 'A', 30, 75000.00, 'active', NOW(), NOW()),
(7, 2, 'Class 3', 'A', 30, 75000.00, 'active', NOW(), NOW()),
(8, 2, 'Class 4', 'A', 30, 75000.00, 'active', NOW(), NOW()),
(9, 2, 'Class 5', 'A', 30, 75000.00, 'active', NOW(), NOW()),
(10, 3, 'Class 11', 'Science', 40, 100000.00, 'active', NOW(), NOW()),
(11, 3, 'Class 12', 'Science', 40, 100000.00, 'active', NOW(), NOW()),
(12, 3, 'BSc', 'Year 1', 40, 100000.00, 'active', NOW(), NOW());

-- Insert sample subjects
INSERT INTO `subjects` (`id`, `institute_id`, `name`, `code`, `type`, `max_marks`, `pass_marks`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'English', 'ENG', 'theory', 100, 35, 'active', NOW(), NOW()),
(2, 1, 'Math', 'MATH', 'theory', 100, 35, 'active', NOW(), NOW()),
(3, 1, 'Art & Craft', 'ART', 'practical', 100, 35, 'active', NOW(), NOW()),
(4, 2, 'English', 'ENG', 'theory', 100, 35, 'active', NOW(), NOW()),
(5, 2, 'Mathematics', 'MATH', 'theory', 100, 35, 'active', NOW(), NOW()),
(6, 2, 'Science', 'SCI', 'both', 100, 35, 'active', NOW(), NOW()),
(7, 3, 'Physics', 'PHY', 'both', 100, 35, 'active', NOW(), NOW()),
(8, 3, 'Chemistry', 'CHEM', 'both', 100, 35, 'active', NOW(), NOW()),
(9, 3, 'Mathematics', 'MATH', 'theory', 100, 35, 'active', NOW(), NOW());

-- Insert sample fee structures
INSERT INTO `fee_structures` (`id`, `institute_id`, `class_id`, `fee_type`, `amount`, `frequency`, `due_date`, `late_fee`, `grace_period_days`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Tuition Fee', 4166.67, 'monthly', DATE_ADD(CURDATE(), INTERVAL 10 DAY), 500.00, 5, 'Monthly tuition fee', 'active', NOW(), NOW()),
(2, 2, 5, 'Tuition Fee', 6250.00, 'monthly', DATE_ADD(CURDATE(), INTERVAL 10 DAY), 500.00, 5, 'Monthly tuition fee', 'active', NOW(), NOW()),
(3, 3, 10, 'Tuition Fee', 8333.33, 'monthly', DATE_ADD(CURDATE(), INTERVAL 10 DAY), 500.00, 5, 'Monthly tuition fee', 'active', NOW(), NOW());

-- Update AUTO_INCREMENT values
ALTER TABLE `institutes` AUTO_INCREMENT = 4;
ALTER TABLE `users` AUTO_INCREMENT = 8;
ALTER TABLE `classes` AUTO_INCREMENT = 13;
ALTER TABLE `subjects` AUTO_INCREMENT = 10;
ALTER TABLE `fee_structures` AUTO_INCREMENT = 4;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Default login credentials:
-- Super Admin: superadmin@erp.com / password123
-- Preschool Admin: admin@littlestars.edu / password123  
-- School Admin: admin@brightfuture.edu / password123
-- College Admin: admin@excellence.edu / password123
-- All passwords are hashed with bcrypt