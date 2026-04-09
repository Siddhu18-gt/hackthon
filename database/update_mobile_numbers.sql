-- Update script to add mobile_number and scheme columns to existing demo_aadhaar_data table
-- Run this if you already have the database set up

USE medixa_db;

-- Add mobile_number column if it doesn't exist
ALTER TABLE demo_aadhaar_data 
ADD COLUMN IF NOT EXISTS mobile_number VARCHAR(10) DEFAULT NULL;

-- Add scheme column if it doesn't exist
ALTER TABLE demo_aadhaar_data 
ADD COLUMN IF NOT EXISTS scheme VARCHAR(50) DEFAULT NULL;

-- Update existing records with mobile numbers and schemes
UPDATE demo_aadhaar_data SET mobile_number = '9876543210', scheme = 'ayushman' WHERE aadhaar_number = '677597815450';
UPDATE demo_aadhaar_data SET mobile_number = '9876543211', scheme = 'cmhs' WHERE aadhaar_number = '884533122661';
UPDATE demo_aadhaar_data SET mobile_number = '9876543212', scheme = 'ayushman' WHERE aadhaar_number = '896548763231';
UPDATE demo_aadhaar_data SET mobile_number = '9876543213', scheme = 'cmhs' WHERE aadhaar_number = '857632764201';
UPDATE demo_aadhaar_data SET mobile_number = '9876543214', scheme = NULL WHERE aadhaar_number = '450460139825';

-- Add mobile_number, scheme, and scheme_discount columns to patients table if they don't exist
ALTER TABLE patients 
ADD COLUMN IF NOT EXISTS mobile_number VARCHAR(10) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS scheme VARCHAR(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS scheme_discount DECIMAL(5,2) DEFAULT 0;

