@extends('layouts.admin')
@section('content')
<div class="main-content">
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="text-primary mb-4">Booking Summary</h2>
                        <div class="card mb-4" style="border-radius:0;">
                            <div class="card-body">
                                <h4 class="mb-3">Booking ID: {{ $booking->id }}</h4>
                                <p><strong>Customer:</strong> {{ $booking->customer->first_name ?? 'N/A' }} {{ $booking->customer->last_name ?? '' }}</p>
                                <p><strong>Start Date:</strong> {{ $booking->start_date }}</p>
                                <p><strong>Due Date:</strong> {{ $booking->due_date ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> {{ $booking->status ?? 'N/A' }}</p>
                                <p><strong>Motorbike Reg No:</strong> {{ $booking->rentingBookingItems->first()->motorbike->reg_no ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="card" style="border-radius:0;">
                            <div class="card-body">
                                <h5>Summary</h5>
                                <ul>
                                    <li><strong>Total Paid Invoices:</strong> {{ $booking->bookingInvoices->where('is_paid', true)->count() }}</li>
                                    <li><strong>Total Income:</strong> £{{ number_format($booking->bookingInvoices->where('is_paid', true)->sum('amount'), 2) }}</li>
                                    <li><strong>Total Maintenance Cost:</strong> £{{ number_format(optional(optional(optional($booking->rentingBookingItems->first())->motorbike)->maintenanceLogs)->sum('cost') ?? 0, 2) }}</li>
                                    <li><strong>Current Weekly Rent:</strong> £{{ $booking->rentingBookingItems->first()->motorbike->currentPricing->weekly_rent ?? 'N/A' }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4" style="border-radius:0;">
                                    <div class="card-header bg-light" style="border-radius:0;">
                                        <strong>Invoices (Paid)</strong>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            @foreach($booking->bookingInvoices->where('is_paid', true) as $invoice)
                                                <li class="list-group-item" style="border-radius:0;">
                                                    <strong>Invoice #{{ $invoice->id }}</strong> - £{{ number_format($invoice->amount, 2) }} ({{ $invoice->created_at->format('d/m/Y') }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4" style="border-radius:0;">
                                    <div class="card-header bg-light" style="border-radius:0;">
                                        <strong>Maintenance Logs</strong>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            @php
                                                $bookingItem = $booking->rentingBookingItems->first();
                                                $motorbike = $bookingItem ? $bookingItem->motorbike : null;
                                                $maintenanceLogs = $motorbike ? $motorbike->maintenanceLogs->where('booking_id', $booking->id) : collect();
                                            @endphp
                                            @forelse($maintenanceLogs as $log)
                                                <li class="list-group-item" style="border-radius:0;">
                                                    <strong>{{ $log->serviced_at ? $log->serviced_at->format('d/m/Y') : 'N/A' }}</strong>: £{{ number_format($log->cost, 2) }} - {{ $log->description ?? '' }}
                                                </li>
                                            @empty
                                                <li class="list-group-item" style="border-radius:0;">No maintenance logs found for this booking.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 