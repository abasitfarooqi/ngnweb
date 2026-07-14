{{-- Staff UI switch: same Judopay surface in Flux or Backpack. $variant = flux|bootstrap --}}
@php
    $variant = $variant ?? (judopay_using_flux() ? 'flux' : 'bootstrap');
    $switchUrl = judopay_counterpart_url();
    $switchLabel = judopay_switch_label();
    $onFlux = judopay_using_flux();
@endphp

@if($variant === 'flux')
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
            UI: {{ $onFlux ? 'Flux' : 'Backpack' }}
        </span>
        <a href="{{ $switchUrl }}">
            <flux:button size="xs" variant="outline" class="!rounded-none" icon="arrow-path">
                {{ $switchLabel }}
            </flux:button>
        </a>
    </div>
@else
    <a href="{{ $switchUrl }}"
       class="btn btn-outline-light btn-sm"
       style="font-size: 0.75rem; border-radius: 0; white-space: nowrap;"
       title="Open the same Judopay page in the other admin UI">
        <i class="fa fa-exchange-alt"></i>
        {{ $switchLabel }}
        <span class="badge badge-{{ $onFlux ? 'info' : 'secondary' }} ml-1" style="border-radius: 0;">
            {{ $onFlux ? 'Flux' : 'Backpack' }}
        </span>
    </a>
@endif
