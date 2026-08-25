@php
    $modules = [
        'overview' => 'Whole business',
        'rentals' => 'Rentals',
        'club' => 'Club',
        'finance' => 'Payment plans',
        'mot' => 'MOT',
        'referrals' => 'Referrals',
        'cash' => 'Cash',
        'pcn' => 'PCNs',
    ];
    $focuses = match ($module) {
        'rentals' => ['all' => 'All rental numbers', 'cash' => 'Cash taken', 'unpaid' => 'Still unpaid'],
        'club' => ['all' => 'All club numbers', 'discounts' => 'Discounts', 'spend' => '0% spend', 'referrals' => 'Club referrals'],
        'finance' => ['all' => 'All finance numbers', 'active' => 'Active plans', 'new' => 'New this period'],
        'mot' => ['all' => 'All MOT numbers', 'expired' => 'Expired / due', 'bookings' => 'Bookings'],
        'referrals' => ['all' => 'Rental + club', 'programme' => 'Programme', 'direct' => 'Staff direct', 'club' => 'Club referrals'],
        'cash' => ['all' => 'All cash', 'in' => 'Coming in', 'out' => 'Given away', 'pending' => 'Still pending'],
        default => [],
    };
    $snapshot = $panel['snapshot'];
    $period = $panel['period'];
@endphp

<div class="space-y-5">
    <x-flux-admin::summary-header
        title="Director panel"
        subtitle="Live position plus the selected week, month or year. Same numbers the desks already use — one place to see them and open the page that investigates them."
        :backUrl="route('flux-admin.dashboard')"
        backLabel="Back to dashboard"
        :badges="[
            ['label' => 'Thiago / Super Admin', 'color' => 'zinc'],
            ['label' => $panel['from']->format('d M Y').' – '.$panel['to']->format('d M Y'), 'color' => 'blue'],
        ]"
    />

    <div class="flux-admin-panel border border-zinc-200 dark:border-zinc-800 p-5 space-y-4">
        <div class="flex flex-wrap gap-2">
            <flux:button size="sm" variant="{{ $from === now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString() && $to === now()->toDateString() ? 'primary' : 'ghost' }}" wire:click="setPreset('week')" class="!rounded-none">This week</flux:button>
            <flux:button size="sm" variant="ghost" wire:click="setPreset('month')" class="!rounded-none">This month</flux:button>
            <flux:button size="sm" variant="ghost" wire:click="setPreset('year')" class="!rounded-none">This year</flux:button>
            <flux:button size="sm" variant="ghost" wire:click="setPreset('days30')" class="!rounded-none">Last 30 days</flux:button>
            @if($filtersActive)
                <flux:button size="sm" variant="danger" wire:click="resetFilters" class="!rounded-none">Reset</flux:button>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-xl">
            <label class="block">
                <span class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500">From</span>
                <input type="date" wire:model.live="from" class="mt-1 block w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
            </label>
            <label class="block">
                <span class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500">To</span>
                <input type="date" wire:model.live="to" class="mt-1 block w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100">
            </label>
        </div>
        <p class="text-xs text-zinc-500">Live boxes (active rentals, unpaid, payment plans) stay as they are now. Period boxes follow the dates.</p>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach($modules as $key => $label)
            <flux:button size="sm" variant="{{ $module === $key ? 'primary' : 'ghost' }}" wire:click="setModule('{{ $key }}')" class="!rounded-none">{{ $label }}</flux:button>
        @endforeach
    </div>

    @if($focuses !== [])
        <div class="flex flex-wrap gap-2">
            @foreach($focuses as $key => $label)
                <flux:button size="sm" variant="{{ $focus === $key ? 'primary' : 'ghost' }}" wire:click="setFocus('{{ $key }}')" class="!rounded-none">{{ $label }}</flux:button>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($panel['cards'] as $card)
            @php
                $bar = match ($card['colour']) {
                    'green' => 'border-l-emerald-600',
                    'red' => 'border-l-red-600',
                    'amber' => 'border-l-amber-500',
                    'blue' => 'border-l-sky-600',
                    'purple' => 'border-l-purple-600',
                    'indigo' => 'border-l-indigo-600',
                    'pink' => 'border-l-pink-600',
                    default => 'border-l-zinc-600',
                };
                $tag = ! empty($card['href']) ? 'a' : 'div';
            @endphp
            <{{ $tag }} @if(! empty($card['href'])) href="{{ $card['href'] }}" wire:navigate @endif class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 border-l-4 {{ $bar }} {{ ! empty($card['href']) ? 'hover:border-zinc-400 dark:hover:border-zinc-500' : '' }}">
                <p class="text-xs font-medium text-zinc-500 uppercase tracking-wide">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $card['value'] }}</p>
                <p class="mt-1 text-xs text-zinc-500">{{ $card['hint'] }}</p>
            </{{ $tag }}>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="flux-admin-panel border border-zinc-200 dark:border-zinc-800">
            <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-800">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Look closer</p>
                <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">What needs a look in {{ strtolower($modules[$module] ?? 'this area') }}</p>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($panel['look'] as $row)
                    @if($row['url'])
                        <a href="{{ $row['url'] }}" wire:navigate class="block px-5 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $row['title'] }}</p>
                            <p class="mt-0.5 text-xs text-zinc-500">{{ $row['meta'] }}</p>
                        </a>
                    @else
                        <div class="px-5 py-3">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $row['title'] }}</p>
                            <p class="mt-0.5 text-xs text-zinc-500">{{ $row['meta'] }}</p>
                        </div>
                    @endif
                @empty
                    <p class="px-5 py-4 text-sm text-zinc-500">Nothing in this filter to open yet.</p>
                @endforelse
            </div>
        </div>

        <div class="flux-admin-panel border border-zinc-200 dark:border-zinc-800">
            <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-800">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500">Open the desk</p>
                <p class="mt-1 text-sm font-semibold text-zinc-900 dark:text-white">Pages Thiago uses to investigate this module</p>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($panel['pages'] as $page)
                    <a href="{{ $page['url'] }}" wire:navigate class="block px-5 py-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $page['label'] }}</p>
                        <p class="mt-0.5 text-xs text-zinc-500">{{ $page['hint'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if($module === 'overview')
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                <p class="text-xs text-zinc-500">Weekly rental book</p>
                <p class="mt-1 text-lg font-bold text-zinc-900 dark:text-white">£{{ number_format((float) $snapshot['weekly_rent'], 2) }}</p>
            </div>
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                <p class="text-xs text-zinc-500">Rental cash this period</p>
                <p class="mt-1 text-lg font-bold text-zinc-900 dark:text-white">£{{ number_format((float) $period['rental_cash_in'], 2) }}</p>
            </div>
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                <p class="text-xs text-zinc-500">Club discount this period</p>
                <p class="mt-1 text-lg font-bold text-zinc-900 dark:text-white">£{{ number_format((float) $period['club_discount'], 2) }}</p>
            </div>
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4">
                <p class="text-xs text-zinc-500">MOT expired now</p>
                <p class="mt-1 text-lg font-bold text-zinc-900 dark:text-white">{{ number_format((int) $snapshot['mot_expired']) }}</p>
            </div>
        </div>
    @endif
</div>
