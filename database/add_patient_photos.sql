-- Add photo_path column to patients table
ALTER TABLE patients 
ADD COLUMN photo_path VARCHAR(500) DEFAULT NULL AFTER address;

-- Add photo_path column to demo_aadhaar_data table
ALTER TABLE demo_aadhaar_data 
ADD COLUMN photo_path VARCHAR(500) DEFAULT NULL AFTER address;

-- Update demo_aadhaar_data with photo paths for the specified Aadhar numbers
UPDATE demo_aadhaar_data 
SET photo_path = 'uploads/patient_photos/450460139825.jpg' 
WHERE aadhaar_number = '450460139825';

UPDATE demo_aadhaar_data 
SET photo_path = 'uploads/patient_photos/884533122661.jpg' 
WHERE aadhaar_number = '884533122661';

UPDATE demo_aadhaar_data 
SET photo_path = 'uploads/patient_photos/677597815450.jpg' 
WHERE aadhaar_number = '677597815450';

UPDATE demo_aadhaar_data 
SET photo_path = 'uploads/patient_photos/857632764201.jpg' 
WHERE aadhaar_number = '857632764201';

UPDATE demo_aadhaar_data 
SET photo_path = 'uploads/patient_photos/896548763231.jpg' 
WHERE aadhaar_number = '896548763231';

