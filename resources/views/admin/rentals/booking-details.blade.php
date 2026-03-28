@extends(backpack_view('blank'))

@php
    $primaryItem = $booking->rentingBookingItems->first();
    $primaryMotorbike = $primaryItem?->motorbike;
@endphp

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none">
        <h1 class="text-capitalize mb-0">Booking Details</h1>
        <p class="ms-2 ml-2 mb-0">Booking #{{ $booking->id }} operational view</p>
    </section>

    <section class="content container-fluid animated fadeIn">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Booking Status</div>
                        <div class="fs-4 fw-bold">{{ $booking->state }}</div>
                        <div class="small text-muted mt-2">Deposit: GBP {{ number_format((float) $booking->deposit, 2) }}</div>
                        <div class="small text-muted">Start: {{ optional($booking->start_date)->format('d M Y H:i') }}</div>
                        <div class="small text-muted">Due: {{ optional($booking->due_date)->format('d M Y H:i') }}</div>
                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <a href="{{ route('page.rental_operations.bookings_management') }}" class="btn btn-sm btn-outline-primary">Open Legacy Management</a>
                            <a href="{{ route('page.rental_operations.booking_invoice_dates') }}" class="btn btn-sm btn-outline-secondary">Invoice Dates</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Customer</div>
                        <div class="fs-5 fw-bold">{{ $booking->customer?->full_name ?: 'N/A' }}</div>
                        <div class="small text-muted">{{ $booking->customer?->phone ?: 'No phone' }}</div>
                        <div class="small text-muted">{{ $booking->customer?->email ?: 'No email' }}</div>
                        <div class="small mt-2">
                            Portal auth:
                            <strong class="{{ $customerAuth ? 'text-success' : 'text-warning' }}">{{ $customerAuth ? 'Linked' : 'Missing' }}</strong>
                        </div>
                        <div class="small">
                            Profile:
                            <strong class="{{ $customerProfile ? 'text-success' : 'text-warning' }}">{{ $customerProfile ? 'Ready' : 'Missing' }}</strong>
                        </div>
                        <div class="small">
                            Addresses:
                            <strong>{{ $customerAddresses->count() }}</strong>
                        </div>
                        @if ($booking->customer_id)
                            <a href="{{ backpack_url('customer/' . $booking->customer_id . '/show') }}" class="btn btn-sm btn-outline-secondary mt-3">Open Customer</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small">Summary</div>
                        <div class="small">Paid invoices: <strong>{{ $summary['paid_invoice_count'] ?? 0 }}</strong></div>
                        <div class="small">Total income: <strong>GBP {{ number_format((float) ($summary['total_income'] ?? 0), 2) }}</strong></div>
                        <div class="small">Total cost: <strong>GBP {{ number_format((float) ($summary['total_cost'] ?? 0), 2) }}</strong></div>
                        <div class="small">Net profit: <strong>GBP {{ number_format((float) ($summary['net_profit'] ?? 0), 2) }}</strong></div>
                        <div class="small">Reg no: <strong>{{ $summary['reg_no'] ?? 'N/A' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-bg-success">&nbsp;</span>
                            <span>ALL COMPLIANCE OBLIGATIONS COMPLIED WITH & GOOD TO GO</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-bg-danger">&nbsp;</span>
                            <span>STOP AS COMPLIANCE ARE NOT YET FINISHED</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-bg-primary">&nbsp;</span>
                            <span>ISSUED TO CUSTOMER</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#booking-view" type="button">BOOKING VIEW</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents" type="button">DOCUMENTS</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#payments" type="button">PAYMENTS</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#issuance" type="button">ISSUANCE</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#charges" type="button">CHARGES</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#closing" type="button">CLOSING</button></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="booking-view">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Item ID</th>
                                        <th>Motorbike</th>
                                        <th>Weekly Rent</th>
                                        <th>Start</th>
                                        <th>Due</th>
                                        <th>End</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($booking->rentingBookingItems as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->motorbike?->reg_no }} - {{ $item->motorbike?->make }} {{ $item->motorbike?->model }}</td>
                                            <td>GBP {{ number_format((float) $item->weekly_rent, 2) }}</td>
                                            <td>{{ optional($item->start_date)->format('d M Y') }}</td>
                                            <td>{{ optional($item->due_date)->format('d M Y') }}</td>
                                            <td>{{ optional($item->end_date)->format('d M Y') ?: 'Active' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="documents">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-primary" id="btnResendDocLink">Resend Document Link</button>
                            <button type="button" class="btn btn-outline-success" id="btnDocumentsComplete">Documents Completed</button>
                            <button type="button" class="btn btn-outline-secondary" id="btnRefreshDocuments">Refresh Documents</button>
                        </div>
                        <div class="alert alert-light border mb-3">
                            <div class="fw-semibold">Customer document sync</div>
                            <div class="small text-muted">
                                Rental and finance requirements are shown from the document-type setup, while uploads, verification state, and file links come from the linked customer records.
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="card h-100 border">
                                    <div class="card-body">
                                        <h5 class="mb-1">Rental Documents</h5>
                                        <div class="small text-muted mb-3">Upload, replace, review, and verify rental-related documents.</div>
                                        <div class="d-grid gap-3">
                                            @forelse ($rentalDocumentTypes as $docType)
                                                @php
                                                    $uploaded = $uploadedDocumentsByType[$docType->id] ?? null;
                                                    $status = $uploaded
                                                        ? ($uploaded->status ?: ($uploaded->is_verified ? 'approved' : 'pending_review'))
                                                        : 'missing';
                                                    $statusClass = match ($status) {
                                                        'approved' => 'text-bg-success',
                                                        'rejected' => 'text-bg-danger',
                                                        'pending_review' => 'text-bg-warning',
                                                        default => 'text-bg-secondary',
                                                    };
                                                    $statusLabel = match ($status) {
                                                        'approved' => 'Approved',
                                                        'rejected' => 'Rejected',
                                                        'pending_review' => 'Under Review',
                                                        default => 'Missing',
                                                    };
                                                    $uploadLabel = $uploaded ? 'Replace' : 'Upload';
                                                    $verifyMode = ($docType->code ?? null) === 'rental_agreement' ? 'agreement' : 'standard';
                                                @endphp
                                                <div class="border rounded p-3">
                                                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                                        <div>
                                                            <div class="fw-semibold">{{ $docType->name }}</div>
                                                            @if ($docType->description)
                                                                <div class="small text-muted mt-1">{{ $docType->description }}</div>
                                                            @endif
                                                            @if ($uploaded)
                                                                <div class="small text-muted mt-2">
                                                                    Uploaded {{ optional($uploaded->created_at)->format('d M Y') ?: 'recently' }}
                                                                    @if ($uploaded->valid_until)
                                                                        · Expires {{ \Carbon\Carbon::parse($uploaded->valid_until)->format('d M Y') }}
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                                    </div>
                                                    <div class="small mt-2 {{ $uploaded?->file_name ? 'text-body' : 'text-muted' }}">
                                                        {{ $uploaded?->file_name ?: 'No file uploaded yet.' }}
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                                        @if ($uploaded?->file_url)
                                                            <a href="{{ $uploaded->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                                        @endif
                                                        <label class="btn btn-sm btn-outline-secondary mb-0">
                                                            {{ $uploadLabel }}
                                                            <input type="file" class="d-none input-document-upload" data-document-type-code="{{ $docType->code }}">
                                                        </label>
                                                        @if ($uploaded && ! $uploaded->is_verified)
                                                            <button type="button" class="btn btn-sm btn-outline-success btn-verify-document" data-document-id="{{ $uploaded->id }}" data-mode="{{ $verifyMode }}">Verify</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-muted">No rental document requirements are configured yet.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card h-100 border">
                                    <div class="card-body">
                                        <h5 class="mb-1">Finance Documents</h5>
                                        <div class="small text-muted mb-3">Upload and review finance application documents from the same admin page.</div>
                                        <div class="d-grid gap-3">
                                            @forelse ($financeDocumentTypes as $docType)
                                                @php
                                                    $uploaded = $uploadedDocumentsByType[$docType->id] ?? null;
                                                    $status = $uploaded
                                                        ? ($uploaded->status ?: ($uploaded->is_verified ? 'approved' : 'pending_review'))
                                                        : 'missing';
                                                    $statusClass = match ($status) {
                                                        'approved' => 'text-bg-success',
                                                        'rejected' => 'text-bg-danger',
                                                        'pending_review' => 'text-bg-warning',
                                                        default => 'text-bg-secondary',
                                                    };
                                                    $statusLabel = match ($status) {
                                                        'approved' => 'Approved',
                                                        'rejected' => 'Rejected',
                                                        'pending_review' => 'Under Review',
                                                        default => 'Missing',
                                                    };
                                                    $uploadLabel = $uploaded ? 'Replace' : 'Upload';
                                                @endphp
                                                <div class="border rounded p-3">
                                                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                                        <div>
                                                            <div class="fw-semibold">{{ $docType->name }}</div>
                                                            @if ($docType->description)
                                                                <div class="small text-muted mt-1">{{ $docType->description }}</div>
                                                            @endif
                                                            @if ($uploaded)
                                                                <div class="small text-muted mt-2">
                                                                    Uploaded {{ optional($uploaded->created_at)->format('d M Y') ?: 'recently' }}
                                                                    @if ($uploaded->valid_until)
                                                                        · Expires {{ \Carbon\Carbon::parse($uploaded->valid_until)->format('d M Y') }}
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                                    </div>
                                                    <div class="small mt-2 {{ $uploaded?->file_name ? 'text-body' : 'text-muted' }}">
                                                        {{ $uploaded?->file_name ?: 'No file uploaded yet.' }}
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                                        @if ($uploaded?->file_url)
                                                            <a href="{{ $uploaded->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                                        @endif
                                                        <label class="btn btn-sm btn-outline-secondary mb-0">
                                                            {{ $uploadLabel }}
                                                            <input type="file" class="d-none input-document-upload" data-document-type-code="{{ $docType->code }}">
                                                        </label>
                                                        @if ($uploaded && ! $uploaded->is_verified)
                                                            <button type="button" class="btn btn-sm btn-outline-success btn-verify-document" data-document-id="{{ $uploaded->id }}" data-mode="standard">Verify</button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-muted">No finance document requirements are configured yet.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="payments">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-outline-success" id="btnReceiveAmount">Receive Amount</button>
                        </div>
                        <div id="payment-heading" class="fw-bold mb-3">PAYMENTS</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-2"><div class="border p-3 text-center"><div class="small text-muted">Deposit</div><div class="fw-bold" id="payment-deposit">GBP 0.00</div></div></div>
                            <div class="col-md-2"><div class="border p-3 text-center"><div class="small text-muted">Weekly</div><div class="fw-bold" id="payment-weekly">GBP 0.00</div></div></div>
                            <div class="col-md-2"><div class="border p-3 text-center"><div class="small text-muted">Total</div><div class="fw-bold" id="payment-total">GBP 0.00</div></div></div>
                            <div class="col-md-2"><div class="border p-3 text-center"><div class="small text-muted">Paid</div><div class="fw-bold" id="payment-paid">GBP 0.00</div></div></div>
                            <div class="col-md-2"><div class="border p-3 text-center"><div class="small text-muted">Balance</div><div class="fw-bold" id="payment-balance">GBP 0.00</div></div></div>
                            <div class="col-md-2"><div class="border p-3 text-center"><div class="small text-muted">Updated</div><div class="fw-bold small" id="payment-last-update">N/A</div></div></div>
                        </div>
                        <div id="payment-invoices"></div>
                    </div>

                    <div class="tab-pane fade" id="issuance">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Issuance Action</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Inspector / Issued By</label>
                                            <input type="text" class="form-control" id="issuance_note">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Current Mileage</label>
                                            <input type="number" class="form-control" id="current_mileage" step="1">
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="is_video_recorded">
                                            <label class="form-check-label" for="is_video_recorded">Video recorded</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="accessories_checked">
                                            <label class="form-check-label" for="accessories_checked">Accessories checked</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="is_insured">
                                            <label class="form-check-label" for="is_insured">Insurance checked on AskMID</label>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label d-block">Issuance Branch</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="issue_from" id="issue_from_catford" value="Catford">
                                                <label class="form-check-label" for="issue_from_catford">Catford</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="issue_from" id="issue_from_tooting" value="Tooting">
                                                <label class="form-check-label" for="issue_from_tooting">Tooting</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="issue_from" id="issue_from_sutton" value="Sutton">
                                                <label class="form-check-label" for="issue_from_sutton">Sutton</label>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mt-4">
                                            <button type="button" class="btn btn-success" id="btnIssueNow">Issue Now</button>
                                            <button type="button" class="btn btn-outline-success" id="btnReissueNow">Inspect & Reissue</button>
                                            <button type="button" class="btn btn-outline-primary" id="btnStartBooking">Start Booking</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Service Videos</h5>
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control" id="video_file" accept="video/*">
                                            <button type="button" class="btn btn-outline-primary" id="btnUploadVideo">Upload Video</button>
                                        </div>
                                        <div id="uploaded-video-link" class="small text-muted mb-3"></div>
                                        <div id="all-uploaded-videos">
                                            @forelse ($serviceVideos as $video)
                                                <div><a href="{{ $video->video_url }}" target="_blank">{{ basename($video->video_path) }}</a></div>
                                            @empty
                                                <div class="text-muted">No videos uploaded for this booking.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="mb-3">Maintenance Logs</h5>
                                        <form id="maintenance-log-form" class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label">Description</label>
                                                <input type="text" class="form-control" id="maintenance_description" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Cost</label>
                                                <input type="number" class="form-control" id="maintenance_cost" min="0" step="0.01" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Serviced At</label>
                                                <input type="datetime-local" class="form-control" id="maintenance_serviced_at" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Note</label>
                                                <input type="text" class="form-control" id="maintenance_note">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" class="btn btn-primary w-100">Add</button>
                                            </div>
                                        </form>
                                        <div class="table-responsive mt-4">
                                            <table class="table table-striped" id="maintenance-log-list-table">
                                                <thead>
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Cost</th>
                                                        <th>Notes</th>
                                                        <th>Date/Time</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div id="maintenance-log-total-cost" class="fw-bold"></div>
                                        <div id="booking-summary" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="charges">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <label class="form-label">Description of Additional Charges</label>
                                <input type="text" id="other-item-desc" class="form-control" placeholder="Enter description">
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">Amount</label>
                                <input type="number" id="other-item-amount" class="form-control" placeholder="Enter amount">
                            </div>
                            <div class="col-lg-5 d-flex align-items-end gap-2">
                                <button id="btn-other-item" class="btn btn-success" type="button">Add Charge</button>
                                <button id="getOtherCharges" class="btn btn-info" type="button">Locate Pending Charges</button>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12" id="load-other-item-data"></div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="closing">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Complete the closing checks in order. Charges, PCN, pending rent, and deposit values are loaded from the live booking data.
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row g-2 align-items-center border p-3">
                                    <div class="col-md-2 fw-bold">1. Notice Period</div>
                                    <div class="col-md-7"><input type="text" id="notice-details" class="form-control" placeholder="Enter details"></div>
                                    <div class="col-md-1"><input type="checkbox" id="notice-checkbox"></div>
                                    <div class="col-md-2"><button id="check-button" class="btn btn-primary w-100" disabled type="button">Check</button></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row g-2 align-items-center border p-3">
                                    <div class="col-md-2 fw-bold">2. Collect Motorbike</div>
                                    <div class="col-md-4"><input type="text" id="collect-details" class="form-control" placeholder="Enter details"></div>
                                    <div class="col-md-2"><input type="date" id="collect-date" class="form-control"></div>
                                    <div class="col-md-2"><input type="time" id="collect-time" class="form-control"></div>
                                    <div class="col-md-1"><input type="checkbox" id="collect-checkbox"></div>
                                    <div class="col-md-1"><button id="collect-button" class="btn btn-primary w-100" disabled type="button">Check</button></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row g-2 align-items-center border p-3">
                                    <div class="col-md-3 fw-bold">3. Additional Cost</div>
                                    <div class="col-md-3"><span id="damages-total-label">TOTAL: GBP 0.00</span><input type="hidden" id="damages-total" value="0"></div>
                                    <div class="col-md-3"><span id="damages-received-label">RECEIVED: GBP 0.00</span><input type="hidden" id="damages-received" value="0"></div>
                                    <div class="col-md-1"><input type="checkbox" id="damages-checkbox"></div>
                                    <div class="col-md-2"><button id="damages-button" class="btn btn-primary w-100" disabled type="button">Check</button></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row g-2 align-items-center border p-3">
                                    <div class="col-md-3 fw-bold">4. PCN</div>
                                    <div class="col-md-3"><span id="pcn-total-label">TOTAL: GBP 0.00</span><input type="hidden" id="pcn-total" value="0"></div>
                                    <div class="col-md-3"><span id="pcn-received-label">RECEIVED: GBP 0.00</span><input type="hidden" id="pcn-received" value="0"></div>
                                    <div class="col-md-1"><input type="checkbox" id="pcn-checkbox"></div>
                                    <div class="col-md-2"><button id="pcn-button" class="btn btn-primary w-100" disabled type="button">Check</button></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row g-2 align-items-center border p-3">
                                    <div class="col-md-3 fw-bold">5. Pending Rent</div>
                                    <div class="col-md-6"><span id="pending-total-label">PENDING TOTAL: GBP 0.00</span><input type="hidden" id="pending-total" value="0"></div>
                                    <div class="col-md-1"><input type="checkbox" id="pending-checkbox"></div>
                                    <div class="col-md-2"><button id="pending-button" class="btn btn-primary w-100" disabled type="button">Check</button></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row g-2 align-items-center border p-3">
                                    <div class="col-md-3 fw-bold">6. Deposit Return</div>
                                    <div class="col-md-6"><span id="total-deposit">TOTAL DEPOSIT: GBP {{ number_format((float) $booking->deposit, 2) }}</span></div>
                                    <div class="col-md-1"><input type="checkbox" id="deposit-checkbox"></div>
                                    <div class="col-md-2"><button id="deposit-button" class="btn btn-primary w-100" disabled type="button">Return</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal-wait" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-info" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5 class="mb-2">Information</h5>
                    <p id="info-message" class="mb-3"></p>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-error" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h5 class="mb-2 text-danger">Error</h5>
                    <p id="error-message" class="mb-3"></p>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-paynow" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receive Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" id="paymentdropdown">
                            <option value="">Payment Method</option>
                            @foreach ($paymentMethods as $paymentMethod)
                                <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enter Received Amount</label>
                        <input type="number" class="form-control" id="paymentvalue" min="0" step="0.01">
                        <div class="form-text">Remaining payable: <span id="rem-payment-payable">0.00</span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-confirm-pay-selection">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-amount-confirm" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Have you received the amount?</p>
                    <button type="button" class="btn btn-success" id="btn-confirm-additional-amount">Yes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
    <script>
        $(function () {
            const bookingId = @json($booking->id);
            const bookingItemId = @json($primaryItem?->id);
            const customerId = @json($booking->customer_id);
            const motorbikeId = @json($primaryMotorbike?->id);
            const regNo = @json($primaryMotorbike?->reg_no);
            let updatedTotal = 0;

            function showInfo(message) {
                $('#info-message').html(message);
                $('#modal-info').modal('show');
            }

            function showError(message) {
                $('#error-message').text(message);
                $('#modal-error').modal('show');
            }

            function withWait(callback) {
                $('#modal-wait').modal('show');
                return callback().always(function () {
                    setTimeout(function () {
                        $('#modal-wait').modal('hide');
                    }, 300);
                });
            }

            function refreshDocuments() {
                window.location.reload();
                return $.Deferred().resolve().promise();
            }

            function uploadDocument(fileInput) {
                const file = fileInput.files[0];
                if (!file) {
                    return;
                }

                const documentTypeCode = $(fileInput).data('document-type-code');
                const formData = new FormData();
                formData.append('document', file);
                formData.append('documentTypeCode', documentTypeCode);
                formData.append('bookingID', bookingId);
                formData.append('motorbikeID', motorbikeId);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                withWait(function () {
                    return $.ajax({
                        url: @json(route('customers.documents.upload', ['customer_id' => '__CUSTOMER__'])).replace('__CUSTOMER__', customerId),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false
                    }).done(function () {
                        showInfo('Document uploaded successfully.');
                        refreshDocuments();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Document upload failed.');
                    });
                });
            }

            function verifyDocument(documentId, mode) {
                const endpoint = mode === 'agreement'
                    ? @json(url('/admin/customers/documents')) + '/' + documentId + '/verifyAgreement'
                    : @json(url('/admin/customers/documents')) + '/' + documentId + '/verify';

                withWait(function () {
                    return $.ajax({
                        url: endpoint,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            documentTypeId: documentId,
                            document_id: documentId,
                            customer_id: customerId,
                            booking_id: bookingId
                        }
                    }).done(function () {
                        showInfo('Document verified.');
                        refreshDocuments();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Document verification failed.');
                    });
                });
            }

            function refreshOutstanding() {
                return $.ajax({
                    url: @json(url('/admin/renting/bookings/motorbike-pricing')),
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        motorbike_id: motorbikeId,
                        reg_no: regNo,
                        booking_id: bookingId
                    }
                }).done(function (response) {
                    const weekly = Number(response.repayment ? response.pricing : response.pricing?.weekly_price || 0);
                    const deposit = Number(response.repayment ? 0 : response.pricing?.minimum_deposit || 0);
                    const totalPaid = Number(response.totalPaid || 0);
                    const total = Number(weekly + deposit - totalPaid);
                    updatedTotal = total;

                    $('#payment-weekly').text('GBP ' + weekly.toFixed(2));
                    $('#payment-deposit').text('GBP ' + deposit.toFixed(2));
                    $('#payment-total').text('GBP ' + (weekly + deposit).toFixed(2));
                    $('#payment-paid').text('GBP ' + totalPaid.toFixed(2));
                    $('#payment-balance').text('GBP ' + total.toFixed(2));
                    $('#rem-payment-payable').text(total.toFixed(2));
                    $('#pending-total').val(total);
                    $('#pending-total-label').text('PENDING TOTAL: GBP ' + total.toFixed(2));
                    $('#payment-last-update').text(response.update_date || 'N/A');
                });
            }

            function loadInvoices() {
                return withWait(function () {
                    return $.ajax({
                        url: @json(route('admin.renting.bookings.invoices', ['bookingId' => '__BOOKING__'])).replace('__BOOKING__', bookingId),
                        method: 'GET'
                    }).done(function (response) {
                        const invoices = response.invoices || [];
                        if (!invoices.length) {
                            $('#payment-invoices').html('<div class="text-muted">No invoices found.</div>');
                            return;
                        }

                        let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Invoice</th><th>Date</th><th>Amount</th><th>Paid</th><th>State</th><th>Action</th></tr></thead><tbody>';
                        invoices.forEach(function (invoice) {
                            const isPaid = Number(invoice.IS_PAID) === 1;
                            html += `<tr>
                                <td>${invoice.INVOICE_ID ?? 'N/A'}</td>
                                <td>${invoice.INVOICE_DATE ?? 'N/A'}</td>
                                <td>${invoice.INVOICE_AMOUNT ?? 'N/A'}</td>
                                <td>${invoice.PAID_AMOUNT ?? 'Awaiting Payment'}</td>
                                <td>${invoice.INV_STATE ?? 'Awaiting Payment'}</td>
                                <td class="d-flex flex-wrap gap-2">
                                    ${!isPaid && invoice.INVOICE_ID ? `<button type="button" class="btn btn-sm btn-outline-primary btn-invoice-details" data-invoice-id="${invoice.INVOICE_ID}">Details</button>` : ''}
                                    ${!isPaid && invoice.INVOICE_ID ? `<button type="button" class="btn btn-sm btn-outline-success btn-pay-line-invoice" data-invoice-id="${invoice.INVOICE_ID}">Receive</button>` : '<span class="badge bg-success">Paid</span>'}
                                </td>
                            </tr>
                            <tr id="invoice-detail-row-${invoice.INVOICE_ID}" style="display:none;">
                                <td colspan="6"><div class="p-3 bg-light" id="invoice-detail-box-${invoice.INVOICE_ID}"></div></td>
                            </tr>`;
                        });
                        html += '</tbody></table></div>';
                        $('#payment-invoices').html(html);
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to load invoices.');
                    });
                });
            }

            function loadInvoiceDetails(invoiceId) {
                return $.ajax({
                    url: @json(route('admin.renting.bookings.invoices.details', ['invoiceId' => '__INVOICE__'])).replace('__INVOICE__', invoiceId),
                    method: 'GET'
                }).done(function (response) {
                    if (!response.success) {
                        showError('Invoice not found.');
                        return;
                    }

                    const invoice = response.invoice;
                    const box = $('#invoice-detail-box-' + invoiceId);
                    box.html(`
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div><strong>Customer:</strong> ${invoice.customer_name || 'N/A'}</div>
                                <div><strong>Phone:</strong> ${invoice.customer_phone || 'N/A'}</div>
                                <div><strong>Registration:</strong> ${invoice.motorbike_reg_no || 'N/A'}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Invoice Date</label>
                                <input type="date" class="form-control form-control-sm mb-2 invoice-date-inline" data-invoice-id="${invoiceId}" value="${invoice.invoice_date ? invoice.invoice_date.substring(0, 10) : ''}">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-update-invoice-date" data-invoice-id="${invoiceId}">Update Date</button>
                                <button type="button" class="btn btn-sm btn-success btn-send-whatsapp ms-2" data-invoice-id="${invoiceId}" data-whatsapp-url="${invoice.whatsapp_url || ''}">WhatsApp Reminder</button>
                            </div>
                        </div>
                    `);
                }).fail(function (xhr) {
                    showError(xhr.responseJSON?.message || 'Failed to load invoice details.');
                });
            }

            function loadOtherCharges() {
                return withWait(function () {
                    return $.ajax({
                        url: @json(route('admin.renting.bookings.other-charges', ['bookingId' => '__BOOKING__'])).replace('__BOOKING__', bookingId),
                        method: 'GET'
                    }).done(function (response) {
                        const charges = response.other_charges || [];
                        if (!charges.length) {
                            $('#load-other-item-data').html('<div class="text-muted">No additional charges found.</div>');
                            return;
                        }

                        let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Description</th><th>Amount</th><th>Paid</th><th>Action</th></tr></thead><tbody>';
                        charges.forEach(function (charge) {
                            html += `<tr>
                                <td>${charge.description}</td>
                                <td>${charge.amount}</td>
                                <td>${charge.is_paid}</td>
                                <td>${charge.is_paid === 'No' ? `<button type="button" class="btn btn-sm btn-danger payOtherCharges" data-id="${charge.id}">Mark Paid</button>` : '<span class="badge bg-success">Paid</span>'}</td>
                            </tr>`;
                        });
                        html += '</tbody></table></div>';
                        $('#load-other-item-data').html(html);
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to load charges.');
                    });
                });
            }

            function loadClosingStatus() {
                $.ajax({
                    url: @json(url('/admin/renting/booking')) + '/' + bookingId + '/closing-status',
                    method: 'GET'
                }).done(function (response) {
                    if (response.data === 'no data found') {
                        return;
                    }

                    $('#notice-details').val(response.notice_details || '');
                    $('#notice-checkbox').prop('checked', !!response.notice_checked).trigger('change');
                    $('#collect-details').val(response.collect_details || '');
                    $('#collect-date').val(response.collect_date || '');
                    $('#collect-time').val(response.collect_time || '');
                    $('#collect-checkbox').prop('checked', !!response.collect_checked).trigger('change');
                    $('#damages-checkbox').prop('checked', !!response.damages_checked).trigger('change');
                    $('#pcn-checkbox').prop('checked', !!response.pcn_checked).trigger('change');
                    $('#pending-checkbox').prop('checked', !!response.pending_checked).trigger('change');
                    $('#deposit-checkbox').prop('checked', !!response.deposit_checked).trigger('change');
                });

                $.ajax({
                    url: @json(url('/admin/renting/booking')) + '/' + bookingId + '/additional-costs',
                    method: 'GET'
                }).done(function (response) {
                    $('#damages-total').val(response.total_amount || 0);
                    $('#damages-received').val(response.paid_amount || 0);
                    $('#damages-total-label').text('TOTAL: GBP ' + Number(response.total_amount || 0).toFixed(2));
                    $('#damages-received-label').text('RECEIVED: GBP ' + Number(response.paid_amount || 0).toFixed(2));
                });

                $.ajax({
                    url: @json(route('admin.renting.bookings.pcn-pendings', ['booking_item_id' => '__ITEM__'])).replace('__ITEM__', bookingItemId),
                    method: 'GET'
                }).done(function (response) {
                    $('#pcn-total').val(response.pcn_pending || 0);
                    $('#pcn-received').val(response.paid_amount || 0);
                    $('#pcn-total-label').text('TOTAL: GBP ' + Number(response.pcn_pending || 0).toFixed(2));
                    $('#pcn-received-label').text('RECEIVED: GBP ' + Number(response.paid_amount || 0).toFixed(2));
                });

                $.ajax({
                    url: @json(url('/admin/renting/booking')) + '/' + bookingId + '/deposit',
                    method: 'GET'
                }).done(function (response) {
                    $('#total-deposit').text('TOTAL DEPOSIT: GBP ' + Number(response.deposit || 0).toFixed(2));
                });
            }

            function refreshServiceVideos() {
                $.ajax({
                    url: @json(route('admin.renting.bookings.videos.index', ['bookingId' => '__BOOKING__'])).replace('__BOOKING__', bookingId),
                    method: 'GET'
                }).done(function (videos) {
                    const target = $('#all-uploaded-videos');
                    if (!videos.length) {
                        target.html('<div class="text-muted">No videos uploaded for this booking.</div>');
                        return;
                    }

                    target.html(videos.map(function (video) {
                        const publicPath = (video.video_path || '').replace('public/', '/storage/');
                        const fileName = publicPath.split('/').pop();
                        return `<div><a href="${publicPath}" target="_blank">${fileName}</a></div>`;
                    }).join(''));
                });
            }

            function loadMaintenanceLogs() {
                $.ajax({
                    url: @json(route('admin.renting.bookings.maintenance-logs.index', ['bookingId' => '__BOOKING__'])).replace('__BOOKING__', bookingId),
                    method: 'GET'
                }).done(function (response) {
                    const logs = response.logs || [];
                    let total = 0;
                    let html = '';

                    if (!logs.length) {
                        html = '<tr><td colspan="5" class="text-center text-muted">No maintenance logs found.</td></tr>';
                    } else {
                        logs.forEach(function (log) {
                            total += parseFloat(log.cost || 0);
                            html += `<tr>
                                <td>${log.description || ''}</td>
                                <td>GBP ${parseFloat(log.cost || 0).toFixed(2)}</td>
                                <td>${log.note || ''}</td>
                                <td>${log.serviced_at ? new Date(log.serviced_at).toLocaleString() : ''}</td>
                                <td><button type="button" class="btn btn-sm btn-danger btn-delete-maintenance-log" data-log-id="${log.id}">Delete</button></td>
                            </tr>`;
                        });
                    }

                    $('#maintenance-log-list-table tbody').html(html);
                    $('#maintenance-log-total-cost').text('Total Cost: GBP ' + total.toFixed(2));
                });
            }

            function loadBookingSummary() {
                $.ajax({
                    url: @json(url('/admin/renting/bookings')) + '/' + bookingId + '/summary',
                    method: 'GET'
                }).done(function (data) {
                    let html = '<div class="alert alert-info mb-0">';
                    html += '<strong>Booking Summary</strong><br>';
                    html += 'Booking ID: <b>' + (data.booking_id || '-') + '</b><br>';
                    html += 'Reg Number: <b>' + (data.reg_no || '-') + '</b><br>';
                    html += 'Start Date: <b>' + (data.start_date || '-') + '</b><br>';
                    html += 'End Date: <b>' + (data.end_date || '-') + '</b><br>';
                    html += 'Weeks Passed: <b>' + (data.weeks || 0) + '</b><br>';
                    html += 'Paid Invoices: <b>' + (data.paid_invoice_count || 0) + '</b><br>';
                    html += 'Total Rental Income: <b>GBP ' + parseFloat(data.total_income || 0).toFixed(2) + '</b><br>';
                    html += 'Total Maintenance Cost: <b>GBP ' + parseFloat(data.total_cost || 0).toFixed(2) + '</b><br>';
                    html += 'Net Profit: <b>GBP ' + parseFloat(data.net_profit || 0).toFixed(2) + '</b><br>';
                    html += 'Current Weekly Rent: <b>GBP ' + parseFloat(data.current_weekly_rent || 0).toFixed(2) + '</b>';
                    html += '</div>';
                    $('#booking-summary').html(html);
                });
            }

            function postClosing(url, data, successMessage) {
                withWait(function () {
                    return $.ajax({
                        url: url,
                        method: 'POST',
                        data: $.extend({
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            booking_id: bookingId
                        }, data)
                    }).done(function () {
                        showInfo(successMessage);
                        loadClosingStatus();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to save closing step.');
                    });
                });
            }

            $('#btnRefreshDocuments').on('click', refreshDocuments);
            $(document).on('change', '.input-document-upload', function () {
                uploadDocument(this);
            });
            $(document).on('click', '.btn-verify-document', function () {
                verifyDocument($(this).data('document-id'), $(this).data('mode'));
            });

            $('#btnResendDocLink').on('click', function () {
                withWait(function () {
                    return $.ajax({
                        url: @json(url('/generate-docs-upload-link-access')) + '/' + customerId + '?booking_id=' + bookingId,
                        method: 'GET'
                    }).done(function (response) {
                        showInfo(response.uploadLink ? `<a href="${response.uploadLink}" target="_blank">${response.uploadLink}</a>` : 'No upload link returned.');
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to generate document upload link.');
                    });
                });
            });

            $('#btnDocumentsComplete').on('click', function () {
                postClosing(@json(route('admin.renting.bookings.doc-confirm')), {
                    booking_id: bookingId
                }, 'Documents confirmed.');
            });

            $('#btnReceiveAmount').on('click', function () {
                $('#paymentvalue').val('');
                $('#rem-payment-payable').text(updatedTotal.toFixed(2));
                $('#modal-paynow').modal('show');
            });

            $(document).on('click', '.btn-pay-line-invoice', function () {
                $('#paymentvalue').val('');
                $('#rem-payment-payable').text(updatedTotal.toFixed(2));
                $('#modal-paynow').modal('show');
            });

            $('#btn-confirm-pay-selection').on('click', function () {
                const paymentMethod = $('#paymentdropdown').val();
                const paymentValue = $('#paymentvalue').val();

                if (!paymentMethod) {
                    showError('Please select a payment method.');
                    return;
                }
                if (!paymentValue || Number(paymentValue) <= 0) {
                    showError('Please enter a valid payment amount.');
                    return;
                }

                withWait(function () {
                    return $.ajax({
                        url: @json(url('/admin/renting/bookings/update')),
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            booking_id: bookingId,
                            payment_method_id: paymentMethod,
                            amount: paymentValue
                        }
                    }).done(function (response) {
                        $('#modal-paynow').modal('hide');
                        showInfo(response.message || 'Payment processed successfully.');
                        refreshOutstanding();
                        loadInvoices();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.error || xhr.responseJSON?.message || 'Payment failed.');
                    });
                });
            });

            $(document).on('click', '.btn-invoice-details', function () {
                const invoiceId = $(this).data('invoice-id');
                const row = $('#invoice-detail-row-' + invoiceId);
                row.toggle();
                if (row.is(':visible')) {
                    loadInvoiceDetails(invoiceId);
                }
            });

            $(document).on('click', '.btn-update-invoice-date', function () {
                const invoiceId = $(this).data('invoice-id');
                const newDate = $('.invoice-date-inline[data-invoice-id="' + invoiceId + '"]').val();
                withWait(function () {
                    return $.ajax({
                        url: @json(url('/admin/renting/bookings/invoices')) + '/' + invoiceId + '/update-date',
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            invoice_date: newDate
                        }
                    }).done(function () {
                        showInfo('Invoice date updated successfully.');
                        loadInvoiceDetails(invoiceId);
                        loadInvoices();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to update invoice date.');
                    });
                });
            });

            $(document).on('click', '.btn-send-whatsapp', function () {
                const invoiceId = $(this).data('invoice-id');
                const whatsappUrl = $(this).data('whatsapp-url');
                withWait(function () {
                    return $.ajax({
                        url: @json(url('/admin/renting/bookings/invoices')) + '/' + invoiceId + '/send-whatsapp',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done(function () {
                        if (whatsappUrl) {
                            window.open(whatsappUrl, '_blank');
                        }
                        showInfo('WhatsApp reminder marked as sent.');
                        loadInvoiceDetails(invoiceId);
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to send WhatsApp reminder.');
                    });
                });
            });

            $('#btnStartBooking').on('click', function () {
                withWait(function () {
                    return $.ajax({
                        url: @json(route('admin.renting.bookings.startbooking', ['bookingId' => '__BOOKING__'])).replace('__BOOKING__', bookingId),
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            booking_id: bookingId
                        }
                    }).done(function (response) {
                        showInfo(response.message || 'Booking started.');
                        location.reload();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to start booking.');
                    });
                });
            });

            function submitIssuance(reissue) {
                const noteIssuance = $('#issuance_note').val();
                const currentMileage = $('#current_mileage').val();
                const issuanceBranch = $('input[name="issue_from"]:checked').val();

                if (!noteIssuance || !currentMileage || !issuanceBranch || !bookingItemId) {
                    showError('Issued by, mileage, and branch are required.');
                    return;
                }

                withWait(function () {
                    return $.ajax({
                        url: @json(url('/admin/renting/bookings')) + '/' + bookingId + '/' + (reissue ? 'reissue' : 'issue'),
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            reg_no: regNo,
                            booking_id: bookingId,
                            booking_item_id: bookingItemId,
                            current_mileage: currentMileage,
                            is_video_recorded: $('#is_video_recorded').is(':checked'),
                            accessories_checked: $('#accessories_checked').is(':checked'),
                            is_insured: $('#is_insured').is(':checked'),
                            issuance_branch: issuanceBranch,
                            notes: noteIssuance
                        }
                    }).done(function (response) {
                        showInfo(response.message || 'Booking issued successfully.');
                        location.reload();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.error || 'Failed to issue booking.');
                    });
                });
            }

            $('#btnIssueNow').on('click', function () {
                submitIssuance(false);
            });

            $('#btnReissueNow').on('click', function () {
                submitIssuance(true);
            });

            $('#btnUploadVideo').on('click', function () {
                const fileInput = $('#video_file')[0];
                if (!fileInput.files.length) {
                    showError('Please select a video file first.');
                    return;
                }

                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('video_file', fileInput.files[0]);

                withWait(function () {
                    return $.ajax({
                        url: @json(route('admin.renting.bookings.video.upload', ['bookingId' => '__BOOKING__'])).replace('__BOOKING__', bookingId),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false
                    }).done(function (response) {
                        $('#uploaded-video-link').html(response.video?.video_path ? 'Video uploaded successfully.' : 'Upload finished.');
                        $('#video_file').val('');
                        refreshServiceVideos();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Video upload failed.');
                    });
                });
            });

            $('#maintenance-log-form').on('submit', function (e) {
                e.preventDefault();

                withWait(function () {
                    return $.ajax({
                        url: @json(route('admin.renting.bookings.maintenance-logs.store', ['bookingId' => '__BOOKING__'])).replace('__BOOKING__', bookingId),
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            motorbike_id: motorbikeId,
                            description: $('#maintenance_description').val(),
                            cost: $('#maintenance_cost').val(),
                            serviced_at: $('#maintenance_serviced_at').val(),
                            note: $('#maintenance_note').val()
                        }
                    }).done(function () {
                        $('#maintenance_description, #maintenance_cost, #maintenance_serviced_at, #maintenance_note').val('');
                        loadMaintenanceLogs();
                        loadBookingSummary();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to save maintenance log.');
                    });
                });
            });

            $(document).on('click', '.btn-delete-maintenance-log', function () {
                const logId = $(this).data('log-id');
                if (!confirm('Delete this maintenance log?')) {
                    return;
                }

                withWait(function () {
                    return $.ajax({
                        url: @json(url('/admin/renting/bookings/maintenance-logs')) + '/' + logId,
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    }).done(function () {
                        loadMaintenanceLogs();
                        loadBookingSummary();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to delete maintenance log.');
                    });
                });
            });

            $('#btn-other-item').on('click', function () {
                withWait(function () {
                    return $.ajax({
                        url: @json(url('/admin/renting/bookings')) + '/' + bookingId + '/other-charges',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            booking_id: bookingId,
                            description: $('#other-item-desc').val(),
                            amount: $('#other-item-amount').val()
                        }
                    }).done(function () {
                        $('#other-item-desc, #other-item-amount').val('');
                        loadOtherCharges();
                        loadClosingStatus();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to add charge.');
                    });
                });
            });

            $('#getOtherCharges').on('click', loadOtherCharges);

            $(document).on('click', '.payOtherCharges', function () {
                $('#btn-confirm-additional-amount').data('id', $(this).data('id'));
                $('#modal-amount-confirm').modal('show');
            });

            $('#btn-confirm-additional-amount').on('click', function () {
                const chargeId = $(this).data('id');
                withWait(function () {
                    return $.ajax({
                        url: @json(route('admin.renting.bookings.other-charges.pay.post')),
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            charges_id: chargeId
                        }
                    }).done(function () {
                        $('#modal-amount-confirm').modal('hide');
                        loadOtherCharges();
                        loadClosingStatus();
                    }).fail(function (xhr) {
                        showError(xhr.responseJSON?.message || 'Failed to mark charge as paid.');
                    });
                });
            });

            $('#notice-checkbox').on('change', function () {
                $('#check-button').prop('disabled', !$(this).is(':checked'));
            });
            $('#collect-checkbox').on('change', function () {
                $('#collect-button').prop('disabled', !$(this).is(':checked'));
            });
            $('#damages-checkbox').on('change', function () {
                const total = Number($('#damages-total').val() || 0);
                const paid = Number($('#damages-received').val() || 0);
                $('#damages-button').prop('disabled', !($(this).is(':checked') && total === paid));
            });
            $('#pcn-checkbox').on('change', function () {
                const total = Number($('#pcn-total').val() || 0);
                const paid = Number($('#pcn-received').val() || 0);
                $('#pcn-button').prop('disabled', !($(this).is(':checked') && total === paid));
            });
            $('#pending-checkbox').on('change', function () {
                const total = Number($('#pending-total').val() || 0);
                $('#pending-button').prop('disabled', !($(this).is(':checked') && total === 0));
            });
            $('#deposit-checkbox').on('change', function () {
                $('#deposit-button').prop('disabled', !$(this).is(':checked'));
            });

            $('#check-button').on('click', function () {
                postClosing(@json(url('/admin/renting/notice-period')), {
                    noticeDetails: $('#notice-details').val(),
                    isChecked: $('#notice-checkbox').is(':checked')
                }, 'Notice period saved.');
            });
            $('#collect-button').on('click', function () {
                postClosing(@json(url('/admin/renting/collect-motorbike')), {
                    booking_item_id: bookingItemId,
                    collectDetails: $('#collect-details').val(),
                    collectDate: $('#collect-date').val(),
                    collectTime: $('#collect-time').val(),
                    isChecked: $('#collect-checkbox').is(':checked')
                }, 'Collection details saved.');
            });
            $('#damages-button').on('click', function () {
                postClosing(@json(url('/admin/renting/damages-cost')), {
                    isChecked: $('#damages-checkbox').is(':checked')
                }, 'Damage status saved.');
            });
            $('#pcn-button').on('click', function () {
                postClosing(@json(url('/admin/renting/pcn-pendings')), {
                    isChecked: $('#pcn-checkbox').is(':checked')
                }, 'PCN status saved.');
            });
            $('#pending-button').on('click', function () {
                postClosing(@json(url('/admin/renting/pending-rent')), {
                    isChecked: $('#pending-checkbox').is(':checked')
                }, 'Pending rent status saved.');
            });
            $('#deposit-button').on('click', function () {
                postClosing(@json(url('/admin/renting/deposit-return')), {
                    isChecked: $('#deposit-checkbox').is(':checked')
                }, 'Deposit return saved.');
            });

            // Do not call refreshDocuments() here — it is window.location.reload() and would loop forever on load.
            refreshOutstanding().always(loadInvoices);
            loadOtherCharges();
            loadClosingStatus();
            refreshServiceVideos();
            loadMaintenanceLogs();
            loadBookingSummary();
        });
    </script>
@endpush
