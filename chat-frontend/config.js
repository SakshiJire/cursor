// Configuration settings for the Chat Application
const CONFIG = {
    // API Base URL - Change this to match your Laravel API URL
    API_BASE_URL: 'http://localhost:8000/api',
    
    // Polling interval for new messages (in milliseconds)
    POLLING_INTERVAL: 3000, // 3 seconds
    
    // Maximum file size for uploads (in MB)
    MAX_FILE_SIZE: 10,
    
    // Allowed file types for uploads
    ALLOWED_FILE_TYPES: ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    
    // Number of messages to load per page
    MESSAGES_PER_PAGE: 50,
    
    // Toast notification duration (in milliseconds)
    TOAST_DURATION: 4000,
    
    // Debug mode
    DEBUG: true
};

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CONFIG;
}