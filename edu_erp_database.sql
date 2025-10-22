-- Educational ERP System Database
-- Complete database structure with sample data
-- MySQL 8.x compatible

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS edu_erp_db;
CREATE DATABASE edu_erp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE edu_erp_db;

-- Create institutions table
CREATE TABLE institutions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(255) NOT NULL UNIQUE,
    type ENUM('preschool', 'school', 'college') NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    principal_name VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Create academic_years table
CREATE TABLE academic_years (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    institution_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_current BOOLEAN NOT NULL DEFAULT FALSE,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
);

-- Create users table
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    institution_id BIGINT UNSIGNED NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    mobile VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'student', 'parent', 'staff', 'teacher') NOT NULL,
    avatar VARCHAR(255) NULL,
    date_of_birth DATE NULL,
    gender ENUM('male', 'female', 'other') NULL,
    address TEXT NULL,
    phone VARCHAR(255) NULL,
    emergency_contact VARCHAR(255) NULL,
    status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
);

-- Create classes table
CREATE TABLE classes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    institution_id BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    section VARCHAR(255) NOT NULL DEFAULT 'A',
    level ENUM('playgroup', 'nursery', 'jr_kg', 'sr_kg', 'primary', 'middle', 'high', 'higher_secondary', 'undergraduate', 'postgraduate') NOT NULL,
    max_students INT NOT NULL DEFAULT 40,
    current_students INT NOT NULL DEFAULT 0,
    class_teacher_id VARCHAR(255) NULL,
    description TEXT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    UNIQUE KEY unique_class (institution_id, academic_year_id, name, section)
);

-- Create subjects table
CREATE TABLE subjects (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    institution_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    type ENUM('core', 'elective', 'activity') NOT NULL,
    max_marks INT NOT NULL DEFAULT 100,
    pass_marks INT NOT NULL DEFAULT 40,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
);

-- Create students table
CREATE TABLE students (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    institution_id BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    class_id BIGINT UNSIGNED NOT NULL,
    admission_number VARCHAR(255) NOT NULL UNIQUE,
    admission_date DATE NOT NULL,
    roll_number VARCHAR(255) NULL,
    parent_id BIGINT UNSIGNED NULL,
    father_name VARCHAR(255) NOT NULL,
    mother_name VARCHAR(255) NOT NULL,
    guardian_name VARCHAR(255) NULL,
    guardian_relation VARCHAR(255) NULL,
    blood_group VARCHAR(255) NULL,
    medical_conditions TEXT NULL,
    previous_school VARCHAR(255) NULL,
    transport_required VARCHAR(255) NOT NULL DEFAULT 'no',
    hostel_required VARCHAR(255) NOT NULL DEFAULT 'no',
    documents JSON NULL,
    remarks TEXT NULL,
    status ENUM('active', 'inactive', 'transferred', 'graduated') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_admission (institution_id, admission_number)
);

-- Create staff table
CREATE TABLE staff (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    institution_id BIGINT UNSIGNED NOT NULL,
    employee_id VARCHAR(255) NOT NULL UNIQUE,
    staff_type ENUM('teaching', 'non_teaching', 'admin') NOT NULL,
    designation VARCHAR(255) NOT NULL,
    department VARCHAR(255) NULL,
    joining_date DATE NOT NULL,
    basic_salary DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    qualifications JSON NULL,
    experience JSON NULL,
    bank_account VARCHAR(255) NULL,
    pan_number VARCHAR(255) NULL,
    aadhar_number VARCHAR(255) NULL,
    subjects JSON NULL,
    classes JSON NULL,
    leave_balance DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
    documents JSON NULL,
    remarks TEXT NULL,
    status ENUM('active', 'inactive', 'terminated') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_employee (institution_id, employee_id)
);

-- Create additional essential tables
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
);

CREATE TABLE roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY roles_name_guard_name_unique (name, guard_name)
);

CREATE TABLE permissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY permissions_name_guard_name_unique (name, guard_name)
);

CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    INDEX model_has_permissions_model_id_model_type_index (model_id, model_type),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (permission_id, model_id, model_type)
);

CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    INDEX model_has_roles_model_id_model_type_index (model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, model_id, model_type)
);

CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (permission_id, role_id)
);

-- Insert sample data
INSERT INTO institutions (id, name, code, type, address, phone, email, website, principal_name, status, created_at, updated_at) VALUES
(1, 'Bright Minds Preschool', 'BMP001', 'preschool', '123 Education Lane, Learning City', '+1234567890', 'info@brightminds.edu', 'https://brightminds.edu', 'Dr. Sarah Johnson', 'active', NOW(), NOW()),
(2, 'Excellence High School', 'EHS001', 'school', '456 Knowledge Street, Education City', '+1234567891', 'info@excellencehs.edu', 'https://excellencehs.edu', 'Prof. Michael Brown', 'active', NOW(), NOW()),
(3, 'Pioneer College', 'PC001', 'college', '789 University Avenue, Higher Ed City', '+1234567892', 'info@pioneercollege.edu', 'https://pioneercollege.edu', 'Dr. Emily Davis', 'active', NOW(), NOW());

INSERT INTO academic_years (id, institution_id, name, start_date, end_date, is_current, status, created_at, updated_at) VALUES
(1, 1, '2024-2025', '2024-04-01', '2025-03-31', 1, 'active', NOW(), NOW()),
(2, 2, '2024-2025', '2024-04-01', '2025-03-31', 1, 'active', NOW(), NOW()),
(3, 3, '2024-2025', '2024-04-01', '2025-03-31', 1, 'active', NOW(), NOW());

