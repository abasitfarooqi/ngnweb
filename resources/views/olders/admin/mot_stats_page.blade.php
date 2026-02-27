@extends(backpack_view('blank'))

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none">
        <h1 class="text-capitalize mb-0">MOT Stats Page</h1>
        <p class="ms-2 ml-2 mb-0">Page for M O T Stats Page</p>
    </section>

    <section class="content container-fluid animated fadeIn">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">MOT Notification List</h5>

                        <!-- 🔍 Filters + Search -->
                        <form method="GET" action="{{ route('page.mot_stats_page.index') }}" class="row g-2 mb-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">WhatsApp Sent</label>
                                <select name="whatsapp_filter" class="form-select">
                                    <option value="">All</option>
                                    <option value="1" {{ request('whatsapp_filter') === '1' ? 'selected' : '' }}>Sent</option>
                                    <option value="0" {{ request('whatsapp_filter') === '0' ? 'selected' : '' }}>Not Sent</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-bold">MOT Status</label>
                                <select name="mot_status_filter" class="form-select">
                                    <option value="">All</option>
                                    <option value="expired" {{ request('mot_status_filter') === 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="valid" {{ request('mot_status_filter') === 'valid' ? 'selected' : '' }}>Valid</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Search (Name / Phone / Reg)</label>
                                <input type="text" name="search" class="form-control" placeholder="e.g. John, 0795, AB12CDE"
                                       value="{{ request('search') }}">
                            </div>

                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Registration Number</th>
                                        <th>Customer Phone</th>
                                        <th>Customer Email</th>
                                        <th>MOT Due Date</th>
                                        <th>Tax Due Date</th>
                                        <th>Insurance Due Date</th>
                                        <th>MOT Status</th>
                                        <th>WhatsApp Sent</th>
                                        <th>Last WhatsApp Notification</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($motList as $mot)
                                        <tr data-id="{{ $mot['id'] }}">
                                            <td contenteditable="false" class="editable" data-field="customer_name">
                                                {{ $mot['customer_name'] }}
                                            </td>
                                            <td contenteditable="false" class="editable" data-field="motorbike_reg">
                                                {{ $mot['reg_no'] }}
                                            </td>
                                            <td contenteditable="false" class="editable" data-field="customer_contact">
                                                {{ $mot['customer_contact'] }}
                                            </td>
                                            <td contenteditable="false" class="editable" data-field="customer_email">
                                                {{ $mot['customer_email'] }}
                                            </td>
                                            <td>{{ $mot['mot_due_date'] }}</td>
                                            <td>{{ $mot['tax_due_date'] }}</td>
                                            <td>{{ $mot['insurance_due_date'] }}</td>
                                            <td>{{ $mot['mot_status'] }}</td>
                                            <td>{{ $mot['is_whatsapp_sent'] ? 'Yes' : 'No' }}</td>
                                            <td>{{ $mot['mot_last_whatsapp_notification_date'] }}</td>
                                            <td>
                                                <form action="{{ route('mot-stats-page.send-reminder', $mot['id']) }}"
                                                      method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <a href="{{ $mot['whatsapp_url'] }}" target="_blank"
                                                       class="btn btn-success btn-sm"
                                                       onclick="event.preventDefault(); this.closest('form').submit(); window.open('{{ $mot['whatsapp_url'] }}', '_blank');">
                                                        Send WhatsApp
                                                    </a>
                                                </form>
                                                <button class="btn btn-secondary btn-sm edit-btn">Edit</button>
                                                <button class="btn btn-primary btn-sm save-btn" disabled>Save</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-centre text-muted">No records found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('after_styles')
<style>
    .editable.editing {
        background-colour: #f5f7d7 !important;
    }
</style>
@endpush

@push('after_scripts')
<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        let row = this.closest('tr');
        row.querySelectorAll('.editable').forEach(td => {
            td.setAttribute('contenteditable', 'true');
            td.classList.add('editing');
        });
        row.querySelector('.save-btn').disabled = false;
        this.disabled = true;
    });
});

document.querySelectorAll('.save-btn').forEach(button => {
    button.addEventListener('click', function() {
        let row = this.closest('tr');
        let id = row.dataset.id;
        let formData = {
            _token: '{{ csrf_token() }}',
            customer_name: row.querySelector('[data-field="customer_name"]').innerText.trim(),
            motorbike_reg: row.querySelector('[data-field="motorbike_reg"]').innerText.trim(),
            customer_contact: row.querySelector('[data-field="customer_contact"]').innerText.trim(),
            customer_email: row.querySelector('[data-field="customer_email"]').innerText.trim()
        };

        fetch(`/ngn-admin/mot-stats-page/update/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                row.querySelectorAll('.editable').forEach(td => {
                    td.setAttribute('contenteditable', 'false');
                    td.classList.remove('editing');
                });
                row.querySelector('.edit-btn').disabled = false;
                button.disabled = true;
            } else {
                alert('❌ Update failed');
            }
        })
        .catch(err => console.error(err));
    });
});
</script>
@endpush
