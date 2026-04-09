// Receptionists Portal JavaScript

function setPatientFieldsReadonly(isReadonly) {
    const selectors = [
        'input[name="name"]',
        'input[name="date_of_birth"]',
        'input[name="age"]',
        'input[name="gender"]',
        'textarea[name="address"]'
    ];

    selectors.forEach((selector) => {
        const field = document.querySelector(selector);
        if (!field) return;

        if (isReadonly) {
            field.setAttribute('readonly', 'readonly');
        } else {
            field.removeAttribute('readonly');
        }
    });
}

function fillPatientFields(data) {
    const nameInput = document.querySelector('input[name="name"]');
    const dobInput = document.querySelector('input[name="date_of_birth"]');
    const ageInput = document.querySelector('input[name="age"]');
    const genderInput = document.querySelector('input[name="gender"]');
    const addressInput = document.querySelector('textarea[name="address"]');
    const mobileInput = document.getElementById('mobileInput') || document.querySelector('input[name="mobile"]');

    if (nameInput) nameInput.value = data.name || '';
    if (dobInput) dobInput.value = data.date_of_birth || '';
    if (ageInput) ageInput.value = data.age || '';
    if (genderInput) genderInput.value = data.gender || '';
    if (addressInput) addressInput.value = data.address || '';
    if (mobileInput) mobileInput.value = data.mobile_number || '';

    const schemeSelect = document.getElementById('schemeSelect');
    if (schemeSelect) {
        schemeSelect.value = data.scheme || '';
        schemeSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function attachAgeAutoCalculation() {
    const dobInput = document.querySelector('input[name="date_of_birth"]');
    const ageInput = document.querySelector('input[name="age"]');

    if (!dobInput || !ageInput || dobInput.dataset.ageBound === '1') return;

    dobInput.dataset.ageBound = '1';
    dobInput.addEventListener('change', function () {
        if (!dobInput.value) {
            ageInput.value = '';
            return;
        }

        const dob = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        ageInput.value = age >= 0 ? age : '';
    });
}

// Receptionist Login + Registration + Patient Forms
document.addEventListener('DOMContentLoaded', function() {
    attachAgeAutoCalculation();

    // Receptionist Login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(loginForm);
            const data = {
                email: formData.get('email'),
                password: formData.get('password')
            };

            try {
                const response = await fetch('../../api/receptionist_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    localStorage.setItem('receptionist_id', result.user.id);
                    localStorage.setItem('receptionist_name', result.user.name);
                    localStorage.setItem('hospital_name', result.user.hospital);

                    showSuccess('Login successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = 'dashboard.html';
                    }, 1000);
                } else {
                    showError(result.message || 'Login failed');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Login error:', error);
            }
        });
    }

    // Receptionist Registration
    const registerForm = document.getElementById('registerForm');
    const aadhaarInput = document.getElementById('receptionistAadhaarInput');

    if (aadhaarInput) {
        aadhaarInput.addEventListener('blur', async function() {
            const aadhaar = getCleanAadhaar(aadhaarInput.value);
            if (aadhaar.length !== 12) return;

            try {
                const response = await fetch('../../api/fetch_aadhaar.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({aadhaar})
                });
                const responseText = await response.text();
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Invalid Aadhaar JSON response:', responseText);
                    showError('Aadhaar service returned an invalid response. Please try again.');
                    return;
                }
                if (result.success && result.data) {
                    const nameInput = document.getElementById('receptionistNameInput');
                    const dobInput = document.getElementById('receptionistDobInput');
                    const genderInput = document.getElementById('receptionistGenderInput');
                    const addressInput = document.getElementById('receptionistAddressInput');
                    const mobileInput = document.getElementById('receptionistMobileInput');

                    if (nameInput) nameInput.value = result.data.name || '';
                    if (dobInput) dobInput.value = result.data.date_of_birth || '';
                    if (genderInput) genderInput.value = result.data.gender || '';
                    if (addressInput) addressInput.value = result.data.address || '';
                    if (mobileInput) mobileInput.value = result.data.mobile_number || '';
                    
                    showSuccess('Aadhaar details fetched successfully');
                }
            } catch (error) {
                console.error('Aadhaar fetch error:', error);
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(registerForm);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');

            if (password !== confirmPassword) {
                showError('Passwords do not match');
                return;
            }

            const data = {
                hospital_name: formData.get('hospital_name'),
                receptionist_name: formData.get('receptionist_name'),
                email: formData.get('email'),
                password: password,
                confirm_password: confirmPassword,
                aadhaar: getCleanAadhaar(formData.get('aadhaar')),
                date_of_birth: formData.get('date_of_birth'),
                gender: formData.get('gender'),
                address: formData.get('address'),
                mobile: formData.get('mobile')
            };

            try {
                const response = await fetch('../../api/register_receptionist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showSuccess('Registration successful! Redirecting to login...');
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 1500);
                } else {
                    showError(result.message || 'Registration failed');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Registration error:', error);
            }
        });
    }

    // Patient Login
    const patientLoginForm = document.getElementById('patientLoginForm');
    if (patientLoginForm) {
        patientLoginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(patientLoginForm);
            const aadhaar = getCleanAadhaar(formData.get('aadhaar'));
            const aadhaarValue = formData.get('aadhaar');

            if (!validateAadhaar(aadhaarValue)) {
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
                    localStorage.setItem('current_patient', JSON.stringify(result.patient));
                    localStorage.setItem('patient_id', result.patient.id);

                    displayPatientDetails(result.patient);
                    showSuccess('Patient login successful!');
                } else {
                    showError(result.message || 'Patient not found');
                }
            } catch (error) {
                showError('Network error. Please try again.');
                console.error('Patient login error:', error);
            }
        });
    }

    // Patient Registration
    const patientRegisterForm = document.getElementById('patientRegisterForm');
    if (patientRegisterForm) {
        patientRegisterForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(patientRegisterForm);

            if (!window.tempAadhaar || !window.tempAadhaarData) {
                showError('Please verify OTP first');
                return;
            }

            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            const email = formData.get('email');

            if (!password || !confirmPassword || password !== confirmPassword) {
                showError('Please enter a valid password and confirm it correctly');
                return;
            }

            const data = {
                aadhaar: window.tempAadhaar,
                name: formData.get('name'),
                date_of_birth: formData.get('date_of_birth'),
                age: parseInt(formData.get('age')) || 0,
                gender: formData.get('gender'),
                address: formData.get('address'),
                mobile: formData.get('mobile'),
                email: email,
                password: password,
                confirm_password: confirmPassword,
                scheme: formData.get('scheme'),
                scheme_discount: formData.get('scheme_discount') || '0',
                cause: formData.get('cause'),
                specialization: formData.get('specialization')
            };

            if (!data.name || !data.date_of_birth || !data.age || !data.gender || !data.address || !data.mobile || !data.email || !data.password) {
                showError('Please fill all patient details');
                return;
            }

            try {
                const response = await fetch('../../api/register_patient.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const responseText = await response.text();
                console.log('RAW REGISTER RESPONSE:', responseText);

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Invalid JSON response:', responseText);
                    throw new Error('Invalid JSON from server');
                }

                if (!response.ok) {
                    throw new Error(result.message || `HTTP error! status: ${response.status}`);
                }

                if (result.success) {
                    showSuccess('Patient registered successfully!');

                    window.tempAadhaarData = null;
                    window.tempAadhaar = null;
                    window.generatedOTP = null;
                    window.isNewAadhaar = false;

                    patientRegisterForm.reset();

                    const otpContainer = document.getElementById('otpContainer');
                    const patientDetailsContainer = document.getElementById('patientDetailsContainer');
                    if (otpContainer) otpContainer.style.display = 'none';
                    if (patientDetailsContainer) patientDetailsContainer.style.display = 'none';

                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showError(result.message || 'Registration failed');
                }
            } catch (error) {
                showError('Network error: ' + error.message);
                console.error('Patient registration error:', error);
            }
        });
    }
});

