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
    if (!soundAlertsEnabled()) {
        return;
    }

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
    if (typeof onIncoming === 'function') {
        window.__supportStaffEchoOnIncoming = onIncoming;
    }
    if (!window.supportEchoEnabled || !window.Echo) {
        return () => {};
    }
    if (window.__supportStaffEchoBound) {
        return () => {};
    }

    const channel = window.Echo.private('support.staff');
    channel.listen('.message.sent', (payload) => {
        if (typeof window.refreshStaffUnreadBadges === 'function') {
            window.refreshStaffUnreadBadges();
            window.setTimeout(window.refreshStaffUnreadBadges, 400);
        }
        if (typeof window.__supportStaffEchoOnIncoming === 'function') {
            window.__supportStaffEchoOnIncoming(payload);
        }
    });

    window.__supportStaffEchoBound = true;

    return () => {};
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

const NGN_SOUND_KEY = 'ngn-communication-alerts';

function soundAlertsEnabled() {
    try {
        return window.localStorage.getItem(NGN_SOUND_KEY) !== '0';
    } catch (error) {
        return true;
    }
}

function persistSoundAlertsEnabled(enabled) {
    try {
        window.localStorage.setItem(NGN_SOUND_KEY, enabled ? '1' : '0');
    } catch (error) {}

    try {
        window.__ngnSoundChannel?.postMessage({ enabled: !!enabled });
    } catch (error) {}
}

function ensureSoundAlertsDefaultOn() {
    try {
        if (window.localStorage.getItem(NGN_SOUND_KEY) === null) {
            window.localStorage.setItem(NGN_SOUND_KEY, '1');
        }
    } catch (error) {}
}

function readableCommunicationPreview(text, fallback) {
    const value = String(text || '');
    if (value.includes('-webkit-text-size-adjust') || value.includes('mso-table-') || value.includes('{mso-')) {
        const before = value.split('{')[0].replace(/\b(?:body|html|table|td|a)(?:,(?:body|html|table|td|a))*\s*$/i, '').trim();
        return before || fallback || '';
    }
    return value || fallback || '';
}

function soundAudioReady() {
    const context = window.__supportNotificationAudioContext;
    return !!(context && context.state !== 'suspended');
}

function customerCommunicationAlertsEnabled() {
    return soundAlertsEnabled()
        && typeof Notification !== 'undefined'
        && Notification.permission === 'granted';
}

function showCustomerCommunicationBrowserAlert(payload) {
    if (!payload || typeof Notification === 'undefined' || Notification.permission !== 'granted') {
        return;
    }

    const notification = new Notification(payload.title || 'NGN Motors', {
        body: readableCommunicationPreview(payload.preview || payload.subject || '', payload.title || ''),
        tag: payload.uuid || 'ngn-communication',
    });

    notification.onclick = function () {
        window.focus();
        if (payload.uuid) {
            window.location.href = `/account/notifications/${payload.uuid}`;
        }
    };
}

function applyPortalNotificationsBadge(count) {
    const n = Math.max(0, parseInt(count, 10) || 0);
    document.querySelectorAll('.js-notifications-unread').forEach((badge) => {
        badge.setAttribute('data-count', String(n));
        badge.textContent = String(n);
        badge.classList.toggle('hidden', n <= 0);
    });
}

function bumpPortalNotificationsBadge() {
    const first = document.querySelector('.js-notifications-unread');
    const next = parseInt((first && (first.getAttribute('data-count') || first.textContent)) || '0', 10) + 1;
    applyPortalNotificationsBadge(next);
}

