@props([
  'title' => null,
  'icon' => 'null',           // e.g. 'fa fa-credit-card'
  'variant' => 'primary',   // primary|info|warning|danger|muted
  'padded' => true,
  'header' => true,
  'headerRight' => null,     // Content for right side of header
  'headerRightUrl' => null,  // URL to make headerRight clickable
  'actions' => null,         // Action buttons/links for header
  'subtitle' => null,        // Subtitle below main title
  'badge' => null,           // Badge/count display
])

@php
  // Debug: Check if attributes are being passed
  // {{-- Debug: variant={{ $variant }}, icon={{ $icon }} --}}
  
  // Dark theme + subtle gradient (one place to tweak it for all panels)
  $panel = 'card border-0 shadow'
         . ' ' . match($variant) {
              'primary' => 'border-primary',
              'warning' => 'border-warning',
              'danger'  => 'border-danger',
              'info'    => 'border-info',
              default   => ''
            };

  $style = 'background: linear-gradient(135deg,#2c3e50 0%,#34495e 100%);';
  $headerStyle = 'background: rgba(52,73,94,.8); border-bottom:1px solid rgba(255,255,255,.1);';
  $bodyStyle = 'background: rgba(44,62,80,.3);';

  $headerRightClass = 'font-weight-bold text-end text-white ms-2';

  $bodyClass = 'card-body' . ($padded ? '' : ' p-0');
@endphp

<div {{ $attributes->merge(['class' => $panel]) }} style="{{ $style }}">
  @if($header && ($title || $headerRight || $actions))
    <div class="card-header d-flex justify-content-between align-items-center" style="{{ $headerStyle }}">
      <div class="d-flex flex-column">
        @if($title)
          <h6 class="card-title mb-0 d-flex align-items-center" style="font-size:.9rem;color:#ecf2ca;font-weight:600;">
            @if($icon)<i class="{{ $icon }}" style="color:#d9d9da;margin-right:8px;"></i> @endif
            {{ $title }}
            @if($badge)
              <span class="badge bg-primary ms-2" style="font-size:0.7rem;">{{ $badge }}</span>
            @endif
          </h6>
        @endif
        @if($subtitle)
          <small class="text-muted" style="color:#bdc3c7 !important;font-size:0.75rem;">{{ $subtitle }}</small>
        @endif
      </div>
      
      <div class="d-flex align-items-center gap-2">
        @if($headerRight)
          <div class="{{ $headerRightClass }}">
            @if($headerRightUrl)
              <a href="{{ $headerRightUrl }}" class="text-white text-decoration-none" style="cursor: pointer;">
                {{ $headerRight }}
              </a>
            @else
              {{ $headerRight }}
            @endif
          </div>
        @endif
        
        @if($actions)
          <div class="header-actions">
            {{ $actions }}
          </div>
        @endif
        
        {{ $headerSlot ?? '' }}
      </div>
    </div>
  @endif

  <div class="{{ $bodyClass }}" style="{{ $bodyStyle }}">
    {{ $slot }}
  </div>

  @isset($footer)
    <div class="card-footer">
      {{ $footer }}
    </div>
  @endisset
</div>
