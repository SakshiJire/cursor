# Chat Application Frontend

A modern, responsive chat application frontend built with vanilla HTML, CSS, and JavaScript that connects to the Laravel Chat API backend.

## Features

- **User Authentication**: Login with JWT token authentication
- **Real-time Chat**: Polling-based real-time message updates (every 3 seconds)
- **Private Messaging**: Send and receive private messages with other users
- **Group Chat**: Create and participate in group conversations (Admin only can create groups)
- **File Attachments**: Send images, PDFs, and documents
- **Message Search**: Search through all messages by keyword
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Role-based Access**: Different features for Admin, Staff, Student, and Parent roles
- **Toast Notifications**: User-friendly notifications for actions and errors
- **Chat Previews**: WhatsApp-like chat list with last message and unread counts

## Demo Accounts

The application comes with pre-configured demo accounts for testing:

- **Admin**: admin@chatapi.com / password
- **Staff**: staff1@chatapi.com / password  
- **Student**: student1@chatapi.com / password
- **Parent**: parent1@chatapi.com / password

## Setup Instructions

### Prerequisites

1. Make sure your Laravel backend is running (see backend README for setup)
2. Ensure the Laravel API is accessible at `http://localhost:8000/api`

### Installation

1. **Clone or download the frontend files**:
   ```bash
   # Files should be in a web-accessible directory
   index.html
   styles.css
   config.js
   api.js
   app.js
   README.md
   ```

2. **Configure the API URL** (if different):
   - Open `config.js`
   - Update `API_BASE_URL` to match your Laravel API URL:
   ```javascript
   const CONFIG = {
       API_BASE_URL: 'http://your-api-url/api', // Change this if needed
       // ... other settings
   };
   ```

3. **Serve the files**:
   
   **Option A: Simple HTTP Server (Python)**
   ```bash
   # Navigate to the frontend directory
   cd chat-frontend
   
   # Python 3
   python3 -m http.server 8080
   
   # Python 2
   python -m SimpleHTTPServer 8080
   ```
   
   **Option B: Node.js HTTP Server**
   ```bash
   npx http-server -p 8080
   ```
   
   **Option C: PHP Built-in Server**
   ```bash
   php -S localhost:8080
   ```

4. **Access the application**:
   - Open your browser and go to `http://localhost:8080`
   - Use one of the demo accounts to login

## Configuration

The `config.js` file contains all configurable settings:

```javascript
const CONFIG = {
    API_BASE_URL: 'http://localhost:8000/api',    // Laravel API URL
    POLLING_INTERVAL: 3000,                       // Real-time polling interval (ms)
    MAX_FILE_SIZE: 10,                           // Maximum file size in MB
    MESSAGES_PER_PAGE: 50,                       // Messages per page for pagination
    TOAST_DURATION: 4000,                        // Notification duration (ms)
    DEBUG: true                                  // Enable console logging
};
```

## Usage Guide

### Login
1. Open the application in your browser
2. Use one of the demo accounts or enter your credentials
3. Click on demo account cards for quick login

### Private Messaging
1. After login, you'll see the chat list on the left
2. Click on any user to start a private conversation
3. Type your message and press Enter or click the send button
4. Attach files using the paperclip icon

### Group Chat
1. Click on the "Groups" tab in the sidebar
2. If you're an admin, you'll see a "+" button to create new groups
3. Click on any group to join the conversation
4. Group messages work the same as private messages

### File Attachments
- Supported file types: Images (JPEG, PNG, GIF), PDFs, Word documents
- Maximum file size: 10MB per file
- Multiple files can be selected at once
- Files are displayed with appropriate icons and download links

### Search Messages
1. Click the search icon in the chat header
2. Enter keywords to search through all messages
3. Click on search results to navigate to that conversation

### Real-time Updates
- The application automatically polls for new messages every 3 seconds
- New messages appear instantly without page refresh
- Chat previews update with latest messages and unread counts

## File Structure

```
chat-frontend/
├── index.html          # Main HTML file with complete UI structure
├── styles.css          # Comprehensive CSS with modern design
├── config.js           # Configuration settings
├── api.js              # API service layer for backend communication
├── app.js              # Main application logic and UI interactions
└── README.md           # This documentation file
```

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Troubleshooting

### Common Issues

1. **"Unable to connect to API server"**
   - Check if the Laravel backend is running
   - Verify the API_BASE_URL in config.js
   - Check browser console for CORS errors

2. **Login fails**
   - Ensure the backend database is properly set up
   - Check if user seeder has been run
   - Verify demo account credentials

3. **Messages not loading**
   - Check browser network tab for failed API requests
   - Verify JWT token is being sent with requests
   - Check Laravel logs for backend errors

4. **File uploads not working**
   - Check file size limits (max 10MB)
   - Verify file types are allowed
   - Ensure Laravel storage is properly configured

5. **Real-time updates not working**
   - Check if polling is enabled (CONFIG.POLLING_INTERVAL > 0)
   - Verify API endpoints are responding correctly
   - Check browser console for JavaScript errors

### Debug Mode

Enable debug mode in `config.js` to see detailed API request/response logs:

```javascript
const CONFIG = {
    DEBUG: true,
    // ... other settings
};
```

## Features by Role

### Admin
- Create and manage groups
- Add/remove users from groups
- All messaging features
- Access to all users for group creation

### Staff
- Join groups (when added by admin)
- Private messaging with all users
- File attachments and search

### Student
- Private messaging with staff and other students
- Join groups when added
- File attachments and search

### Parent
- Private messaging with staff
- Join groups when added
- File attachments and search

## Security Features

- JWT token-based authentication
- Automatic token refresh
- Session expiry handling
- File type validation
- File size restrictions
- HTML content sanitization

## Performance Optimizations

- Efficient polling mechanism
- Message pagination (50 messages per page)
- Lazy loading of chat data
- Optimized CSS with minimal reflows
- Compressed API responses

## Mobile Responsiveness

- Touch-friendly interface
- Responsive sidebar navigation
- Optimized for mobile screens
- Swipe gestures support
- Mobile-first design approach

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Support

For support or questions:
1. Check the troubleshooting section above
2. Review the Laravel backend documentation
3. Check browser console for errors
4. Verify API connectivity

## License

This project is open source and available under the MIT License.