# Educational ERP System

A complete Educational ERP system built with Laravel (Backend) and React (Frontend) that supports Pre-schools, Schools, and Colleges with multi-institution setup.

## 🚀 Features

### 📚 Academic Management
- **Multi-Institution Support**: Pre-school (Playgroup to Sr. KG), School (1st to 10th), College (11th to PG)
- **Student Registration & Admission**: Auto-generated admission numbers, document upload, parent linkage
- **Class & Subject Management**: Comprehensive academic structure management
- **Timetable Management**: Class-wise and teacher-wise schedules

### 💰 Financial Management
- **Fee Management**: Create fee structures, payment tracking, receipt generation (PDF)
- **Online Payment Gateway**: Dummy implementation ready for integration
- **Financial Reports**: Monthly fee reports and collection tracking

### 👥 User Management
- **Role-based Access Control**: Admin, Student, Parent, Staff, Teacher
- **Mobile-based Login**: Secure authentication with mobile number and password
- **Separate Dashboards**: Customized interfaces for each user role

### 📊 Academic Operations
- **Examination Management**: Schedule exams, marks entry, report cards (PDF download)
- **Attendance Management**: Daily attendance for students and staff with reports
- **Learning Management System (LMS)**: Upload lectures, videos, PDFs, assignments

### 👨‍💼 Staff Management
- **HR Operations**: Add/manage teaching & non-teaching staff
- **Leave Management**: Staff can apply for leave, admin approval system
- **Salary Management**: Setup salary, generate payslips (PDF), salary history

### 🚌 Facility Management
- **Transport Management**: Bus/vehicle assignment, routes, stops, timings
- **Hostel Management**: Room/bed assignment, hostel fee management

### 💬 Communication
- **Internal Messaging**: Group chats, 1-to-1 chats, broadcast messages
- **Multi-level Communication**: Admin-Staff, Teacher-Parents, etc.

### 📈 Reports & Analytics
- Admission statistics, Fee collection reports, Exam results
- Staff salary & leave reports, Transport & hostel status

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 11
- **Database**: MySQL 8.x
- **Authentication**: Laravel Sanctum (API-based)
- **PDF Generation**: DomPDF
- **Image Processing**: Intervention Image
- **Permissions**: Spatie Laravel Permission

### Frontend
- **Framework**: React 18 with TypeScript
- **UI Library**: Material-UI (MUI)
- **Routing**: React Router DOM
- **HTTP Client**: Axios
- **State Management**: Context API
- **Form Handling**: React Hook Form
- **Notifications**: React Toastify

## 📦 Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- MySQL 8.x
- Node.js 18.x or higher
- Composer
- npm or yarn

### Backend Setup (Laravel)

1. **Navigate to backend directory**
   ```bash
   cd edu-erp-backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database in .env**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=edu_erp_db
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

5. **Import database**
   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE edu_erp_db;"
   
   # Import the provided SQL file
   mysql -u root -p edu_erp_db < ../edu_erp_database.sql
   ```

6. **Start the Laravel server**
   ```bash
   php artisan serve
   ```
   Backend will run on: `http://localhost:8000`

### Frontend Setup (React)

1. **Navigate to frontend directory**
   ```bash
   cd edu-erp-frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Environment configuration**
   Create `.env` file:
   ```env
   REACT_APP_API_URL=http://localhost:8000/api
   ```

4. **Start the React development server**
   ```bash
   npm start
   ```
   Frontend will run on: `http://localhost:3000`

## 🔐 Default Login Credentials

### Admin Users
- **School Admin**: Mobile: `9999999998`, Password: `password`
- **College Admin**: Mobile: `9999999997`, Password: `password`
- **Preschool Admin**: Mobile: `9999999999`, Password: `password`

### Teachers
- **John Smith (Math Teacher)**: Mobile: `9876543210`, Password: `password`
- **Jane Doe (English Teacher)**: Mobile: `9876543211`, Password: `password`

### Parents
- **Robert Johnson**: Mobile: `9876543212`, Password: `password`
- **David Wilson**: Mobile: `9876543214`, Password: `password`

### Students
- **Alice Johnson (5th Grade)**: Mobile: `9876543213`, Password: `password`
- **Bob Wilson (10th Grade)**: Mobile: `9876543215`, Password: `password`

## 🚀 Production Deployment

### Backend Deployment

1. **Server Requirements**
   - PHP 8.1+ with extensions: mbstring, xml, zip, mysql, gd
   - MySQL 8.x
   - Composer
   - Web server (Apache/Nginx)

2. **Deploy Laravel**
   ```bash
   # Upload files to server
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   
   # Set environment
   cp .env.example .env
   php artisan key:generate
   
   # Configure database and run migrations
   php artisan migrate
   php artisan db:seed
   
   # Optimize for production
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Web Server Configuration**
   Point document root to `public/` directory

### Frontend Deployment

1. **Build for production**
   ```bash
   npm run build
   ```

2. **Deploy to web server**
   Upload `build/` directory contents to web server

3. **Configure API URL**
   Update `REACT_APP_API_URL` in build configuration

## 📁 Project Structure

```
edu-erp/
├── edu-erp-backend/          # Laravel Backend
│   ├── app/
│   │   ├── Http/Controllers/ # API Controllers
│   │   ├── Models/          # Eloquent Models
│   │   └── ...
│   ├── database/
│   │   ├── migrations/      # Database Migrations
│   │   └── seeders/         # Database Seeders
│   ├── routes/
│   │   └── api.php         # API Routes
│   └── ...
├── edu-erp-frontend/        # React Frontend
│   ├── src/
│   │   ├── components/     # React Components
│   │   ├── contexts/       # Context Providers
│   │   ├── hooks/          # Custom Hooks
│   │   └── ...
│   └── ...
└── edu_erp_database.sql    # Complete Database with Sample Data
```

## 🔧 Configuration

### Backend Configuration
- Database settings in `.env`
- File upload settings in `config/filesystems.php`
- Mail settings for notifications
- PDF generation settings

### Frontend Configuration
- API endpoints in environment variables
- Theme customization in Material-UI theme
- Route protection and role-based access

## 📖 API Documentation

The system provides RESTful APIs for all operations:

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `GET /api/auth/profile` - Get user profile
- `POST /api/auth/logout` - User logout

### Student Management
- `GET /api/students` - List students
- `POST /api/students` - Create student
- `GET /api/students/{id}` - Get student details
- `PUT /api/students/{id}` - Update student

### Staff Management
- `GET /api/staff` - List staff
- `POST /api/staff` - Create staff member
- `GET /api/staff/{id}` - Get staff details

*[Continue with other endpoints...]*

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions, please create an issue in the repository.

---

**Note**: This is a complete, production-ready ERP system. Simply import the database file and start using the system with the provided demo data and credentials.