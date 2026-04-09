# Medixa - Quick Setup Guide

## 🚀 Quick Start (5 Minutes)

### Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Click "Start" for **Apache**
3. Click "Start" for **MySQL**

### Step 2: Import Database
1. Open browser: `http://localhost/phpmyadmin`
2. Click "New" to create a database
3. Name it: `medixa_db`
4. Click "Import" tab
5. Choose file: `database/schema.sql`
6. Click "Go"

### Step 3: Access Application
1. Open browser: `http://localhost/MEDIXA/`
2. You should see the landing page!

## 📋 Testing the System

### Test Receptionist Portal
1. Go to Portals → Receptionists
2. Click "Register here"
3. Fill registration form:
   - Hospital Name: Test Hospital
   - Receptionist Name: Test User
   - Email: test@medixa.com
   - Password: password123
4. Click "Register"
5. Login with the credentials

### Test Patient Registration
1. After logging in as Receptionist
2. Go to Patient Management
3. Click "Patient Registration"
4. Enter Aadhaar: `1234 5678 9012`
5. Click "Fetch Details"
6. Details should auto-fill
7. Enter Cause: "Headache"
8. Select Specialization: "Cardiology"
9. Select Doctor: "Dr. Rajesh Kumar"
10. Click "Register Patient"

### Test Patient Login
1. In Patient Management
2. Click "Patient Login"
3. Enter Aadhaar: `1234 5678 9012`
4. Click "Login"
5. Patient details should display

### Test Billing
1. Go to Billing
2. Enter Aadhaar: `1234 5678 9012`
3. Click "Load Patient"
4. Click "Add Test"
5. Enter Test: "Blood Test", Amount: 300
6. Set Status: "Paid"
7. Click "Add Test" again
8. Enter Test: "USG", Amount: 500
9. Set Status: "Unpaid"
10. Click "Save"
11. Select Scheme: "BPL"
12. Total should calculate automatically
13. Click "Download Bill"

## 🔑 Default Credentials

### Demo Aadhaar Numbers (for testing)
- `1234 5678 9012` - Rajesh Kumar
- `2345 6789 0123` - Priya Sharma
- `3456 7890 1234` - Amit Patel
- `4567 8901 2345` - Sneha Reddy
- `5678 9012 3456` - Vikram Singh

### Pre-populated Doctors
- Email: `rajesh.kumar@medixa.com`
- Password: `password`
- (Same password for all pre-populated doctors)

### Test Departments
- Email: `blood@medixa.com`
- Password: `password`
- (Same for all test departments)

## ⚠️ Common Issues

### Issue: Database connection error
**Solution**: 
- Check if MySQL is running in XAMPP
- Verify database name is `medixa_db`
- Check `config/database.php` settings

### Issue: Aadhaar fetch not working
**Solution**:
- Ensure you're using demo Aadhaar numbers
- Check `demo_aadhaar_data` table has data
- Verify API endpoint is accessible

### Issue: Session not working
**Solution**:
- Clear browser cache
- Check PHP session configuration
- Ensure `session_start()` is called

### Issue: Icons not showing
**Solution**:
- Check internet connection (Font Awesome CDN)
- Verify CDN link in HTML files

## 📁 Project Structure

```
MEDIXA/
├── api/              # PHP backend APIs
├── assets/           # CSS, JS, Images
├── config/           # Configuration files
├── database/         # SQL schema
├── portals/          # Portal pages
│   ├── restricted/  # Receptionists portal ✅
│   ├── doctor/      # Doctor portal (TODO)
│   ├── others/      # Test & Nurse (TODO)
│   └── patient/     # Patient portal (TODO)
└── index.html       # Landing page ✅
```

## ✅ What's Working

- ✅ Landing page
- ✅ Receptionist login/registration
- ✅ Patient management (login/registration)
- ✅ Aadhaar fetch and auto-fill
- ✅ Doctor management
- ✅ Billing system
- ✅ Database structure
- ✅ All APIs for Receptionists portal

## 🔄 What's Next

- [ ] Doctor Portal implementation
- [ ] Others Portal (Test & Nurse)
- [ ] Patient Portal
- [ ] Additional API endpoints
- [ ] Background images integration
- [ ] Final testing

## 💡 Tips

1. **Always use demo Aadhaar numbers** for testing
2. **Register a receptionist first** before testing other features
3. **Check browser console** for JavaScript errors
4. **Check PHP error logs** in XAMPP for backend issues
5. **Use Chrome DevTools** to debug

## 📞 Support

For issues or questions:
1. Check `README.md` for detailed documentation
2. Review `PROJECT_SUMMARY.md` for implementation status
3. Check browser console and PHP error logs

---

**Happy Coding! 🚀**

