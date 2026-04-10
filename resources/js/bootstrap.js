import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;
window.supportEchoEnabled = false;

const key = import.meta.env.VITE_PUSHER_APP_KEY;
const cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1';
const wsHost = import.meta.env.VITE_PUSHER_HOST;
const wsPort = Number(import.meta.env.VITE_PUSHER_PORT || 443);
const forceTLS = (import.meta.env.VITE_PUSHER_SCHEME || 'https') === 'https';

if (key) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key,
        cluster,
        wsHost: wsHost || `ws-${cluster}.pusher.com`,
        wsPort,
        wssPort: wsPort,
        forceTLS,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        },
    });
    window.supportEchoEnabled = true;
}