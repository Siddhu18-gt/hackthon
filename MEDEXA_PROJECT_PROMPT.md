 (Medixa) - Smart Healthcare Management System
## Complete Project Specification & AI Development Prompt

---

## PROJECT OVERVIEW

**Project Name:** Medixa  
**Type:** Final Year Major Project  
**Objective:** Create a secure, paperless, interconnected hospital management system  
**Tech Stack:** XAMPP Server, PHP, MySQL, React (Frontend), Inter Font Family  
**Database:** MySQL via XAMPP

---

## DESIGN SPECIFICATIONS

### Typography
- **Font Family:** Inter (Bold, Semi-Bold, Regular)
- **Style:** Clean Sans-Serif, Modern and Professional
- **Usage:** Apply throughout entire application

### Color Scheme
- Primary Accent: #00e6ac (Teal/Green)
- Background: Dark theme preferred
- Text: White/Light colors for contrast

### Layout Requirements
- **Responsive Design:** Mobile and desktop compatible
- **Sidebar Navigation:** For all authenticated modules
- **Back Button:** Small, unobtrusive back button on every page (except landing page)
- **Consistent UI:** All modules follow same design pattern

---

## LANDING PAGE (Single Page Application)

### Navigation Bar
- **Items:** Home | Portals | About Us | Contact Us
- **Behavior:** Smooth scroll to respective sections on click
- **Position:** Fixed at top

### Section 1: Home
**Content:**
```
Welcome to Medixa
[Display in a nice, efficient, modern way with animations/effects]
```

### Section 2: Portals
**Content:** Four portal options displayed as cards/buttons:
1. **Receptionists** (Receptionist Portal)
2. **Doctor**
3. **Others** (Lab/Nurse Portal)
4. **Patient**

**Behavior:** Each portal button navigates to respective login/entry page

### Section 3: About Us
**Content Structure:**

**About Medixa: Revolutionizing Healthcare Management**

Welcome to Medixa, an innovative smart healthcare management system designed to streamline and enhance the operations of Outpatient Departments (OPD) and related medical services. Our vision is to create a seamless, efficient, and patient-centric platform that simplifies administrative tasks for medical professionals and improves the overall healthcare experience.

**Our Mission:**
At Medixa, we are driven by the mission to leverage modern technology to build intuitive and robust solutions for the healthcare sector. We aim to reduce manual overhead, minimize errors, and ensure quick access to critical patient information, ultimately contributing to better patient care and operational efficiency.

**What Medixa Offers:**
- OPD Management: Efficient portal for managing outpatient registrations, patient queues, and initial consultations
- Patient Registration & Records: Streamlined process for new patient intake, including Aadhaar-based verification (mocked for demo) and secure record keeping
- Doctor & Lab Portals: Dedicated interfaces for doctors to view patient history and for lab personnel to manage test results
- Intuitive User Experience: Modern, responsive design built with React, ensuring ease of use for all stakeholders

**Meet the Team: The Innovators Behind Medixa**

Medixa is proudly developed by a passionate team of four software developers from Government Engineering College Karwar.

**Team Members (with image placeholders):**

1. **Vaishali**
   - Role: Software Developer
   - Image: Circular, 150px, border-radius: 50%, border: 2px solid #00e6ac
   - Quote: "Driven by a passion for clean code and user-centric design, Vaishali brings expertise in front-end development and a keen eye for detail to Medixa."
   - Contact: +91 98765 43210

2. **Vaishnavi**
   - Role: Software Developer
   - Image: Circular, 150px, border-radius: 50%, border: 2px solid #00e6ac
   - Quote: "With a strong foundation in logic and problem-solving, Vaishnavi focuses on the system's architecture and ensuring its scalability for future enhancements."
   - Contact: +91 98765 43211

3. **Chandan**
   - Role: Software Developer
   - Image: Circular, 150px, border-radius: 50%, border: 2px solid #00e6ac
   - Quote: "Chandan is instrumental in translating complex requirements into elegant solutions, with a focus on seamless data flow and efficient system performance."
   - Contact: +91 98765 43212

4. **Pruthvi**
   - Role: Software Developer
   - Image: Circular, 150px, border-radius: 50%, border: 2px solid #00e6ac
   - Quote: "Bringing creativity and innovation to the team, Pruthvi is dedicated to enhancing the visual appeal and overall user interaction of the Medixa platform."
   - Contact: +91 98765 43213

**Institution:**
Government Engineering College Karwar  
Email: [Your Group Email Here]  
Website: [Link to your project repo or college website]

