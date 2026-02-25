// File: resources/js/stores/sessionStore/index.js
import { defineStore } from 'pinia';

export const useSessionStore = defineStore('session', {
    state: () => ({
        sessioncid: null, // Store sessioncid here
    }),
    actions: {
        setSessionCid(cid) {
            this.sessioncid = cid; // Update the store state
        },
    },
});
