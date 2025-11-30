// Authentication helper functions

// Get current user from sessionStorage
function getCurrentUser() {
    return {
        id: sessionStorage.getItem('user_id'),
        username: sessionStorage.getItem('username'),
        role: sessionStorage.getItem('role'),
        fullname: sessionStorage.getItem('fullname')
    };
}

// Check if user is logged in
function isLoggedIn() {
    const user = getCurrentUser();
    return user.id && user.role;
}

// Require login - redirect to login if not logged in
function requireLogin(expectedRole = null) {
    if (!isLoggedIn()) {
        // Determine correct path to login
        const path = window.location.pathname;
        let loginPath = 'login.html';
        if (path.includes('/pages/')) {
            loginPath = '../../login.html';
        } else if (path.includes('/professor/') || path.includes('/student/') || path.includes('/admin/')) {
            loginPath = '../../login.html';
        }
        window.location.href = loginPath;
        return false;
    }
    
    const user = getCurrentUser();
    if (expectedRole && user.role !== expectedRole) {
        const path = window.location.pathname;
        let loginPath = 'login.html';
        if (path.includes('/pages/')) {
            loginPath = '../../login.html';
        }
        window.location.href = loginPath;
        return false;
    }
    
    return true;
}

// Logout
function logout() {
    sessionStorage.clear();
    // Determine correct path to login
    const path = window.location.pathname;
    let loginPath = 'login.html';
    if (path.includes('/pages/')) {
        loginPath = '../../login.html';
    } else if (path.includes('/professor/') || path.includes('/student/') || path.includes('/admin/')) {
        loginPath = '../../login.html';
    }
    window.location.href = loginPath;
}

// Get user ID for API calls
function getUserId() {
    const user = getCurrentUser();
    return user.id ? parseInt(user.id) : null;
}

