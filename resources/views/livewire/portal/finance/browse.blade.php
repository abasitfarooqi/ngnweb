@php
    use App\Support\NgnMotorcycleImage;
    $enquiryWireKey = 'portal-finance-enquiry-'.md5(request()->getQueryString());
@endphp
<div wire:key="finance-browse-page" class="space-y-10">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <flux:heading size="xl">Finance enquiry</flux:heading>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 max-w-2xl">
                Browse our stock below, then use <strong>Enquire on finance</strong> on a bike card to open the form for that bike (new, used, or e-bike). We prepare contracts in admin — you do not create a finance application yourself here.
            </p>
        </div>
        <flux:button href="{{ route('account.finance.my-applications') }}" variant="outline" class="border-brand-red text-brand-red shrink-0">
            My finance applications
        </flux:button>
    </div>

    <div class="flex flex-wrap gap-2 text-sm border-b border-gray-200 dark:border-gray-700 pb-4">
        <span class="text-sm text-gray-600 dark:text-gray-400 self-center mr-1">Full catalogue:</span>
        <flux:button href="{{ route('site.bikes', ['filter' => 'new']) }}" variant="ghost" size="sm" class="text-brand-red">Full new stock</flux:button>
        <flux:button href="{{ route('site.bikes') }}" variant="ghost" size="sm" class="text-brand-red">Full used stock</flux:button>
        <flux:button href="{{ route('site.ebikes') }}" variant="ghost" size="sm" class="text-brand-red">E-bikes site page</flux:button>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- New motorcycles first, then e-bikes (same finance flow), then used --}}
    @if ($newForFinance->isNotEmpty())
        <section>
            <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                <flux:heading size="lg">New motorcycles</flux:heading>
                <a href="{{ route('site.bikes', ['filter' => 'new']) }}" class="text-sm font-medium text-brand-red hover:text-brand-red-dark">Full new stock</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($newForFinance as $m)
                    @php
                        $img = NgnMotorcycleImage::urlForNewStock($m->file_path ?? null);
                        $price = (float) ($m->sale_new_price ?? 0);
                    @endphp
                    <flux:card class="overflow-hidden flex flex-col border border-gray-200 dark:border-gray-700">
                        <a href="{{ route('site.bikes.show', ['type' => 'new', 'id' => $m->id]) }}" class="block">
                            <img src="{{ $img }}" alt="{{ $m->make }} {{ $m->model }}" class="w-full h-44 object-cover" loading="lazy" />
                        </a>
                        <div class="p-4 flex flex-col flex-1 gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                <a href="{{ route('site.bikes.show', ['type' => 'new', 'id' => $m->id]) }}" class="hover:text-brand-red">{{ $m->make }} {{ $m->model }}</a>
                            </h3>
                            <p class="text-brand-red font-bold">
                                @if ($price > 0)
                                    £{{ number_format($price, 2) }}
                                @else
                                    <span class="text-gray-600 dark:text-gray-400 font-medium text-sm">Price on application</span>
                                @endif
                            </p>
                            <flux:button
                                href="{{ route('account.finance.browse', array_filter(['prefill_new' => $m->id, 'prefill_price' => $price > 0 ? $price : null])) }}#finance-enquiry"
                                variant="filled"
                                size="sm"
                                class="mt-auto w-full bg-brand-red text-white hover:bg-brand-red-dark"
                            >
                                Enquire on finance
                            </flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </section>
    @endif

    @if ($ebikesForFinance->isNotEmpty())
        <section>
            <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                <flux:heading size="lg">E-bikes for sale</flux:heading>
                <a href="{{ route('site.ebikes') }}" class="text-sm font-medium text-brand-red hover:text-brand-red-dark">E-bikes site page</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($ebikesForFinance as $bike)
                    @php
                        $img = NgnMotorcycleImage::urlForUsedSale($bike->image_one ?? null);
                        $maskedReg = $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : '';
                        $price = (float) ($bike->price ?? 0);
                    @endphp
                    <flux:card class="overflow-hidden flex flex-col border border-gray-200 dark:border-gray-700">
                        <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="block">
                            <img src="{{ $img }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-44 object-cover" loading="lazy" />
                        </a>
                        <div class="p-4 flex flex-col flex-1 gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="hover:text-brand-red">{{ $bike->make }} {{ $bike->model }}</a>
                            </h3>
                            <p class="text-xs text-gray-500">E-bike @if ($maskedReg)· Reg {{ $maskedReg }} @endif</p>
                            <p class="text-brand-red font-bold">
                                @if ($price > 0)
                                    £{{ number_format($price, 2) }}
                                @else
                                    <span class="text-gray-600 dark:text-gray-400 font-medium text-sm">Price on application</span>
                                @endif
                            </p>
                            <flux:button
                                href="{{ route('account.finance.browse', array_filter(['prefill_ebike' => $bike->id, 'prefill_price' => $price > 0 ? $price : null])) }}#finance-enquiry"
                                variant="filled"
                                size="sm"
                                class="mt-auto w-full bg-brand-red text-white hover:bg-brand-red-dark"
                            >
                                Enquire on finance
                            </flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Used motorcycles (listed for sale, excluding e-bikes) --}}
    @if ($usedForFinance->isNotEmpty())
        <section>
            <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                <flux:heading size="lg">Used motorcycles</flux:heading>
                <a href="{{ route('site.bikes') }}" class="text-sm font-medium text-brand-red hover:text-brand-red-dark">Full used stock</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($usedForFinance as $bike)
                    @php
                        $img = NgnMotorcycleImage::urlForUsedSale($bike->image_one ?? null);
                        $maskedReg = $bike->reg_no ? '****'.substr((string) $bike->reg_no, -3) : '';
                        $price = (float) ($bike->price ?? 0);
                    @endphp
                    <flux:card class="overflow-hidden flex flex-col border border-gray-200 dark:border-gray-700">
                        <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="block">
                            <img src="{{ $img }}" alt="{{ $bike->make }} {{ $bike->model }}" class="w-full h-44 object-cover" loading="lazy" />
                        </a>
                        <div class="p-4 flex flex-col flex-1 gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                <a href="{{ route('detail.used-motorcycle', ['id' => $bike->id]) }}" class="hover:text-brand-red">{{ $bike->make }} {{ $bike->model }}</a>
                            </h3>
                            @if ($maskedReg)
                                <p class="text-xs text-gray-500">Reg {{ $maskedReg }}</p>
                            @endif
                            <p class="text-brand-red font-bold">
                                @if ($price > 0)
                                    £{{ number_format($price, 2) }}
                                @else
                                    <span class="text-gray-600 dark:text-gray-400 font-medium text-sm">Price on application</span>
                                @endif
                            </p>
                            <flux:button
                                href="{{ route('account.finance.browse', array_filter(['prefill_used' => $bike->id, 'prefill_price' => $price > 0 ? $price : null])) }}#finance-enquiry"
                                variant="filled"
                                size="sm"
                                class="mt-auto w-full bg-brand-red text-white hover:bg-brand-red-dark"
                            >
                                Enquire on finance
                            </flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </section>
    @endif

    <section id="finance-enquiry" class="scroll-mt-24 pt-4 border-t border-gray-200 dark:border-gray-700">
        <flux:heading size="lg" class="mb-4">Finance calculator &amp; enquiry</flux:heading>
        <livewire:portal.finance.enquiry-panel wire:key="{{ $enquiryWireKey }}" />
    </section>

    {{-- Legacy self-serve listing (hidden; retained for reference — not offered to customers) --}}
    <div class="hidden" aria-hidden="true">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Finance a Motorbike (legacy)</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Browse available motorbikes and apply for finance.</p>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Make, model…" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                    <select wire:model.live="branch_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">All Branches</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Condition</label>
                    <select wire:model.live="condition" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">All</option>
                        <option value="0">New</option>
                        <option value="1">Used</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Min Deposit</label>
                    <input type="number" wire:model.live="minDeposit" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
            </div>
        </div>
        @if ($motorbikes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach ($motorbikes as $bike)
                    @php
                        $estimatedPrice = isset($bike->sale_price) && $bike->sale_price > 0 ? (float) $bike->sale_price : 5000;
                        $bikeMinDeposit = max((float) $minDeposit, $estimatedPrice * 0.1);
                        $financeAmount = max(0, $estimatedPrice - $bikeMinDeposit);
                        $monthlyPayment = $financeAmount > 0 ? round($financeAmount / 52, 2) : 0;
                    @endphp
                    <div wire:key="legacy-bike-{{ $bike->id }}" class="bg-white dark:bg-gray-800 shadow overflow-hidden">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold">{{ $bike->make }} {{ $bike->model }}</h3>
                            <p class="text-sm text-gray-600">£{{ number_format($estimatedPrice, 2) }} · legacy grid</p>
                            <p class="text-sm">From £{{ number_format($monthlyPayment, 2) }} / wk (old calc)</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
