// File: resources/js/layout/utils.js
export function isAuthenticated() {
    return !!localStorage.getItem('authToken');
}