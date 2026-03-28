@extends('backpack::layout')

@section('content')
    <h2>Pending Invoices</h2>
    <p>Total Amount: {{ $totalAmount }}</p>

    <form action="{{ route('admin.finance-application.mergeInvoice.process') }}" method="POST">
        @csrf
        <label for="payment_amount">Payment Amount:</label>
        <input type="number" name="payment_amount" required>
        <button type="submit">Pay</button>
    </form>

    <h3>Invoices</h3>
    <ul>
        @foreach ($pendingInvoices as $invoice)
            <li>{{ $invoice->id }} - {{ $invoice->amount }}</li>
        @endforeach
    </ul>
@endsection
