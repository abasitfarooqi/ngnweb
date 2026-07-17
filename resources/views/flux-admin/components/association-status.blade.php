@props(['status' => ''])

@php
    $status = (string) $status;
    $base = 'inline-block px-2 py-1 text-xs font-semibold';
@endphp

@if($status === 'Unassociated')
    <span class="{{ $base }} bg-[#FFFFED] text-zinc-900">{{ $status }}</span>
@elseif($status === 'COMPANY VEHICLE')
    <span class="{{ $base }} bg-[#FFB6C1] text-zinc-900">{{ $status }}</span>
@elseif(str_contains($status, 'INSTALLMENT TRANSFERRED'))
    <span class="{{ $base }} bg-[#FFDBBB] text-zinc-900">{{ $status }}</span>
@elseif(str_contains($status, 'INSTALLMENT'))
    <span class="{{ $base }} bg-[#DAF7A6] text-zinc-900">{{ $status }}</span>
@elseif(str_contains($status, 'RENTAL'))
    <span class="{{ $base }} bg-sky-200 text-zinc-900">{{ $status }}</span>
@elseif($status === 'SALE')
    <span class="{{ $base }} bg-[#FFDBBB] text-zinc-900">SALE</span>
@elseif($status === 'SOLD')
    <span class="{{ $base }} bg-[#FFDBBB] text-zinc-900">SALE</span>
    <span class="{{ $base }} bg-[#d96868] text-white ml-1">SOLD</span>
@elseif($status !== '')
    <span class="{{ $base }} bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">{{ $status }}</span>
@else
    <span class="text-zinc-400">—</span>
@endif
