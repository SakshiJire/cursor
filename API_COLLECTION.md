# PreSchool ERP System API Documentation

## Overview
This is a comprehensive ERP system for pre-school management built with Laravel 12. The system supports role-based access control for Admin, Staff, Students, and Parents.

## Base URL
```
http://localhost:8000/api
```

## Authentication
The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header for protected routes.

```
Authorization: Bearer {token}
```

## API Endpoints

### 1. Authentication

#### Login
```http
POST /login
Content-Type: application/json

{
    "email": "admin@preschool.com",
    "password": "admin123",
    "role": "admin"
}
```

#### Register
```http
POST /register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "9876543210",
    "role": "parent"
}
```

#### Get Current User
```http
GET /auth/me
Authorization: Bearer {token}
```

#### Update Profile
```http
PUT /auth/profile
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "name": "Updated Name",
    "phone": "9876543210",
    "avatar": [file],
    "preferences": {
        "language": "en",
        "notifications": true
    }
}
```

#### Change Password
```http
PUT /auth/change-password
Authorization: Bearer {token}
Content-Type: application/json

{
    "current_password": "oldpassword",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

#### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

---

### 2. Student Management

#### Get All Students
```http
GET /students?page=1&per_page=15&class_id=1&status=active&search=john
Authorization: Bearer {token}
```

#### Create Student
```http
POST /students
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "email": "student@example.com",
    "password": "student123",
    "first_name": "John",
    "last_name": "Doe",
    "class_id": 1,
    "academic_year_id": 1,
    "date_of_birth": "2020-05-15",
    "gender": "male",
    "blood_group": "A+",
    "address": "123 Main Street",
    "city": "Mumbai",
    "state": "Maharashtra",
    "pincode": "400001",
    "admission_date": "2024-04-01",
    "medical_info": {
        "allergies": ["peanuts"],
        "conditions": []
    },
    "emergency_contacts": [
        {
            "name": "Father Name",
            "phone": "9876543210",
            "relationship": "father"
        }
    ],
    "photo": [file]
}
```

#### Get Student Details
```http
GET /students/{id}
Authorization: Bearer {token}
```

#### Update Student
```http
PUT /students/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "first_name": "Updated Name",
    "class_id": 2,
    "status": "active",
    "photo": [file]
}
```

#### Delete Student
```http
DELETE /students/{id}
Authorization: Bearer {token}
```

#### Get Students by Class
```http
GET /students/class/{classId}
Authorization: Bearer {token}
```

#### Promote Students
```http
POST /students/promote
Authorization: Bearer {token}
Content-Type: application/json

{
    "student_ids": [1, 2, 3],
    "new_class_id": 2,
    "new_academic_year_id": 1
}
```

---

### 3. Fee Management

#### Get Fees
```http
GET /fees?student_id=1&class_id=1&status=pending&month=April&overdue=true
Authorization: Bearer {token}
```

#### Create Fee
```http
POST /fees
Authorization: Bearer {token}
Content-Type: application/json

{
    "student_id": 1,
    "fee_type_id": 1,
    "academic_year_id": 1,
    "month": "April",
    "amount": 3000,
    "due_date": "2024-04-15",
    "remarks": "Monthly fee for April"
}
```

#### Make Payment
```http
POST /fees/{id}/payment
Authorization: Bearer {token}
Content-Type: application/json

{
    "amount": 3000,
    "payment_method": "cash",
    "transaction_id": "TXN123456",
    "remarks": "Full payment"
}
```

#### Get Student Fees
```http
GET /fees/student/{studentId}
Authorization: Bearer {token}
```

#### Generate Monthly Fees
```http
POST /fees/generate-monthly
Authorization: Bearer {token}
Content-Type: application/json

{
    "academic_year_id": 1,
    "month": "April",
    "class_ids": [1, 2, 3]
}
```

#### Get Fees Summary
```http
GET /fees/summary?class_id=1&academic_year_id=1
Authorization: Bearer {token}
```

#### Get Overdue Fees
```http
GET /fees/overdue?class_id=1
Authorization: Bearer {token}
```

---

### 4. Timetable Management

#### Get Timetables
```http
GET /timetables?class_id=1&academic_year_id=1&day=monday
Authorization: Bearer {token}
```

#### Create Timetable Entry
```http
POST /timetables
Authorization: Bearer {token}
Content-Type: application/json

{
    "class_id": 1,
    "academic_year_id": 1,
    "day": "monday",
    "start_time": "09:00",
    "end_time": "09:45",
    "subject_id": 1,
    "staff_id": 1,
    "room": "Room A",
    "period_type": "regular",
    "notes": "First period"
}
```

#### Get Class Timetable
```http
GET /timetables/class/{classId}?academic_year_id=1
Authorization: Bearer {token}
```

#### Get Staff Timetable
```http
GET /timetables/staff/{staffId}?academic_year_id=1
Authorization: Bearer {token}
```

#### Generate Auto Timetable
```http
POST /timetables/generate
Authorization: Bearer {token}
Content-Type: application/json

