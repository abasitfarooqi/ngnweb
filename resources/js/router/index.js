// File: resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import routes from './routes';
import { useUserStore } from '../stores/modules/user';
import { authAPI } from '@/services/api';


const router = createRouter({
    history: createWebHistory(),
    routes,
});

let lastAuthState = null;

// Navigation Guards
router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore();

    // Only log when auth state actually changes
    if (lastAuthState !== userStore.isAuthenticated) {
        console.log(`Auth state changed - Path: ${to.path}, IsAuthenticated: ${userStore.isAuthenticated}`);
        lastAuthState = userStore.isAuthenticated;
    }

    // Skip validation for login/register pages to prevent redirect loops
    if (to.name === 'Login' || to.name === 'Register') {
        // If already authenticated, redirect to account page
        if (userStore.isAuthenticated) {
            console.log('Already authenticated, redirecting to account page');
            return next('/accountinformation');
        }
        return next();
    }

    // For protected routes or if not initialized, validate session
    if (to.meta.requiresAuth || !userStore.initialized) {
        try {
            console.log('Validating session for protected route');
            const isValid = await userStore.validateSession();

            if (!isValid && to.meta.requiresAuth) {
                console.log('Session invalid, redirecting to login');
                return next({
                    path: '/accountinformation/login',
                    query: { redirect: to.fullPath }
                });
            }
        } catch (error) {
            console.error('Session validation error:', error);
            if (to.meta.requiresAuth) {
                return next({
                    path: '/accountinformation/login',
                    query: { redirect: to.fullPath }
                });
            }
        }
    }

    // Handle guest-only routes
    if (userStore.isAuthenticated && to.meta.requiresGuest) {
        console.log('Authenticated user attempting to access guest route - redirecting to account page');
        return next('/accountinformation');
    }

    // If we get here, proceed with navigation
    next();
});

export default router;