// Auto fetch Aadhaar details
async function autoFetchAadhaarDetails() {
    const aadhaarInput = document.getElementById('aadhaarInput') || document.querySelector('input[name="aadhaar"]');
    if (!aadhaarInput) return;

    const aadhaar = getCleanAadhaar(aadhaarInput.value);

    if (aadhaar.length !== 12 || !validateAadhaar(aadhaarInput.value)) {
        return;
    }

    const messageContainer = document.getElementById('message-container');
    if (messageContainer) {
        messageContainer.innerHTML = '<div class="success-message"><i class="fas fa-spinner fa-spin"></i> Checking Aadhaar and sending OTP...</div>';
    }

    try {
        const response = await fetch('../../api/fetch_aadhaar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ aadhaar: aadhaar })
        });

        const responseText = await response.text();
        console.log('RAW AADHAAR RESPONSE:', responseText);

        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Invalid Aadhaar JSON response:', responseText);
            showError('Aadhaar service returned an invalid response. Please try again.');
            return;
        }

        if (result.success) {
            const otpContainer = document.getElementById('otpContainer');
            const patientDetailsContainer = document.getElementById('patientDetailsContainer');

            if (result.is_new) {
                // For new Aadhaar, skip OTP and directly show form
                if (otpContainer) otpContainer.style.display = 'none';
                if (patientDetailsContainer) patientDetailsContainer.style.display = 'block';
                
                setPatientFieldsReadonly(false);
                fillPatientFields(result.data || {});
                showSuccess('New Aadhaar detected. Please enter patient details manually.');
                
                window.tempAadhaarData = result.data || {};
                window.tempAadhaar = aadhaar;
                window.isNewAadhaar = true;
            } else {
                // For existing Aadhaar, show OTP
                if (otpContainer) otpContainer.style.display = 'block';
                if (patientDetailsContainer) patientDetailsContainer.style.display = 'none';

                const mobileDisplay = document.getElementById('mobileNumberDisplay');
                const otpMessage = document.getElementById('otpMessage');
                const demoOTP = document.getElementById('demoOTP');

                if (mobileDisplay) {
                    if (result.data && result.data.mobile_number) {
                        mobileDisplay.innerHTML = `<i class="fas fa-mobile-alt"></i> Mobile: +91 ${result.data.mobile_number}`;
                    } else {
                        mobileDisplay.innerHTML = '<i class="fas fa-mobile-alt"></i> Mobile: Not available';
                    }
                }

                if (otpMessage) {
                    otpMessage.textContent = result.message || 'OTP sent successfully';
                }

                if (demoOTP) {
                    demoOTP.textContent = result.otp || '413256';
                }

                window.tempAadhaarData = result.data || {};
                window.tempAadhaar = aadhaar;
                window.generatedOTP = result.otp || '413256';
                window.isNewAadhaar = false;

                showSuccess('Aadhaar found. Verify OTP to continue.');
            }
        } else {
            showError(result.message || 'Unable to verify Aadhaar');
        }
    } catch (error) {
        showError('Network error: ' + error.message);
        console.error('Fetch Aadhaar error:', error);
    }
}

