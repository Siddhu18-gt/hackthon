-- Update patients table to support email/password login
ALTER TABLE patients
    ADD COLUMN IF NOT EXISTS email VARCHAR(255) UNIQUE DEFAULT NULL AFTER name,
    ADD COLUMN IF NOT EXISTS password VARCHAR(255) DEFAULT NULL AFTER email;
