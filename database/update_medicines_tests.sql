-- Update Medicines Master Table with new list
DELETE FROM medicines_master;

INSERT INTO medicines_master (medicine_name, dosage) VALUES
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

-- Update Tests Master Table with new list
DELETE FROM tests_master;

INSERT INTO tests_master (test_name, cost, department) VALUES
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

-- Update all test departments password to "password" (bcrypt hash)
UPDATE test_departments SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE password IS NOT NULL;

-- Update all nurses password to "password" (bcrypt hash)
UPDATE nurses SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE password IS NOT NULL;

-- Note: The password hash above is for "password" - all test departments and nurses can login with password "password"

