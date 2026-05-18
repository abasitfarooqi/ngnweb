import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;
window.supportEchoEnabled = false;

function ngnEnv(name, fallback = '') {
    const meta = document.querySelector(`meta[name="ngn-env:${name}"]`);

    return meta?.content?.trim() || fallback;
}

const key = ngnEnv('pusher_app_key');
const cluster = ngnEnv('pusher_app_cluster', 'mt1');
const wsHost = ngnEnv('pusher_host');
const wsPort = Number(ngnEnv('pusher_port', '443'));
const forceTLS = (ngnEnv('pusher_scheme', 'https')) === 'https';

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
