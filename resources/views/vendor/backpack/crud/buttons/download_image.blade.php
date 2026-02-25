@if ($entry->picture)
    <a href="{{ url('storage/' . $entry->picture) }}" class="btn btn-sm btn-link" download>Download</a>
@endif
