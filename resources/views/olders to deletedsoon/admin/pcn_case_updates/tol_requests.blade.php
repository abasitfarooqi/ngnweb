@php
    // Backpack passes everything as $data, so extract here
    $tolRequests = $data['tolRequests'] ?? collect();
@endphp

@if($tolRequests->isEmpty())
    <div class="alert alert-info">No TOL Requests for this PCN.</div>
@else
    <h5>Total TOL Requests: {{ $tolRequests->count() }}</h5>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Update ID</th>
                <th>Request Date</th>
                <th>Status</th>
                <th>Letter Sent At</th>
                
                <th>Note</th>
                <th>User</th>
                <th>Created At</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tolRequests as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->update_id }}</td>
                    <td>{{ $r->request_date }}</td>
                    <td>{{ $r->status }}</td>
                    <td>{{ $r->letter_sent_at }}</td>                    
                    <td>{{ $r->note }}</td>
                    <td>{{ optional($r->user)->full_name }}</td>
                    <td>{{ $r->created_at }}</td>
                    
                    <td>{!! $r->generateTolPdfButton() !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
