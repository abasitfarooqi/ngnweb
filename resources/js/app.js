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

// ngnSetColourMode: see resources/views/components/partials/theme-api.blade.php (loaded after @fluxAppearance).

document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.bindSupportThreadRealtime === 'function') {
        window.bindSupportThreadRealtime();
    }
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
});

if (!hadAlpineAlready) {
    AlpineRuntime.start();
}
