@php
    /** @var \App\Models\CustomerDocument|null $entry */
@endphp
@if($entry)
    <div class="card mb-3">
        <div class="card-body small">
            <div><strong>Customer:</strong> {{ $entry->customer?->full_name ?? '—' }}</div>
            <div><strong>Document type:</strong> {{ $entry->documentType?->name ?? '—' }}</div>
            <div><strong>File:</strong> {{ $entry->file_name ?? '—' }}</div>
            @if($entry->file_url)
                <div class="mt-2">
                    <a class="btn btn-sm btn-primary" href="{{ $entry->file_url }}" target="_blank" rel="noopener">Open file</a>
                </div>
            @endif
            @if(filled($entry->document_number))
                <div class="mt-2"><strong>Document number:</strong> {{ $entry->document_number }}</div>
            @endif
        </div>
    </div>
@endif
