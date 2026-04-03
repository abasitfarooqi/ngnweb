@extends(backpack_view('blank'))

@section('content')
<div class="row">
    <div class="col">
        <h1>Rental Due Payments</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($records->isEmpty())
            <div class="alert alert-info" role="alert">
                No rental bookings currently due. (If you were expecting results, please check that invoices are unpaid and dated on or before today.)
            </div>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Booking No</th>
                        <th>Customer</th>
                        <th>Registration Number</th>
                        <th>Weekly Rent</th>
                        <th>Invoice Date <small class="text-muted">(editable)</small></th>
                        <th>Status</th>
                        <th>WhatsApp Sent</th>
                        <th>Last WhatsApp Reminder</th>
                        <th>Notify WhatsApp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $row)
                        <tr>
                            <td>{{ $row->booking_no }}</td>
                            <td>{{ $row->customer }}</td>
                            <td>{{ $row->reg_no }}</td>
                            <td>£{{ number_format($row->weekly, 2) }}</td>
                            <td>
                                <form action="{{ route('rental-due-payments.update-invoice-date', $row->invoice_id) }}" method="POST" class="d-inline" id="invoiceDateForm-{{ $row->invoice_id }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="date" 
                                           name="invoice_date" 
                                           value="{{ $row->invoice_date ? \Carbon\Carbon::parse($row->invoice_date)->format('Y-m-d') : '' }}" 
                                           class="form-control form-control-sm" 
                                           style="width: 160px;"
                                           onchange="this.form.submit();"
                                           title="Click to edit invoice date">
                                </form>
                            </td>
                            <td>{{ $row->state }}</td>
                            <td>{{ $row->is_whatsapp_sent ? 'Yes' : 'No' }}</td>
                            <td>{{ $row->whatsapp_last_reminder_sent_at ? \Carbon\Carbon::parse($row->whatsapp_last_reminder_sent_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                <form action="{{ route('rental-due-payments.send-whatsapp', $row->invoice_id) }}" method="POST" id="whatsappForm-{{ $row->invoice_id }}">
                                    @csrf
                                    <a href="{{ $row->whatsapp_url }}" target="_blank" class="btn btn-success"
                                       onclick="event.preventDefault(); document.getElementById('whatsappForm-{{ $row->invoice_id }}').submit(); window.open('{{ $row->whatsapp_url }}', '_blank');">
                                        <i class="fab fa-whatsapp"></i> Send Reminder WhatsApp
                                    </a>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection