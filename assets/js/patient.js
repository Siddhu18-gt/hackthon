// Patient Portal JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Patient Login
    const patientLoginForm = document.getElementById('patientLoginForm');
    if (patientLoginForm) {
        patientLoginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(patientLoginForm);
            const email = formData.get('email');
            const password = formData.get('password');
            
            if (!email || !password) {
                showError('Please enter both email and password');
                return;
            }
            
            try {
                const response = await fetch('../../api/patient_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Store patient data
                    localStorage.setItem('current_patient', JSON.stringify(result.patient));
                    localStorage.setItem('patient_id', result.patient.id);
                    
                    showSuccess('Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = 'dashboard.html';
                    }, 1000);
                } else {
                    showError(result.message || 'Patient not found. Please register first.');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Patient login error:', error);
            }
        });
    }
});

