import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';
import persist from '@alpinejs/persist';

const hadAlpineAlready = !!window.Alpine;
const AlpineRuntime = hadAlpineAlready ? window.Alpine : Alpine;

if (!hadAlpineAlready) {
    AlpineRuntime.plugin(focus);
    AlpineRuntime.plugin(intersect);
    AlpineRuntime.plugin(persist);
}

AlpineRuntime.data('homeRentalCarousel', (slideCount) => ({
    slideCount,
    index: 0,
    get atStart() {
        return this.index <= 0;
    },
    get atEnd() {
        return this.index >= this.slideCount - 1;
    },
    onScroll() {
        const el = this.$refs.viewport;
        if (!el) return;
        const w = el.clientWidth || 1;
        this.index = Math.min(this.slideCount - 1, Math.max(0, Math.round(el.scrollLeft / w)));
    },
    step(delta) {
        this.goTo(Math.min(this.slideCount - 1, Math.max(0, this.index + delta)));
    },
    goTo(i) {
        const el = this.$refs.viewport;
        if (!el) return;
        const w = el.clientWidth;
        el.scrollTo({ left: i * w, behavior: 'smooth' });
        this.index = i;
    },
}));

window.Alpine = AlpineRuntime;

function unlockSupportNotificationAudio() {
    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (!AudioContextClass) {
        return null;
    }

    if (!window.__supportNotificationAudioContext) {
        window.__supportNotificationAudioContext = new AudioContextClass();
    }

    const context = window.__supportNotificationAudioContext;
    if (context.state === 'suspended') {
        context.resume().catch(() => {});
    }

    return context;
}

window.playSupportNotificationSound = function playSupportNotificationSound() {
    const context = unlockSupportNotificationAudio();
    if (!context || context.state === 'suspended') {
        return;
    }

    const now = context.currentTime;
    [880, 1174].forEach((frequency, index) => {
        const oscillator = context.createOscillator();
        const gain = context.createGain();
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(frequency, now + index * 0.11);
        gain.gain.setValueAtTime(0.0001, now + index * 0.11);
        gain.gain.exponentialRampToValueAtTime(0.12, now + index * 0.11 + 0.015);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + index * 0.11 + 0.16);
        oscillator.connect(gain);
        gain.connect(context.destination);
        oscillator.start(now + index * 0.11);
        oscillator.stop(now + index * 0.11 + 0.18);
    });
};

['pointerdown', 'keydown', 'touchstart'].forEach((eventName) => {
    window.addEventListener(eventName, unlockSupportNotificationAudio, { once: true, passive: true });
});

window.addEventListener('support:incoming-message', () => {
    if (typeof window.playSupportNotificationSound === 'function') {
        window.playSupportNotificationSound();
    }
});

window.setupSupportConversationEcho = function setupSupportConversationEcho(conversationUuid, onIncoming) {
    if (!window.supportEchoEnabled || !window.Echo || !conversationUuid) {
        return () => {};
    }
    const channel = window.Echo.private(`support.conversation.${conversationUuid}`);
    channel.listen('.message.sent', (payload) => {
        if (typeof onIncoming === 'function') {
            onIncoming(payload);
        }
    });

    return () => {
        window.Echo.leave(`private-support.conversation.${conversationUuid}`);
    };
};

window.setupSupportStaffEcho = function setupSupportStaffEcho(onIncoming) {
    if (!window.supportEchoEnabled || !window.Echo) {
        return () => {};
    }
    const channel = window.Echo.private('support.staff');
    channel.listen('.message.sent', (payload) => {
        if (typeof onIncoming === 'function') {
            onIncoming(payload);
        }
    });

    return () => {
        window.Echo.leave('private-support.staff');
    };
};