{
    "class_id": 1,
    "academic_year_id": 1,
    "periods_per_day": 6,
    "period_duration": 45,
    "start_time": "09:00",
    "break_duration": 15,
    "lunch_duration": 30,
    "days": ["monday", "tuesday", "wednesday", "thursday", "friday"]
}
```

#### Copy Timetable
```http
POST /timetables/copy
Authorization: Bearer {token}
Content-Type: application/json

{
    "from_class_id": 1,
    "to_class_id": 2,
    "from_academic_year_id": 1,
    "to_academic_year_id": 1
}
```

---

### 5. Exam Management

#### Get Exams
```http
GET /exams?class_id=1&academic_year_id=1&subject_id=1
Authorization: Bearer {token}
```

#### Create Exam
```http
POST /exams
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "English Unit Test 1",
    "exam_type_id": 1,
    "academic_year_id": 1,
    "class_id": 1,
    "subject_id": 1,
    "exam_date": "2024-05-15",
    "start_time": "10:00",
    "end_time": "11:00",
    "max_marks": 100,
    "instructions": "Answer all questions"
}
```

#### Add Exam Results
```http
POST /exams/{id}/results
Authorization: Bearer {token}
Content-Type: application/json

{
    "results": [
        {
            "student_id": 1,
            "marks_obtained": 85,
            "grade": "A",
            "remarks": "Excellent performance",
            "is_absent": false
        },
        {
            "student_id": 2,
            "marks_obtained": null,
            "grade": null,
            "remarks": "Was absent",
            "is_absent": true
        }
    ]
}
```

#### Generate Marksheet
```http
GET /exams/{examId}/marksheet/{studentId}
Authorization: Bearer {token}
```

#### Get Student Results
```http
GET /exams/student/{studentId}/results?academic_year_id=1
Authorization: Bearer {token}
```

---

### 6. Communication/Chat

#### Get Chat Groups
```http
GET /chat/groups
Authorization: Bearer {token}
```

#### Create Chat Group
```http
POST /chat/groups
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Class 1 Parents",
    "description": "Parent group for Class 1",
    "type": "class",
    "class_id": 1,
    "members": [1, 2, 3, 4]
}
```

#### Send Message
```http
POST /chat/messages
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "content": "Hello everyone!",
    "type": "text",
    "group_id": 1,
    "receiver_id": null,
    "replied_to": null,
    "file": [file]
}
```

#### Get Group Messages
```http
GET /chat/messages/group/{groupId}?page=1
Authorization: Bearer {token}
```

#### Get Private Messages
```http
GET /chat/messages/user/{userId}?page=1
Authorization: Bearer {token}
```

#### React to Message
```http
POST /chat/messages/{messageId}/react
Authorization: Bearer {token}
Content-Type: application/json

{
    "reaction": "👍"
}
```

#### Mark Message as Read
```http
POST /chat/messages/{messageId}/read
Authorization: Bearer {token}
```

---

### 7. Learning Materials

#### Get Learning Materials
```http
GET /learning-materials?class_id=1&subject_id=1&category_id=1&type=pdf
Authorization: Bearer {token}
```

#### Upload Learning Material
```http
POST /learning-materials
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "title": "English Worksheet 1",
    "description": "Basic English worksheet",
    "category_id": 1,
    "class_id": 1,
    "subject_id": 1,
    "type": "pdf",
    "file": [file],
    "tags": ["worksheet", "english", "basic"],
    "is_public": true
}
```

#### Download Material
```http
GET /learning-materials/{id}/download
Authorization: Bearer {token}
```

#### Log Material View
```http
POST /learning-materials/{id}/view
Authorization: Bearer {token}
```

---

### 8. Activities

#### Get Activities
```http
GET /activities?class_id=1&type=sports&status=planned
Authorization: Bearer {token}
```

#### Create Activity
```http
POST /activities
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Sports Day",
    "description": "Annual sports competition",
    "class_id": 1,
    "academic_year_id": 1,
    "activity_date": "2024-06-15",
    "start_time": "10:00",
    "end_time": "15:00",
    "venue": "School Ground",
    "staff_id": 1,
    "type": "sports",
    "requirements": "Sports dress, water bottle"
}
```

#### Register Student for Activity
```http
POST /activities/{id}/register
Authorization: Bearer {token}
Content-Type: application/json

{
    "student_ids": [1, 2, 3]
}
```

#### Update Participation Status
```http
PUT /activities/{activityId}/participation/{studentId}
Authorization: Bearer {token}
Content-Type: application/json

{
    "participation_status": "participated",
    "remarks": "Active participation"
}
```

---

### 9. Classes and Subjects

#### Get Classes
```http
GET /classes
Authorization: Bearer {token}
```

#### Create Class
```http
POST /classes
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Tiny Tots",
    "code": "TT",
    "description": "Pre-nursery class",
    "capacity": 15,
    "monthly_fee": 1500,
    "admission_fee": 4000
}
```

#### Assign Subjects to Class
```http
POST /classes/{id}/subjects
Authorization: Bearer {token}
Content-Type: application/json

