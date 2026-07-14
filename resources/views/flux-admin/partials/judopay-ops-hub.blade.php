@php
    $opsHub = [
        ['label' => 'Judo Pay home', 'route' => 'flux-admin.judopay.index', 'match' => 'flux-admin.judopay.index'],
        ['label' => 'MIT dashboard', 'route' => 'flux-admin.judopay.mit-dashboard', 'match' => 'flux-admin.judopay.mit-dashboard'],
        ['label' => 'Weekly schedule', 'route' => 'flux-admin.judopay.weekly-mit-queue', 'match' => 'flux-admin.judopay.weekly-mit-queue'],
        ['label' => 'Subscriptions', 'route' => 'flux-admin.judopay-subscriptions.index', 'match' => 'flux-admin.judopay-subscriptions.*'],
        ['label' => 'NGN MIT queue', 'route' => 'flux-admin.ngn-mit-queue.index', 'match' => 'flux-admin.ngn-mit-queue.*'],
        ['label' => 'Judopay MIT queue', 'route' => 'flux-admin.judopay-mit-queue.index', 'match' => 'flux-admin.judopay-mit-queue.*'],
    ];
@endphp
<div class="mb-4 flex flex-col gap-3 border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex flex-wrap gap-2">
        @foreach($opsHub as $item)
            <a href="{{ route($item['route']) }}">
                <flux:button
                    size="xs"
                    variant="{{ request()->routeIs($item['match']) ? 'primary' : 'ghost' }}"
                    class="!rounded-none"
                >{{ $item['label'] }}</flux:button>
            </a>
        @endforeach
    </div>
    <x-judopay-ui-switch variant="flux" />
</div>
