@php
    $src = $widget['src'] ?? $widget['content'] ?? $widget['path'];
    $attributes = collect($widget)->except(['name', 'section', 'type', 'stack', 'src', 'content', 'path'])->toArray();

    $publicRelative = is_string($src) ? ltrim($src, '/') : '';
    $usePublicAsset = is_string($src)
        && $publicRelative !== ''
        && ! \Illuminate\Support\Str::isUrl($src)
        && file_exists(public_path($publicRelative));

    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= ' '.$key.($value === true || $value === '' ? '' : '="'.e($value).'"');
    }
@endphp

@push($widget['stack'] ?? 'after_scripts')
    @if ($usePublicAsset)
        <script src="{{ asset($publicRelative) }}"{!! $attrString !!}></script>
    @else
        @basset($src, true, $attributes)
    @endif
@endpush
