# Education ERP System

A comprehensive Enterprise Resource Planning (ERP) system designed for educational institutions supporting multi-institute setups including preschools, schools, and colleges.

## 🎯 Overview

This ERP system supports complete management of educational institutions with the following key features:

### Supported Institutions:
- **Pre-school**: Playgroup to Sr. KG
- **School**: 1st to 10th grade
- **College**: 11th to PG level

### Technology Stack:
- **Frontend**: React.js 18/19 with TypeScript
- **Backend**: Laravel 11 with API-based architecture
- **Authentication**: Laravel Sanctum
- **Database**: MySQL
- **CSS**: Pure CSS (No Material UI)

## 👥 User Roles

1. **Super Admin** - System-wide management
2. **Admin** - Institute-level administration
3. **Staff** - Administrative staff
4. **Teacher** - Academic staff
5. **Student** - Enrolled students
6. **Parent** - Student guardians

## ✨ Core Features

### 1. Registration & Admission
- Student registration with auto-generated admission numbers
- Document upload functionality
- Parent profile linking
- Class assignment

### 2. Authentication & Authorization
- Mobile number + password login
- OTP-based authentication
- Role-based dashboard redirection
- Laravel Sanctum token-based API security

### 3. Fee Management
- Dynamic fee structure creation
- Multiple payment methods (Cash, Online, Card, etc.)
- Receipt generation (PDF)
- Late fee calculation
- Monthly collection reports
- Dummy payment gateway integration

### 4. Academic Management
- Syllabus management by class
- Subject assignment to teachers
- Study material uploads

### 5. Timetable Management
- Weekly class schedules
- Teacher-wise and class-wise views
- Room allocation

### 6. Examination & Results
- Exam schedule creation
- Marks entry system
- Result summary and report cards
- PDF report generation

### 7. Learning Management System (LMS)
- Video, document, and assignment uploads
- Class/subject-wise material organization
- Assignment submission and grading

### 8. Staff Management
- Staff profile management
- Department and role assignments
- Leave management system
- Salary management with payslip generation

### 9. Student Management
- Complete student profiles
- Academic records tracking
- Attendance monitoring
- Parent portal access

### 10. Communication System
- Internal chat system
- Group messaging (Admin-Staff, Teacher-Parents)
- Individual authenticated messaging

### 11. Attendance Module
- Daily attendance marking
- Monthly attendance reports
- Student and staff tracking

### 12. Transport Management
- Vehicle and route management
- Stop assignments with timings
- Transport fee collection
- Route-wise reporting

### 13. Hostel Management
- Room and bed allocation
- Hostel fee management
- Attendance tracking

### 14. Comprehensive Reporting
- Admission statistics
- Fee collection reports
- Academic performance analytics
- Transport and hostel summaries
- Staff management reports

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Node.js 16+
- Composer

### Backend Setup (Laravel)

1. **Clone and Navigate**
   ```bash
   cd education-erp-system/backend
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   ```