window.setupSupportCustomerEcho = function setupSupportCustomerEcho(customerAuthId, onIncoming) {
    if (!window.supportEchoEnabled || !window.Echo || !customerAuthId) {
        return () => {};
    }
    const channel = window.Echo.private(`support.customer.${customerAuthId}`);
    channel.listen('.message.sent', (payload) => {
        if (typeof onIncoming === 'function') {
            onIncoming(payload);
        }
    });

    return () => {
        window.Echo.leave(`private-support.customer.${customerAuthId}`);
    };
};

function customerCommunicationAlertsEnabled() {
    if (typeof Notification === 'undefined') {
        return false;
    }

    return Notification.permission === 'granted'
        || window.localStorage.getItem('ngn-communication-alerts') === '1';
}

function showCustomerCommunicationBrowserAlert(payload) {
    if (!payload || typeof Notification === 'undefined' || Notification.permission !== 'granted') {
        return;
    }

    const notification = new Notification(payload.title || 'NGN Motors', {
        body: payload.preview || payload.subject || '',
        tag: payload.uuid || 'ngn-communication',
    });

    notification.onclick = function () {
        window.focus();
        if (payload.uuid) {
            window.location.href = `/account/notifications/${payload.uuid}`;
        }
    };
}

function bumpPortalNotificationsBadge() {
    document.querySelectorAll('.js-notifications-unread').forEach((badge) => {
        const next = parseInt(badge.getAttribute('data-count') || badge.textContent || '0', 10) + 1;
        badge.setAttribute('data-count', String(next));
        badge.textContent = String(next);
        badge.classList.remove('hidden');
    });
}

function prependCustomerNotificationMenuItem(payload) {
    if (!payload || !payload.uuid) {
        return;
    }

    document.querySelectorAll('.js-notifications-dropdown-list').forEach((list) => {
        const empty = list.querySelector('.js-notifications-empty');
        if (empty) {
            empty.remove();
        }

        if (list.querySelector(`[data-notification-uuid="${payload.uuid}"]`)) {
            return;
        }

        const link = document.createElement('a');
        link.href = `/account/notifications/${payload.uuid}`;
        link.setAttribute('data-notification-uuid', payload.uuid);
        link.className = 'block border-b border-gray-100 px-3 py-2.5 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/60';

        const title = document.createElement('p');
        title.className = 'flex items-start justify-between gap-2 text-sm font-medium text-gray-900 dark:text-white';
        const titleText = document.createElement('span');
        titleText.className = 'min-w-0 truncate';
        titleText.textContent = payload.title || 'Notification';
        const unreadDot = document.createElement('span');
        unreadDot.className = 'mt-1 inline-block h-2 w-2 shrink-0 bg-brand-red';
        title.appendChild(titleText);
        title.appendChild(unreadDot);
        link.appendChild(title);

        if (payload.preview) {
            const preview = document.createElement('p');
            preview.className = 'mt-0.5 line-clamp-2 text-xs text-gray-500 dark:text-gray-400';
            preview.textContent = payload.preview;
            link.appendChild(preview);
        }

        list.insertBefore(link, list.firstChild);

        const rows = list.querySelectorAll('a[data-notification-uuid]');
        rows.forEach((row, index) => {
            if (index >= 5) {
                row.remove();
            }
        });
    });
}

function refreshCommunicationAlertStatus() {
    const status = document.getElementById('portal-browser-alerts-status');
    if (!status) {
        return;
    }

    if (typeof Notification === 'undefined') {
        status.textContent = 'Browser alerts are not available in this browser.';
        return;
    }

    if (!window.supportEchoEnabled) {
        status.textContent = 'Realtime is off until Pusher is configured. You can still allow browser alerts.';
    }

    if (Notification.permission === 'granted') {
        status.textContent = window.supportEchoEnabled
            ? 'Browser alerts are on. New messages will sound in real time.'
            : 'Browser alerts are allowed, but realtime sound needs Pusher.';
        return;
    }

    if (Notification.permission === 'denied') {
        status.textContent = 'Browser alerts are blocked. Allow notifications for this site in the browser settings.';
        return;
    }

    status.textContent = 'Browser alerts are off.';
}

