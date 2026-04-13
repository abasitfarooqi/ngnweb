@props(['type' => 'info', 'title' => null, 'icon' => null])

@php
  $defs = [
    'success' => ['bg' => 'rgba(39,174,96,.2)',  'bd' => 'rgba(39,174,96,.4)',  'fg' => '#d4edda', 'icon' => 'fa fa-check-circle'],
    'info'    => ['bg' => 'rgba(52,152,219,.2)', 'bd' => 'rgba(52,152,219,.4)', 'fg' => '#d1ecf1', 'icon' => 'fa fa-info-circle'],
    'warning' => ['bg' => 'rgba(241,196,15,.2)', 'bd' => 'rgba(241,196,15,.4)', 'fg' => '#fff3cd', 'icon' => 'fa fa-exclamation-triangle'],
    'danger'  => ['bg' => 'rgba(231,76,60,.2)',  'bd' => 'rgba(231,76,60,.4)',  'fg' => '#ffebee', 'icon' => 'fa fa-exclamation-circle'],
  ];
  $d = $defs[$type] ?? $defs['info'];
@endphp

<div class="alert mb-2" style="background:{{ $d['bg'] }}; border:1px solid {{ $d['bd'] }}; color:{{ $d['fg'] }}; border-radius:4px;">
  @if($title)
    <h6 style="font-weight:600; margin:0 0 4px; color:inherit;">
      <i class="{{ $icon ?? $d['icon'] }}"></i> {{ $title }}
    </h6>
  @endif
  {{ $slot }}
</div>
