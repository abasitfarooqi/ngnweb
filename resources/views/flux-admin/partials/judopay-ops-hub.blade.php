@php
    $opsHub = [
        ['label' => 'Judo Pay home', 'route' => 'flux-admin.judopay.index'],
        ['label' => 'MIT dashboard', 'route' => 'flux-admin.judopay.mit-dashboard'],
        ['label' => 'Weekly schedule', 'route' => 'flux-admin.judopay.weekly-mit-queue'],
        ['label' => 'Subscriptions', 'route' => 'flux-admin.judopay-subscriptions.index'],
        ['label' => 'NGN MIT queue', 'route' => 'flux-admin.ngn-mit-queue.index'],
        ['label' => 'Judopay MIT queue', 'route' => 'flux-admin.judopay-mit-queue.index'],
    ];
@endphp
<div class="mb-4 flex flex-wrap gap-2 border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-900">
    @foreach($opsHub as $item)
        <a href="{{ route($item['route']) }}">
            <flux:button
                size="xs"
                variant="{{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'primary' : 'ghost' }}"
                class="!rounded-none"
            >{{ $item['label'] }}</flux:button>
        </a>
    @endforeach
</div>
