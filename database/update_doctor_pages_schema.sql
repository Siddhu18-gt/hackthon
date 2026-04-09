-- Update doctor_pages table to add symptoms and nurse_status columns
ALTER TABLE doctor_pages 
ADD COLUMN IF NOT EXISTS symptoms TEXT AFTER doctor_id,
ADD COLUMN IF NOT EXISTS nurse_status VARCHAR(20) DEFAULT 'pending' AFTER nurse_instructions;

-- Create tests_master table if not exists
CREATE TABLE IF NOT EXISTS tests_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_name VARCHAR(255) NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create medicines_master table if not exists
CREATE TABLE IF NOT EXISTS medicines_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Tests Master Data (only if table is empty)
INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'Blood Test' as test_name, 300.00 as cost, 'Blood' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'Blood Test')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'Urine Test' as test_name, 200.00 as cost, 'Urine' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'Urine Test')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'USG' as test_name, 500.00 as cost, 'USG' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'USG')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'ECG' as test_name, 400.00 as cost, 'ECG' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'ECG')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'X-Ray' as test_name, 350.00 as cost, 'X-Ray' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'X-Ray')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'CT Scan' as test_name, 2000.00 as cost, 'CT Scan' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'CT Scan')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'MRI' as test_name, 3000.00 as cost, 'MRI' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'MRI')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'CBC' as test_name, 250.00 as cost, 'Blood' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'CBC')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'Lipid Profile' as test_name, 400.00 as cost, 'Blood' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'Lipid Profile')
LIMIT 1;

INSERT INTO tests_master (test_name, cost, department) 
SELECT * FROM (SELECT 'Liver Function Test' as test_name, 450.00 as cost, 'Blood' as department) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM tests_master WHERE test_name = 'Liver Function Test')
LIMIT 1;

-- Insert Medicines Master Data (only if table is empty)
INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Paracetamol' as medicine_name, '500mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Paracetamol')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Ibuprofen' as medicine_name, '400mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Ibuprofen')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Amoxicillin' as medicine_name, '500mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Amoxicillin')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Azithromycin' as medicine_name, '250mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Azithromycin')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Saline' as medicine_name, '10mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Saline')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Dextrose' as medicine_name, '5%' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Dextrose')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Metformin' as medicine_name, '500mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Metformin')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Amlodipine' as medicine_name, '5mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Amlodipine')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Atorvastatin' as medicine_name, '10mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Atorvastatin')
LIMIT 1;

INSERT INTO medicines_master (medicine_name, dosage) 
SELECT * FROM (SELECT 'Omeprazole' as medicine_name, '20mg' as dosage) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM medicines_master WHERE medicine_name = 'Omeprazole')
LIMIT 1;

