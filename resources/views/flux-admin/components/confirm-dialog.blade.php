@props([
    'name',
    'title' => 'Are you sure?',
    'message' => 'This action cannot be undone.',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'variant' => 'danger',
])

<div
    x-data="{ open: false, payload: null }"
    x-on:confirm-{{ $name }}.window="open = true; payload = $event.detail"
    x-cloak
>
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-black/50"
        @click="open = false"
    ></div>

    <div
        x-show="open"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div class="w-full max-w-md border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ $title }}</h3>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $message }}</p>

            <div class="mt-5 flex items-center justify-end gap-2">
                <flux:button size="sm" variant="ghost" @click="open = false" class="!rounded-none">{{ $cancelLabel }}</flux:button>
                <flux:button
                    size="sm"
                    :variant="$variant === 'danger' ? 'danger' : 'primary'"
                    class="!rounded-none"
                    @click="$wire.call($event.target.dataset.method, payload); open = false"
                    :data-method="'confirmed_'+ {{ json_encode($name) }}"
                >{{ $confirmLabel }}</flux:button>
            </div>
        </div>
    </div>
</div>
