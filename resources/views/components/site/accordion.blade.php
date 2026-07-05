@props([
    'items' => [],
])

<div x-data="{ open: null }" {{ $attributes->merge(['class' => 'site-accordion']) }}>
    @foreach($items as $i => $item)
        @php
            $question = is_array($item) ? ($item['q'] ?? '') : ($item->q ?? '');
            $answer = is_array($item) ? ($item['a'] ?? '') : ($item->a ?? '');
        @endphp
        <div class="site-accordion-item" :class="{ 'is-open': open === {{ $i }} }">
            <button
                type="button"
                class="site-accordion-trigger"
                @click="open = open === {{ $i }} ? null : {{ $i }}"
            >
                <span>{{ $question }}</span>
                <span class="site-accordion-icon" x-text="open === {{ $i }} ? '−' : '+'"></span>
            </button>
            <div x-show="open === {{ $i }}" x-collapse class="site-accordion-panel">
                {{ $answer }}
            </div>
        </div>
    @endforeach
</div>
