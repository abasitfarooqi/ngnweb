@if (isset($widget['link']))
    <a href="{{ $widget['link'] }}" class="text-decoration-none">
@endif
<div class="card">
    <div class="card-body text-center">
        <div class="text-{{ $widget['color'] }} mb-2">
            <i class="{{ $widget['icon'] }} la-3x"></i>
        </div>
        <h3 class="text-{{ $widget['color'] }}">{{ $widget['value'] }}</h3>
        <p class="mb-0">{{ $widget['label'] }}</p>
    </div>
</div>
@if (isset($widget['link']))
    </a>
@endif
