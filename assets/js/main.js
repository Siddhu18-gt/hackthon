// Medixa - Main JavaScript File

// Smooth scrolling for navigation links
document.addEventListener('DOMContentLoaded', function() {
    // Navigation smooth scroll
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Active navigation link highlighting
    const sections = document.querySelectorAll('.section');
    const navItems = document.querySelectorAll('.nav-link');
    
    window.addEventListener('scroll', function() {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (window.pageYOffset >= sectionTop - 100) {
                current = section.getAttribute('id');
            }
        });
        
        navItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href') === `#${current}`) {
                item.classList.add('active');
            }
        });
    });
    
    // Format Aadhaar number input (XXXX XXXX XXXX) and auto-fetch
    const aadhaarInputs = document.querySelectorAll('input[type="text"][data-aadhaar]');
    
    aadhaarInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            if (value.length > 12) {
                value = value.substring(0, 12);
            }
            
            // Format as XXXX XXXX XXXX
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formatted += ' ';
                }
                formatted += value[i];
            }
            
            e.target.value = formatted;
            
            // Auto-fetch when 12 digits are entered (for registration form)
            if (value.length === 12 && input.closest('#registerFormContainer')) {
                // Small delay to ensure formatting is complete
                setTimeout(() => {
                    if (typeof autoFetchAadhaarDetails === 'function') {
                        autoFetchAadhaarDetails();
                    }
                }, 300);
            }
        });
        
        // Prevent non-numeric input
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
                e.preventDefault();
            }
        });
    });
});

// Format Aadhaar number helper function
function formatAadhaar(value) {
    value = value.replace(/\s/g, '');
    if (value.length > 12) {
        value = value.substring(0, 12);
    }
    
    let formatted = '';
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) {
            formatted += ' ';
        }
        formatted += value[i];
    }
    
    return formatted;
}

// Get clean Aadhaar number (without spaces)
function getCleanAadhaar(value) {
    return value.replace(/\s/g, '');
}

// Show success message
function showSuccess(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'success-message';
    messageDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    
    const container = document.getElementById('message-container') || document.querySelector('.form-container') || document.querySelector('.main-content');
    if (container) {
        container.insertBefore(messageDiv, container.firstChild);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

// Show error message
function showError(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'error-message';
    messageDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    const container = document.getElementById('message-container') || document.querySelector('.form-container') || document.querySelector('.main-content');
    if (container) {
        container.insertBefore(messageDiv, container.firstChild);
        
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

// API Helper Functions
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API Call Error:', error);
        return { success: false, message: 'Network error. Please try again.' };
    }
}

// Validate Aadhaar number
function validateAadhaar(aadhaar) {
    const cleanAadhaar = getCleanAadhaar(aadhaar);
    return cleanAadhaar.length === 12 && /^\d+$/.test(cleanAadhaar);
}

// Calculate age from date of birth
function calculateAge(dateOfBirth) {
    const today = new Date();
    const birthDate = new Date(dateOfBirth);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

// Format date for display
function formatDate(dateString) {
    if (!dateString) {
        return 'N/A';
    }

    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) {
        return 'N/A';
    }

    return date.toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Export functions for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatAadhaar,
        getCleanAadhaar,
        showSuccess,
        showError,
        apiCall,
        validateAadhaar,
        calculateAge,
        formatDate
    };
}