window.resizeCommunicationEmailFrame = function resizeCommunicationEmailFrame(iframe) {
    if (!iframe) {
        return;
    }

    const apply = function applyHeight() {
        try {
            const doc = iframe.contentDocument || iframe.contentWindow?.document;
            if (!doc) {
                iframe.style.height = '720px';
                return;
            }
            const height = Math.max(
                doc.documentElement?.scrollHeight || 0,
                doc.body?.scrollHeight || 0,
                doc.documentElement?.offsetHeight || 0,
                doc.body?.offsetHeight || 0,
                720,
            );
            iframe.style.height = `${height + 24}px`;
        } catch (error) {
            iframe.style.height = '960px';
        }
    };

    apply();
    window.setTimeout(apply, 250);
    window.setTimeout(apply, 1000);
};

window.enableCustomerCommunicationAlerts = function enableCustomerCommunicationAlerts() {
    unlockSupportNotificationAudio();
    window.localStorage.setItem('ngn-communication-alerts', '1');

    if (typeof Notification === 'undefined') {
        refreshCommunicationAlertStatus();
        window.playSupportNotificationSound();
        return Promise.resolve('unsupported');
    }

    const apply = (permission) => {
        refreshCommunicationAlertStatus();
        window.playSupportNotificationSound();
        if (permission === 'granted') {
            new Notification('NGN Motors alerts enabled', {
                body: 'You will hear a sound and see an alert when a new notification arrives.',
            });
        }

        return permission;
    };

    if (Notification.permission === 'granted') {
        return Promise.resolve(apply('granted'));
    }

    if (Notification.permission === 'denied') {
        refreshCommunicationAlertStatus();
        window.playSupportNotificationSound();
        return Promise.resolve('denied');
    }

    return Notification.requestPermission().then(apply);
};

function bindCommunicationAlertControls() {
    refreshCommunicationAlertStatus();
}

document.addEventListener('click', function (event) {
    const button = event.target.closest('#enable-communication-alerts');
    if (!button) {
        return;
    }
    event.preventDefault();
    window.enableCustomerCommunicationAlerts();
});

window.setupCustomerCommunicationsEcho = function setupCustomerCommunicationsEcho(customerAuthId) {
    if (!window.supportEchoEnabled || !window.Echo || !customerAuthId) {
        return () => {};
    }
    if (window.__customerCommunicationsEchoBound === customerAuthId) {
        return () => {};
    }

    const channel = window.Echo.private(`communications.customer.${customerAuthId}`);
    channel.listen('.communication.created', (payload) => {
        bumpPortalNotificationsBadge();
        prependCustomerNotificationMenuItem(payload);
        window.playSupportNotificationSound();
        if (customerCommunicationAlertsEnabled()) {
            showCustomerCommunicationBrowserAlert(payload);
        }
        if (window.Livewire && payload && payload.uuid) {
            window.Livewire.dispatch('customerCommunicationCreated', { uuid: payload.uuid });
        }
    });
    channel.listen('.communication.reply', (payload) => {
        window.playSupportNotificationSound();
        if (window.Livewire && payload && payload.uuid) {
            window.Livewire.dispatch('customerCommunicationReply', { uuid: payload.uuid });
        }
    });

    window.__customerCommunicationsEchoBound = customerAuthId;

    return () => {
        window.Echo.leave(`private-communications.customer.${customerAuthId}`);
        window.__customerCommunicationsEchoBound = null;
    };
};

