<flux:card class="p-6 space-y-5 border border-gray-200 dark:border-gray-700">
    <div>
        <flux:heading size="lg">Finance enquiry</flux:heading>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Enquiry only — our team prepares your contract and sends a signing link when ready. Figures here are illustrative.
        </p>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Pick a bike from the listings above (new, used, or e-bike) and use <strong>Enquire on finance</strong> on that card so this form is linked to the correct bike — required before you can send.
        </p>
    </div>

    @error('bikeFromListing')
        <flux:callout variant="danger" icon="exclamation-triangle" class="mb-2">
            <flux:callout.text>{{ $message }}</flux:callout.text>
        </flux:callout>
    @enderror

    @if ($bikeSummaryLine)
        <flux:callout variant="info" icon="information-circle">
            <flux:callout.text>Selected: {{ $bikeSummaryLine }}</flux:callout.text>
        </flux:callout>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <flux:field>
            <flux:label>Bike price (£)</flux:label>
            <flux:input wire:model.live="bikePrice" type="number" min="500" step="50" />
            <flux:error name="bikePrice" />
        </flux:field>
        <flux:field>
            <flux:label>Deposit (£)</flux:label>
            <flux:input wire:model.live="deposit" type="number" min="0" max="{{ max(0, (float) $bikePrice) }}" step="50" />
            <flux:error name="deposit" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cannot exceed bike price.</p>
        </flux:field>
    </div>

    <flux:field>
        <flux:label>Instalment term (months) *</flux:label>
        <flux:select wire:model.live="termMonths" variant="listbox" placeholder="Select term">
            <flux:select.option value="6">6 months</flux:select.option>
            <flux:select.option value="12">12 months</flux:select.option>
        </flux:select>
        <flux:error name="termMonths" />
    </flux:field>

    <flux:field>
        <flux:label>Preferred arrangement</flux:label>
        <flux:select wire:model.live="financePlan" variant="listbox" placeholder="Choose one">
            <flux:select.option value="{{ \App\Livewire\Portal\Finance\EnquiryPanel::CONTRACT_SALE }}">
                Instalment sale (standard sale agreement; term above)
            </flux:select.option>
            <flux:select.option value="{{ \App\Livewire\Portal\Finance\EnquiryPanel::CONTRACT_SUBSCRIPTION }}">
                12-month subscription (merged sale + subscription terms)
            </flux:select.option>
        </flux:select>
        <flux:error name="financePlan" />
    </flux:field>

    @if ($financePlan === \App\Livewire\Portal\Finance\EnquiryPanel::CONTRACT_SALE)
        @php $indicative = $this->indicativeMonthly(); @endphp
        @if ($indicative !== null)
            <div class="border border-gray-200 dark:border-gray-600 p-4 text-center bg-gray-50 dark:bg-gray-900/50">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Indicative monthly (balance ÷ {{ (int) $termMonths }})</p>
                <p class="text-2xl font-bold text-brand-red mt-1">£{{ number_format($indicative, 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">Illustrative only. Final payment schedule on your contract from our team.</p>
            </div>
        @endif
    @else
        <flux:field>
            <flux:label>Subscription group (monthly fee) *</flux:label>
            <flux:select wire:model.live="subscriptionGroup" variant="listbox" placeholder="Choose one group">
                @foreach (\App\Livewire\Portal\Finance\EnquiryPanel::SUBSCRIPTION_GROUPS_TEXT as $groupKey => $groupLabel)
                    <flux:select.option value="{{ $groupKey }}">{{ $groupLabel }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:error name="subscriptionGroup" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">One group only. Final group and inclusions are confirmed by staff on your contract.</p>
        </flux:field>
    @endif

    <flux:field>
        <flux:label>Notes for our team (optional)</flux:label>
        <flux:textarea wire:model="notes" rows="3" placeholder="Accessories, part-exchange, preferred branch…" />
        <flux:error name="notes" />
    </flux:field>

    <flux:button type="button" wire:click="submitEnquiry" variant="filled" class="w-full bg-brand-red text-white hover:bg-brand-red-dark" wire:loading.attr="disabled" wire:target="submitEnquiry">
        <span wire:loading.remove wire:target="submitEnquiry">Send finance enquiry</span>
        <span wire:loading wire:target="submitEnquiry">Sending…</span>
    </flux:button>
</flux:card>
