// Billing JavaScript

let currentPatient = null;
let billingRows = [];

// Load Patient Billing
async function loadPatientBilling() {
    const aadhaarInput = document.getElementById('patientAadhaar');
    const aadhaar = getCleanAadhaar(aadhaarInput.value);
    
    if (!validateAadhaar(aadhaarInput.value)) {
        showError('Please enter a valid 12-digit Aadhaar number');
        return;
    }
    
    try {
        const response = await fetch('../../api/get_patient_billing.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ aadhaar: aadhaar })
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentPatient = result.patient;
            displayPatientBillingDetails(result.patient);
            
            // Auto-select scheme from patient data
            const schemeSelect = document.getElementById('schemeSelect');
            if (schemeSelect && result.patient.scheme) {
                schemeSelect.value = result.patient.scheme;
                
                // Trigger change event to update discount display
                const changeEvent = new Event('change', { bubbles: true });
                schemeSelect.dispatchEvent(changeEvent);
            }
            
            // Load existing billing if any
            if (result.billing && result.billing.length > 0) {
                billingRows = result.billing.map(item => ({
                    id: item.id || Date.now(), // Use database ID or generate temp ID
                    db_id: item.id, // Store database ID separately
                    item_type: item.item_type || 'test',
                    test_name: item.test_name || '',
                    amount: item.amount || 0,
                    status: item.status || 'unpaid'
                }));
                renderBillingTable();
                calculateTotal();
            } else {
                billingRows = [];
                // Automatically add consultation fee if patient has visited doctor
                if (currentPatient && currentPatient.assigned_doctor_id) {
                    addConsultationFee();
                }
                renderBillingTable();
            }
            
            document.getElementById('patientDetailsSection').style.display = 'block';
        } else {
            showError(result.message || 'Patient not found');
        }
    } catch (error) {
        showError('Network error. Please try again.');
        console.error('Load patient billing error:', error);
    }
}

// Display Patient Billing Details
function displayPatientBillingDetails(patient) {
    const container = document.getElementById('patientBillingDetails');
    const photoPath = patient.photo_path ? `../../${patient.photo_path}` : null;
    const photoHtml = photoPath ? `<img src="${photoPath}" alt="Patient Photo" class="patient-photo" onerror="this.style.display='none'">` : '';
    
    container.innerHTML = `
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
            <span class="detail-value">${patient.address || 'N/A'}</span>
        </div>
        ${patient.cause ? `
        <div class="detail-row">
            <span class="detail-label">Cause:</span>
            <span class="detail-value">${patient.cause}</span>
        </div>
        ` : ''}
    `;
}

// Add Billing Row
function addBillingRow(itemType = 'test') {
    const row = {
        id: Date.now(),
        item_type: itemType,
        test_name: itemType === 'consultation' ? 'Doctor Consultation' : '',
        amount: itemType === 'consultation' ? 500 : 0, // Default consultation fee
        status: 'unpaid'
    };
    
    billingRows.push(row);
    renderBillingTable();
}

// Add Consultation Fee
function addConsultationFee() {
    // Check if consultation fee already exists
    const hasConsultation = billingRows.some(row => row.item_type === 'consultation');
    if (!hasConsultation) {
        addBillingRow('consultation');
        showSuccess('Consultation fee added');
    } else {
        showError('Consultation fee already added');
    }
}

// Remove Billing Row
function removeBillingRow(id) {
    billingRows = billingRows.filter(row => row.id !== id);
    renderBillingTable();
    calculateTotal();
}

