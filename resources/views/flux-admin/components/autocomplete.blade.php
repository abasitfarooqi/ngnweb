@props([
    'wireModel' => null,
    'placeholder' => 'Search…',
    'suggestions' => [],
    'labelKey' => 'label',
    'selectMethod' => 'selectSuggestion',
    'idKey' => 'id',
])

@php
    $open = is_countable($suggestions) && count($suggestions) > 0;
@endphp

<div {{ $attributes->class($open ? 'flux-admin-autocomplete flux-admin-autocomplete-open' : 'flux-admin-autocomplete') }}>
    <flux:input
        @if($wireModel) wire:model.live.debounce.300ms="{{ $wireModel }}" @endif
        placeholder="{{ $placeholder }}"
        autocomplete="off"
    />

    @if($open)
        <ul class="flux-admin-autocomplete-menu" role="listbox">
            @foreach($suggestions as $suggestion)
                @php
                    $id = is_array($suggestion) ? ($suggestion[$idKey] ?? null) : ($suggestion->{$idKey} ?? null);
                    $label = is_array($suggestion) ? ($suggestion[$labelKey] ?? '') : ($suggestion->{$labelKey} ?? '');
                @endphp
                @if($id !== null)
                    <li
                        role="option"
                        wire:mousedown.prevent="{{ $selectMethod }}({{ (int) $id }})"
                    >{{ $label }}</li>
                @endif
            @endforeach
        </ul>
    @endif
</div>
