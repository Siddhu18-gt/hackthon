<?php

function getPatientPhotoPath(string $aadhaar = null): ?string
{
    if (empty($aadhaar)) {
        return null;
    }

    $cleanAadhaar = preg_replace('/\D+/', '', $aadhaar);
    if (strlen($cleanAadhaar) !== 12) {
        return null;
    }

    $photoDir = __DIR__ . '/../uploads/patient_photos/';
    $relativeDir = 'uploads/patient_photos/';
    $extensions = ['jpg', 'jpeg', 'png', 'webp'];

    foreach ($extensions as $ext) {
        $fileName = $cleanAadhaar . '.' . $ext;
        $fullPath = $photoDir . $fileName;
        if (file_exists($fullPath)) {
            return $relativeDir . $fileName;
        }
    }

    return null;
}


