# PreSchool ERP Management System

A comprehensive ERP (Enterprise Resource Planning) system designed specifically for pre-school management, built with Laravel 12 and featuring a complete API-based architecture.

## 🌟 Features

### 📚 Academic Management
- **Student Registration & Admission Management**
  - Complete student profiles with photos and medical information
  - Admission number generation and tracking
  - Class-wise student organization (Playgroup, Nursery, Jr.KG, Sr.KG)
  - Student promotion and transfers

### 💰 Fee Management
- **Comprehensive Fee System**
  - Multiple fee types (Monthly, Admission, Annual, Transport, etc.)
  - Automated monthly fee generation
  - Payment tracking with receipt generation
  - Overdue fee monitoring and reminders
  - Fee summary reports

### 👩‍🏫 Staff Management
- **Employee Management**
  - Staff registration and profile management
  - Role-based designations (Principal, Teacher, Admin, Helper, Security)
  - Salary management and employee records
  - Document storage and management

### 👨‍👩‍👧‍👦 Parent Management
- **Parent Portal**
  - Parent registration and profile management
  - Multiple parent-student relationships
  - Emergency contact management
  - Communication with school staff

### 📅 Timetable Management
- **Auto-Generated Timetables**
  - Intelligent timetable generation algorithm
  - Class and subject scheduling
  - Staff assignment to periods
  - Conflict detection and resolution
  - Copy timetables between classes

### 📝 Exam & Assessment
- **Examination System**
  - Multiple exam types (Unit Tests, Mid-term, Final)
  - Auto-generated mark sheets
  - Grade calculation and reporting
  - Result analysis and tracking

### 🎯 Activity Management
- **Event & Activity Planning**
  - Activity scheduling and management
  - Student participation tracking
  - Sports, cultural, and educational events
  - Registration and attendance management

### 📖 Learning Materials
- **Digital Content Management**
  - Upload and categorize learning materials
  - Video, audio, document, and image support
  - Class and subject-wise organization
  - Download tracking and analytics

### 💬 Communication Module
- **Integrated Messaging System**
  - Private messaging between users
  - Group chats (class-wise, staff, parents)
  - File and media sharing
  - Message reactions and replies
  - Real-time notifications

### 🔐 Role-Based Access Control
- **Multi-Role Authentication**
  - Admin: Full system access
  - Staff: Teaching and management functions
  - Students: Learning materials and activities
  - Parents: Child's progress and communication

## 🏗️ System Architecture

### Database Schema
The system uses a comprehensive relational database with the following key entities:
- Users & Authentication
- Students & Parents
- Classes & Subjects
- Staff & Roles
- Fees & Payments
- Timetables & Schedules
- Exams & Results
- Activities & Participation
- Learning Materials
- Communication & Messages

### API Architecture
- **RESTful API Design**: Clean, consistent API endpoints
- **JWT Authentication**: Secure token-based authentication
- **Role-based Permissions**: Granular access control
- **Standardized Responses**: Consistent JSON response format
- **Comprehensive Documentation**: Complete API documentation

## 🚀 Installation

### Requirements
- PHP 8.2 or higher
- Composer
- MySQL 8.0 or higher
- Node.js (for frontend if needed)
- Redis (for caching and queues)

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd preschool-erp
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### Step 4: Database Configuration
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=preschool_erp
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Migrations and Seeders
```bash
php artisan migrate
php artisan db:seed
```

### Step 6: Storage Setup
```bash
php artisan storage:link
```

### Step 7: Start the Server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## 🔑 Default Login Credentials

After running the seeders, you can use these credentials:

### Admin
- **Email**: admin@preschool.com
- **Password**: admin123

### Principal
- **Email**: principal@preschool.com
- **Password**: principal123

### Teacher
- **Email**: sunita@preschool.com
- **Password**: teacher123

## 📖 API Documentation

The system provides a comprehensive REST API. See [API_COLLECTION.md](API_COLLECTION.md) for detailed documentation including:

- Authentication endpoints
- Student management
- Fee management
- Timetable generation
- Exam and assessment
- Communication system
- Learning materials
- Activity management

### API Base URL
```
http://localhost:8000/api
```

### Authentication
All protected endpoints require a Bearer token:
```
Authorization: Bearer {your-token}
```

## 🏫 Pre-School Classes Supported

The system comes pre-configured with standard pre-school classes:

1. **Playgroup (PG)** - Ages 2-3
   - Capacity: 20 students
   - Monthly Fee: ₹2,000
   - Subjects: Rhymes, Drawing, Physical Activity, Craft

2. **Nursery (NUR)** - Ages 3-4
   - Capacity: 25 students
   - Monthly Fee: ₹2,500
   - Subjects: English, Rhymes, Drawing, Physical Activity, Craft

