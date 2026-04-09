-- Medixa Healthcare Management System Database Schema
-- Run this in phpMyAdmin or MySQL

CREATE DATABASE IF NOT EXISTS medixa_db;
USE medixa_db;

-- Disable foreign key checks temporarily to avoid constraint errors during import
SET FOREIGN_KEY_CHECKS = 0;

-- Receptionists Table
CREATE TABLE IF NOT EXISTS receptionists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_name VARCHAR(255) NOT NULL,
    receptionist_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    aadhaar_number VARCHAR(12) UNIQUE DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender VARCHAR(10) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    mobile_number VARCHAR(10) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Doctors Table (Pre-populated with dummy data)
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_name VARCHAR(255) NOT NULL,
    doctor_id VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    aadhaar_number VARCHAR(12) UNIQUE DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender VARCHAR(10) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    mobile_number VARCHAR(10) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Demo Aadhaar Data Table
CREATE TABLE IF NOT EXISTS demo_aadhaar_data (
    aadhaar_number VARCHAR(12) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    mobile_number VARCHAR(10) DEFAULT NULL,
    scheme VARCHAR(50) DEFAULT NULL,
    photo_path VARCHAR(500) DEFAULT NULL
);

-- Patients Table
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aadhaar_number VARCHAR(12) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    date_of_birth DATE NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    mobile_number VARCHAR(10),
    scheme VARCHAR(50),
    photo_path VARCHAR(500) DEFAULT NULL,
    scheme_discount DECIMAL(5,2) DEFAULT 0,
    cause TEXT,
    assigned_doctor_id INT,
    specialization VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_doctor_id) REFERENCES doctors(id)
);

-- Patient Registrations Table
CREATE TABLE IF NOT EXISTS patient_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    receptionist_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'active',
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (receptionist_id) REFERENCES receptionists(id)
);

-- Doctor Pages Table
CREATE TABLE IF NOT EXISTS doctor_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    symptoms TEXT,
    cause TEXT,
    prescription TEXT,
    test TEXT,
    nurse_instructions TEXT,
    nurse_status VARCHAR(20) DEFAULT 'pending',
    summarize_text TEXT,
    is_admitted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);

-- Tests Master Table
CREATE TABLE IF NOT EXISTS tests_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_name VARCHAR(255) NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicines Master Table
CREATE TABLE IF NOT EXISTS medicines_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Tests Master Data (Use INSERT IGNORE to avoid duplicate key errors)
INSERT IGNORE INTO tests_master (test_name, cost, department) VALUES
('Blood', 300.00, 'Blood'),
('Urine', 200.00, 'Urine'),
('USG', 500.00, 'USG'),
('ECG', 400.00, 'ECG'),
('X-Ray', 350.00, 'X-Ray'),
('CT Scan', 2000.00, 'CT Scan'),
('MRI', 3000.00, 'MRI'),
('Echo', 800.00, 'Echo'),
('Chest X-Ray', 400.00, 'X-Ray'),
('Doppler Scan', 600.00, 'USG'),
('Electrolytes (Na, K, Cl)', 300.00, 'Blood'),
('PT/INR', 250.00, 'Blood'),
('Lipid Profile', 400.00, 'Blood'),
('Thyroid (T3, T4, TSH)', 500.00, 'Blood'),
('Urine Microscopy', 200.00, 'Urine'),
('Urine Culture', 300.00, 'Urine'),
('Pregnancy Test', 150.00, 'Urine');

-- Insert Medicines Master Data (Use INSERT IGNORE to avoid duplicate key errors)
INSERT IGNORE INTO medicines_master (medicine_name, dosage) VALUES
('Paracetamol', '500 mg / 1 g'),
('Ibuprofen', '400 mg'),
('Cetirizine', '10 mg'),
('Pantoprazole', '40 mg'),
('Azithromycin', '500 mg'),
('Amoxicillin', '500 mg'),
('Metformin', '500 mg'),
('Amlodipine', '5 mg'),
('Telmisartan', '40 mg'),
('Dolo', '650 mg'),
('Ceftriaxone', '1 g IV'),
('Tramadol', '50 mg IV/IM'),
('Avil (Pheniramine)', '25 mg IV'),
('Ondansetron', '4 mg IV'),
('Pantoprazole', '40 mg IV'),
('Dexamethasone', '4 mg IV'),
('Normal Saline (NS)', '1 litre'),
('Ringer Lactate (RL)', '1 litre'),
('Dextrose 5%', '500 ml'),
('DNS (Dextrose Normal Saline)', '500 ml / 1 litre'),
('Duolin Nebulization', '1 nebule'),
('Budesonide', '1 nebule');