**Layout:** Team members displayed in flex layout, 4 columns on desktop, responsive on mobile

### Section 4: Contact Us
**Content:**

**Contact Us: Medixa Project Team**

We are thrilled to present the Medixa Smart Healthcare Management System. For any inquiries, feedback, or to learn more about our project, please feel free to reach out to our dedicated team.

**General Inquiries & Support**
- Email: info.medixa@example.com
- Phone (Mobile): +91 98765 43210 (Project Lead - Dummy)
- Telephone (Landline): 08382-290XXX (Dummy - for institutional contact)

**Operating Hours for Correspondence**
- Monday - Friday: 9:00 AM - 5:00 PM (IST)
- Saturday - Sunday: Closed (or by prior appointment for urgent inquiries)

**Project Address**
Medixa Project Team  
Department of Computer Science & Engineering  
Government Engineering College Karwar  
Shirwad, Karwar - 581301  
Karnataka, India

**Note:** For direct contact with individual team members, please refer to the "About Us" page for their respective contact details.

---

## MODULE 1: RECEPTIONISTS PORTAL

### Authentication
**Registration Fields:**
- Hospital Name
- Receptionist Name
- Hospital Email
- Create Password
- Confirm Password

**Login Fields:**
- Email
- Password

### Post-Login Layout
**Sidebar Structure:**
- **Top:** "Receptionist
- **Navigation Items:**
  1. Patient Management
  2. Doctor Management
  3. Billing
- **Bottom:** 
  - Display logged-in user's name
  - Sign Out button

**Back Button:** Small back button on every page (top-left or top-right)

### 1.1 Patient Management

#### Patient Login
- **Field:** Aadhaar Number (12 digits, formatted as: XXXX XXXX XXXX)
- **Format:** Auto-format input to show spaces after every 4 digits
- **Validation:** Must be exactly 12 digits
- **Action:** Login button

#### Patient Registration
**Step 1: Aadhaar Input**
- **Field:** Aadhaar Number (formatted: XXXX XXXX XXXX)
- **Button:** "Fetch Details" (appears below Aadhaar input)

**Step 2: Auto-Fill (After Fetch Details)**
**Demo Aadhaar Numbers (Pre-configured in database):**
- Store at least 3-5 demo Aadhaar numbers with complete details
- When user enters a demo Aadhaar, clicking "Fetch Details" auto-fills:
  - Name
  - Date of Birth
  - Age (auto-calculated from DOB)
  - Gender
  - Address

**Step 3: Registration Completion**
- Display fetched details (read-only or editable)
- **Button:** "Register" or "Complete Registration"
- Save to database

**Post-Login**
- Show two options:
  1. **Cause:** Text box for patient's complaint (e.g., "stomach pain", "headache")
  2. **Specialization Selection:** Dropdown with 10 specializations:
     - Cardiology
     - Neurology
     - Orthopedics
     - Pediatrics
     - Dermatology
     - Ophthalmology
     - ENT
     - General Medicine
     - Gynecology
     - Psychiatry
  3. **Doctor Selection:** Dropdown showing 10 dummy doctors
  : Dr.Rajesh Kumar -Dr. Chandan Yeshi - Dr. Vaishali Anadinni - Dr. Vaishnavi Reddi - Dr. Pruthvi Shegavi -  (pre-registered in database)
     - Display: Doctor Name
     - Filter by selected specialization

**Final Display:**
- Show patient details in 1/4 A4 sheet format (top portion)
- Include: Name, DOB, Age, Gender, Address, Cause
- This format will be reused in reports and billing

### 1.2 Doctor Management

**Function:** Receptionist can ONLY register doctors (no login access)

**Registration Fields:**
- Doctor Name
- Doctor ID (unique identifier)
- Email
- Create Password
- Confirm Password
- Specialization (from the 10 specializations list)

**Action:** Register button saves to database

**Note:** Doctors login separately through Doctor Portal

### 1.3 Billing

**Layout Structure:**

**Top Section (1/4 A4 Sheet):**
- Patient Details (same format as registration)
- Cause
- Precautions (if available from doctor)

**Horizontal Line Separator**

**Main Billing Table:**
- **Columns:** Test | Amount | Status
- **Test Column:** List of tests (Blood, Urine, USG, ECG, etc.)
- **Amount Column:** Cost of each test
- **Status Column:** Dropdown/Select with options: "Paid" | "Unpaid"
- **Add Row Button:** Allows adding multiple test rows dynamically