function notificationMenuRow(item) {
    const link = document.createElement('a');
    link.href = `/account/notifications/${item.uuid}`;
    link.setAttribute('data-notification-uuid', item.uuid);
    link.className = 'block border-b border-gray-100 px-3 py-2.5 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700';

    const title = document.createElement('p');
    title.className = 'flex items-start justify-between gap-2 text-sm font-medium text-gray-900 dark:text-white';
    const titleText = document.createElement('span');
    titleText.className = 'min-w-0 truncate';
    titleText.textContent = item.title || 'Notification';
    title.appendChild(titleText);
    if (item.unread) {
        const unreadDot = document.createElement('span');
        unreadDot.className = 'mt-1 inline-block h-2 w-2 shrink-0 bg-brand-red';
        title.appendChild(unreadDot);
    }
    link.appendChild(title);

    if (item.preview) {
        const preview = document.createElement('p');
        preview.className = 'mt-0.5 line-clamp-2 text-xs text-gray-500 dark:text-gray-400';
        preview.textContent = readableCommunicationPreview(item.preview, item.title || '');
        link.appendChild(preview);
    }

    if (item.created_at) {
        const when = document.createElement('p');
        when.className = 'mt-1 text-[11px] text-gray-400';
        when.textContent = item.created_at;
        link.appendChild(when);
    }

    return link;
}

function renderCustomerNotificationMenu(items) {
    document.querySelectorAll('.js-notifications-dropdown-list').forEach((list) => {
        list.replaceChildren();
        if (!items || items.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'js-notifications-empty px-3 py-4 text-sm text-gray-500 dark:text-gray-400';
            empty.textContent = 'No notifications yet.';
            list.appendChild(empty);
            return;
        }
        items.forEach((item) => {
            if (item && item.uuid) {
                list.appendChild(notificationMenuRow(item));
            }
        });
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

        list.insertBefore(notificationMenuRow({
            uuid: payload.uuid,
            title: payload.title || 'Notification',
            preview: payload.preview || '',
            created_at: '',
            unread: true,
        }), list.firstChild);

        const rows = list.querySelectorAll('a[data-notification-uuid]');
        rows.forEach((row, index) => {
            if (index >= 5) {
                row.remove();
            }
        });
    });
}

function staffSoundAlertsEnabled() {
    return soundAlertsEnabled();
}

function communicationAlertStatusNodes() {
    return document.querySelectorAll('#portal-browser-alerts-status, .js-communication-alerts-status');
}

function refreshCommunicationAlertStatus() {
    const on = soundAlertsEnabled();
    const nodes = communicationAlertStatusNodes();
    let text = 'Sound is off. Click to turn it on. This applies to every tab.';

    if (on) {
        if (typeof Notification !== 'undefined' && Notification.permission === 'denied') {
            text = 'Sound is on in every tab until you turn it off. Browser pop-ups are blocked — sound still plays.';
        } else if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
            text = window.supportEchoEnabled
                ? 'Sound is on in every tab until you turn it off. Browser alerts are also on.'
                : 'Sound is on in every tab until you turn it off. Live beeps need Pusher.';
        } else {
            text = 'Sound is on in every tab until you turn it off.';
        }
    }

    nodes.forEach((status) => {
        status.textContent = text;
    });

    document.querySelectorAll('[data-sound-toggle]').forEach((button) => {
        button.textContent = on ? 'Turn sound off' : 'Turn sound on';
    });
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
    ensureSoundAlertsDefaultOn();
    unlockSupportNotificationAudio();

    if (soundAlertsEnabled() && soundAudioReady()) {
        persistSoundAlertsEnabled(false);
        refreshCommunicationAlertStatus();
        return Promise.resolve('off');
    }

    persistSoundAlertsEnabled(true);

    const apply = (permission) => {
        refreshCommunicationAlertStatus();
        window.playSupportNotificationSound();

        if (permission === 'granted' && typeof Notification !== 'undefined') {
            new Notification('NGN Motors alerts enabled', {
                body: 'You will hear a sound when a notification is sent or received.',
            });
        }

        return permission;
    };

    if (typeof Notification === 'undefined') {
        return Promise.resolve(apply('unsupported'));
    }

    if (Notification.permission === 'granted') {
        return Promise.resolve(apply('granted'));
    }

    if (Notification.permission === 'denied') {
        return Promise.resolve(apply('denied'));
    }

    return Notification.requestPermission().then(apply).catch(() => apply('denied'));
};

document.addEventListener('click', function (event) {
    const button = event.target.closest('#enable-communication-alerts, .js-enable-communication-alerts');
    if (!button) {
        return;
    }
    event.preventDefault();
    window.enableCustomerCommunicationAlerts();
});

