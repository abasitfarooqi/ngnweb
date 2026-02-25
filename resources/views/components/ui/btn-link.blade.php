@props(['href','#','size'=>'sm','icon'=>null,'variant'=>'primary','target'=>null])

@php
  $map = [
    'primary' => ['#3498db','#2980b9'],
    'warning' => ['#f39c12','#e67e22'],
    'danger'  => ['#e74c3c','#c0392b'],
    'muted'   => ['#7f8c8d','#95a5a6'],
  ];
  [$a,$b] = $map[$variant] ?? $map['primary'];
@endphp

<a href="{{ $href }}" @if($target) target="{{ $target }}" @endif
   {{ $attributes->merge(['class'=>"btn btn-{$size}"]) }}
   style="background:linear-gradient(45deg, {{ $a }}, {{ $b }}); color:#fff; border:none; font-size:.7rem; padding:6px 12px; border-radius:4px; text-decoration:none;">
  @if($icon)<i class="{{ $icon }}"></i> @endif
  {{ $slot }}
</a>