**Save Button:**
- Small button below the table
- When clicked, calculates:
  - **Subtotal:** Sum of all test amounts
  - **Scheme Selection:** Dropdown with schemes (e.g., BPL, General, Insurance)
  - **Discount Percentage:** Based on selected scheme
    - BPL: 50%
    - General: 0%
    - Insurance: 30% (example)
  - **Discount Amount:** Calculated automatically (Subtotal × Discount %)
  - **Total Amount:** Subtotal - Discount Amount

**Display After Save:**
```
Subtotal: [calculated amount]
Scheme: [selected scheme] ([discount %]%)
Discount: [calculated discount amount]
Total Amount: [final amount]
```

**Download Button:**
- Large, prominent button
- Downloads complete billing report as PDF/printable format
- Includes: Patient details, test table, totals

**Interconnection:**
- When status is set to "Paid" for a test, automatically send patient details to respective test department (Others Portal)

---

## MODULE 2: DOCTOR PORTAL

### Authentication
**Login Only** (Registration done by Receptionist)
- Email
- Password

### Post-Login Layout
**Sidebar Structure:**
- **Top:** "Doctor"
- **Navigation Items:**
  1. Patient Login
     - Sub-options (after selecting patient):
       - Report Option (Patient Final Report)
       - Doctor Page
       - Test Report Option
- **Bottom:**
  - Display doctor's name
  - Sign Out button

**Back Button:** On every page

### 2.1 Patient Login
- **Field:** Aadhaar Number (formatted: XXXX XXXX XXXX)
- **Action:** Login button
- **Result:** Shows patient details and enables sub-options

### 2.2 Doctor Page

**Layout: A4 Sheet Format**

**Top Section (1/4 A4 Sheet):**
- Patient Details (from Aadhaar fetch)
- Cause
- Prescription (if any initial notes)

**Horizontal Line Separator**

**Main Section - Record Table:**
- **Columns:** Cause | Prescription | Test | Nurse
- **Add Record Button:** Adds new row with 4 empty columns
- **Functionality:**
  - **Cause:** Text input for patient's condition
  - **Prescription:** Text input for medicines/treatment
  - **Test:** Text input for required tests (e.g., "ECG", "USG", "Blood Test")
  - **Nurse:** 
    - Enabled only if patient is admitted
    - Selection box/dropdown for medicines and dosages
    - Options: Saline (10mg), Paracetamol (2 tablets), etc.
    - **Add Button:** Adds medicine + dosage to list

**Example Flow:**
1. Doctor clicks "Add Record"
2. Fills: Cause: "Stomach ache", Prescription: "Antacid", Test: "USG", Nurse: (if admitted)
3. Clicks "Add Record" again for second condition
4. Fills: Cause: "Headache", Prescription: "Paracetamol", Test: "", Nurse: ""

**Bottom Section:**
- **Left Side:** Doctor Name + Specialization (e.g., "Vaishnavi Reddy - Cardiologist")
- **Right Side:** Two buttons:
  1. **Summarize Button:**
     - Converts all medical terms to simple language
     - Sends summarized content to Patient Final Report
  2. **Discharge Button:**
     - Marks patient as discharged
     - Finalizes the record

### 2.3 Patient Final Report

**Layout:**

**Top Section (1/4 or 1/2 page):**
- Patient Details (same format as registration)
- Cause
- Precautions

**Horizontal Line Separator**

**Two-Column Layout:**

**Column 1: Summary**
- Simple, non-medical language summary
- Generated from Doctor Page after "Summarize" button click
- Easy to understand for layperson

**Column 2: Medicines**
- List of all prescribed medicines
- Format: Medicine Name, Dosage, Frequency
- Example: "Paracetamol - 500mg - Twice daily"

**Download Button:**
- Downloads complete report as PDF/printable format

**Interconnection:**
- Automatically visible to patient in Patient Portal after doctor finalizes

### 2.4 Test Report

**Layout:**
- **Table Structure:**
  - Serial Number | Test Name | View Option
- **Example:**
  - 1 | Blood Test | [View Button]
  - 2 | Urine Test | [View Button]
  - 3 | USG | [View Button]

**View Functionality:**
- Clicking "View" opens the uploaded test report (PDF/image)
- Report uploaded by test department (Others Portal)
- Display in modal or new page

**Interconnection:**
- Shows tests requested in Doctor Page
- Reports uploaded by Others Portal (Test Department)

---

## MODULE 3: OTHERS PORTAL (LAB & NURSE)

