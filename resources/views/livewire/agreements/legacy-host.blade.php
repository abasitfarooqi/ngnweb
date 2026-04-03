@php
    $legacyView = (string) ($legacyView ?? '');
    $resolvedView = '';
    if ($legacyView !== '') {
        if (str_starts_with($legacyView, 'livewire.agreements.migrated.')) {
            $resolvedView = $legacyView;
        } elseif (str_starts_with($legacyView, 'olders.')) {
            $candidate = 'livewire.agreements.migrated.'.substr($legacyView, 7);
            $resolvedView = view()->exists($candidate) ? $candidate : $legacyView;
        } else {
            $resolvedView = $legacyView;
        }
    }
@endphp

@if($resolvedView !== '' && view()->exists($resolvedView))
    @include($resolvedView)
@endif
