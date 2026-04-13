@props(['status' => null, 'text' => null])

@php
  $map = [
    'active'    => ['#27ae60','#2ecc71'],
    'success'   => ['#27ae60','#2ecc71'],
    'pending'   => ['#f39c12','#e67e22'],
    'cancelled' => ['#e74c3c','#c0392b'],
    'declined'  => ['#e74c3c','#c0392b'],
    'error'     => ['#e74c3c','#c0392b'],
    'expired'   => ['#f39c12','#e67e22'],
    'created'   => ['#3498db','#2980b9'],
    'default'   => ['#95a5a6','#7f8c8d'],
  ];
  [$a,$b] = $map[strtolower($status ?? 'default')] ?? $map['default'];
@endphp

<span {{ $attributes->merge(['class' => 'badge']) }}
      style="background:linear-gradient(45deg, {{ $a }}, {{ $b }}); color:#fff; font-size:.7rem; padding:6px 10px; font-weight:600;">
  {{ $text ?? ucfirst($status ?? 'Status') }}
</span>