try {
    window.__ngnSoundChannel = new BroadcastChannel('ngn-communication-alerts');
    window.__ngnSoundChannel.onmessage = function () {
        refreshCommunicationAlertStatus();
        if (soundAlertsEnabled()) {
            unlockSupportNotificationAudio();
        }
    };
} catch (error) {}

window.addEventListener('storage', function (event) {
    if (event.key !== NGN_SOUND_KEY) {
        return;
    }
    refreshCommunicationAlertStatus();
    if (soundAlertsEnabled()) {
        unlockSupportNotificationAudio();
    }
});

function bindCommunicationAlertControls() {
    ensureSoundAlertsDefaultOn();
    refreshCommunicationAlertStatus();
}

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
        if (typeof window.refreshPortalNotificationsLive === 'function') {
            window.refreshPortalNotificationsLive();
        }
        if (window.Livewire && payload && payload.uuid) {
            window.Livewire.dispatch('customerCommunicationCreated', { uuid: payload.uuid });
        }
    });
    channel.listen('.communication.reply', (payload) => {
        window.playSupportNotificationSound();
        if (typeof window.refreshPortalNotificationsLive === 'function') {
            window.refreshPortalNotificationsLive();
        }
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
        if (staffSoundAlertsEnabled()) {
            window.playSupportNotificationSound();
        }
        if (staffSoundAlertsEnabled() && typeof Notification !== 'undefined' && Notification.permission === 'granted') {
            const notification = new Notification((payload && payload.title) || 'NGN Motors', {
                body: readableCommunicationPreview((payload && (payload.preview || payload.subject)) || '', 'New customer notification'),
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
            window.Livewire.dispatch('staffCommunicationCreated', (payload && payload.uuid) ? { uuid: payload.uuid } : {});
        }
        if (typeof window.refreshStaffUnreadBadges === 'function') {
            window.refreshStaffUnreadBadges();
            window.setTimeout(window.refreshStaffUnreadBadges, 400);
        }
    });
    channel.listen('.communication.reply', (payload) => {
        if (staffSoundAlertsEnabled()) {
            window.playSupportNotificationSound();
        }
        if (window.Livewire) {
            window.Livewire.dispatch('staffCommunicationReply', (payload && payload.uuid) ? { uuid: payload.uuid } : {});
        }
        if (typeof window.refreshStaffUnreadBadges === 'function') {
            window.refreshStaffUnreadBadges();
            window.setTimeout(window.refreshStaffUnreadBadges, 400);
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

    state.pollTimer = window.setInterval(syncFromServer, 5000);
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
    window.__supportStaffEchoOnIncoming = null;
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

function applyStaffUnreadBadge(selector, count) {
    const n = Math.max(0, parseInt(count, 10) || 0);
    document.querySelectorAll(selector).forEach((el) => {
        el.textContent = n > 99 ? '99+' : String(n);
        el.setAttribute('data-count', String(n));
        el.classList.toggle('hidden', n <= 0);
        el.hidden = n <= 0;
    });
}

window.refreshStaffUnreadBadges = function refreshStaffUnreadBadges() {
    const url = document.body.getAttribute('data-staff-unread-url');
    if (!url || document.hidden || window.__staffUnreadBadgeBusy) {
        return;
    }

    window.__staffUnreadBadgeBusy = true;
    fetch(`${url}${url.includes('?') ? '&' : '?'}_cb=${Date.now()}`, {
        cache: 'no-store',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then((response) => (response.ok ? response.json() : null))
        .then((data) => {
            if (!data) {
                return;
            }
            applyStaffUnreadBadge('.js-staff-inbox-unread', data.inbox);
            applyStaffUnreadBadge('.js-staff-notifications-unread', data.notifications);
        })
        .catch(() => {})
        .finally(() => {
            window.__staffUnreadBadgeBusy = false;
        });
};

window.bindStaffUnreadBadgePoll = function bindStaffUnreadBadgePoll() {
    if (window.__staffUnreadBadgeTimer) {
        window.clearInterval(window.__staffUnreadBadgeTimer);
        window.__staffUnreadBadgeTimer = null;
    }
    if (!document.body.getAttribute('data-staff-unread-url')) {
        return;
    }
    window.refreshStaffUnreadBadges();
    window.__staffUnreadBadgeTimer = window.setInterval(window.refreshStaffUnreadBadges, 8000);
    if (!window.__staffUnreadBadgeVisibilityBound) {
        window.__staffUnreadBadgeVisibilityBound = true;
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                window.refreshStaffUnreadBadges();
            }
        });
    }
};

window.refreshPortalNotificationsLive = function refreshPortalNotificationsLive() {
    const url = document.body.getAttribute('data-notifications-live-url');
    if (!url || document.hidden || window.__portalNotificationsBusy) {
        return;
    }

    window.__portalNotificationsBusy = true;
    fetch(`${url}${url.includes('?') ? '&' : '?'}_cb=${Date.now()}`, {
        cache: 'no-store',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then((response) => (response.ok ? response.json() : null))
        .then((data) => {
            if (!data) {
                return;
            }
            const items = Array.isArray(data.items) ? data.items : [];
            const fingerprint = `${data.unread || 0}:${items.map((item) => `${item.uuid}:${item.unread ? 1 : 0}`).join(',')}`;
            if (window.__portalNotificationsFingerprint === fingerprint) {
                return;
            }
            const wasReady = typeof window.__portalNotificationsFingerprint === 'string';
            const unreadGrew = wasReady && (parseInt(data.unread, 10) || 0) > (parseInt(String(window.__portalNotificationsFingerprint).split(':')[0], 10) || 0);
            window.__portalNotificationsFingerprint = fingerprint;
            applyPortalNotificationsBadge(data.unread);
            renderCustomerNotificationMenu(items);
            if (unreadGrew && !window.supportEchoEnabled && typeof window.playSupportNotificationSound === 'function') {
                window.playSupportNotificationSound();
            }
        })
        .catch(() => {})
        .finally(() => {
            window.__portalNotificationsBusy = false;
        });
};

window.bindPortalNotificationsLive = function bindPortalNotificationsLive() {
    if (!document.body.getAttribute('data-notifications-live-url')) {
        if (window.__portalNotificationsTimer) {
            window.clearInterval(window.__portalNotificationsTimer);
            window.__portalNotificationsTimer = null;
        }
        return;
    }

    window.refreshPortalNotificationsLive();

    if (window.__portalNotificationsTimer) {
        return;
    }

    const intervalMs = window.supportEchoEnabled ? 30000 : 8000;
    window.__portalNotificationsTimer = window.setInterval(window.refreshPortalNotificationsLive, intervalMs);

    if (!window.__portalNotificationsVisibilityBound) {
        window.__portalNotificationsVisibilityBound = true;
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                window.refreshPortalNotificationsLive();
            }
        });
    }
};

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
    if (document.body.getAttribute('data-staff-unread-url') && typeof window.setupSupportStaffEcho === 'function') {
        window.setupSupportStaffEcho();
    }
    if (typeof window.refreshStaffUnreadBadges === 'function') {
        window.refreshStaffUnreadBadges();
    }
    if (typeof window.bindStaffUnreadBadgePoll === 'function') {
        window.bindStaffUnreadBadgePoll();
    }
    if (typeof window.bindPortalNotificationsLive === 'function') {
        window.bindPortalNotificationsLive();
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
    window.Livewire.on('staffUnreadBadgesChanged', function () {
        if (typeof window.refreshStaffUnreadBadges === 'function') {
            window.refreshStaffUnreadBadges();
            window.setTimeout(window.refreshStaffUnreadBadges, 400);
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
    if (document.body.getAttribute('data-staff-unread-url') && typeof window.setupSupportStaffEcho === 'function') {
        window.setupSupportStaffEcho();
    }
    if (typeof window.refreshStaffUnreadBadges === 'function') {
        window.refreshStaffUnreadBadges();
    }
    if (typeof window.bindStaffUnreadBadgePoll === 'function') {
        window.bindStaffUnreadBadgePoll();
    }
    if (typeof window.bindPortalNotificationsLive === 'function') {
        window.bindPortalNotificationsLive();
    }
    bindCommunicationAlertControls();
});
