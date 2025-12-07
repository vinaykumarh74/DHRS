# DHRS Deployment Guide

## 🚀 **Quick Start Guide**

### **Prerequisites**
- **Web Server**: XAMPP, WAMP, or similar (Apache + PHP + MySQL)
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Browser**: Modern web browser (Chrome, Firefox, Safari, Edge)

### **Step 1: Database Setup**

1. **Start your web server** (XAMPP, WAMP, etc.)
2. **Open phpMyAdmin** (usually at `http://localhost/phpmyadmin`)
3. **Create a new database**:
   - Click "New" in the left sidebar
   - Enter database name: `dhrs_db`
   - Select collation: `utf8mb4_unicode_ci`
   - Click "Create"

4. **Import the database schema**:
   - Select the `dhrs_db` database
   - Click "Import" tab
   - Choose file: `database/updated_schema.sql`
   - Click "Go" to import

### **Step 2: Configuration**

1. **Edit `config/config.php`**:
   ```php
   // Database configuration
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');           // Your MySQL username
   define('DB_PASS', '');               // Your MySQL password
   define('DB_NAME', 'dhrs_db');
   
   // Application configuration
   define('SITE_URL', 'http://localhost/DHRS');
   define('SITE_NAME', 'Digital Health Record System');
   
   // Security configuration
   define('HASH_COST', 12);
   define('SESSION_TIMEOUT', 3600);
   ```

2. **Set proper permissions**:
   ```bash
   chmod 755 uploads/
   chmod 644 config/config.php
   ```

### **Step 3: Test the System**

1. **Open your browser** and navigate to: `http://localhost/DHRS`
2. **Test the function system**: `http://localhost/DHRS/test_functions.php`
3. **Register a new user** to test the system

### **Step 4: Create Initial Users**

#### **Create Admin User**
1. Register a new user with role "admin"
2. Verify the account using OTP
3. Log in and access admin dashboard

#### **Create Doctor User**
1. Register a new user with role "doctor"
2. Fill in doctor-specific information
3. Verify the account
4. Log in and set up schedule

#### **Create Citizen User**
1. Register a new user with role "citizen"
2. Fill in personal information
3. Verify the account
4. Log in and complete health profile

## 🔧 **Troubleshooting**

### **Common Issues**

#### **Database Connection Error**
- Check if MySQL service is running
- Verify database credentials in `config/config.php`
- Ensure database `dhrs_db` exists

#### **Function Already Declared Error**
- ✅ **FIXED**: Duplicate functions have been resolved
- If you see this error, check for any custom modifications

#### **Page Not Found (404)**
- Ensure Apache mod_rewrite is enabled
- Check file permissions
- Verify .htaccess file exists (if using URL rewriting)

#### **Session Issues**
- Check if PHP sessions are enabled
- Verify session directory permissions
- Clear browser cookies if needed

### **File Permissions**
```bash
# Set proper permissions for uploads
chmod -R 755 uploads/
chmod -R 755 assets/

# Set proper permissions for configuration
chmod 644 config/config.php
chmod 644 includes/*.php
```

## 📱 **Testing the System**

### **Test Scenarios**

1. **User Registration & Login**
   - Test citizen registration
   - Test doctor registration
   - Test admin registration
   - Test OTP verification
   - Test login functionality

2. **Appointment System**
   - Book appointment as citizen
   - Confirm appointment as doctor
   - Complete appointment as doctor
   - Cancel appointment as citizen

3. **Prescription System**
   - Create prescription as doctor
   - View prescription as citizen
   - Download prescription as PDF

4. **Profile Management**
   - Update citizen profile
   - Update doctor profile
   - Update health profile
   - Upload profile images

### **Expected Behavior**

- ✅ **Registration**: Users can register with different roles
- ✅ **OTP Verification**: Email/phone verification works
- ✅ **Login**: Users can log in and access appropriate dashboards
- ✅ **Appointments**: Full appointment lifecycle works
- ✅ **Prescriptions**: Prescription creation and management works
- ✅ **Profiles**: Profile updates and health information works

## 🚀 **Production Deployment**

### **Security Considerations**
1. **Change default passwords**
2. **Use HTTPS in production**
3. **Set up proper file permissions**
4. **Configure firewall rules**
5. **Regular security updates**

### **Performance Optimization**
1. **Enable PHP OPcache**
2. **Configure MySQL query cache**
3. **Use CDN for static assets**
4. **Implement caching strategies**

### **Backup Strategy**
1. **Regular database backups**
2. **File system backups**
3. **Configuration backups**
4. **Test restore procedures**

## 📞 **Support**

If you encounter issues:

1. **Check the error logs** in your web server
2. **Verify database connectivity**
3. **Test individual components** using the test script
4. **Check file permissions** and ownership
5. **Review the PROJECT_STATUS.md** for current progress

## 🎯 **Next Steps After Deployment**

1. **Test all functionality** thoroughly
2. **Create sample data** for demonstration
3. **Train users** on the system
4. **Implement additional features** as needed
5. **Set up monitoring** and logging

---

**🎉 Congratulations! Your DHRS system should now be running successfully!**

If you need help with any specific feature or encounter issues, refer to the troubleshooting section above.
