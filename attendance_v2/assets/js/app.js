// Global Configuration
const API_BASE = getApiBase();
const UPLOAD_BASE = '../../uploads/';

// Get API base URL based on current path
function getApiBase() {
    const path = window.location.pathname;
    if (path.includes('/pages/admin/') || path.includes('/pages/professor/') || path.includes('/pages/student/')) {
        return '../../api/';
    } else if (path.includes('/pages/')) {
        return '../api/';
    }
    return 'api/';
}

// Authentication
function getCurrentUser() {
    const userStr = sessionStorage.getItem('user');
    return userStr ? JSON.parse(userStr) : null;
}

function setCurrentUser(user) {
    sessionStorage.setItem('user', JSON.stringify(user));
}

function isLoggedIn() {
    return getCurrentUser() !== null;
}

function requireAuth(role = null) {
    const user = getCurrentUser();
    if (!user) {
        window.location.href = '../../index.html';
        return false;
    }
    if (role && user.role !== role) {
        window.location.href = '../../index.html';
        return false;
    }
    return true;
}

function logout() {
    sessionStorage.clear();
    window.location.href = '../../index.html';
}

// API Helper
async function apiCall(endpoint, method = 'GET', data = null, formData = null) {
    const options = {
        method: method,
        headers: {}
    };
    
    if (formData) {
        options.body = formData;
    } else if (data && method !== 'GET') {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(API_BASE + endpoint, options);
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            throw new Error('Server returned non-JSON response');
        }
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Request failed');
        }
        
        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// UI Helpers
function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <span>${escapeHtml(message)}</span>
        <button class="modal-close" onclick="this.parentElement.remove()" style="margin-left: auto;">×</button>
    `;
    
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => alertDiv.remove(), 5000);
}

function showModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Close modals on outside click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('show');
    }
});






