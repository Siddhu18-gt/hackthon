// Doctor Portal JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Doctor Login
    const doctorLoginForm = document.getElementById('doctorLoginForm');
    if (doctorLoginForm) {
        doctorLoginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(doctorLoginForm);
            const data = {
                email: formData.get('email'),
                password: formData.get('password')
            };
            
            try {
                const response = await fetch('../../api/doctor_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Store doctor data
                    localStorage.setItem('doctor_id', result.doctor.id);
                    localStorage.setItem('doctor_name', result.doctor.name);
                    localStorage.setItem('doctor_specialization', result.doctor.specialization);
                    
                    showSuccess('Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = 'dashboard.html';
                    }, 1000);
                } else {
                    showError(result.message || 'Login failed');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Doctor login error:', error);
            }
        });
    }
    
    // Patient Login (Doctor Portal)
    const patientLoginForm = document.getElementById('patientLoginForm');
    if (patientLoginForm) {
        patientLoginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(patientLoginForm);
            const aadhaar = getCleanAadhaar(formData.get('aadhaar'));
            
            if (!validateAadhaar(formData.get('aadhaar'))) {
                showError('Please enter a valid 12-digit Aadhaar number');
                return;
            }
            
            try {
                const response = await fetch('../../api/patient_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ aadhaar: aadhaar })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Store patient data
                    localStorage.setItem('current_patient', JSON.stringify(result.patient));
                    localStorage.setItem('patient_id', result.patient.id);
                    
                    showSuccess('Patient login successful!');
                    
                    // Reload page to show patient options
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showError(result.message || 'Patient not found');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Patient login error:', error);
            }
        });
    }
});

