<div>
    <div class="border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 mb-4">
        <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Rental referrals</h2>
            <div class="mt-2 flex flex-wrap items-center gap-2">
                @include('flux-admin.partials.rentals.status-pill', ['label' => $availablePoints.' unused', 'tone' => $availablePoints > 0 ? 'green' : 'zinc'])
                @include('flux-admin.partials.rentals.status-pill', ['label' => $pendingPoints.' pending', 'tone' => $pendingPoints > 0 ? 'orange' : 'zinc'])
                @include('flux-admin.partials.rentals.status-pill', ['label' => $directAwards->count().' direct gifts', 'tone' => 'blue'])
            </div>
        </div>
        <div class="p-5 space-y-5">
            @if(! $eligible)
                <p class="text-sm text-zinc-500">Not yet eligible to refer (needs one paid weekly invoice).</p>
            @endif

            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Friends they referred</h3>
                <p class="text-xs text-zinc-500 mt-1">Each qualified friend = 100 programme points. Direct gifts are listed separately and do not spend these points.</p>
                <ul class="mt-3 text-sm space-y-2">
                    @forelse($made as $row)
                        <li class="flex flex-wrap items-center gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                            <a href="{{ route('flux-admin.rental-referrals.show', $row) }}" class="text-blue-600 dark:text-blue-400 hover:underline">#{{ $row->id }} {{ $row->submitted_name }}</a>
                            @if($row->referred)
                                <a href="{{ route('flux-admin.customers.show', $row->referred_customer_id) }}" class="text-xs text-zinc-500 hover:underline">matched #{{ $row->referred_customer_id }} {{ $row->referred->first_name }} {{ $row->referred->last_name }}</a>
                            @endif
                            @include('flux-admin.partials.rentals.status-pill', ['label' => $row->staffStatusLabel(), 'tone' => $row->staffStatusTone()])
                            @include('flux-admin.partials.rentals.status-pill', ['label' => $row->pointsStatusLabel(), 'tone' => $row->pointsStatusTone()])
                        </li>
                    @empty
                        <li class="text-zinc-500">None.</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Who referred them</h3>
                <ul class="mt-3 text-sm space-y-2">
                    @forelse($received as $row)
                        <li class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('flux-admin.rental-referrals.show', $row) }}" class="text-blue-600 dark:text-blue-400 hover:underline">#{{ $row->id }}</a>
                            @if($row->referrer)
                                <a href="{{ route('flux-admin.customers.show', $row->referrer_customer_id) }}" class="hover:underline">{{ $row->referrer->first_name }} {{ $row->referrer->last_name }}</a>
                            @endif
                            @include('flux-admin.partials.rentals.status-pill', ['label' => $row->staffStatusLabel(), 'tone' => $row->staffStatusTone()])
                        </li>
                    @empty
                        <li class="text-zinc-500">None.</li>
                    @endforelse
                </ul>
            </div>

            @if($directAwards->isNotEmpty())
                <div>
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Staff direct gifts</h3>
                    <ul class="mt-3 text-sm space-y-2">
                        @foreach($directAwards as $award)
                            <li class="flex flex-wrap items-center gap-2">
                                @include('flux-admin.partials.rentals.status-pill', ['label' => 'Direct', 'tone' => 'blue'])
                                @include('flux-admin.partials.rentals.status-pill', ['label' => $award->payoutStatusLabel(), 'tone' => $award->payoutStatusTone()])
                                <span>£{{ number_format((float) $award->amount, 2) }}</span>
                                <a href="{{ route('flux-admin.rentals.show', $award->awarded_booking_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">booking #{{ $award->awarded_booking_id }}</a>
                                <span class="text-xs text-zinc-500">invoice #{{ $award->awarded_invoice_id }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
