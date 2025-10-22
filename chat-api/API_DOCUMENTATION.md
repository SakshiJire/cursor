# Chat API Documentation

This is a Laravel 12 REST API for a Communication Chat Module using JWT authentication. The API supports private messaging, group chats, file attachments, and comprehensive user management.

## Base URL
```
http://localhost:8000/api
```

## Authentication

The API uses JWT (JSON Web Token) authentication. All protected endpoints require a valid JWT token in the Authorization header.

### Headers
```
Authorization: Bearer {your-jwt-token}
Content-Type: application/json
Accept: application/json
```

## User Roles

- **admin**: Can create/manage groups, add/remove users, full system access
- **staff**: Can participate in chats and groups
- **student**: Can participate in chats and groups  
- **parent**: Can participate in chats and groups

## API Endpoints

### Authentication Endpoints

#### POST /api/auth/register
Register a new user.

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "student"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User successfully registered",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600,
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "student"
        }
    }
}
```

#### POST /api/auth/login
Login with email and password.

**Request:**
```json
{
    "email": "john@example.com",
    "password": "password"
}
```

**Response:** Same as register response.

#### POST /api/auth/logout
Logout and invalidate token. (Requires Authentication)

#### POST /api/auth/refresh
Refresh JWT token. (Requires Authentication)

#### GET /api/auth/user-profile
Get current user profile. (Requires Authentication)

#### PUT /api/auth/update-profile
Update user profile. (Requires Authentication)

### Chat Endpoints (All require authentication)

#### POST /api/chat/send-private
Send a private message to another user.

**Request:**
```json
{
    "receiver_id": 2,
    "message": "Hello! How are you?"
}
```

**With File Attachment:**
```json
{
    "receiver_id": 2,
    "message": "Check out this file",
    "attachments": [file1, file2]
}
```

#### POST /api/chat/send-group
Send a message to a group.

**Request:**
```json
{
    "group_id": 1,
    "message": "Hello everyone!"
}
```

#### GET /api/chat/private/{userId}
Get private conversation with a specific user.

**Query Parameters:**
- `page` (optional): Page number for pagination
- `per_page` (optional): Number of messages per page (default: 20)

#### GET /api/chat/group/{groupId}
Get group conversation messages.

**Query Parameters:**
- `page` (optional): Page number for pagination
- `per_page` (optional): Number of messages per page (default: 20)

#### GET /api/chat/previews
Get chat previews (last message with each user/group) - like WhatsApp chat list.

**Response:**
```json
{
    "success": true,
    "message": "Chat previews retrieved successfully",
    "data": {
        "private_conversations": [
            {
                "type": "private",
                "user_id": 2,
                "user_name": "Jane Doe",
                "user_email": "jane@example.com",
                "last_message": "Thanks for the help!",
                "last_message_time": "2025-01-26T10:30:00Z",
                "unseen_count": 2
            }
        ],
        "group_conversations": [
            {
                "type": "group",
                "group_id": 1,
                "group_name": "Study Group",
                "last_message": "Meeting at 3 PM",
                "last_message_time": "2025-01-26T09:15:00Z"
            }
        ]
    }
}
```

#### POST /api/chat/search
Search messages by keyword.

**Request:**
```json
{
    "keyword": "meeting",
    "type": "all",
    "user_id": 2,
    "group_id": 1
}
```

**Parameters:**
- `keyword` (required): Search term (minimum 3 characters)
- `type` (optional): "private", "group", or "all" (default: "all")
- `user_id` (optional): Search in specific private conversation
- `group_id` (optional): Search in specific group

#### POST /api/chat/mark-seen
Mark a message as seen.

**Request:**
```json
{
    "message_id": 123
}
```

#### GET /api/chat/unseen-count
Get count of unseen messages for current user.

### Group Management Endpoints (Require authentication)

#### GET /api/groups/my-groups
Get all groups for current user.

#### GET /api/groups/{groupId}
Get group details and members.

#### POST /api/groups/{groupId}/leave
Leave a group (creators cannot leave).

### Admin-Only Group Endpoints

#### POST /api/groups/create
Create a new group. (Admin only)

**Request:**
```json
{
    "group_name": "Study Group",
    "member_ids": [2, 3, 4, 5]
}
```

#### GET /api/groups
Get all groups in the system. (Admin only)

#### PUT /api/groups/{groupId}
Update group details. (Admin only)

**Request:**
```json
{
    "group_name": "Updated Group Name"
}
```

#### DELETE /api/groups/{groupId}
Delete a group. (Admin only)

#### POST /api/groups/{groupId}/add-users
Add users to a group. (Admin only)

**Request:**
```json
{
    "user_ids": [6, 7, 8]
}
```

#### POST /api/groups/{groupId}/remove-user
Remove a user from a group. (Admin only)

**Request:**
```json
{
    "user_id": 6
}
```

#### GET /api/groups/admin/available-users
Get all users available for adding to groups. (Admin only)

## Sample Users for Testing

The system comes with pre-seeded users:

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

## File Uploads

The API supports file attachments in messages:

- **Supported file types**: Images, PDFs, documents
- **Maximum file size**: 10MB per file
- **Multiple files**: Supported in a single message
- **Storage**: Files are stored in `storage/app/public/chat_attachments/`

## Response Format

All API responses follow this standard format:

```json
{
    "success": true|false,
    "message": "Human readable message",
    "data": {
        // Response data here
    },
    "errors": {
        // Validation errors (only on failure)
    }
}
```

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Real-time Updates

Since this API doesn't use WebSockets, clients should implement polling to get real-time updates:

1. **Chat List Updates**: Poll `/api/chat/previews` every 5-10 seconds
2. **New Messages**: Poll conversation endpoints every 3-5 seconds when chat is active
3. **Unseen Count**: Poll `/api/chat/unseen-count` every 10-15 seconds

## Usage Examples

### 1. Login and Send a Message

```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "student1@chatapi.com", "password": "password"}'

# Send private message (use token from login response)
curl -X POST http://localhost:8000/api/chat/send-private \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"receiver_id": 2, "message": "Hello there!"}'
```

### 2. Create a Group (Admin)

```bash
# Login as admin
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@chatapi.com", "password": "password"}'

# Create group
curl -X POST http://localhost:8000/api/groups/create \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"group_name": "Class Discussion", "member_ids": [2, 3, 4, 5]}'
```

### 3. Upload File with Message

```bash
curl -X POST http://localhost:8000/api/chat/send-private \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "receiver_id=2" \
  -F "message=Check this file" \
  -F "attachments[]=@/path/to/file.pdf"
```

## Development Notes

- Database: SQLite (can be easily changed to MySQL)
- JWT Secret: Auto-generated during setup
- File Storage: Laravel's storage system with public disk
- Validation: Comprehensive request validation on all endpoints
- Authorization: Role-based access control for admin features

## Testing the API

1. Start the Laravel development server:
   ```bash
   php artisan serve
   ```

2. Test with any API client (Postman, Insomnia, curl)

3. Base URL will be: `http://localhost:8000/api`