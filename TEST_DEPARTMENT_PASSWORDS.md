# Test Department Passwords

## Default Password
All test departments use the same default password: **`password`**

## Test Department Login Credentials

| Department | Email | Password |
|------------|-------|----------|
| Blood | blood@medixa.com | password |
| Urine | urine@medixa.com | password |
| USG | usg@medixa.com | password |
| ECG | ecg@medixa.com | password |
| X-Ray | xray@medixa.com | password |
| CT Scan | ctscan@medixa.com | password |
| MRI | mri@medixa.com | password |

## Note
- All passwords are encrypted using bcrypt in the database
- The default password hash is: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`
- This corresponds to the plain text password: **`password`**

## Usage
When logging into any test department portal, use:
- **Email**: [department]@medixa.com (e.g., blood@medixa.com)
- **Password**: password

