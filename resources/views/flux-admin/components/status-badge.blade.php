@props([
    'status' => null,
    'map' => [],
    'size' => 'sm',
])

@php
    $defaults = [
        'approved' => ['green', 'Approved'],
        'verified' => ['green', 'Verified'],
        'allowed' => ['green', 'Allowed'],
        'active' => ['green', 'Active'],
        'resolved' => ['green', 'Resolved'],
        'yes' => ['green', 'Yes'],
        'pending' => ['amber', 'Pending'],
        'pending_review' => ['amber', 'Pending review'],
        'uploaded' => ['blue', 'Uploaded'],
        'rejected' => ['red', 'Rejected'],
        'blocked' => ['red', 'Blocked'],
        'inactive' => ['zinc', 'Inactive'],
        'archived' => ['zinc', 'Archived'],
        'no' => ['zinc', 'No'],
    ];
    $lookup = array_merge($defaults, $map);
    $key = is_bool($status) ? ($status ? 'yes' : 'no') : (string) $status;
    [$colour, $label] = $lookup[$key] ?? ['zinc', ucfirst(str_replace('_', ' ', (string) $status))];
@endphp

<flux:badge :color="$colour" :size="$size">{{ $label }}</flux:badge>
