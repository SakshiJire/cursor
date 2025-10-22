// Main Chat Application
class ChatApp {
    constructor() {
        this.currentChat = null;
        this.currentChatType = null; // 'private' or 'group'
        this.pollingInterval = null;
        this.currentPage = 1;
        this.isLoadingMessages = false;
        this.selectedUsers = new Set();
        
        this.init();
    }

    async init() {
        // Check if user is already logged in
        if (api.isAuthenticated()) {
            this.showChatInterface();
            await this.loadInitialData();
            this.startPolling();
        } else {
            this.showLoginScreen();
        }

        this.setupEventListeners();
    }

    setupEventListeners() {
        // Login form
        document.getElementById('loginForm').addEventListener('submit', this.handleLogin.bind(this));
        
        // Demo account clicks
        document.querySelectorAll('.demo-account').forEach(account => {
            account.addEventListener('click', this.handleDemoLogin.bind(this));
        });

        // Logout button
        document.getElementById('logoutBtn').addEventListener('click', this.handleLogout.bind(this));

        // Refresh button
        document.getElementById('refreshBtn').addEventListener('click', this.handleRefresh.bind(this));

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', this.handleTabSwitch.bind(this));
        });

        // Message form
        document.getElementById('messageForm').addEventListener('submit', this.handleSendMessage.bind(this));

        // File input
        document.getElementById('fileInput').addEventListener('change', this.handleFileSelect.bind(this));

        // Create group button
        document.getElementById('createGroupBtn').addEventListener('click', this.showCreateGroupModal.bind(this));

        // Create group form
        document.getElementById('createGroupForm').addEventListener('submit', this.handleCreateGroup.bind(this));

        // Search messages button
        document.getElementById('searchMessagesBtn').addEventListener('click', this.showSearchModal.bind(this));

        // Search form
        document.getElementById('searchBtn').addEventListener('click', this.handleSearchMessages.bind(this));

        // Modal close buttons
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', this.closeModals.bind(this));
        });

        // Modal background click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModals();
                }
            });
        });

        // Search chats
        document.getElementById('searchChats').addEventListener('input', this.handleSearchChats.bind(this));

        // Messages container scroll (for pagination)
        document.getElementById('messagesContainer').addEventListener('scroll', this.handleMessagesScroll.bind(this));
    }

    // Authentication Methods
    async handleLogin(e) {
        e.preventDefault();
        this.showLoading();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await api.login(email, password);
            
            if (response.success) {
                this.showToast('Login successful!', 'success');
                this.showChatInterface();
                await this.loadInitialData();
                this.startPolling();
            } else {
                this.showToast(response.message || 'Login failed', 'error');
            }
        } catch (error) {
            this.showToast(error.message || 'Login failed', 'error');
        } finally {
            this.hideLoading();
        }
    }

    handleDemoLogin(e) {
        const email = e.currentTarget.dataset.email;
        const password = e.currentTarget.dataset.password;
        
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }

    async handleLogout() {
        this.showLoading();
        this.stopPolling();
        await api.logout();
    }

    // UI State Management
    showLoginScreen() {
        document.getElementById('loginScreen').style.display = 'flex';
        document.getElementById('chatInterface').style.display = 'none';
    }

    showChatInterface() {
        document.getElementById('loginScreen').style.display = 'none';
        document.getElementById('chatInterface').style.display = 'flex';
        
        const user = api.getCurrentUser();
        document.getElementById('currentUserName').textContent = user.name;
        document.getElementById('currentUserRole').textContent = user.role;
        
        // Show admin elements if user is admin
        if (api.isAdmin()) {
            document.body.classList.add('admin');
        }
    }

    // Data Loading Methods
    async loadInitialData() {
        try {
            await Promise.all([
                this.loadChatPreviews(),
                this.loadUserGroups()
            ]);
        } catch (error) {
            this.showToast('Failed to load initial data', 'error');
        }
    }

    async loadChatPreviews() {
        try {
            const response = await api.getChatPreviews();
            
            if (response.success) {
                this.renderChatPreviews(response.data);
            }
        } catch (error) {
            console.error('Failed to load chat previews:', error);
        }
    }

    async loadUserGroups() {
        try {
            const response = await api.getUserGroups();
            
            if (response.success) {
                this.renderGroups(response.data);
            }
        } catch (error) {
            console.error('Failed to load groups:', error);
        }
    }

    // Rendering Methods
    renderChatPreviews(chats) {
        const chatsList = document.getElementById('chatsList');
        chatsList.innerHTML = '';

        if (chats.length === 0) {
            chatsList.innerHTML = '<div class="no-data">No chats available</div>';
            return;
        }

        chats.forEach(chat => {
            const chatElement = this.createChatPreviewElement(chat);
            chatsList.appendChild(chatElement);
        });
    }

    createChatPreviewElement(chat) {
        const div = document.createElement('div');
        div.className = 'chat-item';
        div.dataset.userId = chat.user.id;
        div.dataset.chatType = 'private';

        div.innerHTML = `
            <div class="chat-avatar">${api.getUserInitials(chat.user.name)}</div>
            <div class="chat-info">
                <div class="chat-name">${chat.user.name}</div>
                <div class="last-message">${chat.last_message || 'No messages yet'}</div>
            </div>
            <div class="chat-meta">
                <div class="message-time">${chat.last_message_time ? api.formatTime(chat.last_message_time) : ''}</div>
                ${chat.unread_count > 0 ? `<div class="unread-count">${chat.unread_count}</div>` : ''}
            </div>
        `;

        div.addEventListener('click', () => this.openChat('private', chat.user.id, chat.user.name));
        
        return div;
    }

    renderGroups(groups) {
        const groupsList = document.getElementById('groupsList');
        groupsList.innerHTML = '';

        if (groups.length === 0) {
            groupsList.innerHTML = '<div class="no-data">No groups available</div>';
            return;
        }

        groups.forEach(group => {
            const groupElement = this.createGroupElement(group);
            groupsList.appendChild(groupElement);
        });
    }

    createGroupElement(group) {
        const div = document.createElement('div');
        div.className = 'group-item';
        div.dataset.groupId = group.id;
        div.dataset.chatType = 'group';

        div.innerHTML = `
            <div class="group-avatar">${api.getUserInitials(group.group_name)}</div>
            <div class="group-info">
                <div class="group-name">${group.group_name}</div>
                <div class="last-message">${group.members_count} members</div>
            </div>
            <div class="chat-meta">
                <div class="message-time">${group.last_message_time ? api.formatTime(group.last_message_time) : ''}</div>
            </div>
        `;

        div.addEventListener('click', () => this.openChat('group', group.id, group.group_name));
        
        return div;
    }

    // Chat Management
    async openChat(type, id, name) {
        // Update UI state
        this.currentChat = id;
        this.currentChatType = type;
        this.currentPage = 1;

        // Update active chat styling
        document.querySelectorAll('.chat-item, .group-item').forEach(item => {
            item.classList.remove('active');
        });
        
        const chatElement = document.querySelector(`[data-${type === 'private' ? 'user' : 'group'}-id="${id}"]`);
        if (chatElement) {
            chatElement.classList.add('active');
        }

        // Show chat window
        document.getElementById('welcomeScreen').style.display = 'none';
        document.getElementById('chatWindow').style.display = 'flex';

        // Update chat header
        document.getElementById('chatTitle').textContent = name;
        document.getElementById('chatStatus').textContent = type === 'private' ? 'Private Chat' : 'Group Chat';

        // Load messages
        await this.loadMessages();
    }

    async loadMessages(append = false) {
        if (this.isLoadingMessages) return;
        
        this.isLoadingMessages = true;
        
        try {
            let response;
            
            if (this.currentChatType === 'private') {
                response = await api.getPrivateConversation(this.currentChat, this.currentPage);
            } else {
                response = await api.getGroupConversation(this.currentChat, this.currentPage);
            }

            if (response.success) {
                this.renderMessages(response.data.data, append);
                
                // Mark messages as seen
                const unseenMessageIds = response.data.data
                    .filter(msg => !msg.is_seen && msg.sender.id !== api.getCurrentUser().id)
                    .map(msg => msg.id);
                
                if (unseenMessageIds.length > 0) {
                    await api.markAsSeen(unseenMessageIds);
                }
            }
        } catch (error) {
            this.showToast('Failed to load messages', 'error');
        } finally {
            this.isLoadingMessages = false;
        }
    }

    renderMessages(messages, append = false) {
        const container = document.getElementById('messagesContainer');
        
        if (!append) {
            container.innerHTML = '';
        }

        const currentUser = api.getCurrentUser();
        
        messages.forEach(message => {
            const messageElement = this.createMessageElement(message, currentUser);
            
            if (append) {
                container.prepend(messageElement);
            } else {
                container.appendChild(messageElement);
            }
        });

        if (!append) {
            container.scrollTop = container.scrollHeight;
        }
    }

    createMessageElement(message, currentUser) {
        const div = document.createElement('div');
        div.className = `message ${message.sender.id === currentUser.id ? 'sent' : 'received'}`;
        div.dataset.messageId = message.id;

        const showSender = message.sender.id !== currentUser.id || this.currentChatType === 'group';
        
        div.innerHTML = `
            ${message.sender.id !== currentUser.id ? `<div class="message-avatar">${api.getUserInitials(message.sender.name)}</div>` : ''}
            <div class="message-bubble">
                ${showSender && message.sender.id !== currentUser.id ? `<div class="message-sender">${message.sender.name}</div>` : ''}
                <div class="message-content">${this.escapeHtml(message.message)}</div>
                ${message.attachments && message.attachments.length > 0 ? this.renderAttachments(message.attachments) : ''}
                <div class="message-time">${api.formatTime(message.created_at)}</div>
            </div>
        `;

        return div;
    }

    renderAttachments(attachments) {
        return attachments.map(attachment => `
            <div class="message-attachment">
                <div class="attachment-icon">
                    <i class="fas ${this.getFileIcon(attachment.file_type)}"></i>
                </div>
                <div class="attachment-info">
                    <div class="attachment-name">${attachment.original_name}</div>
                    <div class="attachment-size">${api.formatFileSize(attachment.file_size)}</div>
                </div>
            </div>
        `).join('');
    }

    getFileIcon(fileType) {
        if (fileType.startsWith('image/')) return 'fa-image';
        if (fileType.includes('pdf')) return 'fa-file-pdf';
        if (fileType.includes('word') || fileType.includes('document')) return 'fa-file-word';
        return 'fa-file';
    }

    // Message Sending
    async handleSendMessage(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('messageInput');
        const fileInput = document.getElementById('fileInput');
        
        const message = messageInput.value.trim();
        const files = Array.from(fileInput.files);

        if (!message && files.length === 0) {
            this.showToast('Please enter a message or select a file', 'warning');
            return;
        }

        if (!this.currentChat) {
            this.showToast('Please select a chat first', 'warning');
            return;
        }

        // Validate files
        try {
            files.forEach(file => api.validateFile(file));
        } catch (error) {
            this.showToast(error.message, 'error');
            return;
        }

        try {
            let response;
            
            if (this.currentChatType === 'private') {
                response = await api.sendPrivateMessage(this.currentChat, message, files.length > 0 ? files : null);
            } else {
                response = await api.sendGroupMessage(this.currentChat, message, files.length > 0 ? files : null);
            }

            if (response.success) {
                messageInput.value = '';
                fileInput.value = '';
                
                // Add message to UI immediately
                const messageElement = this.createMessageElement(response.data, api.getCurrentUser());
                document.getElementById('messagesContainer').appendChild(messageElement);
                document.getElementById('messagesContainer').scrollTop = document.getElementById('messagesContainer').scrollHeight;
                
                // Refresh chat previews
                await this.loadChatPreviews();
            }
        } catch (error) {
            this.showToast(error.message || 'Failed to send message', 'error');
        }
    }

    handleFileSelect(e) {
        const files = Array.from(e.target.files);
        
        if (files.length > 0) {
            const fileNames = files.map(f => f.name).join(', ');
            this.showToast(`Selected: ${fileNames}`, 'success');
        }
    }

    // Group Management
    async showCreateGroupModal() {
        try {
            const response = await api.getAvailableUsers();
            
            if (response.success) {
                this.renderAvailableUsers(response.data);
                document.getElementById('createGroupModal').classList.add('active');
            }
        } catch (error) {
            this.showToast('Failed to load users', 'error');
        }
    }

    renderAvailableUsers(users) {
        const usersList = document.getElementById('usersList');
        usersList.innerHTML = '';
        this.selectedUsers.clear();

        users.forEach(user => {
            const userElement = this.createUserSelectionElement(user);
            usersList.appendChild(userElement);
        });
    }

    createUserSelectionElement(user) {
        const div = document.createElement('div');
        div.className = 'user-item';
        
        div.innerHTML = `
            <input type="checkbox" class="user-checkbox" value="${user.id}">
            <div class="user-avatar">${api.getUserInitials(user.name)}</div>
            <div class="user-details">
                <div class="user-name">${user.name}</div>
                <div class="user-role-badge">${user.role}</div>
            </div>
        `;

        const checkbox = div.querySelector('.user-checkbox');
        checkbox.addEventListener('change', (e) => {
            if (e.target.checked) {
                this.selectedUsers.add(parseInt(user.id));
            } else {
                this.selectedUsers.delete(parseInt(user.id));
            }
        });

        return div;
    }

    async handleCreateGroup(e) {
        e.preventDefault();
        
        const groupName = document.getElementById('groupName').value.trim();
        const memberIds = Array.from(this.selectedUsers);

        if (!groupName) {
            this.showToast('Please enter a group name', 'warning');
            return;
        }

        if (memberIds.length === 0) {
            this.showToast('Please select at least one member', 'warning');
            return;
        }

        try {
            const response = await api.createGroup(groupName, memberIds);
            
            if (response.success) {
                this.showToast('Group created successfully!', 'success');
                this.closeModals();
                document.getElementById('groupName').value = '';
                await this.loadUserGroups();
            }
        } catch (error) {
            this.showToast(error.message || 'Failed to create group', 'error');
        }
    }

    // Search Functionality
    showSearchModal() {
        document.getElementById('searchModal').classList.add('active');
        document.getElementById('searchKeyword').focus();
    }

    async handleSearchMessages() {
        const keyword = document.getElementById('searchKeyword').value.trim();
        
        if (!keyword) {
            this.showToast('Please enter a search keyword', 'warning');
            return;
        }

        try {
            const response = await api.searchMessages(keyword);
            
            if (response.success) {
                this.renderSearchResults(response.data);
            }
        } catch (error) {
            this.showToast('Search failed', 'error');
        }
    }

    renderSearchResults(results) {
        const container = document.getElementById('searchResults');
        container.innerHTML = '';

        if (results.length === 0) {
            container.innerHTML = '<div class="no-data">No messages found</div>';
            return;
        }

        results.forEach(result => {
            const resultElement = this.createSearchResultElement(result);
            container.appendChild(resultElement);
        });
    }

    createSearchResultElement(result) {
        const div = document.createElement('div');
        div.className = 'search-result';
        
        div.innerHTML = `
            <div class="search-result-header">
                <span class="search-result-sender">${result.sender.name}</span>
                <span class="search-result-time">${api.formatTime(result.created_at)}</span>
            </div>
            <div class="search-result-content">${this.escapeHtml(result.message)}</div>
        `;

        div.addEventListener('click', () => {
            this.closeModals();
            if (result.group_id) {
                // Navigate to group chat
                // You could implement this to open the specific group
            } else {
                // Navigate to private chat
                const otherUser = result.sender.id === api.getCurrentUser().id ? result.receiver : result.sender;
                this.openChat('private', otherUser.id, otherUser.name);
            }
        });

        return div;
    }

    handleSearchChats(e) {
        const query = e.target.value.toLowerCase();
        const chatItems = document.querySelectorAll('.chat-item, .group-item');
        
        chatItems.forEach(item => {
            const name = item.querySelector('.chat-name, .group-name').textContent.toLowerCase();
            if (name.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Tab Management
    handleTabSwitch(e) {
        const tabName = e.target.dataset.tab;
        
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        e.target.classList.add('active');
        
        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById(`${tabName}${tabName === 'chats' ? 'Tab' : 'TabContent'}`).classList.add('active');
    }

    // Polling for real-time updates
    startPolling() {
        this.stopPolling(); // Clear any existing interval
        
        this.pollingInterval = setInterval(async () => {
            try {
                // Refresh chat previews
                await this.loadChatPreviews();
                
                // Refresh current conversation if open
                if (this.currentChat) {
                    const container = document.getElementById('messagesContainer');
                    const wasAtBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 1;
                    
                    await this.loadMessages();
                    
                    // Maintain scroll position unless user was at bottom
                    if (wasAtBottom) {
                        container.scrollTop = container.scrollHeight;
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, CONFIG.POLLING_INTERVAL);
    }

    stopPolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }

    // Pagination
    handleMessagesScroll(e) {
        const container = e.target;
        
        // Load more messages when scrolled to top
        if (container.scrollTop === 0 && !this.isLoadingMessages) {
            this.currentPage++;
            this.loadMessages(true);
        }
    }

    // Utility Methods
    async handleRefresh() {
        this.showLoading();
        
        try {
            await this.loadInitialData();
            if (this.currentChat) {
                await this.loadMessages();
            }
            this.showToast('Refreshed successfully', 'success');
        } catch (error) {
            this.showToast('Refresh failed', 'error');
        } finally {
            this.hideLoading();
        }
    }

    closeModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('active');
        });
    }

    showLoading() {
        document.getElementById('loadingOverlay').classList.add('active');
    }

    hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('active');
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        toast.innerHTML = `
            <div class="toast-header">
                <i class="fas ${icons[type]} toast-icon"></i>
                <span class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
            </div>
            <div class="toast-message">${message}</div>
        `;

        document.getElementById('toastContainer').appendChild(toast);

        // Auto remove after duration
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, CONFIG.TOAST_DURATION);
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ChatApp();
});