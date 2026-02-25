// resources/js/api/initializeCsrf.js
import { getCsrfToken } from '@/services/api';

const initializeCsrf = async () => {
    try {
        await getCsrfToken.get('/sanctum/csrf-cookie');
    } catch (error) {
        console.error('Failed to initialize CSRF protection:', error);
    }
};

export default initializeCsrf;