### 3.1 Test Department

#### Authentication
**Login Only** (No registration - pre-registered)
- Email
- Password

#### Post-Login Layout
**Sidebar Structure:**
- **Top:** "Test Department" or selected department name
- **Navigation:**
  1. Department Selection (if multiple departments)
  2. Patient Login (after selecting department)
- **Bottom:**
  - Display user's name
  - Sign Out button

#### Department Selection
- **Options:** Blood, Urine, USG, ECG, X-Ray, CT Scan, MRI, etc.
- **Display:** Cards or dropdown
- **Action:** Select department

#### Patient Login (After Department Selection)
- **Field:** Aadhaar Number (formatted: XXXX XXXX XXXX)
- **Action:** Login/Submit button

#### Patient Records Table
**Auto-Populated from Billing Module:**
- When Receptionist sets test status to "Paid" in Billing, patient details automatically appear here
- **Columns:**
  - Serial Number
  - Name
  - Aadhaar Number (formatted)
  - Age
  - Test Name (pre-filled based on department)
  - Upload Option (button)

**Upload Functionality:**
- Click "Upload" button
- File picker for PDF/image
- Upload test report
- Save to database
- **Interconnection:** Makes report visible in Doctor Portal → Test Report → View

**Note:** Records are NOT manually entered - they come automatically from Billing when payment is marked as "Paid"

### 3.2 Nurse Module

#### Authentication
**Login Only**
- Email
- Password

#### Post-Login Layout
**Sidebar Structure:**
- **Top:** "Nurse"
- **Navigation:**
  1. Patient Login
- **Bottom:**
  - Display nurse's name
  - Sign Out button

#### Patient Login
- **Field:** Aadhaar Number (formatted: XXXX XXXX XXXX)
- **Action:** Submit/OK button

#### Patient Prescription View
**Display (After Patient Login):**
- Shows doctor's prescriptions from Doctor Page (Nurse column)
- **Format:**
  - Medicine Name | Dosage | Checkbox
- **Example:**
  - Saline | 10mg | [ ] (checkbox)
  - Paracetamol | 2 tablets | [ ] (checkbox)

**Checkbox Functionality:**
- Nurse checks box when medicine is administered
- **Interconnection:** 
  - When checked, updates Doctor Portal → Doctor Page → Nurse column
  - Shows "Given" or checkmark in doctor's view

**Note:** Nurse module only shows prescriptions for admitted patients (where doctor enabled Nurse option)

---

## MODULE 4: PATIENT PORTAL

### Authentication
**Login Only**
- **Field:** Aadhaar Number (formatted: XXXX XXXX XXXX)
- **Action:** Login button

### Post-Login Layout
**Sidebar Structure:**
- **Top:** "Patient"
- **Navigation:**
  1. Patient Report
  2. Billing
- **Bottom:**
  - Display patient's name (from Aadhaar fetch)
  - Sign Out button

**Back Button:** On every page

### 4.1 Patient Report

**Content:** Same as Doctor Portal → Patient Final Report
- Patient Details (top section)
- Summary (simple language)
- Medicines list
- **Download Button:** Available

**Interconnection:**
- Automatically populated when doctor clicks "Summarize" in Doctor Page
- Read-only for patient

### 4.2 Billing

**Content:** Final billing view (read-only)
- Patient Details (top section)
- Test table with amounts
- Status (Paid/Unpaid) - visible but not editable
- Subtotal, Scheme, Discount, Total Amount
- **Download/Print Button:** Available

**Interconnection:**
- Same data as Receptionist → Billing
- Patient can only view, not edit
- Download functionality available

---

## DATABASE STRUCTURE

### Required Tables:

1. **receptionists**
   - id, hospital_name, receptionist_name, email, password, created_at

2. **doctors**
   - id, doctor_name, doctor_id (unique), email, password, specialization, created_at

3. **patients**
   - id, aadhaar_number (unique), name, date_of_birth, age, gender, address, cause, assigned_doctor_id, created_at

4. **patient_registrations**
   - id, patient_id, receptionist_id, registration_date, status

5. **doctor_pages**
   - id, patient_id, doctor_id, cause, prescription, test, nurse_instructions, summarize_text, created_at, updated_at

6. **patient_reports**
   - id, patient_id, doctor_id, summary, medicines, report_date, finalized

7. **billing**
   - id, patient_id, receptionist_id, test_name, amount, status (paid/unpaid), scheme, discount_percentage, discount_amount, subtotal, total_amount, created_at, updated_at

