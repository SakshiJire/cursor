# EduERP Deployment Guide

## Quick Start (5 Minutes Setup)

### 1. Prerequisites
- PHP 8.1+ with MySQL extension
- MySQL 8.x server
- Node.js 18.x+
- Composer installed globally

### 2. Database Setup
```bash
# Start MySQL service
sudo systemctl start mysql  # Linux
# OR
brew services start mysql   # macOS

# Create database and import schema
mysql -u root -p -e "CREATE DATABASE edu_erp_db;"
mysql -u root -p edu_erp_db < edu_erp_database.sql
```

### 3. Backend Setup
```bash
cd edu-erp-backend
composer install
cp .env.example .env
php artisan key:generate

# Update .env file with your database credentials:
# DB_DATABASE=edu_erp_db
# DB_USERNAME=root
# DB_PASSWORD=your_password

php artisan serve
```
Backend runs on: `http://localhost:8000`

### 4. Frontend Setup
```bash
cd edu-erp-frontend
npm install
npm start
```
Frontend runs on: `http://localhost:3000`

## Login & Test

Navigate to `http://localhost:3000` and login with:

**Admin**: Mobile: `9999999998`, Password: `password`
**Teacher**: Mobile: `9876543210`, Password: `password`
**Parent**: Mobile: `9876543212`, Password: `password`
**Student**: Mobile: `9876543213`, Password: `password`

## Production Deployment

### Backend (Laravel)
1. Upload files to server
2. Run `composer install --optimize-autoloader --no-dev`
3. Configure `.env` with production settings
4. Import database: `mysql -u username -p database_name < edu_erp_database.sql`
5. Set document root to `public/` directory
6. Configure web server (Apache/Nginx)

### Frontend (React)
1. Run `npm run build`
2. Upload `build/` folder contents to web server
3. Configure API URL in build environment

## Features Included

✅ Multi-institution support (Preschool, School, College)
✅ Role-based authentication (Admin, Teacher, Student, Parent, Staff)
✅ Student registration and management
✅ Staff and teacher management
✅ Fee management with payment tracking
✅ Attendance management
✅ Examination and result management
✅ Timetable management
✅ Learning Management System (LMS)
✅ Leave and salary management
✅ Transport and hostel management
✅ Internal messaging system
✅ Comprehensive reporting

## Support

The system is fully functional with sample data. All major ERP features are implemented and ready for production use.

For customizations or additional features, modify the Laravel controllers and React components as needed.