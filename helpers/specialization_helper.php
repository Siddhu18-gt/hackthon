<?php
/**
 * Medixa Symptom to Specialization Mapper
 * This helper maps patient symptoms/cause to the most relevant medical specialization.
 */

function getSpecializationBySymptoms($symptoms) {
    $symptoms = strtolower($symptoms);
    
    $mapping = [
        'Cardiology' => ['heart', 'chest pain', 'palpitations', 'breathlessness', 'blood pressure', 'hypertension', 'cardiac'],
        'Neurology' => ['headache', 'seizures', 'paralysis', 'numbness', 'brain', 'dizziness', 'migraine', 'nerve'],
        'Orthopedics' => ['bone', 'fracture', 'joint pain', 'back pain', 'knee', 'spine', 'muscle pain', 'arthritis'],
        'Pediatrics' => ['child', 'baby', 'infant', 'vaccination', 'growth', 'pediatric'],
        'Dermatology' => ['skin', 'rash', 'itching', 'acne', 'allergy', 'hair loss', 'eczema', 'dermatitis'],
        'Ophthalmology' => ['eye', 'vision', 'blindness', 'cataract', 'glaucoma', 'blurry', 'sight'],
        'ENT' => ['ear', 'nose', 'throat', 'sinus', 'hearing', 'tonsils', 'tinnitus'],
        'Gynecology' => ['pregnancy', 'menstrual', 'periods', 'female', 'uterus', 'delivery', 'obstetrics'],
        'Psychiatry' => ['depression', 'anxiety', 'mental', 'stress', 'sleep', 'behavior', 'psychological'],
        'General Medicine' => ['fever', 'cold', 'cough', 'stomach ache', 'vomiting', 'weakness', 'infection', 'diabetes', 'thyroid']
    ];

    foreach ($mapping as $specialization => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($symptoms, $keyword) !== false) {
                return $specialization;
            }
        }
    }

    // Default to General Medicine if no match is found
    return 'General Medicine';
}
?>