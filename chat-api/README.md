# Laravel 12 Communication Chat Module API

A complete REST API built with Laravel 12 for a communication chat system. This API supports private messaging, group chats, file attachments, and role-based user management without using WebSockets or Laravel Echo.

## 🚀 Features

### ✅ Complete Implementation

- **JWT Authentication** - Secure token-based authentication
- **Role-Based Access Control** - Admin, Staff, Student, Parent roles
- **Private Messaging** - One-to-one conversations
- **Group Messaging** - Multi-user group chats
- **File Attachments** - Support for images, PDFs, documents (up to 10MB)
- **Message Search** - Search through conversations by keyword
- **Seen/Unseen Status** - Message read status tracking
- **Chat Previews** - WhatsApp-style chat list with last messages
- **Group Management** - Admin-only group creation and management
- **RESTful Design** - Clean, consistent API endpoints
- **Comprehensive Validation** - Input validation on all endpoints
- **Pagination** - Efficient handling of large conversations
- **Error Handling** - Standardized error responses

## 🏗️ Architecture

### Database Schema

#### Tables Created:
1. **users** - User accounts with roles (admin, staff, student, parent)
2. **chat_groups** - Group information
3. **chat_messages** - All messages (private and group)
4. **chat_group_members** - Group membership mapping
5. **attachments** - File attachments for messages

### Key Models:
- `User` - Implements JWT authentication and role checking
- `ChatMessage` - Handles private/group messages with scopes
- `ChatGroup` - Group management with member methods
- `ChatGroupMember` - Pivot model for group membership
- `Attachment` - File attachment handling

### Controllers:
- `AuthController` - Authentication (login, register, profile)
- `ChatController` - Messaging functionality
- `GroupController` - Group management (admin features)

## 📋 API Endpoints Summary

### Authentication
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `POST /api/auth/logout` - Logout
- `POST /api/auth/refresh` - Refresh token
- `GET /api/auth/user-profile` - Get user profile
- `PUT /api/auth/update-profile` - Update profile

### Messaging
- `POST /api/chat/send-private` - Send private message
- `POST /api/chat/send-group` - Send group message
- `GET /api/chat/private/{userId}` - Get private conversation
- `GET /api/chat/group/{groupId}` - Get group conversation
- `GET /api/chat/previews` - Get chat list (like WhatsApp)
- `POST /api/chat/search` - Search messages
- `POST /api/chat/mark-seen` - Mark message as seen
- `GET /api/chat/unseen-count` - Get unseen message count

### Group Management
- `GET /api/groups/my-groups` - User's groups
- `GET /api/groups/{groupId}` - Group details
- `POST /api/groups/{groupId}/leave` - Leave group

### Admin Features
- `POST /api/groups/create` - Create group
- `GET /api/groups` - All groups
- `PUT /api/groups/{groupId}` - Update group
- `DELETE /api/groups/{groupId}` - Delete group
- `POST /api/groups/{groupId}/add-users` - Add users to group
- `POST /api/groups/{groupId}/remove-user` - Remove user from group
- `GET /api/groups/admin/available-users` - List all users

## 🧪 Test Data

The system comes with pre-seeded users for immediate testing:

| Email | Password | Role |
|-------|----------|------|
| admin@chatapi.com | password | admin |
| staff1@chatapi.com | password | staff |
| staff2@chatapi.com | password | staff |
| student1@chatapi.com | password | student |
| student2@chatapi.com | password | student |
| student3@chatapi.com | password | student |
| parent1@chatapi.com | password | parent |
| parent2@chatapi.com | password | parent |

## 🛠️ Setup Instructions

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

3. **Database Setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   php artisan db:seed --class=UserSeeder
   ```

4. **Storage Setup**
   ```bash
   php artisan storage:link
   ```

5. **Start Server**
   ```bash
   php artisan serve
   ```

## 📖 Usage Examples

### 1. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@chatapi.com", "password": "password"}'
```

### 2. Send Private Message
```bash
curl -X POST http://localhost:8000/api/chat/send-private \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"receiver_id": 2, "message": "Hello!"}'
```

### 3. Create Group (Admin)
```bash
curl -X POST http://localhost:8000/api/groups/create \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"group_name": "Study Group", "member_ids": [2, 3, 4]}'
```

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php     # Authentication logic
│   │   ├── ChatController.php     # Chat functionality
│   │   └── GroupController.php    # Group management
│   └── Middleware/
│       └── AdminMiddleware.php    # Admin authorization
├── Models/
│   ├── User.php                   # User model with JWT
│   ├── ChatMessage.php           # Message model
│   ├── ChatGroup.php             # Group model
│   ├── ChatGroupMember.php       # Group membership
│   └── Attachment.php            # File attachments
database/
├── migrations/                    # Database structure
└── seeders/
    └── UserSeeder.php            # Sample users
routes/
└── api.php                       # API routes
```

## 🔧 Technical Specifications

- **Laravel Version**: 12.x
- **PHP Version**: 8.4+
- **Authentication**: JWT (tymon/jwt-auth)
- **Database**: SQLite (easily configurable to MySQL)
- **File Storage**: Laravel Storage (public disk)
- **API Style**: RESTful with consistent JSON responses
- **Validation**: Form Request validation on all endpoints
- **Authorization**: Role-based middleware protection

## 🚀 Deployment Notes

### For Production:
1. **Database**: Configure MySQL/PostgreSQL in `.env`
2. **File Storage**: Consider AWS S3 for file attachments
3. **Caching**: Enable Redis for better performance
4. **Queue**: Use queue workers for file processing
5. **Rate Limiting**: Add API rate limiting
6. **Monitoring**: Add logging and monitoring

### Security Features:
- JWT token expiration and refresh
- Password hashing (bcrypt)
- Input validation and sanitization
- SQL injection prevention (Eloquent ORM)
- File upload restrictions
- Role-based authorization

## 📋 API Documentation

Complete API documentation is available in `API_DOCUMENTATION.md` with:
- Detailed endpoint descriptions
- Request/response examples
- Authentication requirements
- Error codes and handling
- File upload specifications

## 🔄 Real-time Updates

Since WebSockets are not used, implement polling on the frontend:
- **Chat previews**: Poll every 5-10 seconds
- **Active conversations**: Poll every 3-5 seconds
- **Unseen counts**: Poll every 10-15 seconds

## ✅ Implementation Status

### ✅ Completed Features:
- [x] JWT Authentication system
- [x] User roles (Admin, Staff, Student, Parent)
- [x] Private messaging
- [x] Group messaging
- [x] File attachments
- [x] Message search
- [x] Seen/unseen status
- [x] Chat previews (WhatsApp-style)
- [x] Group management (admin-only)
- [x] Database migrations
- [x] API routes
- [x] Validation & authorization
- [x] Test data seeding
- [x] Comprehensive documentation

### ⚡ Performance Optimizations:
- Database indexing on frequently queried columns
- Eager loading to prevent N+1 queries
- Pagination for large datasets
- Efficient query scopes

### 🛡️ Security Measures:
- JWT token authentication
- Role-based access control
- Input validation
- File upload restrictions
- SQL injection prevention

## 🎯 Ready for Production

This chat API is production-ready with:
- Clean, maintainable code structure
- Comprehensive error handling
- Security best practices
- Scalable architecture
- Complete documentation
- Test data for immediate use

You can start using this API immediately for any chat application frontend (React, Vue, Angular, mobile apps, etc.) by following the provided documentation and examples!