INSERT INTO roles (id, name, guard_name, created_at, updated_at) VALUES
(1, 'admin', 'web', NOW(), NOW()),
(2, 'student', 'web', NOW(), NOW()),
(3, 'parent', 'web', NOW(), NOW()),
(4, 'staff', 'web', NOW(), NOW()),
(5, 'teacher', 'web', NOW(), NOW());

INSERT INTO users (id, institution_id, first_name, last_name, email, mobile, password, user_type, status, created_at, updated_at) VALUES
(1, 1, 'Admin', 'Preschool', 'admin@brightminds.edu', '9999999999', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW(), NOW()),
(2, 2, 'Admin', 'School', 'admin@excellencehs.edu', '9999999998', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW(), NOW()),
(3, 3, 'Admin', 'College', 'admin@pioneercollege.edu', '9999999997', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', NOW(), NOW()),
(4, 2, 'John', 'Smith', 'john.smith@excellencehs.edu', '9876543210', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'active', NOW(), NOW()),
(5, 2, 'Jane', 'Doe', 'jane.doe@excellencehs.edu', '9876543211', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 'active', NOW(), NOW()),
(6, 2, 'Robert', 'Johnson', 'robert.johnson@example.com', '9876543212', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', 'active', NOW(), NOW()),
(7, 2, 'Alice', 'Johnson', 'alice.johnson@example.com', '9876543213', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', NOW(), NOW()),
(8, 2, 'David', 'Wilson', 'david.wilson@example.com', '9876543214', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', 'active', NOW(), NOW()),
(9, 2, 'Bob', 'Wilson', 'bob.wilson@example.com', '9876543215', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 'active', NOW(), NOW());

INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 2),
(1, 'App\\Models\\User', 3),
(5, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 9);

INSERT INTO classes (id, institution_id, academic_year_id, name, section, level, max_students, current_students, class_teacher_id, status, created_at, updated_at) VALUES
(1, 1, 1, 'Nursery', 'A', 'nursery', 25, 0, NULL, 'active', NOW(), NOW()),
(2, 1, 1, 'Jr. KG', 'A', 'jr_kg', 30, 0, NULL, 'active', NOW(), NOW()),
(3, 1, 1, 'Sr. KG', 'A', 'sr_kg', 30, 0, NULL, 'active', NOW(), NOW()),
(4, 2, 2, '1st Grade', 'A', 'primary', 40, 0, NULL, 'active', NOW(), NOW()),
(5, 2, 2, '5th Grade', 'A', 'primary', 40, 1, '4', 'active', NOW(), NOW()),
(6, 2, 2, '10th Grade', 'A', 'high', 35, 1, '5', 'active', NOW(), NOW()),
(7, 3, 3, 'Plus Two Science', 'A', 'higher_secondary', 45, 0, NULL, 'active', NOW(), NOW()),
(8, 3, 3, 'B.Sc. Computer Science', 'A', 'undergraduate', 50, 0, NULL, 'active', NOW(), NOW());

INSERT INTO subjects (id, institution_id, name, code, description, type, max_marks, pass_marks, status, created_at, updated_at) VALUES
(1, 2, 'Mathematics', 'MATH', NULL, 'core', 100, 40, 'active', NOW(), NOW()),
(2, 2, 'English', 'ENG', NULL, 'core', 100, 40, 'active', NOW(), NOW()),
(3, 2, 'Science', 'SCI', NULL, 'core', 100, 40, 'active', NOW(), NOW()),
(4, 2, 'Social Studies', 'SS', NULL, 'core', 100, 40, 'active', NOW(), NOW()),
(5, 3, 'Physics', 'PHY', NULL, 'core', 100, 40, 'active', NOW(), NOW()),
(6, 3, 'Chemistry', 'CHE', NULL, 'core', 100, 40, 'active', NOW(), NOW()),
(7, 3, 'Biology', 'BIO', NULL, 'core', 100, 40, 'active', NOW(), NOW()),
(8, 3, 'Computer Science', 'CS', NULL, 'core', 100, 40, 'active', NOW(), NOW());

INSERT INTO staff (id, user_id, institution_id, employee_id, staff_type, designation, department, joining_date, basic_salary, leave_balance, status, created_at, updated_at) VALUES
(1, 4, 2, 'EMP001', 'teaching', 'Mathematics Teacher', 'Mathematics', '2024-01-15', 50000.00, 0.00, 'active', NOW(), NOW()),
(2, 5, 2, 'EMP002', 'teaching', 'English Teacher', 'English', '2024-02-01', 48000.00, 0.00, 'active', NOW(), NOW());

INSERT INTO students (id, user_id, institution_id, academic_year_id, class_id, admission_number, admission_date, roll_number, parent_id, father_name, mother_name, blood_group, status, created_at, updated_at) VALUES
(1, 7, 2, 2, 5, 'ADM2024001', '2024-04-01', '001', 6, 'Robert Johnson', 'Susan Johnson', 'A+', 'active', NOW(), NOW()),
(2, 9, 2, 2, 6, 'ADM2024002', '2024-04-01', '001', 8, 'David Wilson', 'Lisa Wilson', 'B+', 'active', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;

-- Default password for all users is 'password'
-- Login credentials:
-- School Admin: mobile: 9999999998, password: password
-- College Admin: mobile: 9999999997, password: password  
-- Preschool Admin: mobile: 9999999999, password: password
-- Teacher (John): mobile: 9876543210, password: password
-- Teacher (Jane): mobile: 9876543211, password: password
-- Parent (Robert): mobile: 9876543212, password: password
-- Student (Alice): mobile: 9876543213, password: password
-- Parent (David): mobile: 9876543214, password: password
-- Student (Bob): mobile: 9876543215, password: password