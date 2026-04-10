@extends(backpack_view('blank'))

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none">
        <h1 class="text-capitalize mb-0">Conversations Inbox</h1>
        <p class="ms-2 ml-2 mb-0">Unified customer and enquiry conversations</p>
    </section>

    <section class="content container-fluid animated fadeIn">
        <div class="row g-3 mt-2">
            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <form id="conversation-filters" class="row g-2 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold mb-1">Search</label>
                                <input id="filter-search" type="text" class="form-control form-control-sm" placeholder="Customer, email, enquiry, UUID">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold mb-1">Type</label>
                                <select id="filter-type" class="form-select form-select-sm">
                                    <option value="all">All</option>
                                    <option value="general">General</option>
                                    <option value="enquiry">Enquiry</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold mb-1">Status</label>
                                <select id="filter-status" class="form-select form-select-sm">
                                    <option value="all">All</option>
                                    <option value="open">Open</option>
                                    <option value="waiting_for_staff">Waiting for staff</option>
                                    <option value="waiting_for_customer">Waiting for customer</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold mb-1">Assignment</label>
                                <select id="filter-assignment" class="form-select form-select-sm">
                                    <option value="all">All</option>
                                    <option value="mine">Assigned to me</option>
                                    <option value="unassigned">Unassigned</option>
                                    @foreach($staffUsers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold mb-1">Enquiry ID</label>
                                <input id="filter-enquiry-id" type="number" class="form-control form-control-sm" placeholder="Optional">
                            </div>
                        </form>

                        <div id="conversation-list" class="support-conversation-list">
                            <div class="text-muted small">Loading conversations...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-8">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column support-thread-wrap">
                        <div id="thread-header" class="border-bottom pb-2 mb-2 text-muted">
                            Select a conversation to load full history.
                        </div>

                        <div id="thread-messages" class="flex-grow-1 support-thread-messages">
                            <div class="text-muted small">No conversation selected.</div>
                        </div>

                        <div class="border-top pt-3 mt-2">
                            <div class="row g-2 mb-2">
                                <div class="col-md-5">
                                    <label class="form-label fw-bold mb-1">Status</label>
                                    <select id="thread-status" class="form-select form-select-sm" disabled>
                                        <option value="open">Open</option>
                                        <option value="waiting_for_staff">Waiting for staff</option>
                                        <option value="waiting_for_customer">Waiting for customer</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold mb-1">Assignee</label>
                                    <select id="thread-assignee" class="form-select form-select-sm" disabled>
                                        <option value="">Unassigned</option>
                                        @foreach($staffUsers as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button id="thread-save-meta" class="btn btn-outline-primary btn-sm w-100" disabled>Update</button>
                                </div>
                            </div>

                            <label class="form-label fw-bold mb-1">Reply</label>
                            <textarea id="thread-reply" class="form-control" rows="3" maxlength="6000" placeholder="Type your message"></textarea>
                            <div class="d-flex justify-content-end mt-2">
                                <button id="thread-send" class="btn btn-primary btn-sm" disabled>Send message</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('after_styles')
<style>
    .support-conversation-list {
        max-height: 70vh;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
    }
    .support-conversation-item {
        border-bottom: 1px solid #e5e7eb;
        padding: 10px;
        cursor: pointer;
    }
    .support-conversation-item.active {
        background: #f3f4f6;
    }
    .support-thread-wrap {
        min-height: 72vh;
    }
    .support-thread-messages {
        overflow-y: auto;
        max-height: 52vh;
        padding-right: 4px;
    }
    .support-bubble {
        max-width: 84%;
        padding: 8px 10px;
        margin-bottom: 10px;
        border: 1px solid #d1d5db;
        background: #fff;
    }
    .support-bubble.staff {
        margin-left: auto;
        border-color: #1d4ed8;
        background: #eff6ff;
    }
    .support-bubble.customer {
        margin-right: auto;
        border-color: #16a34a;
        background: #f0fdf4;
    }
    .support-meta {
        font-size: 11px;
        color: #6b7280;
    }
</style>
@endpush

@push('after_scripts')
<script>
(() => {
    const urls = {
        list: @json(route('page.support_inbox.conversations')),
        showBase: @json(url(backpack_url('support-inbox/conversations'))),
        sendBase: @json(url(backpack_url('support-inbox/conversations'))),
        updateBase: @json(url(backpack_url('support-inbox/conversations'))),
    };

    const csrf = @json(csrf_token());
    const els = {
        list: document.getElementById('conversation-list'),
        header: document.getElementById('thread-header'),
        messages: document.getElementById('thread-messages'),
        search: document.getElementById('filter-search'),
        type: document.getElementById('filter-type'),
        status: document.getElementById('filter-status'),
        assignment: document.getElementById('filter-assignment'),
        enquiryId: document.getElementById('filter-enquiry-id'),
        reply: document.getElementById('thread-reply'),
        send: document.getElementById('thread-send'),
        threadStatus: document.getElementById('thread-status'),
        threadAssignee: document.getElementById('thread-assignee'),
        threadSaveMeta: document.getElementById('thread-save-meta'),
    };

    let selectedConversationId = null;
    let selectedConversationUuid = null;
    let refreshTimer = null;
    let detachEcho = null;

    const escapeHtml = (unsafe) => {
        return String(unsafe ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const buildQueryString = () => {
        const params = new URLSearchParams();
        params.set('search', els.search.value.trim());
        params.set('type', els.type.value);
        params.set('status', els.status.value);
        params.set('assignment', els.assignment.value);
        if (els.enquiryId.value) {
            params.set('enquiry_id', els.enquiryId.value);
        }
        return params.toString();
    };

    const setThreadEnabled = (enabled) => {
        els.send.disabled = !enabled;
        els.reply.disabled = !enabled;
        els.threadStatus.disabled = !enabled;
        els.threadAssignee.disabled = !enabled;
        els.threadSaveMeta.disabled = !enabled;
    };

    const renderConversationList = (items) => {
        if (!items.length) {
            els.list.innerHTML = '<div class="p-2 text-muted small">No conversations found.</div>';
            return;
        }

        els.list.innerHTML = items.map((item) => {
            const unread = item.unread_count > 0 ? `<span class="badge bg-danger ms-1">${item.unread_count}</span>` : '';
            const enquiry = item.enquiry ? ` · Enquiry #${item.enquiry.id}` : '';
            const preview = item.last_message_preview ? escapeHtml(item.last_message_preview) : 'No messages yet';
            const assignee = item.assignee ? ` · ${escapeHtml(item.assignee.name)}` : ' · Unassigned';
            return `
                <div class="support-conversation-item ${selectedConversationId === item.id ? 'active' : ''}" data-id="${item.id}" data-uuid="${item.uuid}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="fw-bold">${escapeHtml(item.customer?.name ?? 'Unknown customer')} ${unread}</div>
                        <small class="text-muted">${escapeHtml(item.last_message_human ?? '')}</small>
                    </div>
                    <div class="small text-muted">${escapeHtml(item.type)} · ${escapeHtml(item.status)}${enquiry}${assignee}</div>
                    <div class="small mt-1">${preview}</div>
                </div>
            `;
        }).join('');

        els.list.querySelectorAll('.support-conversation-item').forEach((row) => {
            row.addEventListener('click', () => {
                loadConversation(Number(row.dataset.id), row.dataset.uuid);
            });
        });
    };

    const renderMessages = (messages) => {
        if (!messages.length) {
            els.messages.innerHTML = '<div class="text-muted small">No messages yet.</div>';
            return;
        }

        els.messages.innerHTML = messages.map((message) => {
            const kind = message.sender_type === 'staff' ? 'staff' : (message.sender_type === 'customer' ? 'customer' : 'system');
            const attachments = (message.attachments || []).map((attachment) =>
                `<div><a href="${attachment.url}" target="_blank" rel="noopener">${escapeHtml(attachment.name)}</a></div>`
            ).join('');

            return `
                <div class="support-bubble ${kind}">
                    <div class="support-meta">${escapeHtml(message.sender_type)} · ${escapeHtml(message.created_human ?? '')}</div>
                    <div class="mt-1">${escapeHtml(message.body || '')}</div>
                    ${attachments ? `<div class="mt-1">${attachments}</div>` : ''}
                </div>
            `;
        }).join('');

        els.messages.scrollTop = els.messages.scrollHeight;
    };

    const loadConversations = async () => {
        try {
            const response = await fetch(`${urls.list}?${buildQueryString()}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) {
                els.list.innerHTML = `<div class="p-2 text-danger small">Unable to load conversations (${response.status}).</div>`;
                return;
            }
            const payload = await response.json();
            renderConversationList(payload.items || []);
        } catch (error) {
            els.list.innerHTML = '<div class="p-2 text-danger small">Conversation list failed to load. Refresh and try again.</div>';
            console.error('support inbox: list load failed', error);
        }
    };

    const loadConversation = async (conversationId, conversationUuid) => {
        const response = await fetch(`${urls.showBase}/${conversationId}`, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        if (!response.ok) {
            els.header.innerHTML = `<div class="text-danger">Unable to open conversation (${response.status}).</div>`;
            return;
        }

        selectedConversationId = conversationId;
        selectedConversationUuid = conversationUuid;

        const payload = await response.json();
        const conversation = payload.conversation;
        const enquiryText = conversation.enquiry ? ` · Enquiry #${conversation.enquiry.id}` : '';
        const customerName = conversation.customer?.name || 'Unknown customer';
        const customerEmail = conversation.customer?.email ? ` (${conversation.customer.email})` : '';
        els.header.innerHTML = `
            <div class="fw-bold">${escapeHtml(customerName)}${escapeHtml(customerEmail)}</div>
            <div class="small text-muted">${escapeHtml(conversation.type)} · ${escapeHtml(conversation.status)}${enquiryText}</div>
        `;

        els.threadStatus.value = conversation.status;
        els.threadAssignee.value = conversation.assignee_id || '';
        renderMessages(payload.messages || []);
        setThreadEnabled(true);
        await loadConversations();
        subscribeRealtime(conversationUuid);
    };

    const sendMessage = async () => {
        const body = els.reply.value.trim();
        if (!selectedConversationId || body === '') {
            return;
        }

        els.send.disabled = true;
        try {
            const response = await fetch(`${urls.sendBase}/${selectedConversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ body }),
            });

            if (response.ok) {
                els.reply.value = '';
                await loadConversation(selectedConversationId, selectedConversationUuid);
            } else {
                alert(`Message failed to send (${response.status}).`);
            }
        } finally {
            els.send.disabled = false;
        }
    };

    const updateConversationMeta = async () => {
        if (!selectedConversationId) {
            return;
        }

        await fetch(`${urls.updateBase}/${selectedConversationId}`, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                status: els.threadStatus.value,
                assigned_backpack_user_id: els.threadAssignee.value || null,
            }),
        });

        await loadConversation(selectedConversationId, selectedConversationUuid);
    };

    const subscribeRealtime = (conversationUuid) => {
        if (detachEcho) {
            detachEcho();
            detachEcho = null;
        }

        if (window.setupSupportConversationEcho && conversationUuid) {
            detachEcho = window.setupSupportConversationEcho(conversationUuid, () => {
                if (selectedConversationId) {
                    loadConversation(selectedConversationId, selectedConversationUuid);
                }
            });
        }
    };

    const scheduleRefresh = () => {
        if (refreshTimer) {
            clearInterval(refreshTimer);
        }
        refreshTimer = setInterval(async () => {
            await loadConversations();
            if (selectedConversationId) {
                await loadConversation(selectedConversationId, selectedConversationUuid);
            }
        }, 4000);
    };

    const wireFilters = () => {
        const debouncedReload = (() => {
            let timer = null;
            return () => {
                if (timer) {
                    clearTimeout(timer);
                }
                timer = setTimeout(() => loadConversations(), 300);
            };
        })();

        [els.search, els.type, els.status, els.assignment, els.enquiryId].forEach((el) => {
            el.addEventListener('input', debouncedReload);
            el.addEventListener('change', debouncedReload);
        });
    };

    els.send.addEventListener('click', sendMessage);
    els.reply.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            event.preventDefault();
            sendMessage();
        }
    });
    els.threadSaveMeta.addEventListener('click', updateConversationMeta);

    setThreadEnabled(false);
    wireFilters();
    loadConversations();
    scheduleRefresh();
})();
</script>
@endpush

