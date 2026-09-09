# CSCS School Management System

A comprehensive School Management System built with CodeIgniter 4 for Loboc Pilot High School (LPHS).

## Features

- **Student Management**: Complete student enrollment, profile management, and academic tracking
- **Teacher Management**: Teacher profiles, schedules, and class assignments
- **Grade Management**: Grade recording, report cards, and academic analytics
- **Attendance Tracking**: Daily attendance monitoring and reporting
- **Announcements**: School-wide communication system
- **Parent Portal**: Parent access to student information and progress
- **Document Management**: Upload and manage student documents
- **Analytics Dashboard**: Comprehensive reporting and analytics

## System Requirements

- PHP version 8.1 or higher
- MySQL/MariaDB database
- Web server (Apache/Nginx)
- Composer for dependency management

## Installation

1. Clone the repository:
```bash
git clone https://github.com/Teo199-2005/CAPSTONE-NA-SA-FRIDAY.git
cd CAPSTONE-NA-SA-FRIDAY
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp env .env
```

4. Update database settings in `.env` file:
```
database.default.hostname = localhost
database.default.database = lphs_sms
database.default.username = your_username
database.default.password = your_password
```

5. Import the database:
```bash
mysql -u your_username -p lphs_sms < app/TRANSFER.sql
```

6. Set up web server to point to the `public` folder

## Usage

### Default Login Credentials

**Admin:**
- Username: admin
- Password: admin123

**Teacher:**
- Username: teacher1
- Password: teacher123

**Student:**
- LRN: [Student LRN]
- Password: [Default password]

## Project Structure

```
lphs-sms/
├── app/
│   ├── Controllers/     # Application controllers
│   ├── Models/         # Database models
│   ├── Views/          # View templates
│   ├── Config/         # Configuration files
│   └── Database/       # Migrations and seeds
├── public/             # Web accessible files
├── writable/           # Cache, logs, uploads
└── vendor/             # Composer dependencies
```

## Key Features

### For Administrators
- Complete student and teacher management
- Grade oversight and analytics
- System configuration
- Report generation

### For Teachers
- Class management
- Grade recording
- Attendance tracking
- Student progress monitoring

### For Students
- View grades and attendance
- Access announcements
- Profile management

### For Parents
- Monitor child's academic progress
- View attendance records
- Receive school announcements

## Contributing

This is a capstone project for educational purposes. For any issues or suggestions, please contact the development team.

## License

This project is developed as part of an academic capstone project.

## Support

For technical support or questions about the system, please contact the development team.
