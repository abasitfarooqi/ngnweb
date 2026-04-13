@extends('layouts.admin')

@section('content')
    <style>
        .application-item {
            border: 1px solid #ccc;
            font-color: #333;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
    <div class="content-page">
        <div class="content">
            <!-- Start Content-->
            <div class="container-fluid">
                <h1>NEW APPLICATION</h1>

                <!-- New Application Form -->
                <form action="{{ route('admin.finance.application.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="customer_id">Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="deposit">Deposit</label>
                        <input type="number" step="0.01" name="deposit" id="deposit" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="4"></textarea>
                    </div>

                    <hr>

                    <h2>Application Items</h2>
                    <div id="application-items">
                        <div class="application-item">
                            <div class="form-group">
                                <label for="motorbike_id">Motorbike</label>
                                <select name="application_items[0][motorbike_id]" id="motorbike_id" class="form-control"
                                    required>
                                    @foreach ($motorbikes as $motorbike)
                                        <option value="{{ $motorbike->id }}">{{ $motorbike->reg_no }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label for="item_start_date">Start Date</label>
                                <input type="date" name="application_items[0][start_date]" id="item_start_date"
                                    class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="item_due_date">Due Date</label>
                                <input type="date" name="application_items[0][due_date]" id="item_due_date"
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="item_end_date">End Date</label>
                                <input type="date" name="application_items[0][end_date]" id="item_end_date"
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="weekly_instalment">Weekly Instalment</label>
                                <input type="number" step="0.01" name="application_items[0][weekly_instalment]"
                                    id="weekly_instalment" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" id="add-item">Add Item</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-item').addEventListener('click', function() {
            let itemCount = document.querySelectorAll('.application-item').length;
            let newItem = document.querySelector('.application-item').cloneNode(true);
            newItem.querySelectorAll('select, input').forEach(function(element) {
                let name = element.getAttribute('name');
                if (name) {
                    let newName = name.replace(/\d+/, itemCount);
                    element.setAttribute('name', newName);
                }
            });
            document.getElementById('application-items').appendChild(newItem);
        });
    </script>
@endsection