window.setupCommunicationsStaffEcho = function setupCommunicationsStaffEcho() {
    if (!window.supportEchoEnabled || !window.Echo) {
        return () => {};
    }
    if (window.__communicationsStaffEchoBound) {
        return () => {};
    }

    const channel = window.Echo.private('communications.staff');
    channel.listen('.communication.created', (payload) => {
        window.playSupportNotificationSound();
        if (customerCommunicationAlertsEnabled() && typeof Notification !== 'undefined' && Notification.permission === 'granted') {
            const notification = new Notification((payload && payload.title) || 'NGN Motors', {
                body: (payload && (payload.preview || payload.subject)) || 'New customer notification',
                tag: (payload && payload.uuid) || 'ngn-staff-communication',
            });
            notification.onclick = function () {
                window.focus();
                if (payload && payload.uuid) {
                    window.location.href = `/flux-admin/communications/sent/${payload.uuid}`;
                }
            };
        }
        if (window.Livewire) {
            window.Livewire.dispatch('staffCommunicationCreated', payload || {});
        }
    });
    channel.listen('.communication.reply', (payload) => {
        window.playSupportNotificationSound();
        if (window.Livewire) {
            window.Livewire.dispatch('staffCommunicationReply', payload || {});
        }
    });

    window.__communicationsStaffEchoBound = true;

    return () => {
        window.Echo.leave('private-communications.staff');
        window.__communicationsStaffEchoBound = false;
    };
};

function teardownSupportThreadRealtime() {
    const s = window.__supportThreadRealtimeState;
    if (!s) {
        return;
    }
    if (s.pollTimer) {
        window.clearInterval(s.pollTimer);
    }
    if (typeof s.detachConversation === 'function') {
        s.detachConversation();
    }
    if (typeof s.detachCustomer === 'function') {
        s.detachCustomer();
    }
    window.__supportThreadRealtimeState = null;
    window.__supportThreadSync = null;
}

