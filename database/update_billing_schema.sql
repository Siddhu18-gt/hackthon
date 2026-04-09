-- Update billing table to add item_type column
ALTER TABLE billing 
ADD COLUMN IF NOT EXISTS item_type ENUM('test', 'consultation', 'medicine', 'other') DEFAULT 'test' AFTER receptionist_id,
MODIFY COLUMN test_name VARCHAR(255) NULL;

-- Update existing records to have item_type
UPDATE billing SET item_type = 'test' WHERE item_type IS NULL OR item_type = '';

