@props([
    'dateModel',
    'timeModel',
    'min' => null,
    'dateLabel' => 'Date',
    'timeLabel' => 'Time',
])

@php
    $minDate = $min ?? \App\Support\BookingSchedule::minBookableDate();
@endphp

<div {{ $attributes->merge(['class' => 'grid grid-cols-1 sm:grid-cols-2 gap-4']) }}>
    <flux:field class="min-w-0">
        <flux:label>{{ $dateLabel }}</flux:label>
        <x-site.booking-date-picker wire:model="{{ $dateModel }}" min="{{ $minDate }}" />
        <flux:error name="{{ $dateModel }}" />
    </flux:field>
    <flux:field class="min-w-0">
        <flux:label>{{ $timeLabel }}</flux:label>
        <flux:input type="time" wire:model="{{ $timeModel }}" class="w-full" />
        <flux:error name="{{ $timeModel }}" />
    </flux:field>
</div>
