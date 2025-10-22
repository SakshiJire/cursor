// API Service Layer for Chat Application
class ChatAPI {
    constructor() {
        this.baseURL = CONFIG.API_BASE_URL;
        this.token = localStorage.getItem('auth_token');
        this.user = JSON.parse(localStorage.getItem('user') || 'null');
    }

    // Set authorization token
    setToken(token) {
        this.token = token;
        localStorage.setItem('auth_token', token);
    }

    // Get authorization headers
    getHeaders(includeContentType = true) {
        const headers = {};
        if (includeContentType) {
            headers['Content-Type'] = 'application/json';
        }
        headers['Accept'] = 'application/json';
        if (this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }
        return headers;
    }

    // Make HTTP request
    async makeRequest(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            ...options,
            headers: {
                ...this.getHeaders(!options.body || typeof options.body === 'string'),
                ...options.headers
            }
        };

        if (CONFIG.DEBUG) {
            console.log(`API Request: ${config.method || 'GET'} ${url}`, config);
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (CONFIG.DEBUG) {
                console.log(`API Response: ${response.status}`, data);
            }

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            
            // Handle authentication errors
            if (error.message.includes('401') || error.message.includes('Unauthenticated')) {
                this.logout();
                throw new Error('Session expired. Please login again.');
            }
            
            throw error;
        }
    }

    // Authentication Methods
    async login(email, password) {
        const response = await this.makeRequest('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        if (response.success) {
            this.setToken(response.data.access_token);
            this.user = response.data.user;
            localStorage.setItem('user', JSON.stringify(this.user));
        }

        return response;
    }

    async logout() {
        try {
            if (this.token) {
                await this.makeRequest('/auth/logout', {
                    method: 'POST'
                });
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.token = null;
            this.user = null;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.reload();
        }
    }

    async refreshToken() {
        const response = await this.makeRequest('/auth/refresh', {
            method: 'POST'
        });

        if (response.success) {
            this.setToken(response.data.access_token);
        }

        return response;
    }

    async getUserProfile() {
        return await this.makeRequest('/auth/user-profile');
    }

    async updateProfile(data) {
        return await this.makeRequest('/auth/update-profile', {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    // Chat Methods
    async sendPrivateMessage(receiverId, message, attachments = null) {
        const formData = new FormData();
        formData.append('receiver_id', receiverId);
        formData.append('message', message);

        if (attachments && attachments.length > 0) {
            for (let i = 0; i < attachments.length; i++) {
                formData.append(`attachments[${i}]`, attachments[i]);
            }
        }

        return await this.makeRequest('/chat/send-private', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${this.token}`
            }
        });
    }

    async sendGroupMessage(groupId, message, attachments = null) {
        const formData = new FormData();
        formData.append('group_id', groupId);
        formData.append('message', message);

        if (attachments && attachments.length > 0) {
            for (let i = 0; i < attachments.length; i++) {
                formData.append(`attachments[${i}]`, attachments[i]);
            }
        }

        return await this.makeRequest('/chat/send-group', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${this.token}`
            }
        });
    }

    async getPrivateConversation(userId, page = 1) {
        return await this.makeRequest(`/chat/private/${userId}?page=${page}`);
    }

    async getGroupConversation(groupId, page = 1) {
        return await this.makeRequest(`/chat/group/${groupId}?page=${page}`);
    }

    async getChatPreviews() {
        return await this.makeRequest('/chat/previews');
    }

    async searchMessages(keyword, chatType = 'all', chatId = null) {
        const body = { keyword };
        if (chatType !== 'all') {
            body.chat_type = chatType;
            body.chat_id = chatId;
        }

        return await this.makeRequest('/chat/search', {
            method: 'POST',
            body: JSON.stringify(body)
        });
    }

    async markAsSeen(messageIds) {
        return await this.makeRequest('/chat/mark-seen', {
            method: 'POST',
            body: JSON.stringify({ message_ids: messageIds })
        });
    }

    async getUnseenCount() {
        return await this.makeRequest('/chat/unseen-count');
    }

    // Group Methods
    async createGroup(groupName, memberIds) {
        return await this.makeRequest('/groups/create', {
            method: 'POST',
            body: JSON.stringify({
                group_name: groupName,
                member_ids: memberIds
            })
        });
    }

    async getUserGroups() {
        return await this.makeRequest('/groups/my-groups');
    }

    async getAllGroups() {
        return await this.makeRequest('/groups');
    }

    async getGroupDetails(groupId) {
        return await this.makeRequest(`/groups/${groupId}`);
    }

    async addUsersToGroup(groupId, userIds) {
        return await this.makeRequest(`/groups/${groupId}/add-users`, {
            method: 'POST',
            body: JSON.stringify({ user_ids: userIds })
        });
    }

    async removeUserFromGroup(groupId, userId) {
        return await this.makeRequest(`/groups/${groupId}/remove-user`, {
            method: 'POST',
            body: JSON.stringify({ user_id: userId })
        });
    }

    async updateGroup(groupId, groupName) {
        return await this.makeRequest(`/groups/${groupId}`, {
            method: 'PUT',
            body: JSON.stringify({ group_name: groupName })
        });
    }

    async deleteGroup(groupId) {
        return await this.makeRequest(`/groups/${groupId}`, {
            method: 'DELETE'
        });
    }

    async leaveGroup(groupId) {
        return await this.makeRequest(`/groups/${groupId}/leave`, {
            method: 'POST'
        });
    }

    async getAvailableUsers() {
        return await this.makeRequest('/groups/admin/available-users');
    }

    // Test API connection
    async testConnection() {
        try {
            const response = await fetch(`${this.baseURL}/test`);
            return await response.json();
        } catch (error) {
            throw new Error('Unable to connect to API server');
        }
    }

    // Utility methods
    isAuthenticated() {
        return !!this.token && !!this.user;
    }

    getCurrentUser() {
        return this.user;
    }

    isAdmin() {
        return this.user && this.user.role === 'admin';
    }

    isStaff() {
        return this.user && this.user.role === 'staff';
    }

    getUserInitials(name) {
        return name
            .split(' ')
            .map(word => word.charAt(0))
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInHours = (now - date) / (1000 * 60 * 60);

        if (diffInHours < 24) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else if (diffInHours < 24 * 7) {
            return date.toLocaleDateString([], { weekday: 'short' });
        } else {
            return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
        }
    }

    validateFile(file) {
        // Check file size
        if (file.size > CONFIG.MAX_FILE_SIZE * 1024 * 1024) {
            throw new Error(`File size must be less than ${CONFIG.MAX_FILE_SIZE}MB`);
        }

        // Check file type
        if (!CONFIG.ALLOWED_FILE_TYPES.includes(file.type)) {
            throw new Error('File type not allowed');
        }

        return true;
    }
}

// Create global API instance
const api = new ChatAPI();