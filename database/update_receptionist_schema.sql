-- Update receptionists table to support Aadhaar-backed registration and personal details
ALTER TABLE receptionists
    ADD COLUMN IF NOT EXISTS aadhaar_number VARCHAR(12) UNIQUE DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS date_of_birth DATE DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS gender VARCHAR(10) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS mobile_number VARCHAR(10) DEFAULT NULL;