3. **Jr. KG (JKG)** - Ages 4-5
   - Capacity: 25 students
   - Monthly Fee: ₹3,000
   - Subjects: English, Hindi, Mathematics, Drawing, Physical Activity, Craft, General Knowledge

4. **Sr. KG (SKG)** - Ages 5-6
   - Capacity: 25 students
   - Monthly Fee: ₹3,500
   - Subjects: English, Hindi, Mathematics, General Knowledge, Drawing, Physical Activity, Craft

## 🎨 Features in Detail

### 📊 Dashboard
- Real-time statistics and analytics
- Quick access to key metrics
- Role-based dashboard views
- Recent activities and notifications

### 🔄 Auto-Generated Features

#### Timetable Generation
- Intelligent algorithm considers:
  - Subject periods per week
  - Staff availability
  - Classroom capacity
  - Break and lunch timings
  - No conflicts between periods

#### Monthly Fee Generation
- Automatically creates monthly fees for all students
- Class-wise fee structure
- Bulk generation for multiple classes
- Due date calculations

#### Mark Sheet Generation
- PDF generation for exam results
- Class-wise and student-wise reports
- Grade calculations based on marks
- Professional formatting

### 📱 Communication Features

#### Group Messaging
- Class-wise parent groups
- Staff communication groups
- Announcement broadcasts
- File and media sharing

#### Private Messaging
- One-on-one conversations
- Parent-teacher communication
- Admin-staff messaging
- Message read receipts

#### Notifications
- Real-time push notifications
- Email notifications
- SMS integration ready
- Announcement system

### 📈 Reporting & Analytics

#### Student Reports
- Academic performance tracking
- Attendance reports
- Fee payment history
- Activity participation

#### Financial Reports
- Fee collection summary
- Outstanding payments
- Payment method analysis
- Monthly revenue reports

#### Administrative Reports
- Enrollment statistics
- Staff performance
- Class capacity utilization
- Activity participation rates

## 🛠️ Technology Stack

### Backend
- **Laravel 12**: PHP framework
- **MySQL**: Database
- **Redis**: Caching and queues
- **Laravel Sanctum**: API authentication
- **Spatie Laravel Permission**: Role management

### Additional Packages
- **Intervention Image**: Image processing
- **Laravel Excel**: Excel import/export
- **Pusher**: Real-time communication
- **Laravel Horizon**: Queue monitoring

## 🔧 Configuration

### File Upload Settings
Configure in `.env`:
```env
MAX_FILE_SIZE=10240
ALLOWED_FILE_TYPES=jpg,jpeg,png,pdf,doc,docx
```

### Academic Year Settings
```env
ACADEMIC_YEAR_START_MONTH=4
FEE_DUE_REMINDER_DAYS=7
```

### Notification Settings
```env
SMS_PROVIDER=twilio
SMS_API_KEY=your_key
SMS_API_SECRET=your_secret
```

## 📦 Database Structure

### Key Tables
- `users` - System users with roles
- `students` - Student information
- `parents` - Parent/guardian details
- `staff` - Employee records
- `classes` - Pre-school classes
- `subjects` - Academic subjects
- `fees` - Fee management
- `timetables` - Class schedules
- `exams` - Examination system
- `activities` - Event management
- `learning_materials` - Educational content
- `messages` - Communication system

## 🔒 Security Features

- **Role-based Access Control**: Granular permissions
- **Input Validation**: Comprehensive data validation
- **SQL Injection Protection**: Parameterized queries
- **XSS Protection**: Output sanitization
- **CSRF Protection**: Cross-site request forgery prevention
- **Rate Limiting**: API rate limiting
- **File Upload Security**: File type and size validation

## 🚀 Deployment

### Production Setup
1. Use environment-specific `.env` file
2. Enable caching: `php artisan config:cache`
3. Optimize routes: `php artisan route:cache`
4. Set up queue workers: `php artisan queue:work`
5. Configure scheduled tasks in cron
6. Set up Redis for better performance
7. Configure proper file permissions

### Recommended Server Configuration
- PHP 8.2+ with required extensions
- MySQL 8.0+ or PostgreSQL
- Redis server
- Web server (Apache/Nginx)
- SSL certificate for HTTPS
- Regular database backups

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Make your changes and commit: `git commit -m 'Add feature'`
4. Push to the branch: `git push origin feature-name`
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and queries:
- Create an issue on GitHub
- Email: support@preschoolerp.com
- Documentation: [API_COLLECTION.md](API_COLLECTION.md)

## 📋 Roadmap

### Upcoming Features
- [ ] Mobile application (React Native)
- [ ] Advanced reporting dashboard
- [ ] Integration with payment gateways
- [ ] Biometric attendance system
- [ ] Parent mobile app
- [ ] Transport management module
- [ ] Library management system
- [ ] Multi-branch support
- [ ] Advanced analytics and insights
- [ ] Export to PDF/Excel features

---

**Built with ❤️ for educational institutions**