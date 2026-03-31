@props([
    'member',
    'dash',
    'sellFormId' => 'clubdash_sell_form',
])

@php
    $purchases = $dash['purchases'];
    $redemptions = $dash['redemptions'];
    $spendings = $dash['spendings'];
    $transactions = $dash['transactions'];
    $referrals = $dash['referrals'];
    $total_reward = $dash['total_reward'];
    $total_redeemed = $dash['total_redeemed'];
    $total_not_redeemed = $dash['total_not_redeemed'];
    $qualified_referal = $dash['qualified_referal'];
    $currentYear = (int) date('Y');
@endphp

<style>
    .club-uk-vrm-container {
        background: #FED71B;
        border: 3px solid #000;
        padding: 0;
        display: inline-block;
        width: 100%;
        max-width: 280px;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background-image: linear-gradient(45deg, #FED71B 25%, #FFE03D 25%, #FFE03D 50%, #FED71B 50%, #FED71B 75%, #FFE03D 75%, #FFE03D 100%);
        background-size: 4px 4px;
    }
    .club-uk-vrm-container::before {
        content: "GB";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 30px;
        background: #0055a4;
        color: white;
        font-size: 12px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 2px solid #003876;
        z-index: 1;
    }
    .club-uk-vrm-input {
        width: 90%;
        height: 44px;
        padding: 8px 12px 8px 35px;
        font-size: 22px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
        border: none;
        outline: none;
        background: transparent;
        color: #000;
        text-align: center;
        margin: 0;
    }
    .club-uk-vrm-input:focus {
        outline: none;
        box-shadow: none;
    }
</style>

<div class="space-y-6"
    x-data="{
        tab: 'purchases',
        storageKey: 'ngnclub_dashboard_tab',
        tabs: ['sell', 'purchases', 'spendings', 'transactions', 'referrals', 'profile'],
        init() {
            const saved = localStorage.getItem(this.storageKey);
            if (saved && this.tabs.includes(saved)) this.tab = saved;
            this.$watch('tab', (v) => { if (this.tabs.includes(v)) localStorage.setItem(this.storageKey, v); });
        },
        show(name) { this.tab = name; }
    }">
    {{-- Summary (legacy totals) --}}
    <div class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
        <div class="text-center mb-4">
            <p class="text-lg font-semibold text-gray-900 dark:text-white">Hey, {{ $member->full_name }}!</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div class="p-4 border border-green-200 dark:border-green-800">
                <p class="text-sm font-semibold text-green-700 dark:text-green-400">Your total savings</p>
                <p class="text-2xl font-black text-green-600 dark:text-green-300">£{{ number_format($total_reward, 2) }}</p>
            </div>
            <div class="p-4 border border-amber-200 dark:border-amber-800">
                <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">Credits redeemed</p>
                <p class="text-2xl font-black text-amber-600 dark:text-amber-300">£{{ number_format($total_redeemed, 2) }}</p>
            </div>
            <div class="p-4 border border-green-200 dark:border-green-800">
                <p class="text-sm font-semibold text-green-700 dark:text-green-400">Available credits</p>
                <p class="text-2xl font-black text-green-600 dark:text-green-300">£{{ number_format($total_not_redeemed, 2) }}</p>
            </div>
        </div>
    </div>

    @if ($qualified_referal)
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.text>
                You are qualified for a referral reward of £5 on each successful referral (T&amp;C apply).
            </flux:callout.text>
        </flux:callout>
    @endif

    {{-- Tabs --}}
    <div class="border-b border-gray-200 dark:border-gray-700 flex flex-wrap gap-0">
        <button type="button" @click="show('sell')"
            :class="tab === 'sell' ? 'border-b-2 border-brand-red text-brand-red font-semibold' : 'text-gray-600 dark:text-gray-400'"
            class="px-4 py-3 text-sm">
            <span class="text-[10px] font-bold uppercase text-brand-red align-top">New</span> Sell your motorbike
        </button>
        <button type="button" @click="show('purchases')"
            :class="tab === 'purchases' ? 'border-b-2 border-brand-red text-brand-red font-semibold' : 'text-gray-600 dark:text-gray-400'"
            class="px-4 py-3 text-sm">Purchases</button>
        <button type="button" @click="show('spendings')"
            :class="tab === 'spendings' ? 'border-b-2 border-brand-red text-brand-red font-semibold' : 'text-gray-600 dark:text-gray-400'"
            class="px-4 py-3 text-sm">Spendings</button>
        <button type="button" @click="show('transactions')"
            :class="tab === 'transactions' ? 'border-b-2 border-brand-red text-brand-red font-semibold' : 'text-gray-600 dark:text-gray-400'"
            class="px-4 py-3 text-sm">Transactions</button>
        <button type="button" @click="show('referrals')"
            :class="tab === 'referrals' ? 'border-b-2 border-brand-red text-brand-red font-semibold' : 'text-gray-600 dark:text-gray-400'"
            class="px-4 py-3 text-sm">Referrals</button>
        <button type="button" @click="show('profile')"
            :class="tab === 'profile' ? 'border-b-2 border-brand-red text-brand-red font-semibold' : 'text-gray-600 dark:text-gray-400'"
            class="px-4 py-3 text-sm">Profile</button>
    </div>

    <div class="min-h-[200px]">
        {{-- Purchases --}}
        <div x-show="tab === 'purchases'" x-cloak class="space-y-4">
            <h2 class="text-base font-bold text-brand-red border-l-4 border-brand-red pl-3">Your purchases</h2>
            @if ($purchases->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-sm">You have no purchases yet.</p>
            @else
                @foreach ($purchases as $purchase)
                    <div class="border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/40">
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ $purchase->date ? \Carbon\Carbon::parse($purchase->date)->format('jS F Y, h:i A') : '—' }}
                        </p>
                        <div class="flex flex-wrap justify-between gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">POS invoice</p>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $purchase->pos_invoice }}</p>
                            </div>
                            <div class="text-end">
                                <p class="text-xs text-gray-500 uppercase">Credit</p>
                                <p class="font-bold text-green-600">£{{ number_format($purchase->discount, 2) }}</p>
                                <p class="text-sm mt-1 {{ ($purchase->discount == $purchase->redeem_amount) ? 'text-green-600' : 'text-red-600' }}">
                                    @if ($purchase->discount == $purchase->redeem_amount)
                                        {{ number_format($purchase->redeem_amount, 2) }} redeemed
                                    @elseif ($purchase->redeem_amount > 0)
                                        {{ number_format($purchase->redeem_amount, 2) }} redeemed — £{{ number_format($purchase->discount - $purchase->redeem_amount, 2) }} left
                                    @else
                                        £0.00
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Spendings --}}
        <div x-show="tab === 'spendings'" x-cloak class="space-y-4">
            <h2 class="text-base font-bold text-brand-red border-l-4 border-brand-red pl-3">Your spending record</h2>
            <p class="text-sm"><strong>Total spending:</strong> £{{ number_format($spendings->sum('total'), 2) }}</p>
            @if ($spendings->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-sm">You have no spending records yet.</p>
            @else
                @foreach ($spendings as $spending)
                    <div class="border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/40">
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                            {{ $spending->date ? \Carbon\Carbon::parse($spending->date)->format('jS F Y, h:i A') : '—' }}
                        </p>
                        <div class="flex flex-wrap justify-between gap-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">POS invoice</p>
                                <p class="font-bold">{{ $spending->pos_invoice }}</p>
                            </div>
                            <div class="text-end">
                                <p class="text-xs text-gray-500 uppercase">Total amount</p>
                                <p class="font-bold">£{{ number_format($spending->total, 2) }}</p>
                                @if ($spending->branch_id)
                                    <p class="text-xs text-gray-500 mt-1">Branch: {{ $spending->branch_id }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Transactions --}}
        <div x-show="tab === 'transactions'" x-cloak class="space-y-4">
            <h2 class="text-base font-bold text-brand-red border-l-4 border-brand-red pl-3">All transactions</h2>
            @if ($transactions->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-sm">You have no transactions yet.</p>
            @else
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800 border-b-2 border-brand-red text-left text-xs uppercase tracking-wide">
                                <th class="p-2">Inv#</th>
                                <th class="p-2 text-end">Total</th>
                                <th class="p-2 text-end">Discount</th>
                                <th class="p-2 text-end">Redeemed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $tx)
                                <tr class="border-t border-gray-100 dark:border-gray-800 odd:bg-gray-50/80 dark:odd:bg-gray-900/30">
                                    <td class="p-2 align-top">
                                        <strong>{{ $tx->pos_invoice }}</strong><br>
                                        <span class="text-xs text-gray-500">{{ $tx->date }}</span>
                                    </td>
                                    <td class="p-2 text-end tabular-nums">£{{ number_format($tx->amount, 2) }}</td>
                                    <td class="p-2 text-end tabular-nums">£{{ number_format($tx->discount, 2) }}</td>
                                    <td class="p-2 text-end tabular-nums">£{{ number_format($tx->redeemed, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Referrals list --}}
        <div x-show="tab === 'referrals'" x-cloak class="space-y-4">
            <h2 class="text-base font-bold text-brand-red border-l-4 border-brand-red pl-3">Your referrals</h2>
            @if ($referrals->isEmpty())
                <p class="text-gray-600 dark:text-gray-400 text-sm">You have no referrals yet.</p>
            @else
                @foreach ($referrals as $referral)
                    <div class="border border-gray-300 dark:border-gray-600 p-4">
                        <div class="flex flex-wrap justify-between gap-2">
                            <div>
                                <p class="text-sm"><strong>Full name:</strong> {{ $referral->referred_full_name }}</p>
                                <p class="text-sm"><strong>Phone:</strong> {{ $referral->referred_phone }}</p>
                            </div>
                            <div>
                                @if (! $referral->validated)
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-amber-100 text-amber-900 dark:bg-amber-900/40 dark:text-amber-200">Pending</span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-900 dark:bg-green-900/40 dark:text-green-200">Accepted</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Profile --}}
        <div x-show="tab === 'profile'" x-cloak class="space-y-4">
            <h2 class="text-base font-bold text-brand-red border-l-4 border-brand-red pl-3">Your profile</h2>
            @if (session('profile_success'))
                <flux:callout variant="success" icon="check-circle">
                    <flux:callout.text>{{ session('profile_success') }}</flux:callout.text>
                </flux:callout>
            @endif
            @if ($errors->has('profile.*'))
                <flux:callout variant="danger" icon="exclamation-triangle">
                    @foreach ($errors->get('profile.*') as $err)
                        <flux:callout.text>{{ $err[0] ?? '' }}</flux:callout.text>
                    @endforeach
                </flux:callout>
            @endif

            <form action="{{ route('ngnclub.profile.update') }}" method="POST" class="space-y-6 border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Full name</flux:label>
                        <flux:input value="{{ $member->full_name }}" readonly />
                        <p class="text-xs text-gray-500 mt-1">Contact our store to change this if it is wrong.</p>
                    </flux:field>
                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input value="{{ $member->email }}" readonly type="email" />
                        <p class="text-xs text-gray-500 mt-1">Contact our store to change this if it is wrong.</p>
                    </flux:field>
                    <flux:field>
                        <flux:label>Phone</flux:label>
                        <flux:input value="{{ $member->phone }}" readonly />
                    </flux:field>
                </div>

                <div class="border border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/30">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4">Vehicle details (optional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Make</flux:label>
                            <flux:input name="profile[make]" value="{{ $member->make }}" maxlength="50" class="uppercase" pattern="[A-Za-z0-9/\s-]*" title="Letters, numbers, forward slash, and hyphens only" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Model</flux:label>
                            <flux:input name="profile[model]" value="{{ $member->model }}" maxlength="50" class="uppercase" pattern="[A-Za-z0-9/ -]*" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Year</flux:label>
                            <flux:input name="profile[year]" value="{{ $member->year }}" maxlength="4" inputmode="numeric" pattern="[0-9]*" title="4-digit year" />
                        </flux:field>
                    </div>
                    <flux:field class="mt-4">
                        <flux:label class="text-center">Vehicle registration number</flux:label>
                        <div class="flex justify-center">
                            <div class="club-uk-vrm-container">
                                <input type="text" name="profile[vrm]" id="profile_vrm_{{ $sellFormId }}"
                                    class="club-uk-vrm-input" value="{{ $member->vrm }}" maxlength="12"
                                    autocomplete="off" />
                            </div>
                        </div>
                    </flux:field>
                </div>
                <div class="text-center">
                    <flux:button type="submit" variant="filled" class="bg-brand-red text-white">Update profile</flux:button>
                </div>
            </form>
        </div>

        {{-- Sell bike --}}
        <div x-show="tab === 'sell'" x-cloak>
            <h2 class="text-base font-bold text-brand-red border-l-4 border-brand-red pl-3 mb-4">Sell your bike</h2>
            <x-club.partials.sell-bike-estimator :form-id="$sellFormId" />
        </div>
    </div>
</div>