// Manual fetch function
async function fetchAadhaarDetails() {
    await autoFetchAadhaarDetails();
}

// Verify OTP
function verifyOTP() {
    const otpInput = document.getElementById('otpInput');
    const enteredOTP = otpInput ? otpInput.value.trim() : '';
    const correctOTP = window.generatedOTP || '413256';

    if (enteredOTP !== correctOTP) {
        showError(`Invalid OTP. Please enter the correct OTP: ${correctOTP}`);
        return;
    }

    const patientDetailsContainer = document.getElementById('patientDetailsContainer');
    const otpContainer = document.getElementById('otpContainer');

    const isNew = !!window.isNewAadhaar;

    if (isNew) {
        setPatientFieldsReadonly(false);
        fillPatientFields(window.tempAadhaarData || {});
        showSuccess('OTP verified. Enter new patient details and save.');
    } else {
        setPatientFieldsReadonly(true);
        fillPatientFields(window.tempAadhaarData || {});
        showSuccess('OTP verified. Existing patient details loaded.');
    }

    if (patientDetailsContainer && window.tempAadhaarData && window.tempAadhaarData.photo_path) {
        const patientDetailsCard = patientDetailsContainer.querySelector('.patient-details-card');
        if (patientDetailsCard) {
            const oldPhotoWrap = patientDetailsCard.querySelector('.patient-photo-wrap');
            if (oldPhotoWrap) oldPhotoWrap.remove();

            const photoWrap = document.createElement('div');
            photoWrap.className = 'patient-photo-wrap';
            photoWrap.style.textAlign = 'center';
            photoWrap.style.marginBottom = '20px';

            const img = document.createElement('img');
            img.src = `../../${window.tempAadhaarData.photo_path}`;
            img.alt = 'Patient Photo';
            img.className = 'patient-photo';
            img.onerror = function() {
                photoWrap.remove();
            };

            photoWrap.appendChild(img);
            patientDetailsCard.prepend(photoWrap);
        }
    }

    if (otpContainer) otpContainer.style.display = 'none';
    if (patientDetailsContainer) patientDetailsContainer.style.display = 'block';
}

// Display Patient Details
function displayPatientDetails(patient) {
    const displayContainer = document.getElementById('patientDetailsDisplay');
    const infoContent = document.getElementById('patientInfoContent');

    if (!displayContainer || !infoContent) return;

    const photoPath = patient.photo_path ? `../../${patient.photo_path}` : null;
    const photoHtml = photoPath ? `<img src="${photoPath}" alt="Patient Photo" class="patient-photo" onerror="this.style.display='none'">` : '';

    infoContent.innerHTML = `
        ${photoHtml}
        <div class="detail-row">
            <span class="detail-label">Name:</span>
            <span class="detail-value">${patient.name}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Aadhaar:</span>
            <span class="detail-value">${formatAadhaar(patient.aadhaar_number)}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Date of Birth:</span>
            <span class="detail-value">${formatDate(patient.date_of_birth)}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Age:</span>
            <span class="detail-value">${patient.age ? `${patient.age} years` : 'N/A'}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Gender:</span>
            <span class="detail-value">${patient.gender || 'N/A'}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Address:</span>
            <span class="detail-value">${patient.address}</span>
        </div>
        ${patient.cause ? `
        <div class="detail-row">
            <span class="detail-label">Cause:</span>
            <span class="detail-value">${patient.cause}</span>
        </div>
        ` : ''}
    `;

    displayContainer.style.display = 'block';

    const loginFormContainer = document.getElementById('loginFormContainer');
    const registerFormContainer = document.getElementById('registerFormContainer');

    if (loginFormContainer) loginFormContainer.style.display = 'none';
    if (registerFormContainer) registerFormContainer.style.display = 'none';
}