// Render Billing Table
function renderBillingTable() {
    const tbody = document.getElementById('billingTableBody');
    tbody.innerHTML = '';
    
    billingRows.forEach((row, index) => {
        const tr = document.createElement('tr');
        const itemType = row.item_type || 'test';
        const isConsultation = itemType === 'consultation';
        
        tr.innerHTML = `
            <td>
                <input type="text" class="form-input" value="${row.test_name || ''}" 
                       onchange="updateBillingRow(${row.id}, 'test_name', this.value)" 
                       placeholder="${isConsultation ? 'Consultation' : 'e.g., Blood Test, USG'}"
                       ${isConsultation ? 'readonly' : ''}>
            </td>
            <td>
                <input type="number" class="form-input" value="${row.amount}" 
                       onchange="updateBillingRow(${row.id}, 'amount', parseFloat(this.value) || 0)" 
                       placeholder="Amount" step="0.01">
            </td>
            <td>
                <select class="form-select" value="${row.status}" 
                        onchange="updateBillingRow(${row.id}, 'status', this.value)">
                    <option value="unpaid" ${row.status === 'unpaid' ? 'selected' : ''}>Unpaid</option>
                    <option value="paid" ${row.status === 'paid' ? 'selected' : ''}>Paid</option>
                </select>
            </td>
            <td>
                <button class="btn btn-secondary btn-small" onclick="removeBillingRow(${row.id})" ${isConsultation ? 'disabled title="Cannot remove consultation fee"' : ''}>
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    if (billingRows.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No billing items. Click "Add Test" to add items.</td></tr>';
    }
    
    calculateTotal();
}

// Update Billing Row
function updateBillingRow(id, field, value) {
    const row = billingRows.find(r => r.id === id);
    if (row) {
        row[field] = value;
        calculateTotal();
        
        // If status changed to paid and we have a database ID, send to test department
        if (field === 'status' && value === 'paid' && row.item_type === 'test' && row.db_id) {
            updateBillingStatusAndSendToDepartment(row.db_id, value);
        }
    }
}

// Update billing status and send to department
async function updateBillingStatusAndSendToDepartment(billingId, status) {
    try {
        const response = await fetch('../../api/update_billing_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                billing_id: billingId,
                status: status
            })
        });
        
        const result = await response.json();
        if (result.success) {
            showSuccess('Test sent to department after payment!');
        }
    } catch (error) {
        console.error('Error updating billing status:', error);
    }
}

// Save Billing
async function saveBilling() {
    if (!currentPatient) {
        showError('Please load a patient first');
        return;
    }
    
    if (billingRows.length === 0) {
        showError('Please add at least one test');
        return;
    }
    
    const scheme = document.getElementById('schemeSelect').value;
    const subtotal = calculateSubtotal();
    const discountPercentage = getDiscountPercentage(scheme);
    const discountAmount = (subtotal * discountPercentage) / 100;
    const totalAmount = subtotal - discountAmount;
    
    const data = {
        patient_id: currentPatient.id,
        billing_items: billingRows.map(row => ({
            item_type: row.item_type || 'test',
            test_name: row.test_name || '',
            amount: row.amount || 0,
            status: row.status || 'unpaid'
        })),
        scheme: scheme,
        subtotal: subtotal,
        discount_percentage: discountPercentage,
        discount_amount: discountAmount,
        total_amount: totalAmount
    };
    
    try {
        const response = await fetch('../../api/save_billing.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('Billing saved successfully!');
            
            // Update billing rows with database IDs
            if (result.billing_ids && result.billing_ids.length > 0) {
                billingRows.forEach((row, index) => {
                    if (result.billing_ids[index]) {
                        row.db_id = result.billing_ids[index];
                        row.id = result.billing_ids[index]; // Use database ID
                    }
                });
            }
            
            calculateTotal();
            
            // Auto-send to test departments if status is paid
            const paidTests = billingRows.filter(row => row.status === 'paid' && row.item_type === 'test');
            if (paidTests.length > 0) {
                paidTests.forEach(test => {
                    if (test.db_id) {
                        updateBillingStatusAndSendToDepartment(test.db_id, 'paid');
                    }
                });
            }
        } else {
            showError(result.message || 'Failed to save billing');
        }
    } catch (error) {
        showError('Network error. Please try again.');
        console.error('Save billing error:', error);
    }
}

// Calculate Subtotal
function calculateSubtotal() {
    return billingRows.reduce((sum, row) => sum + (parseFloat(row.amount) || 0), 0);
}

// Get Discount Percentage
function getDiscountPercentage(scheme) {
    const discounts = {
        'ayushman': 50,
        'cmhs': 30,
        'general': 0
    };
    return discounts[scheme] || 0;
}

// Calculate Total
function calculateTotal() {
    const subtotal = calculateSubtotal();
    const scheme = document.getElementById('schemeSelect').value;
    const discountPercentage = getDiscountPercentage(scheme);
    const discountAmount = (subtotal * discountPercentage) / 100;
    const totalAmount = subtotal - discountAmount;
    
    document.getElementById('subtotal').textContent = `₹${subtotal.toFixed(2)}`;
    document.getElementById('schemeDisplay').textContent = scheme ? `${scheme} (${discountPercentage}%)` : '-';
    document.getElementById('discountAmount').textContent = `₹${discountAmount.toFixed(2)}`;
    document.getElementById('totalAmount').innerHTML = `<strong>₹${totalAmount.toFixed(2)}</strong>`;
    
    document.getElementById('billingSummary').style.display = 'block';
}

// Send to Test Departments
async function sendToTestDepartments(paidTests) {
    // This will automatically create records in test_departments when billing is saved
    // The API handles this
}

// Download Billing
function downloadBilling() {
    if (!currentPatient) {
        showError('Please load a patient first');
        return;
    }
    
    // Create printable content
    const content = generateBillingPDF();
    
    // Create a blob and download as PDF
    const blob = new Blob([content], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Billing_${currentPatient.name}_${Date.now()}.html`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    // Also open in new window for printing
    const printWindow = window.open('', '_blank');
    printWindow.document.write(content);
    printWindow.document.close();
}

// Generate Billing PDF Content
function generateBillingPDF() {
    const subtotal = calculateSubtotal();
    const scheme = document.getElementById('schemeSelect').value;
    const discountPercentage = getDiscountPercentage(scheme);
    const discountAmount = (subtotal * discountPercentage) / 100;
    const totalAmount = subtotal - discountAmount;
    
    return `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Billing Report - ${currentPatient.name}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h2 { color: #00e6ac; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #00e6ac; color: white; }
                .summary { margin-top: 20px; }
                .total { font-weight: bold; font-size: 18px; }
            </style>
        </head>
        <body>
            <h2>Medixa Healthcare - Billing Report</h2>
            <h3>Patient Details</h3>
            <p><strong>Name:</strong> ${currentPatient.name || 'N/A'}</p>
            <p><strong>Aadhaar:</strong> ${formatAadhaar(currentPatient.aadhaar_number || '')}</p>
            <p><strong>Age:</strong> ${currentPatient.age ? `${currentPatient.age} years` : 'N/A'} | <strong>Gender:</strong> ${currentPatient.gender || 'N/A'}</p>
            <p><strong>Address:</strong> ${currentPatient.address || 'N/A'}</p>
            ${currentPatient.cause ? `<p><strong>Cause:</strong> ${currentPatient.cause}</p>` : ''}
            
            <hr>
            
            <h3>Tests</h3>
            <table>
                <thead>
                    <tr>
                        <th>Test</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${billingRows.map(row => `
                        <tr>
                            <td>${row.test_name}</td>
                            <td>₹${parseFloat(row.amount).toFixed(2)}</td>
                            <td>${row.status}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            
            <div class="summary">
                <p>Subtotal: ₹${subtotal.toFixed(2)}</p>
                <p>Scheme: ${scheme} (${discountPercentage}%)</p>
                <p>Discount: ₹${discountAmount.toFixed(2)}</p>
                <p class="total">Total Amount: ₹${totalAmount.toFixed(2)}</p>
            </div>
            
            <p style="margin-top: 30px;"><em>Generated on ${new Date().toLocaleString()}</em></p>
        </body>
        </html>
    `;
}

