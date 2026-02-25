@extends('backpack::layout')

@section('content')
    <h2>{{ $title }}</h2>

    <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="import_file">Choose file</label>
            <input type="file" name="import_file" id="import_file" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Import</button>
    </form>
@endsection
