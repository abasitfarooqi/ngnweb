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
        'available' => ['green', 'Available'],
        'completed' => ['green', 'Completed'],
        'booked' => ['blue', 'Booked'],
        'confirmed' => ['blue', 'Confirmed'],
        'cancelled' => ['red', 'Cancelled'],
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
    $fallback = ['colour' => 'zinc', 'label' => ucfirst(str_replace('_', ' ', (string) $status))];
    $entry = $lookup[$key] ?? $fallback;
    $colour = is_array($entry) ? ($entry['colour'] ?? $entry['color'] ?? $entry[0] ?? 'zinc') : 'zinc';
    $label = is_array($entry) ? ($entry['label'] ?? $entry[1] ?? ucfirst(str_replace('_', ' ', (string) $status))) : (string) $entry;
@endphp

<flux:badge :color="$colour" :size="$size">{{ $label }}</flux:badge>
