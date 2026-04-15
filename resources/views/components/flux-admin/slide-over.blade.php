@props([
    'name',
    'title' => '',
    'maxWidth' => 'lg',
])

<flux:modal :name="$name" variant="flyout" position="right" class="max-w-{{ $maxWidth }}">
    <div class="space-y-6">
        @if($title)
            <flux:heading size="lg">{{ $title }}</flux:heading>
        @endif
        {{ $slot }}
    </div>
</flux:modal>
