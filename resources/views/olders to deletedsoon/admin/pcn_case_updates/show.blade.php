{{-- resources/views/admin/pcn_case_updates/show.blade.php --}}
{{-- @extends('backpack::layout') --}}

@section('content')
    <h3>Updates for PCN Case: {{ $pcnCase->pcn_number }}</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Appealed</th>
                <th>Note</th>
                <th>Picture</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pcnCase->updates as $update)
                <tr>
                    <td>{{ $update->update_date }}</td>
                    <td>{{ $update->is_appealed ? 'Yes' : 'No' }}</td>
                    <td>{{ $update->note }}</td>
                    <td><a href="{{ $update->picture_url }}" target="_blank">View</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
