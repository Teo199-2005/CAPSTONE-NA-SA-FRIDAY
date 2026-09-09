/**
 * Notification System JavaScript
 * Handles notification functionality for CSCS SMS
 */

class NotificationSystem {
    constructor() {
        this.baseUrl = window.location.origin;
        this.init();
    }

    getRoleBasePath() {
        const path = window.location.pathname || '';
        if (path.startsWith('/admin/')) return '/admin';
        if (path.startsWith('/student/')) return '/student';
        if (path.startsWith('/teacher/')) return '/teacher';
        return '/teacher';
    }

    init() {
        this.setupEventListeners();
        this.loadUnreadCount();
        
        // Auto-refresh unread count every 30 seconds
        setInterval(() => {
            this.loadUnreadCount();
        }, 30000);
    }

    setupEventListeners() {
        // Handle notification bell click
        const notificationBell = document.querySelector('.notification-bell');
        if (notificationBell) {
            notificationBell.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleNotificationDropdown();
            });
        }

        // Handle mark as read buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('mark-as-read')) {
                e.preventDefault();
                const notificationId = e.target.dataset.id;
                this.markAsRead(notificationId);
            }
        });

        // Handle notification form submission
        const notificationForm = document.querySelector('#notification-form');
        if (notificationForm) {
            notificationForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.sendNotification(notificationForm);
            });
        }
    }

    async loadUnreadCount() {
        try {
            const roleBase = this.getRoleBasePath();
            const response = await fetch(`${this.baseUrl}${roleBase}/notifications/unread-count`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateNotificationBadge(data.count);
            }
        } catch (error) {
            console.error('Error loading unread count:', error);
        }
    }

    updateNotificationBadge(count) {
        const badges = document.querySelectorAll('.notification-badge, #topbar-notifications-count');
        if (!badges || badges.length === 0) return;
        badges.forEach((badge) => {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    async markAsRead(notificationId) {
        try {
            const formData = new FormData();
            formData.append('notification_id', notificationId);
            
            // Add CSRF token if available
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append(csrfToken.getAttribute('name'), csrfToken.getAttribute('content'));
            }

            const roleBase = this.getRoleBasePath();
            const response = await fetch(`${this.baseUrl}${roleBase}/notifications/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    // Remove the notification from the UI or mark it as read
                    const notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notificationElement) {
                        notificationElement.classList.add('read');
                        const markButton = notificationElement.querySelector('.mark-as-read');
                        if (markButton) {
                            markButton.remove();
                        }
                    }
                    
                    // Update the unread count
                    this.loadUnreadCount();
                    
                    this.showMessage('Notification marked as read', 'success');
                } else {
                    this.showMessage('Failed to mark notification as read', 'error');
                }
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
            this.showMessage('An error occurred', 'error');
        }
    }

    async sendNotification(form) {
        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                // Check if it's a redirect (successful submission)
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    this.showMessage('Notification sent successfully', 'success');
                    form.reset();
                }
            } else {
                this.showMessage('Failed to send notification', 'error');
            }
        } catch (error) {
            console.error('Error sending notification:', error);
            this.showMessage('An error occurred while sending notification', 'error');
        }
    }

    toggleNotificationDropdown() {
        const dropdown = document.querySelector('.notification-dropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    showMessage(message, type = 'info') {
        // Create a simple toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }
}

// Initialize notification system when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new NotificationSystem();
});

// Export for use in other scripts
window.NotificationSystem = NotificationSystem;
