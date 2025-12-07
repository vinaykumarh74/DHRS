# Digital Health Record System (DHRS)

A comprehensive digital health record system for citizens, doctors, and administrators of Ballari. This system is designed to manage medical records, appointments, prescriptions, and provide various healthcare services digitally.

## Features

### For Citizens
- User registration and authentication with OTP
- View and manage personal health records
- Book and manage appointments with doctors
- Access to prescriptions and lab reports
- Vaccination tracking
- Chronic disease management
- Telemedicine capabilities

### For Doctors
- Manage patient appointments
- Create and manage prescriptions
- View patient health records
- Upload lab results
- Schedule follow-ups

### For Administrators
- User management
- Doctor onboarding and verification
- System monitoring and reporting
- Analytics dashboard

## Technology Stack

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Additional Libraries**: Bootstrap, Chart.js, FPDF
- **External Services**: Twilio/PHPMailer for notifications

## Installation

1. Clone the repository
2. Import the database schema from `database/dhrs_db.sql`
3. Configure database connection in `config/config.php`
4. Set up a web server (Apache/Nginx) to serve the application
5. Access the application through the web browser

## Project Structure

```
/DHRS
├── assets/            # Static assets (CSS, JS, images)
├── config/            # Configuration files
├── controllers/       # Controller files
├── database/          # Database schema and migrations
├── includes/          # Reusable PHP components
├── models/            # Data models
├── uploads/           # User uploaded files
├── vendor/            # Third-party libraries
├── views/             # UI templates
├── index.php          # Entry point
└── README.md          # Project documentation
```

## Security Features

- Role-based access control
- Secure authentication with OTP
- Data encryption for sensitive information
- Input validation and sanitization
- Protection against common web vulnerabilities

## License

This project is licensed under the MIT License - see the LICENSE file for details.