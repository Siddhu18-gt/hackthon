-- Fix Patients Table - Add missing columns if they don't exist
-- Run this script to ensure all required columns exist in the patients table

USE medixa_db;

-- Add mobile_number column if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = "patients";
SET @columnname = "mobile_number";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " VARCHAR(10) DEFAULT NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add scheme column if it doesn't exist
SET @columnname = "scheme";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " VARCHAR(50) DEFAULT NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add scheme_discount column if it doesn't exist
SET @columnname = "scheme_discount";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " DECIMAL(5,2) DEFAULT 0")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Simple alternative (if above doesn't work, use this):
-- ALTER TABLE patients ADD COLUMN IF NOT EXISTS mobile_number VARCHAR(10) DEFAULT NULL;
-- ALTER TABLE patients ADD COLUMN IF NOT EXISTS scheme VARCHAR(50) DEFAULT NULL;
-- ALTER TABLE patients ADD COLUMN IF NOT EXISTS scheme_discount DECIMAL(5,2) DEFAULT 0;

