// Others Portal JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Test Department Login
    const testLoginForm = document.getElementById('testLoginFormSubmit');
    if (testLoginForm) {
        testLoginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(testLoginForm);
            const data = {
                email: formData.get('email'),
                password: formData.get('password')
            };
            
            try {
                const response = await fetch('../../api/test_department_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    localStorage.setItem('test_department_id', result.department.id);
                    localStorage.setItem('department_name', result.department.name);
                    localStorage.setItem('test_user_name', result.department.name);
                    
                    showSuccess('Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = 'test-dashboard.html';
                    }, 1000);
                } else {
                    showError(result.message || 'Login failed');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Test login error:', error);
            }
        });
    }
    
    // Nurse Login
    const nurseLoginForm = document.getElementById('nurseLoginFormSubmit');
    if (nurseLoginForm) {
        nurseLoginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(nurseLoginForm);
            const data = {
                email: formData.get('email'),
                password: formData.get('password')
            };
            
            try {
                const response = await fetch('../../api/nurse_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    localStorage.setItem('nurse_id', result.nurse.id);
                    localStorage.setItem('nurse_name', result.nurse.name);
                    
                    showSuccess('Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = 'nurse-dashboard.html';
                    }, 1000);
                } else {
                    showError(result.message || 'Login failed');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Nurse login error:', error);
            }
        });
    }
    
    // Nurse Patient Login
    const nursePatientLoginForm = document.getElementById('nursePatientLoginForm');
    if (nursePatientLoginForm) {
        nursePatientLoginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(nursePatientLoginForm);
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
                    localStorage.setItem('nurse_current_patient', JSON.stringify(result.patient));
                    
                    showSuccess('Patient login successful!');
                    
                    // Load prescriptions
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showError(result.message || 'Patient not found');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Nurse patient login error:', error);
            }
        });
    }
});

