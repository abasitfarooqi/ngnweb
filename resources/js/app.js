// File: resources/js/app.js
import './bootstrap';
import './assets/styles/main.css';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createMetaManager } from 'vue-meta';
import pinia from './stores';
import { useSessionStore } from './stores/sessionStore';
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';

// // Initialize Pusher and Echo
// window.Pusher = Pusher;

// const echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY, // Fetch Pusher App Key from .env
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER, // Fetch Pusher Cluster from .env
//     secret: import.meta.env.VITE_PUSHER_APP_SECRET, // Fetch Pusher Secret from .env
//     encrypted: true,
// });

// // Listen for user login and logout events
// echo.channel('user-status')
//     .listen('UserLoggedIn', (data) => {
//         console.log('User logged in:', data);
//         // Optionally, you can update the user store or perform other actions here
//     });

// echo.channel('user-status')
//     .listen('UserLoggedOut', (data) => {
//         console.log('User logged out:', data);
//         // Optionally, you can update the user store or perform other actions here
//     });


const app1 = createApp(App);

app1.use(router);
app1.use(createMetaManager());
app1.use(pinia);
app1.mount('#app');

console.log('Login component mounted');

const sessionStore = useSessionStore();

// Check if Laravel sessioncid is available
if (window.Laravel && window.Laravel.sessioncid) {
    const sessioncid = window.Laravel.sessioncid;

    // Store sessioncid in the Pinia store
    sessionStore.setSessionCid(sessioncid);

    // Store sessioncid in a cookie
    document.cookie = `sessioncid=${sessioncid}; path=/; max-age=604800; secure; SameSite=Lax`;
}