{
    "subjects": [
        {
            "subject_id": 1,
            "periods_per_week": 5
        },
        {
            "subject_id": 2,
            "periods_per_week": 3
        }
    ]
}
```

#### Get Subjects
```http
GET /subjects
Authorization: Bearer {token}
```

#### Create Subject
```http
POST /subjects
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Environmental Science",
    "code": "EVS",
    "description": "Basic environmental awareness",
    "color": "#28a745"
}
```

---

### 10. Staff Management

#### Get Staff
```http
GET /staff?designation=teacher&status=active
Authorization: Bearer {token}
```

#### Create Staff
```http
POST /staff
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "email": "teacher@preschool.com",
    "password": "teacher123",
    "first_name": "Jane",
    "last_name": "Smith",
    "date_of_birth": "1990-03-20",
    "gender": "female",
    "phone": "9876543214",
    "emergency_contact": "9876543215",
    "address": "456 Staff Colony",
    "city": "Mumbai",
    "state": "Maharashtra",
    "pincode": "400002",
    "designation": "teacher",
    "qualification": "B.Ed, MA English",
    "experience": "3 years teaching experience",
    "joining_date": "2024-04-01",
    "salary": 30000,
    "photo": [file]
}
```

---

### 11. Parent Management

#### Get Parents
```http
GET /parents
Authorization: Bearer {token}
```

#### Create Parent
```http
POST /parents
Authorization: Bearer {token}
Content-Type: application/json

{
    "email": "parent@example.com",
    "password": "parent123",
    "father_name": "John Doe",
    "father_phone": "9876543210",
    "father_email": "john@example.com",
    "father_occupation": "Engineer",
    "mother_name": "Jane Doe",
    "mother_phone": "9876543211",
    "mother_email": "jane@example.com",
    "mother_occupation": "Teacher",
    "address": "123 Parent Street",
    "city": "Mumbai",
    "state": "Maharashtra",
    "pincode": "400001"
}
```

#### Link Student to Parent
```http
POST /parents/link-student
Authorization: Bearer {token}
Content-Type: application/json

{
    "parent_id": 1,
    "student_id": 1,
    "relationship": "father",
    "is_primary": true
}
```

---

### 12. Notifications

#### Get Notifications
```http
GET /notifications?type=info&is_read=false
Authorization: Bearer {token}
```

#### Create Notification
```http
POST /notifications
Authorization: Bearer {token}
Content-Type: application/json

{
    "user_id": 1,
    "title": "Fee Due Reminder",
    "message": "Your monthly fee is due on 15th April",
    "type": "warning",
    "action_url": "/fees"
}
```

#### Mark as Read
```http
POST /notifications/{id}/read
Authorization: Bearer {token}
```

#### Get Unread Count
```http
GET /notifications/unread/count
Authorization: Bearer {token}
```

#### Create Announcement
```http
POST /notifications/announcements
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "title": "School Holiday Notice",
    "content": "School will remain closed on 15th August due to Independence Day",
    "target_audience": "all",
    "class_id": null,
    "priority": "medium",
    "publish_date": "2024-08-10",
    "expire_date": "2024-08-16",
    "attachment": [file]
}
```

---

### 13. Dashboard

#### Get Dashboard Data
```http
GET /dashboard
Authorization: Bearer {token}
```

#### Get Dashboard Stats
```http
GET /dashboard/stats
Authorization: Bearer {token}
```

---

## Response Format

All API responses follow this standard format:

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": {
        // Response data
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

### Pagination Response
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            // Array of items
        ],
        "first_page_url": "http://localhost:8000/api/students?page=1",
        "from": 1,
        "last_page": 5,
        "last_page_url": "http://localhost:8000/api/students?page=5",
        "next_page_url": "http://localhost:8000/api/students?page=2",
        "path": "http://localhost:8000/api/students",
        "per_page": 15,
        "prev_page_url": null,
        "to": 15,
        "total": 75
    }
}
```

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

The API is rate limited to 60 requests per minute per IP address.

## File Uploads

Supported file types and sizes:
- Images: JPG, JPEG, PNG (max 2MB)
- Documents: PDF, DOC, DOCX (max 10MB)
- Audio: MP3, WAV (max 10MB)

## Sample Postman Collection

Import the following into Postman to get started quickly:

```json
{
    "info": {
        "name": "PreSchool ERP API",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "variable": [
        {
            "key": "baseUrl",
            "value": "http://localhost:8000/api"
        },
        {
            "key": "token",
            "value": ""
        }
    ],
    "auth": {
        "type": "bearer",
        "bearer": [
            {
                "key": "token",
                "value": "{{token}}",
                "type": "string"
            }
        ]
    }
}
```