@extends('layouts.admin')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <h3>All Booking Invoices (Edit Invoice Dates)</h3>
            <form method="get" class="mb-3" id="search-form">
                <label for="search">Search (Booking ID, Customer, Email, Phone, Reg, Invoice ID):</label>
                <input type="text" name="search" id="search" class="form-control" style="width:300px;display:inline-block;" value="{{ $search ?? '' }}" placeholder="Type and press Enter...">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                @if($search)
                    <a href="{{ route('admin.renting.invoice.dates.all') }}" class="btn btn-secondary btn-sm">Clear</a>
                @endif
            </form>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Motorbike Reg</th>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>State</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                <tr class="${invoice.invoice_state === 'Completed' ? 'table-success' : 'table-warning'}">
                                    <td>{{ $invoice->booking_id }}</td>
                                    <td>{{ $invoice->customer_full_name }}</td>
                                    <td>{{ $invoice->customer_email_address }}</td>
                                    <td>{{ $invoice->customer_phone }}</td>
                                    <td>{{ $invoice->motorbike_reg }}</td>
                                    <td>{{ $invoice->invoice_id }}</td>
                                    <td>
                                        <input type="date"
                                            class="form-control form-control-sm invoice-date"
                                            value="{{ $invoice->invoice_date }}"
                                            data-invoice-id="{{ $invoice->invoice_id }}"
                                            disabled>
                                    </td>
                                    <td>£{{ number_format($invoice->amount, 2) }}</td>
                                    <td>{{ $invoice->invoice_state }}</td>
                                    <td>
                                    <button class="btn btn-sm btn-secondary edit-date"
                                            data-invoice-id="{{ $invoice->invoice_id }}">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-primary update-date d-none"
                                            data-invoice-id="{{ $invoice->invoice_id }}">
                                        Update
                                    </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($invoices->isEmpty())
                            <div class="alert alert-info mt-3">No invoices found for your search.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div id="modal-info" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content modal-filled bg-info">
            <div class="modal-body">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-white"></i>
                    <h4 class="mt-2 text-white">Information</h4>
                    <p class="mt-3 text-white" id="info-message"></p>
                    <button type="button" class="btn btn-light my-2" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Waiting Spinner -->
<div id="modal-wait" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-body">
            <div id="loader">
                <div class="spinner-grow text-primary m-2" role="status"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-success {
        background-color: #e6f7e6 !important;
        color: #2d6a2d;
    }
    .table-warning {
        background-color: #fff8e6 !important;
        color: #856404;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
    .table td, .table th {
        vertical-align: middle;
        padding: 12px;
    }
</style>

@endsection

@push('scripts')
<script>
if (typeof jQuery !== 'undefined') {
    console.log('jQuery is loaded');
    jQuery(document).ready(function($) {
        console.log('Document is ready');
        $(document).on('click', '.edit-date', function() {
            
            var row = $(this).closest('tr');
            var dateInput = row.find('.invoice-date');
            var updateBtn = row.find('.update-date');
            dateInput.prop('disabled', false).focus();
            updateBtn.removeClass('d-none');
            $(this).addClass('d-none');
        });

        $(document).on('click', '.update-date', function() {
            const button = $(this);
            const row = button.closest('tr');
            const invoiceId = button.data('invoice-id');
            const newDate = row.find('.invoice-date').val();

            if (!newDate) {
                $('#info-message').text('Please select a date');
                $('#modal-info').modal('show');
                return;
            }

            $('#modal-wait').modal('show');

            $.ajax({
                url: '{{ route("admin.renting.invoice.dates.update") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    invoice_id: invoiceId,
                    new_date: newDate
                },
                success: function(response) {
                    $('#modal-wait').modal('hide');
                    $('#info-message').text('Invoice date updated successfully');
                    $('#modal-info').modal('show');
                    row.find('.invoice-date').prop('disabled', true);
                    row.find('.update-date').addClass('d-none');
                    row.find('.edit-date').removeClass('d-none');
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(xhr) {
                    $('#modal-wait').modal('hide');
                    $('#info-message').text('Error updating invoice date: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    $('#modal-info').modal('show');
                }
            });
        });
    });
} else {
    console.log('jQuery is not loaded');
}
</script>
@endpush 