4. **Configure Database**
   Edit `.env` file:
   ```env
   DB_DATABASE=education_erp
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Import Database**
   ```bash
   mysql -u your_username -p education_erp < ../education_erp.sql
   ```

7. **Run Migrations (Optional - if not using SQL import)**
   ```bash
   php artisan migrate --seed
   ```

8. **Start Server**
   ```bash
   php artisan serve
   ```

### Frontend Setup (React)

1. **Navigate to Frontend**
   ```bash
   cd education-erp-system/frontend
   ```

2. **Install Dependencies**
   ```bash
   npm install
   ```

3. **Start Development Server**
   ```bash
   npm start
   ```

### Access the Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000

## 🔐 Default Login Credentials

| Role | Email | Password | Description |
|------|--------|----------|-------------|
| Super Admin | superadmin@erp.com | password123 | System-wide access |
| Preschool Admin | admin@littlestars.edu | password123 | Little Stars Preschool |
| School Admin | admin@brightfuture.edu | password123 | Bright Future School |
| College Admin | admin@excellence.edu | password123 | Excellence College |

## 📁 Project Structure

```
education-erp-system/
├── backend/                    # Laravel Backend
│   ├── app/
│   │   ├── Http/Controllers/API/    # API Controllers
│   │   ├── Models/                  # Eloquent Models
│   │   └── ...
│   ├── database/
│   │   ├── migrations/              # Database Migrations
│   │   └── seeders/                 # Sample Data Seeders
│   ├── routes/
│   │   └── api.php                  # API Routes
│   └── ...
├── frontend/                   # React Frontend
│   ├── src/
│   │   ├── components/              # Reusable Components
│   │   ├── pages/                   # Page Components
│   │   ├── services/                # API Services
│   │   ├── auth/                    # Authentication Logic
│   │   └── ...
│   └── ...
└── education_erp.sql          # Database Schema & Sample Data
```

## 🔌 API Endpoints

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `POST /api/auth/send-otp` - Send OTP
- `POST /api/auth/verify-otp` - Verify OTP
- `POST /api/auth/logout` - User logout

### Student Management
- `GET /api/students` - List students
- `POST /api/students` - Create student
- `GET /api/students/{id}` - Get student details
- `PUT /api/students/{id}` - Update student
- `POST /api/students/{id}/documents` - Upload documents

### Fee Management
- `GET /api/fees/structures` - Get fee structures
- `POST /api/fees/payments` - Record payment
- `GET /api/fees/student/{id}` - Get student fees
- `GET /api/fees/receipt/{id}` - Generate receipt

### Additional Modules
- Attendance, Exams, Timetables, LMS, Communication, Transport, Hostel
- Complete API documentation available via routes file

## 🏗️ Deployment

### Production Deployment

1. **Environment Configuration**
   ```bash
   # Update .env for production
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```

2. **Build Frontend**
   ```bash
   cd frontend
   npm run build
   ```

3. **Database Setup**
   ```bash
   # Import SQL file or run migrations
   mysql -u username -p database_name < education_erp.sql
   ```

4. **Laravel Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Hostinger Deployment

1. Upload files via File Manager or Git
2. Import `education_erp.sql` via phpMyAdmin
3. Update `.env` with Hostinger database credentials
4. Configure domain to point to `public` folder

## 🧪 Testing

### Backend Testing
```bash
cd backend
php artisan test
```

### Frontend Testing
```bash
cd frontend
npm test
```

## 📱 Postman Collection

Import the included Postman collection (`ERP_API_Collection.json`) to test all API endpoints with pre-configured requests and sample data.

## 🛠️ Development

### Adding New Features

1. **Backend**: Create new controllers in `app/Http/Controllers/API/`
2. **Frontend**: Add components in `src/components/` or `src/pages/`
3. **Database**: Create migrations with `php artisan make:migration`
4. **API**: Update `routes/api.php` for new endpoints

### Code Standards

- Follow PSR-12 for PHP code
- Use ESLint and Prettier for TypeScript/React
- Maintain consistent naming conventions
- Document all new API endpoints

## 🔒 Security Features

- Laravel Sanctum API authentication
- Role-based access control
- CSRF protection
- Input validation and sanitization
- SQL injection prevention
- File upload security

## 📊 Features Completion

✅ Multi-institute support (Preschool, School, College)  
✅ Complete user role management  
✅ Student registration & admission  
✅ Fee management with payment gateway  
✅ Academic management  
✅ Examination & result system  
✅ Attendance tracking  
✅ LMS with file uploads  
✅ Staff & salary management  
✅ Communication system  
✅ Transport management  
✅ Hostel management  
✅ Comprehensive reporting  
✅ PDF generation capabilities  
✅ Mobile-responsive design  

## 📞 Support

For support and questions:
- Review API documentation in routes files
- Check sample data in database seeders
- Refer to controller implementations for business logic

## 📜 License

This project is open-source and available under the MIT License.

---

**Ready for Production Deployment** 🚀  
Complete ERP solution with all requested features implemented and tested.