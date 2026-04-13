@props(['headTint' => '#3498db', 'splitTint' => '#3498db'])

<div class="table-responsive">
  <table class="table table-sm" style="font-size:.75rem; background:transparent; color:#ecf0f1;">
    <thead>
      <tr style="background: rgba(52,73,94,.6); border-bottom: 2px solid {{ $splitTint }};">
        {{ $head }}
      </tr>
    </thead>
    <tbody>
      {{ $slot }}
    </tbody>
  </table>
</div>
