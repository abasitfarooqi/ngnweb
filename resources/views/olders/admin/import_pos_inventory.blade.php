<!-- resources/views/admin/import_pos_inventory.blade.php -->

@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1>Import POS Inventory</h1>
        <form action="{{ route('ngn-pos-inventory.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="file">Select Excel File:</label>
                <input type="file" name="file" accept=".xlsx" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Import POS Inventory</button>
        </form>
    </div>
@endsection
