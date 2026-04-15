@props([
    'name',
    'title' => null,
    'maxWidth' => 'lg',
])

<flux:modal :name="$name" variant="flyout" position="right" :class="'max-w-' . $maxWidth">
    @if($title)
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $title }}</h2>
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
            {{ $footer }}
        </div>
    @endif
</flux:modal>
