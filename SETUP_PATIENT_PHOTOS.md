# Patient Photos Setup Guide

## Overview
This guide explains how to set up patient photos for the Medixa Healthcare Management System.

## Database Setup

1. Run the SQL migration script to add the `photo_path` column to both `patients` and `demo_aadhaar_data` tables:
   ```sql
   -- Run this in phpMyAdmin or MySQL
   source database/add_patient_photos.sql
   ```
   
   Or manually execute:
   ```sql
   ALTER TABLE patients ADD COLUMN photo_path VARCHAR(500) DEFAULT NULL AFTER address;
   ALTER TABLE demo_aadhaar_data ADD COLUMN photo_path VARCHAR(500) DEFAULT NULL AFTER address;
   ```

## Image Setup

The system expects patient photos to be stored in the `uploads/patient_photos/` directory with the following naming convention:
- `{AADHAR_NUMBER}.jpg` (or .png, .jpeg, .gif)

### For the specified Aadhar numbers:

1. **450460139825** - Place image as: `uploads/patient_photos/450460139825.jpg`
2. **884533122661** - Place image as: `uploads/patient_photos/884533122661.jpg`
3. **677597815450** - Place image as: `uploads/patient_photos/677597815450.jpg`
4. **857632764201** - Place image as: `uploads/patient_photos/857632764201.jpg`
5. **896548763231** - Place image as: `uploads/patient_photos/896548763231.jpg`

### Uploading Images

You can upload images in two ways:

#### Method 1: Manual Upload
1. Place the image files directly in the `uploads/patient_photos/` directory
2. Name them according to the Aadhar number (e.g., `450460139825.jpg`)
3. The database will be updated automatically when patients are fetched/registered

#### Method 2: Using the Upload API
Use the `api/upload_patient_photo.php` endpoint:
```javascript
const formData = new FormData();
formData.append('photo', fileInput.files[0]);
formData.append('aadhaar', '450460139825');

fetch('api/upload_patient_photo.php', {
    method: 'POST',
    body: formData
});
```

## Database Updates

The SQL script (`database/add_patient_photos.sql`) will automatically update the `demo_aadhaar_data` table with the photo paths for the specified Aadhar numbers.

## Features

- Patient photos are displayed in all patient detail views:
  - Receptionist Portal - Patient Management
  - Doctor Portal - Patient Login & Doctor Page
  - Patient Portal - Billing & Reports
- Photos are automatically copied from `demo_aadhaar_data` to `patients` table when a patient is registered
- If a photo doesn't exist, the system gracefully handles it (no broken image)

## File Structure

```
uploads/
└── patient_photos/
    ├── 450460139825.jpg
    ├── 884533122661.jpg
    ├── 677597815450.jpg
    ├── 857632764201.jpg
    └── 896548763231.jpg
```

## Notes

- Supported image formats: JPG, JPEG, PNG, GIF
- Recommended image size: 120x120 pixels (will be automatically resized)
- Images are stored with relative paths in the database: `uploads/patient_photos/{aadhar}.jpg`

