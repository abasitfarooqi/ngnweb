@props(['min' => null])

@php
    $minDate = $min ?? \App\Support\BookingSchedule::minBookableDate();
    $unavailableDates = \App\Support\BookingSchedule::unavailableSundaysCsv();
@endphp

<flux:date-picker
    {{ $attributes->merge(['class' => 'w-full min-w-0 block']) }}
    unavailable="{{ $unavailableDates }}"
    min="{{ $minDate }}"
/>