-- Patient Reports Table
CREATE TABLE IF NOT EXISTS patient_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    summary TEXT,
    medicines TEXT,
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    finalized BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id)
);

-- Billing Table
CREATE TABLE IF NOT EXISTS billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    receptionist_id INT NOT NULL,
    item_type ENUM('test', 'consultation', 'medicine', 'other') DEFAULT 'test',
    test_name VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('paid', 'unpaid') DEFAULT 'unpaid',
    scheme VARCHAR(50),
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    subtotal DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (receptionist_id) REFERENCES receptionists(id)
);

-- Test Departments Table
CREATE TABLE IF NOT EXISTS test_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Test Records Table
CREATE TABLE IF NOT EXISTS test_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    test_department_id INT NOT NULL,
    test_name VARCHAR(255) NOT NULL,
    report_file_path VARCHAR(500),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending',
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (test_department_id) REFERENCES test_departments(id)
);

-- Nurses Table
CREATE TABLE IF NOT EXISTS nurses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nurse_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    aadhaar_number VARCHAR(12) UNIQUE DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender VARCHAR(10) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    mobile_number VARCHAR(10) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Nurse Prescriptions Table
CREATE TABLE IF NOT EXISTS nurse_prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    medicine_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    given_status BOOLEAN DEFAULT FALSE,
    given_at TIMESTAMP NULL,
    nurse_id INT,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (nurse_id) REFERENCES nurses(id)
);

-- Insert Demo Aadhaar Data (Use INSERT IGNORE to avoid duplicate key errors)
INSERT IGNORE INTO demo_aadhaar_data (aadhaar_number, name, date_of_birth, gender, address, mobile_number, scheme, photo_path) VALUES
('677597815450', 'Chandan Yeshi', '2004-08-16', 'Female', '123 Main Street, Belagavi, Karnataka - 591221', '9876543210', 'ayushman', 'uploads/patient_photos/677597815450.jpg'),
('884533122661', 'Vaishnavi Reddi', '2005-06-17', 'Female', '456 Park Avenue, Mumbai, Maharashtra - 400001', '9876543211', 'cmhs', 'uploads/patient_photos/884533122661.jpg'),
('896548763231', 'Amit Patel', '1992-11-30', 'Male', '789 MG Road, Delhi, Delhi - 110001', '9876543212', 'ayushman', 'uploads/patient_photos/896548763231.jpg'),
('857632764201', 'Sneha Reddy', '1988-03-18', 'Female', '321 Church Street, Hyderabad, Telangana - 500001', '9876543213', 'cmhs', 'uploads/patient_photos/857632764201.jpg'),
('450460139825', 'Vaishali Anadinni', '2004-07-02', 'Female', '654 Gandhi Nagar, Pune, Maharashtra - 411001', '9876543214', NULL, 'uploads/patient_photos/450460139825.jpg');

-- Insert Pre-populated Doctors (Use INSERT IGNORE to avoid duplicate key errors)
INSERT IGNORE INTO doctors (doctor_name, doctor_id, email, password, specialization) VALUES
('Dr. Rajesh Kumar', 'DOC001', 'rajesh.kumar@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cardiology'),
('Dr. Chandan Yeshi', 'DOC002', 'chandan.yeshi@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Neurology'),
('Dr. Vaishali Anadinni', 'DOC003', 'vaishali.anadinni@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Orthopedics'),
('Dr. Vaishnavi Reddy', 'DOC004', 'vaishnavi.reddy@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pediatrics'),
('Dr. Pruthvi Shegavi', 'DOC005', 'pruthvi.shegavi@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dermatology'),
('Dr. Anjali Mehta', 'DOC006', 'anjali.mehta@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ophthalmology'),
('Dr. Ravi Verma', 'DOC007', 'ravi.verma@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ENT'),
('Dr. Kavita Nair', 'DOC008', 'kavita.nair@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'General Medicine'),
('Dr. Sunil Desai', 'DOC009', 'sunil.desai@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gynecology'),
('Dr. Meera Joshi', 'DOC010', 'meera.joshi@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Psychiatry');

-- Insert Test Departments (Use INSERT IGNORE to avoid duplicate key errors)
INSERT IGNORE INTO test_departments (department_name, email, password) VALUES
('Blood', 'blood@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Urine', 'urine@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('USG', 'usg@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('ECG', 'ecg@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('X-Ray', 'xray@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('CT Scan', 'ctscan@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('MRI', 'mri@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert Sample Nurses (Use INSERT IGNORE to avoid duplicate key errors)
INSERT IGNORE INTO nurses (nurse_name, email, password) VALUES
('Nurse Sarah', 'nurse.sarah@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Nurse Priya', 'nurse.priya@medixa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Note: Default password for all users is 'password' (hashed with bcrypt)

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
