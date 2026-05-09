<a
    href="javascript:void(0)"
    class="dropdown-item js-make-available-btn"
    data-preview-url="{{ route('admin.motorbike.make-available.preview', ['motorbikeId' => $entry->getKey()]) }}"
    data-execute-url="{{ route('admin.motorbike.make-available.execute', ['motorbikeId' => $entry->getKey()]) }}"
    data-reg-no="{{ $entry->reg_no }}"
    onclick="(async function(button){
        try {
            const parseJsonSafe = async function (response) {
                const raw = await response.text();
                try { return JSON.parse(raw); } catch (e) { throw new Error(raw || ('Request failed with status ' + response.status)); }
            };
            const regNo = button.dataset.regNo || 'Unknown';
            const previewResponse = await fetch(button.dataset.previewUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' });
            const preview = await parseJsonSafe(previewResponse);
            if (!previewResponse.ok || !preview.success) throw new Error(preview.message || 'Failed to load preview.');
            const lines = [];
            lines.push('Bike: ' + regNo + ' (#' + preview.motorbike_id + ')');
            lines.push('Open posted items: ' + preview.preview.open_posted_items_count);
            (preview.preview.items || []).forEach(function (item) { lines.push('- Item #' + item.item_id + ', Booking #' + item.booking_id + ', booking state: ' + item.booking_state + ', item start: ' + (item.start_date || '-')); });
            lines.push('Current pricing: ' + (preview.checks.has_current_pricing ? 'yes' : 'no'));
            lines.push('Registration row: ' + (preview.checks.has_registration ? 'yes' : 'no'));
            lines.push('Compliance pass: ' + (preview.checks.compliance_pass ? 'yes' : 'no'));
            lines.push('');
            lines.push('This will force-end active rental linkage for this bike. Continue?');
            if (!window.confirm(lines.join('\n'))) return;
            const tokenMeta = document.querySelector('meta[name=\'csrf-token\']');
            const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';
            const executeResponse = await fetch(button.dataset.executeUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ confirm_force: true }) });
            const executed = await parseJsonSafe(executeResponse);
            if (!executeResponse.ok || !executed.success) throw new Error((executed.message || 'Failed to make bike available.') + (executed.error ? ('\n' + executed.error) : ''));
            alert(executed.message);
            window.location.reload();
        } catch (error) {
            alert(error.message || 'Unable to complete make available action.');
        }
    })(this); return false;"
>
    <i class="la la-unlock"></i> Make Available
</a>