8. **test_departments**
   - id, department_name, email, password

9. **test_records**
   - id, patient_id, test_department_id, test_name, report_file_path, uploaded_at, status

10. **nurses**
    - id, nurse_name, email, password

11. **nurse_prescriptions**
    - id, patient_id, doctor_id, medicine_name, dosage, given_status, given_at, nurse_id

12. **demo_aadhaar_data**
    - aadhaar_number (primary key), name, date_of_birth, gender, address

### Demo Aadhaar Data (Pre-populate):
Store at least 3-5 demo Aadhaar numbers with complete details for testing "Fetch Details" functionality.

---

## INTERCONNECTION FLOW

### Flow 1: Patient Registration → Doctor → Billing → Test Department
1. Receptionist registers patient → Fetches Aadhaar details
2. Patient selects cause, specialization, doctor
3. Doctor logs in → Selects patient → Adds prescriptions/tests in Doctor Page
4. Receptionist creates billing → Adds tests → Sets status to "Paid"
5. **Auto:** Test department receives patient record
6. Test department uploads report
7. **Auto:** Doctor can view report in Test Report section

### Flow 2: Doctor → Nurse → Patient
1. Doctor enables Nurse option for admitted patient
2. Adds medicine + dosage in Nurse column
3. Nurse logs in → Selects patient → Sees prescriptions
4. Nurse checks checkbox when given
5. **Auto:** Doctor sees "Given" status in Doctor Page

### Flow 3: Doctor → Patient Report → Patient Portal
1. Doctor clicks "Summarize" in Doctor Page
2. **Auto:** Patient Final Report is generated
3. **Auto:** Patient Portal shows the report

### Flow 4: Billing → Patient Portal
1. Receptionist creates/updates billing
2. **Auto:** Patient Portal shows billing (read-only)

---

## TECHNICAL REQUIREMENTS

### Frontend:
- React.js
- React Router for navigation
- Responsive design (mobile + desktop)
- PDF generation library (for downloads)
- File upload functionality
- Form validation
- Aadhaar number formatting (XXXX XXXX XXXX)

### Backend:
- PHP (XAMPP)
- MySQL database
- RESTful API endpoints
- File upload handling
- Session management
- Password hashing (bcrypt)

### Security:
- Password hashing
- Session management
- Input validation
- SQL injection prevention (prepared statements)
- XSS prevention

### UI Components:
- Sidebar navigation (consistent across modules)
- Back button (small, top-left/right)
- Form inputs with validation
- Tables for data display
- Modal dialogs (for file views)
- Download buttons
- Add/Remove row functionality

---

## ADDITIONAL NOTES

1. **Aadhaar Formatting:** All Aadhaar inputs must auto-format to XXXX XXXX XXXX
2. **Demo Data:** Pre-populate database with:
   - Demo Aadhaar numbers (3-5)
   - Dummy doctors (10, with different specializations)
   - Test departments
   - Sample schemes for billing
3. **Background Images:** User will provide background images - apply to all modules consistently
4. **Responsive:** All pages must work on mobile and desktop
5. **Error Handling:** Proper error messages for invalid inputs, failed logins, etc.
6. **Loading States:** Show loading indicators for async operations
7. **Success Messages:** Confirm actions (registration successful, report uploaded, etc.)

---

## IMPLEMENTATION PRIORITY

1. Landing Page (Home, Portals, About Us, Contact Us)
2. Receptionists Portal - Complete flow
3. Doctor Portal - Complete flow
4. Others Portal (Test + Nurse) - Complete flow
5. Patient Portal - Complete flow
6. Interconnections between modules
7. Testing and refinement

---

## TESTING CHECKLIST

- [ ] All login/registration forms work
- [ ] Aadhaar formatting works correctly
- [ ] Fetch Details works with demo Aadhaar numbers
- [ ] Billing calculations are accurate
- [ ] Test department receives records when billing is paid
- [ ] Doctor can view uploaded test reports
- [ ] Nurse checkbox updates doctor's view
- [ ] Patient report appears in patient portal after doctor summarizes
- [ ] Download buttons generate proper PDFs
- [ ] All interconnections work as specified
- [ ] Responsive design works on mobile
- [ ] Back buttons work on all pages
- [ ] Sidebar navigation works correctly
- [ ] Sign out functionality works

---

**END OF SPECIFICATION**

This prompt contains all requirements for building the complete Medixa Smart Healthcare Management System. Use this as a comprehensive guide for AI-assisted development tools.