window.bindSupportThreadRealtime = function bindSupportThreadRealtime() {
    teardownSupportThreadRealtime();
    const root = document.getElementById('support-thread-live-root');
    if (!root) {
        return;
    }

    const latestUrl = root.getAttribute('data-latest-url');
    const htmlUrl = root.getAttribute('data-messages-html-url');
    let last = parseInt(root.getAttribute('data-last-message-id') || '0', 10);
    const uuid = root.getAttribute('data-conversation-uuid');
    const customerAuthId = parseInt(root.getAttribute('data-customer-auth-id') || '0', 10);

    const state = {
        pollTimer: null,
        detachConversation: null,
        detachCustomer: null,
    };
    window.__supportThreadRealtimeState = state;

    async function syncFromServer() {
        try {
            const sep = latestUrl.includes('?') ? '&' : '?';
            const r = await fetch(`${latestUrl}${sep}_cb=${Date.now()}`, {
                cache: 'no-store',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!r.ok) {
                return;
            }
            const j = await r.json();
            const lid = parseInt(String(j.latest_message_id || 0), 10);
            if (lid <= last) {
                return;
            }
            const shouldNotify = j.latest_sender_type === 'staff';
            last = lid;
            const sep2 = htmlUrl.includes('?') ? '&' : '?';
            const r2 = await fetch(`${htmlUrl}${sep2}_cb=${Date.now()}`, {
                cache: 'no-store',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!r2.ok) {
                return;
            }
            const panel = document.getElementById('support-thread-messages-root');
            if (!panel) {
                return;
            }
            panel.innerHTML = await r2.text();
            panel.scrollTop = panel.scrollHeight;
            if (shouldNotify && typeof window.playSupportNotificationSound === 'function') {
                window.playSupportNotificationSound();
            }
        } catch (e) {
            /* ignore */
        }
    }

    state.pollTimer = window.setInterval(syncFromServer, 400);
    syncFromServer();
    window.__supportThreadSync = syncFromServer;

    if (typeof window.setupSupportConversationEcho === 'function' && uuid) {
        state.detachConversation = window.setupSupportConversationEcho(uuid, syncFromServer);
    }
    if (typeof window.setupSupportCustomerEcho === 'function' && customerAuthId) {
        state.detachCustomer = window.setupSupportCustomerEcho(customerAuthId, syncFromServer);
    }
};

function teardownSupportAdminRealtime() {
    const s = window.__supportAdminRealtimeState;
    if (!s) {
        return;
    }
    if (typeof s.detachStaff === 'function') {
        s.detachStaff();
    }
    window.__supportAdminRealtimeState = null;
}

window.bindSupportAdminRealtime = function bindSupportAdminRealtime() {
    teardownSupportAdminRealtime();
    const root = document.getElementById('support-admin-live-root');
    if (!root) {
        return;
    }

    const state = { detachStaff: null };
    window.__supportAdminRealtimeState = state;

    if (typeof window.setupSupportStaffEcho === 'function') {
        state.detachStaff = window.setupSupportStaffEcho((payload) => {
            if (payload && payload.sender_type === 'customer' && window.Livewire) {
                window.Livewire.dispatch('supportInboxRealtimeTick');
            }
        });
    }
};

// ngnSetColourMode: see resources/views/components/partials/theme-api.blade.php (loaded after @fluxAppearance).

function ngnStartAlpineIfNeeded() {
    if (window.__ngnAlpineStarted) {
        return;
    }

    // Livewire 3 bundles Alpine and must start it. Starting Alpine in app.js first breaks wire:submit
    // (forms fall back to a GET against /livewire-…/update).
    if (document.querySelector('[wire\\:id]') || window.Livewire) {
        return;
    }

    AlpineRuntime.start();
    window.__ngnAlpineStarted = true;
}

document.addEventListener('DOMContentLoaded', function () {
    ngnStartAlpineIfNeeded();

    if (typeof window.bindSupportThreadRealtime === 'function') {
        window.bindSupportThreadRealtime();
    }
    if (typeof window.bindSupportAdminRealtime === 'function') {
        window.bindSupportAdminRealtime();
    }
    const customerAuthId = parseInt(document.body.getAttribute('data-customer-auth-id') || '0', 10);
    if (typeof window.setupCustomerCommunicationsEcho === 'function' && customerAuthId) {
        window.setupCustomerCommunicationsEcho(customerAuthId);
    }
    if (document.body.getAttribute('data-staff-communications') === '1' && typeof window.setupCommunicationsStaffEcho === 'function') {
        window.setupCommunicationsStaffEcho();
    }
    bindCommunicationAlertControls();
});

document.addEventListener('livewire:init', function () {
    if (!window.Livewire || typeof window.Livewire.on !== 'function') {
        return;
    }
    window.Livewire.on('support:incoming-message', function () {
        if (typeof window.playSupportNotificationSound === 'function') {
            window.playSupportNotificationSound();
        }
    });
});

document.addEventListener('livewire:navigated', function () {
    var ngn = localStorage.getItem('ngn-theme');
    var fa = localStorage.getItem('flux.appearance');
    var mode = ngn === 'dark' || ngn === 'light' ? ngn : (fa === 'dark' || fa === 'light' ? fa : null);
    if (mode && window.Flux && typeof window.Flux.applyAppearance === 'function') {
        window.Flux.applyAppearance(mode);
    } else if (mode) {
        document.documentElement.classList.toggle('dark', mode === 'dark');
    } else if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
        window.Flux.applyAppearance('system');
    }
    if (typeof window.bindSupportThreadRealtime === 'function') {
        window.bindSupportThreadRealtime();
    }
    if (typeof window.bindSupportAdminRealtime === 'function') {
        window.bindSupportAdminRealtime();
    }
    const customerAuthId = parseInt(document.body.getAttribute('data-customer-auth-id') || '0', 10);
    if (typeof window.setupCustomerCommunicationsEcho === 'function' && customerAuthId) {
        window.setupCustomerCommunicationsEcho(customerAuthId);
    }
    if (document.body.getAttribute('data-staff-communications') === '1' && typeof window.setupCommunicationsStaffEcho === 'function') {
        window.setupCommunicationsStaffEcho();
    }
    bindCommunicationAlertControls();
});

