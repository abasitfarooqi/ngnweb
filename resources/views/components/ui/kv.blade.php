@props(['label','value'=>null])

<p class="mb-1" style="font-size:.75rem; color:#ecf0f1;">
  <strong style="color:#ecf0f1;">{{ $label }}:</strong>
  <span style="color:#3498db;">{{ $value ?? $slot }}</span>
</p>
