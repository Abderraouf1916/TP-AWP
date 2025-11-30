// Common JavaScript functions

// Determine API base path based on current location
function getApiBase() {
    const path = window.location.pathname;
    if (path.includes('/pages/')) {
        return '../../api/';
    } else if (path.includes('/professor/') || path.includes('/student/') || path.includes('/admin/')) {
        return '../../api/';
    } else {
        return 'api/';
    }
}

const API_BASE = getApiBase();

// Show message
function showMessage(message, type = 'success') {
    const messageDiv = document.getElementById('message');
    if (!messageDiv) return;
    
    messageDiv.textContent = message;
    messageDiv.className = `message ${type}`;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// API call helper
async function apiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }
    
    try {
        const apiBase = window.API_BASE || API_BASE;
        const response = await fetch(apiBase + endpoint, options);
        
        // Check if response is actually JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text.substring(0, 200));
            throw new Error('Server returned non-JSON response. Check API path and PHP errors.');
        }
        
        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Request failed');
        }
        
        return result;
    } catch (error) {
        console.error('API Error:', error);
        console.error('API URL:', API_BASE + endpoint);
        throw error;
    }
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

